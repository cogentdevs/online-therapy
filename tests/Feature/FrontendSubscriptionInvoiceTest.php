<?php

use App\Mail\SubscriptionActivatedMailToUser;
use App\Mail\SubscriptionPurchasedMailToAdmin;
use App\Models\Currency;
use App\Models\GeneralSetting;
use App\Models\SubscriptionProduct;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionInvoiceService;
use Barryvdh\DomPDF\PDF;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::findOrCreate('user', 'web');
    $this->user = User::factory()->create(['name' => 'Invoice Owner', 'email' => 'owner@example.test', 'phone' => '03001234567', 'is_active' => true]);
    $this->user->assignRole('user');
    $this->otherUser = User::factory()->create(['is_active' => true]);
    $this->otherUser->assignRole('user');
    $this->currency = Currency::query()->create(['name' => 'Pakistani Rupee', 'code' => 'PKR', 'symbol' => 'Rs', 'isActive' => true]);
    $this->product = SubscriptionProduct::query()->create([
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'name' => 'Current Product Name',
        'currency_id' => $this->currency->id,
        'price' => 250,
        'duration_value' => 1,
        'duration_unit' => 'month',
        'isActive' => true,
    ]);
    GeneralSetting::query()->create([
        'app_name' => 'Digital Magazine',
        'contact_1' => '03001234567',
        'email' => 'digital@example.test',
        'footer_text' => 'Digital Magazine. All rights reserved.',
    ]);
});

function makeInvoiceSubscription(User $user, SubscriptionProduct $product, Currency $currency, array $overrides = []): UserSubscription
{
    return UserSubscription::query()->create(array_merge([
        'user_id' => $user->id,
        'subscription_product_id' => $product->id,
        'product_for' => SubscriptionProduct::FOR_MEMBERSHIP,
        'product_name' => 'Monthly Pack Snapshot',
        'currency_id' => $currency->id,
        'price' => 300,
        'discount' => 50,
        'total' => 250,
        'payment_method' => 'card',
        'start_date' => '2026-09-11',
        'end_date' => '2026-10-11',
        'status' => 'active',
        'is_active' => true,
        'created_at' => '2026-09-11 10:00:00',
    ], $overrides));
}

function fakeInvoicePdf(string $method, string $filename, Closure $assertData): void
{
    $pdfDocument = Mockery::mock(PDF::class);
    $pdfDocument->shouldReceive('loadView')->once()->with(
        'frontend.user-account.invoice_pdf',
        Mockery::on(function (array $data) use ($assertData): bool {
            $assertData($data);

            return true;
        }),
    )->andReturnSelf();
    $pdfDocument->shouldReceive('setPaper')->once()->with('a4', 'portrait')->andReturnSelf();
    $pdfDocument->shouldReceive($method)->once()->with($filename)->andReturn(
        response('pdf-document', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($method === 'stream' ? 'inline' : 'attachment').'; filename="'.$filename.'"',
        ]),
    );
    app()->instance('dompdf.wrapper', $pdfDocument);
}

test('guest cannot view or download a subscription invoice', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency);

    $this->get(route('front.account.subscriptions.invoice.view', $subscription))->assertRedirect(route('front.login'));
    $this->get(route('front.account.subscriptions.invoice.download', $subscription))->assertRedirect(route('front.login'));
});

test('owner can view an inline invoice using snapshot and related invoice data', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency, [
        'invoice_no' => 20260001,
        'order_no' => 20261001,
        'transaction_id' => 'gateway-transaction-42',
        'duration_value_snapshot' => 3,
        'duration_unit_snapshot' => 'month',
    ]);

    fakeInvoicePdf('stream', 'invoice-20260001.pdf', function (array $data) use ($subscription): void {
        expect($data['userSubscription']->is($subscription))->toBeTrue()
            ->and($data['userSubscription']->product_name)->toBe('Monthly Pack Snapshot')
            ->and($data['userSubscription']->total)->toBe('250.00')
            ->and($data['userSubscription']->user->email)->toBe('owner@example.test')
            ->and($data['duration'])->toBe('3 Months')
            ->and($data['productType'])->toBe('Membership')
            ->and($data['statusContent']['heading'])->toBe('Subscription Activated');
    });

    $this->actingAs($this->user)
        ->get(route('front.account.subscriptions.invoice.view', $subscription))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'inline; filename="invoice-20260001.pdf"');
});

