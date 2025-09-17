@include('template.header')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create SMS Template</title>

    <!-- Bootstrap 5 & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
       body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', sans-serif;
}

.form-wrapper {
    background-color: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
}

.form-section-title {
    font-weight: 600;
    margin-bottom: 1.5rem;
    color: #198754;
    font-size: 1.25rem;
}

.form-label {
    font-weight: 500;
    color: #333;
}

input[type="text"],
textarea,
select,
input[type="file"] {
    border-radius: 8px;
    border: 1px solid #ced4da;
    transition: all 0.2s ease-in-out;
}

input:focus,
textarea:focus,
select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
}

.message-item {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    margin-bottom: 1rem;
    background-color: #fefefe;
    position: relative;
    transition: box-shadow 0.2s ease-in-out;
}

.message-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.remove-message {
    margin-top: 0.5rem;
    float: right;
}

.btn-outline-danger,
.btn-outline-primary {
    border-radius: 6px;
    transition: all 0.2s ease-in-out;
}

.btn-outline-danger:hover,
.btn-outline-primary:hover {
    transform: translateY(-1px);
}

.btn-success {
    border-radius: 6px;
    padding: 0.5rem 1.5rem;
}

    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-4">
        <h2 class="text-success"><i class="fas fa-sms"></i> Create SMS Template</h2>
    </div>

    <div class="form-wrapper">
        <form action="{{ route('template.store')}}" method="post" enctype="multipart/form-data">
            @csrf

            <!-- Template Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                <input value="{{ old('name') }}" type="text" class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name" required>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Template Messages -->
            <div class="mb-3">
                <label class="form-label">Template Messages <span class="text-danger">*</span></label>
                <div id="messages-list">
                    <!-- First message -->
                    <div class="message-item" data-index="0">
                        <div class="mb-2">
                            <label class="form-label">Message Text</label>
                            <textarea class="form-control @error('messages.0.message') is-invalid @enderror"
                                      name="messages[0][message]" rows="3" required>{{ old('messages.0.message') }}</textarea>
                            <input type="hidden" name="messages[0][title]" value="Message">
                            @error('messages.0.message')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-message" style="display: none;">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>

                <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-message">
                    <i class="fas fa-plus"></i> Add Another Message
                </button>
            </div>

            <!-- File Upload -->
            <div class="mb-3">
                <label for="template_file" class="form-label">Upload Excel/CSV File (Optional)</label>
                <input type="file" class="form-control @error('template_file') is-invalid @enderror"
                       id="template_file" name="template_file" accept=".csv, .xlsx, .xls">
                <small class="text-muted">Upload a CSV or Excel file with message templates.</small>
                @error('template_file')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Authorizer -->
            <div class="mb-4">
                <label for="authorizer" class="form-label">Choose an Authorizer <span class="text-danger">*</span></label>
                <select class="form-select @error('authorizer') is-invalid @enderror" id="authorizer" name="authorizer" required>
                    <option value="">-- Select Authorizer --</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->email_address }}">
                                            {{ $employee->full_name }} ({{ $employee->email_address }})
                        </option>
                    @endforeach
                </select>
                @error('authorizer')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit -->
            <div class="text-end">
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save"></i> Save Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    let messageIndex = 1;

    $('#add-message').click(function() {
        const messageHtml = `
            <div class="message-item" data-index="${messageIndex}">
                <div class="mb-2">
                    <label class="form-label">Message Text</label>
                    <textarea class="form-control" name="messages[${messageIndex}][message]" rows="3" required></textarea>
                    <input type="hidden" name="messages[${messageIndex}][title]" value="Message">
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm remove-message">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        `;
        $('#messages-list').append(messageHtml);
        messageIndex++;
        updateRemoveButtons();
    });

    $(document).on('click', '.remove-message', function() {
        $(this).closest('.message-item').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        $('.remove-message').toggle($('.message-item').length > 1);
    }

    updateRemoveButtons();
});
</script>
</body>
</html>
