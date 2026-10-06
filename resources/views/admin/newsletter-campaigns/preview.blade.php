@extends('layouts.adminLayout.admin-design')

@section('title', 'Preview: '.$campaign->title)

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div><h2 class="mb-1">Newsletter Preview</h2><nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li><li class="breadcrumb-item"><a href="{{ route('admin.newsletter-campaigns.index') }}">Newsletter Campaigns</a></li><li class="breadcrumb-item"><a href="{{ route('admin.newsletter-campaigns.show', $campaign) }}">{{ $campaign->title }}</a></li><li class="breadcrumb-item active" aria-current="page">Preview</li></ol></nav></div>
            <a class="btn btn-outline-secondary" href="{{ route('admin.newsletter-campaigns.show', $campaign) }}"><i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Campaign</a>
        </div>
        <section class="card admin-settings-card">
            <div class="card-header"><h3>Email Preview</h3><p>This preview uses the same campaign email layout. Its unsubscribe text is non-actionable.</p></div>
            <div class="card-body bg-light p-3 p-lg-4">@include('frontend.mails.newsletter-campaign', ['campaign' => $campaign, 'isTest' => true])</div>
        </section>
    </div>
@endsection
