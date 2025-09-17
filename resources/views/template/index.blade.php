@include('template.header')

@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template Management</title>
    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        h2 {
            font-weight: 600;
        }
        .card {
            border: none;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }
        .table th {
            background-color: #e9f5ee;
            color: #333;
        }
        .badge {
            font-size: 0.85rem;
        }
        .btn-sm i {
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Header and Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-success">
                <i class="fas fa-sliders-h me-2"></i> Template Management
            </h2>
            <div>
                <a href="{{ route('template.create') }}" class="btn btn-outline-success me-2">
                    <i class="fas fa-file-circle-plus"></i> New Template
                </a>
                <a href="{{ route('template.sms_form') }}" class="btn btn-outline-danger">
                    <i class="fas fa-paper-plane"></i> Send SMS
                </a>
            </div>
        </div>

        <!-- Success Alert -->
        @if (Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ Session::get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Template Table Card -->
        <div class="card">
            <div class="card-body">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Template Name</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($templates as $template)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    <i class="fas fa-file-alt text-muted me-1"></i> {{ $template->name }}
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $template->templateMessages->count() }}
                                        {{ Str::plural('message', $template->templateMessages->count()) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $status = is_string($template->approval_status)
                                            ? $template->approval_status
                                            : $template->approval_status->value;
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
                                <td>{{ $template->created_at->format('d M, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('template.view', $template->name) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                      @if ($template->id)
    <!-- <a href="{{ route('template.edit', $template->id) }}" class="btn btn-outline-warning">
        <i class="fas fa-edit"></i> Edit
    </a> -->
@else
    <span class="text-danger">Invalid ID</span>
@endif

                                       <form action="{{ route('template.destroy', $template->id) }}" method="POST" class="d-inline delete-template-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                       
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted">No templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).on('submit', '.delete-template-form', function(e) {
    e.preventDefault();
    if (!confirm('Hide this template from the list?')) return;

    const form = $(this);
    const row = form.closest('tr');
    const formData = form.serialize();

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                row.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Update row numbers after removal
                    $('tbody tr').each(function(index) {
                        $(this).find('td:first').text(index + 1);
                    });
                });
                
                // Show toast notification instead of alert
                showToast('success', response.message);
            } else {
                showToast('error', response.message || 'Action failed');
            }
        },
        error: function(xhr) {
            let message = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showToast('error', message);
            console.error('Error:', xhr.responseText);
        }
    });
});

// Toast notification function
function showToast(type, message) {
    // Remove any existing toasts
    $('.toast').remove();
    
    // Create toast HTML
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    // Append and show toast
    $('body').append(toast);
    new bootstrap.Toast(toast[0]).show();
    
    // Auto-hide after 5 seconds
    setTimeout(() => toast.remove(), 5000);
}
</script>
</body>
</html>
