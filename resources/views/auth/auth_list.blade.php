@include('template.header')
@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success">
            <i class="fas fa-sliders-h me-2"></i> SMS  Approval Queue
        </h2>
                    <div>
                        <!-- <a href="{{ route('template.sms_form') }}" class="btn btn-sm btn-light">Send New SMS</a> -->
                        <a href="{{ route('sms.history') }}" class="btn btn-sm btn-light">View History</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(!isset($makerStatus) || $makerStatus->isEmpty())
                        <div class="alert alert-info">No SMS records found in the queue.</div>
                    @else
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Template Name</th>
                                <th>Message Details</th>
                                <th>File</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($makerStatus as $status)
                                <tr>
                                    <td><strong>{{ $status->template_name ?? $status->template->name ?? 'No Template' }}</strong></td>
                                    <td>{{ Str::limit($status->message ?? 'No message', 50) }}</td>
                                    <!-- <td>
                                        @if($status->file_path)
                                            <span class="badge bg-info">{{ $status->original_filename }}</span>
                                        @else
                                            <span class="badge bg-secondary">No file</span>
                                        @endif
                                    </td> -->
                                    <td>
                                @if($status->original_filename)
                                    <span class="badge bg-warning text-dark" title="{{ $status->original_filename }}">
                                        {{ Str::limit($status->original_filename, 20) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark">No file</span>
                                @endif
                            </td>
                                    <td>
                                        <span class="badge status-badge {{ $status->status->value == 'approved' ? 'bg-success' : ($status->status->value == 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ $status->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($status->timestamp)->format('d-m-Y H:i:s') }}</td>
                                    <td>
                                                        <div class="btn-group" role="group">
                                                            @if($status->status->value == 'pending' || $status->status->value == 'rejected')
                                                                <button type="button" class="btn btn-sm btn-success approve-btn" data-id="{{ $status->id }}">
                                                                    <i class="fas fa-check"></i> Approve
                                                                </button>
                                                            @endif

                                                            @if($status->status->value == 'pending' || $status->status->value == 'rejected')
                                                                <button type="button" class="btn btn-sm btn-danger reject-btn" data-id="{{ $status->id }}">
                                                                    <i class="fas fa-times"></i> Reject
                                                                </button>
                                                            @endif
                                                        </div>
                                        @if($status->file_path)
                                            <a href="{{ asset('storage/' . $status->file_path) }}" class="btn btn-sm btn-primary mt-1" download>
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Pagination Links -->
                    @if(method_exists($makerStatus, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $makerStatus->links() }}
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle approve button click
        $('.approve-btn').on('click', function() {
            const id = $(this).data('id');
            updateStatus(id, 'approved');
        });

        // Handle reject button click
        $('.reject-btn').on('click', function() {
            const id = $(this).data('id');
            updateStatus(id, 'rejected');
        });

        // Function to update the status
        function updateStatus(id, newStatus) {
            $.ajax({
                url: '{{ route('updateStatus') }}',
                type: 'POST',
                data: {
                    id: id,
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        alert('Status updated to ' + newStatus);
                        
                        // Reload the page to show the updated status
                        location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update status'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error details:', xhr.responseText);
                    let errorMessage = 'An error occurred while updating the status.';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        } else if (response.error) {
                            errorMessage = response.error;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    
                    alert(errorMessage);
                }
            });
        }
    });
</script>
@endsection
