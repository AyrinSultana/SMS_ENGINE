@include('template.header')
@extends('layouts.layout')

@section('content')
<div class="container py-5">
    <!-- Header and Action Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success">
            <i class="fas fa-sliders-h me-2"></i> Template Approval Queue
        </h2>
        <!-- <div>
            <a href="{{ route('template.create') }}" class="btn btn-outline-success me-2">
                <i class="fas fa-file-circle-plus"></i> New Template
            </a>
            <a href="{{ route('template.sms_form') }}" class="btn btn-outline-danger">
                <i class="fas fa-paper-plane"></i> Send SMS
            </a>
        </div> -->
    </div>

    <!-- Alerts -->
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

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($templateStats->isEmpty())
                <div class="alert alert-info">No templates waiting for approval.</div>
            @else
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Template Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templateStats as $template)
                            <tr>
                                <td class="text-start">
                                    <i class="fas fa-file-alt text-muted me-1"></i> {{ $template->name }}
                                </td>
                                <td>
                                    @php
                                        $status = is_string($template->approval_status) ? $template->approval_status : $template->approval_status->value;
                                        $badgeClass = match($status) {
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-warning text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('template.view', ['name' => $template->name, 'from' => 'auth']) }}" class="btn btn-outline-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>

                                        @if($status == 'pending')
                                            <button class="btn btn-outline-success approve-template" data-template-name="{{ $template->name }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-outline-danger reject-template" data-template-name="{{ $template->name }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        @elseif($status == 'approved')
                                            <button class="btn btn-outline-danger reject-template" data-template-name="{{ $template->name }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        @elseif($status == 'rejected')
                                            <button class="btn btn-outline-success approve-template" data-template-name="{{ $template->name }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(method_exists($templateStats, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $templateStats->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.approve-template').click(function() {
            const name = $(this).data('template-name');
            updateStatus(name, 'approved');
        });

        $('.reject-template').click(function() {
            const name = $(this).data('template-name');
            updateStatus(name, 'rejected');
        });

        function updateStatus(name, status) {
            $.post("{{ route('update.template.status') }}", { name, status }, function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }).fail(function(xhr) {
                const error = xhr.responseJSON?.errors ? Object.values(xhr.responseJSON.errors).flat().join('\n') : 'An error occurred.';
                alert(error);
            });
        }
    });
</script>
@endsection
