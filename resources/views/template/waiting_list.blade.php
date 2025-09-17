@include('template.header')
@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-success">
            <i class="fas fa-sliders-h me-2"></i> SMS Queue Status
        </h2>
                    {{-- <a href="{{ route('template.sms_form') }}" class="btn btn-sm btn-light">Send New SMS</a> --}}
                </div>
                <div class="card-body">
                    @if(isset($message))
                        <div class="alert alert-info">{{ $message }}</div>
                    @elseif(isset($makerStatus) && $makerStatus->isEmpty())
                        <div class="alert alert-info">No SMS records found in the queue.</div>
                    @elseif(!isset($makerStatus))
                        <div class="alert alert-warning">SMS status data is not available.</div>
                    @else
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Template Name</th>
                                <th>Message Details</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($makerStatus as $status)
                                <tr>
                                    <td><strong>{{ $status->template_name ?? $status->template->name ?? 'No Template' }}</strong></td>
                                    <td>{{ Str::limit($status->message ?? 'No message', 50) }}</td>
                                    <td>
                                        @if(isset($status->file_path) && $status->file_path)
                                            <span class="badge bg-info">{{ $status->original_filename ?? 'File uploaded' }}</span>
                                        @else
                                            <span class="badge bg-secondary">No file</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge status-badge {{ $status->status->value == 'sent' ? 'bg-success' : ($status->status->value == 'failed' ? 'bg-danger' : 'bg-warning') }}"
                                            data-id="{{ $status->id }}"
                                            data-status="{{ $status->status->value }}">
                                            {{ $status->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($status->timestamp)->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $makerStatus->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


