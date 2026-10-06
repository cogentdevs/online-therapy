<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendNewsletterTestRequest;
use App\Http\Requests\Admin\StoreNewsletterCampaignRequest;
use App\Mail\NewsletterCampaignTestMail;
use App\Models\NewsletterCampaign;
use App\Services\NewsletterBrevoCampaignService;
use App\Services\NewsletterCampaignContentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NewsletterCampaignController extends Controller
{
    public function __construct(
        private readonly NewsletterCampaignContentService $content,
        private readonly NewsletterBrevoCampaignService $delivery,
    ) {}

    public function index(): View
    {
        return view('admin.newsletter-campaigns.index', ['campaigns' => NewsletterCampaign::query()->with('creator')->withCount('contents')->latest()->paginate(25)]);
    }

    public function create(): View
    {
        return $this->formView(new NewsletterCampaign);
    }

    public function store(StoreNewsletterCampaignRequest $request): RedirectResponse
    {
        $campaign = DB::transaction(function () use ($request) {
            $campaign = NewsletterCampaign::query()->create($request->safe()->only(['title', 'short_description']));
            $this->syncContents($campaign, $request->validated('contents'));

            return $campaign;
        });

        return to_route('admin.newsletter-campaigns.show', $campaign)->with('status', 'Newsletter campaign created.');
    }

    public function show(NewsletterCampaign $newsletterCampaign): View
    {
        return view('admin.newsletter-campaigns.show', ['campaign' => $this->content->hydrate($newsletterCampaign)]);
    }

    public function edit(NewsletterCampaign $newsletterCampaign): View
    {
        abort_unless($newsletterCampaign->status === NewsletterCampaign::STATUS_DRAFT, 422);

        return $this->formView($this->content->hydrate($newsletterCampaign));
    }

    public function update(StoreNewsletterCampaignRequest $request, NewsletterCampaign $newsletterCampaign): RedirectResponse
    {
        abort_unless($newsletterCampaign->status === NewsletterCampaign::STATUS_DRAFT, 422);
        DB::transaction(function () use ($request, $newsletterCampaign) {
            $newsletterCampaign->update($request->safe()->only(['title', 'short_description']));
            $this->syncContents($newsletterCampaign, $request->validated('contents'));
        });

        return to_route('admin.newsletter-campaigns.show', $newsletterCampaign)->with('status', 'Newsletter campaign updated.');
    }

    public function destroy(NewsletterCampaign $newsletterCampaign): RedirectResponse
    {
        abort_unless($newsletterCampaign->status === NewsletterCampaign::STATUS_DRAFT, 422);
        $newsletterCampaign->delete();

        return to_route('admin.newsletter-campaigns.index')->with('status', 'Draft campaign deleted.');
    }

    public function preview(NewsletterCampaign $newsletterCampaign): View
    {
        return view('admin.newsletter-campaigns.preview', ['campaign' => $this->content->hydrate($newsletterCampaign), 'isTest' => true]);
    }

    public function sendTest(SendNewsletterTestRequest $request, NewsletterCampaign $newsletterCampaign): RedirectResponse
    {
        abort_unless($newsletterCampaign->status === NewsletterCampaign::STATUS_DRAFT, 422);
        $campaign = $this->content->hydrate($newsletterCampaign);
        Mail::to($request->validated('email'))->send(new NewsletterCampaignTestMail($campaign));

        return back()->with('status', 'Test email sent.');
    }

    public function sendNow(NewsletterCampaign $newsletterCampaign): RedirectResponse
    {
        $sent = $this->delivery->sendNow($newsletterCampaign);

        return back()->with($sent ? 'status' : 'error', $sent ? 'Newsletter campaign sent successfully.' : 'Newsletter campaign delivery failed. You may retry the failed campaign.');
    }

    public function retry(NewsletterCampaign $newsletterCampaign): RedirectResponse
    {
        $sent = $this->delivery->retry($newsletterCampaign);

        return back()->with($sent ? 'status' : 'error', $sent ? 'Newsletter campaign sent successfully.' : 'Newsletter campaign retry failed. You may try again.');
    }

    private function formView(NewsletterCampaign $campaign): View
    {
        return view('admin.newsletter-campaigns.form', ['campaign' => $campaign, 'articles' => $this->content->eligibleArticles(), 'magazines' => $this->content->eligibleMagazines()]);
    }

    private function syncContents(NewsletterCampaign $campaign, array $contents): void
    {
        $campaign->contents()->delete();
        foreach (collect($contents)->sortBy('sort_order')->values() as $i => $item) {
            $campaign->contents()->create(['content_type' => $item['type'], 'content_id' => $item['id'], 'sort_order' => $i + 1]);
        }
    }
}
