@extends('layouts.adminLayout.admin-design')

@section('title', 'Ask Questions')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1">Ask Questions</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ask Questions</li>
            </ol>
        </nav>
    </div>

    <div class="card admin-settings-card">
        <div class="card-header">
            <h3>Ask Questions</h3>
            <p>Review questions submitted by authenticated frontend users.</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" data-admin-datatable data-admin-serials>
                    <thead>
                        <tr><th>#</th><th>Question No.</th><th>Name</th><th>Email</th><th>Phone</th><th>Subject</th><th>Status</th><th>Submitted At</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $askQuestion)
                            <tr>
                                <td data-admin-serial-value>{{ $loop->iteration }}</td>
                                <td>{{ $askQuestion->question_no }}</td>
                                <td>{{ $askQuestion->name }}</td>
                                <td>{{ $askQuestion->email }}</td>
                                <td>{{ $askQuestion->phone }}</td>
                                <td>{{ $askQuestion->subject }}</td>
                                <td><span class="badge {{ match ($askQuestion->status) { 'answered' => 'text-bg-success', 'closed' => 'text-bg-secondary', default => 'text-bg-warning' } }}">{{ $askQuestion->statusLabel() }}</span></td>
                                <td>{{ $askQuestion->created_at?->format('d M Y, h:i A') }}</td>
                                <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.ask-questions.show', $askQuestion) }}"><i class="fa-solid fa-eye me-1" aria-hidden="true"></i>View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
