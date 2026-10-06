@extends('layouts.adminLayout.admin-design')

@section('title', 'Ask Question '.$askQuestion->question_no)

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $askQuestion->question_no }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ask-questions.index') }}">Ask Questions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.ask-questions.index') }}"><i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i>Back to Ask Questions</a>
    </div>

    @if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <section class="card admin-settings-card mb-4">
        <div class="card-header"><h3>Question Details</h3></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4"><strong>Question No.</strong><div>{{ $askQuestion->question_no }}</div></div>
                <div class="col-md-4"><strong>Status</strong><div><span class="badge {{ match ($askQuestion->status) { 'answered' => 'text-bg-success', 'closed' => 'text-bg-secondary', default => 'text-bg-warning' } }}">{{ $askQuestion->statusLabel() }}</span></div></div>
                <div class="col-md-4"><strong>Submitted</strong><div>{{ $askQuestion->created_at?->format('d M Y, h:i A') }}</div></div>
                <div class="col-md-4"><strong>Name</strong><div>{{ $askQuestion->name }}</div></div>
                <div class="col-md-4"><strong>Email</strong><div>{{ $askQuestion->email }}</div></div>
                <div class="col-md-4"><strong>Phone</strong><div>{{ $askQuestion->phone }}</div></div>
                <div class="col-12"><strong>Subject</strong><div>{{ $askQuestion->subject }}</div></div>
                <div class="col-12"><strong>Question</strong><div class="border rounded bg-light p-3" style="white-space: pre-line; overflow-wrap: anywhere;">{{ $askQuestion->sawal }}</div></div>
            </div>
        </div>
    </section>

    <section class="card admin-settings-card">
        <div class="card-header"><h3>Admin Response</h3><p>Update only the response and Ask Question status.</p></div>
        <div class="card-body">
            @can('ask-questions.edit')
                <form method="POST" action="{{ route('admin.ask-questions.update-response', $askQuestion) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label" for="ask-question-response">Response</label>
                        <textarea id="ask-question-response" name="admin_response" class="form-control @error('admin_response') is-invalid @enderror" rows="7" maxlength="10000" placeholder="Enter the response for the user...">{{ old('admin_response', $askQuestion->admin_response) }}</textarea>
                        @error('admin_response')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="ask-question-status">Status</label>
                        <select id="ask-question-status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            @foreach (\App\Models\AskQuestion::STATUS_LABELS as $status => $label)
                                <option value="{{ $status }}" @selected(old('status', $askQuestion->status) === $status)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary" data-action-confirm data-action-title="Are you sure?" data-action-text="You are about to update this question's response and status." data-action-confirm-text="Yes, Update"><i class="fa-solid fa-paper-plane me-2" aria-hidden="true"></i>Save / Send Response</button>
                </form>
            @else
                <div class="row g-3">
                    <div class="col-md-4"><strong>Status</strong><div>{{ $askQuestion->statusLabel() }}</div></div>
                    <div class="col-12"><strong>Response</strong><div class="border rounded bg-light p-3" style="white-space: pre-line; overflow-wrap: anywhere;">{{ $askQuestion->admin_response ?: 'No response has been provided.' }}</div></div>
                </div>
            @endcan
        </div>
    </section>
</div>
@endsection