test('owner can download with fallback filename while nullable document fields stay blank', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency);
    $filename = 'invoice-subscription.pdf';

    fakeInvoicePdf('download', $filename, function (array $data): void {
        expect($data['userSubscription']->invoice_no)->toBeNull()
            ->and($data['userSubscription']->order_no)->toBeNull()
            ->and($data['userSubscription']->transaction_id)->toBeNull();

        $html = view('frontend.user-account.invoice_pdf', $data)->render();
        expect($html)->toContain('Invoice #')->toContain('Transaction ID')->toContain('Order Number')
            ->toContain('data:image/svg+xml;base64,')
            ->not->toContain('>YT<')->not->toContain('>IG<')
            ->not->toContain('MEM-')->not->toContain('TXN-')->not->toContain('ORD-');
    });

    $this->actingAs($this->user)
        ->get(route('front.account.subscriptions.invoice.download', $subscription))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf')
        ->assertHeader('content-disposition', 'attachment; filename="'.$filename.'"');
});

test('a user cannot view or download another users invoice', function () {
    $subscription = makeInvoiceSubscription($this->otherUser, $this->product, $this->currency);

    $this->actingAs($this->user)->get(route('front.account.subscriptions.invoice.view', $subscription))->assertNotFound();
    $this->actingAs($this->user)->get(route('front.account.subscriptions.invoice.download', $subscription))->assertNotFound();
});

test('subscription history exposes row-specific invoice actions in html and datatable data', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency);

    $this->actingAs($this->user)->get(route('front.account.subscriptions'))
        ->assertSuccessful()
        ->assertSee(route('front.account.subscriptions.invoice.view', $subscription), false)
        ->assertSee(route('front.account.subscriptions.invoice.download', $subscription), false);

    $this->actingAs($this->user)->getJson(route('front.account.subscriptions', ['datatable' => 1]))
        ->assertSuccessful()
        ->assertJsonPath('data.0.invoice_view_url', route('front.account.subscriptions.invoice.view', $subscription))
        ->assertJsonPath('data.0.invoice_download_url', route('front.account.subscriptions.invoice.download', $subscription));
});

test('user purchase mail attaches one invoice rendered through the shared pdf template', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency, [
        'invoice_no' => 20260001,
    ]);
    $pdfBytes = '%PDF-1.4 shared-invoice-document';
    $pdfDocument = Mockery::mock(PDF::class);
    $pdfDocument->shouldReceive('loadView')->once()->with(
        'frontend.user-account.invoice_pdf',
        Mockery::on(fn (array $data): bool => $data['userSubscription']->is($subscription)
            && $data['duration'] === '1 Month'
            && $data['productType'] === 'Membership'),
    )->andReturnSelf();
    $pdfDocument->shouldReceive('setPaper')->once()->with('a4', 'portrait')->andReturnSelf();
    $pdfDocument->shouldReceive('output')->once()->andReturn($pdfBytes);
    app()->instance('dompdf.wrapper', $pdfDocument);

    $attachments = (new SubscriptionActivatedMailToUser($subscription))->attachments();
    $attachmentBytes = $attachments[0]->attachWith(
        fn (): null => null,
        fn (Closure $data): string => $data(),
    );

    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe('invoice-20260001.pdf')
        ->and($attachments[0]->mime)->toBe('application/pdf')
        ->and($attachmentBytes)->toBe($pdfBytes)
        ->and(str_starts_with($attachmentBytes, '%PDF-'))->toBeTrue();
});

test('user mail accepts blank invoice fields and uses the subscription filename fallback', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency);
    $invoiceService = Mockery::mock(SubscriptionInvoiceService::class);
    $invoiceService->shouldReceive('filename')->once()->with($subscription)->andReturn('invoice-subscription.pdf');
    $invoiceService->shouldReceive('content')->once()->with($subscription)->andReturn('%PDF-1.4 blank-fields');
    app()->instance(SubscriptionInvoiceService::class, $invoiceService);

    $attachments = (new SubscriptionActivatedMailToUser($subscription))->attachments();
    $attachmentBytes = $attachments[0]->attachWith(fn (): null => null, fn (Closure $data): string => $data());

    expect($subscription->invoice_no)->toBeNull()
        ->and($subscription->order_no)->toBeNull()
        ->and($subscription->transaction_id)->toBeNull()
        ->and($attachments[0]->as)->toBe('invoice-subscription.pdf')
        ->and($attachmentBytes)->toStartWith('%PDF-');
});

test('admin purchase mail remains without an invoice attachment', function () {
    $subscription = makeInvoiceSubscription($this->user, $this->product, $this->currency);

    expect((new SubscriptionPurchasedMailToAdmin($subscription))->attachments())->toBe([]);
});
