<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterBrevoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $filter = (string) $request->query('filter', 'all');
        $subscribers = NewsletterSubscriber::query()
            ->when($filter === 'subscribed', fn ($query) => $query->where('status', NewsletterSubscriber::STATUS_SUBSCRIBED))
            ->when($filter === 'unsubscribed', fn ($query) => $query->where('status', NewsletterSubscriber::STATUS_UNSUBSCRIBED))
            ->when($filter === 'failed', fn ($query) => $query->where('brevo_sync_status', NewsletterSubscriber::SYNC_FAILED))
            ->latest()
            ->get();

        return view('admin.newsletter-subscribers.index', compact('filter', 'subscribers'));
    }

    public function resync(NewsletterSubscriber $newsletterSubscriber, NewsletterBrevoService $brevo): RedirectResponse
    {
        $synced = $brevo->sync($newsletterSubscriber);

        return back()->with(
            $synced ? 'status' : 'error',
            $synced ? 'Subscriber state synchronized with the Newsletter list.' : 'Brevo synchronization failed. The local subscriber state remains saved.',
        );
    }
}
