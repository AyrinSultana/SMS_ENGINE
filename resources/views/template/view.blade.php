<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>View Messages</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Messages for Template: {{ $templateName }}</h2>
            <div>
                <a href="{{ route('template.download', $templateName) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download 
                    @if(isset($hasFile) && $hasFile)
                        <span class="badge bg-light text-dark">Original File</span>
                    @else
                        <span class="badge bg-light text-dark">Generated CSV</span>
                    @endif
                </a>
                {{-- <div class="btn-group ml-2">
                    <button id="approveTemplateBtn" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve All
                    </button>
                    <button id="rejectTemplateBtn" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject All
                    </button>
                </div> --}}
                <a href="{{ $backUrl ?? route('template.index') }}" class="btn btn-secondary ml-2">Back to List</a>
            </div>
        </div>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Template Messages</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Message Title</th>
                            <th>Message Text</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templateMessages as $message)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $message->title }}</strong></td>
                                <td>{{ $message->message }}</td>
                                <td>{{ isset($message->created_at) && is_object($message->created_at) ? $message->created_at->format('d-m-Y H:i') : $message->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No messages found for this template</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal for confirming status change -->
    <div class="modal fade" id="confirmStatusModal" tabindex="-1" role="dialog" aria-labelledby="confirmStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmStatusModalLabel">Confirm Status Change</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="confirmStatusMessage">Are you sure you want to change the status of all messages in this template?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusBtn">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Setup CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            let statusToChange = '';
            
            // Handle approve button click
            $('#approveTemplateBtn').click(function() {
                statusToChange = 'approved';
                $('#confirmStatusMessage').text('Are you sure you want to approve all messages in this template?');
                $('#confirmStatusBtn').removeClass('btn-danger').addClass('btn-success');
                $('#confirmStatusModal').modal('show');
            });
            
            // Handle reject button click
            $('#rejectTemplateBtn').click(function() {
                statusToChange = 'rejected';
                $('#confirmStatusMessage').text('Are you sure you want to reject all messages in this template?');
                $('#confirmStatusBtn').removeClass('btn-success').addClass('btn-danger');
                $('#confirmStatusModal').modal('show');
            });
            
            // Handle confirmation button click
            $('#confirmStatusBtn').click(function() {
                updateTemplateStatus('{{ $templateName }}', statusToChange);
            });
            
            // Function to update template status
            function updateTemplateStatus(name, status) {
                $.ajax({
                    url: '{{ route("update.template.status") }}',
                    type: 'POST',
                    data: {
                        name: name,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message and reload the page
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', xhr.responseText);
                        alert('An error occurred while updating the template status: ' + error);
                    },
                    complete: function() {
                        $('#confirmStatusModal').modal('hide');
                    }
                });
            }
        });
    </script>
</body>
</html>
