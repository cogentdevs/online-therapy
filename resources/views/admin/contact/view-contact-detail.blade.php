@extends('layouts.adminLayout.admin-design')

@section('title', 'Contact Message Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="mb-1">Contact Message Details</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Contact Messages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('admin.contacts.index') }}">
                    <i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Contacts
                </a>
                @can('contacts.delete')
                    <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger" type="submit" data-delete-confirm
                            data-delete-title="Delete this Contact message?"
                            data-delete-text="This Contact message will be permanently deleted.">
                            <i class="fa-solid fa-trash me-2" aria-hidden="true"></i>Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <section class="card admin-settings-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h3>{{ $contact->subject }}</h3>
                    <p>Message received {{ $contact->created_at?->format('d M Y, h:i A') }}</p>
                </div>
                <span class="badge text-bg-success">Read</span>
            </div>
            <div class="card-body">
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3"><small class="d-block text-muted mb-1">Name</small><strong>{{ $contact->name }}</strong></div>
                    <div class="col-md-6 col-xl-3"><small class="d-block text-muted mb-1">Email</small><a class="text-break" href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></div>
                    <div class="col-md-6 col-xl-3"><small class="d-block text-muted mb-1">Phone</small><a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></div>
                    <div class="col-md-6 col-xl-3"><small class="d-block text-muted mb-1">Received Date</small><strong>{{ $contact->created_at?->format('d M Y, h:i A') }}</strong></div>
                </div>
                <div class="border rounded p-4 bg-light">
                    <small class="d-block text-muted mb-2">Message</small>
                    <div class="text-break" style="white-space: pre-wrap;">{{ $contact->message }}</div>
                </div>
            </div>
        </section>
    </div>
@endsection
