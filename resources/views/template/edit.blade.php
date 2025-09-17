<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .message-item {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .btn i {
            margin-right: 4px;
        }

        .form-section-title {
            font-weight: 600;
            margin-bottom: 15px;
        }

        .btn-primary, .btn-secondary {
            padding: 8px 20px;
        }

        textarea {
            resize: none;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card p-4">
        <h3 class="mb-4">Edit SMS Template</h3>

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <form action="{{ route('template.update', $template->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Template Name -->
            <div class="form-group">
                <label for="name">Template Name:</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control @error('name') is-invalid @enderror" 
                    value="{{ old('name', $template->name) }}" 
                    required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Messages Section -->
            <div id="messages-container" class="mt-4">
                <div class="form-section-title">Template Messages:</div>
                <div id="messages-list">
                    @forelse($template->templateMessages as $index => $message)
                        <div class="message-item" data-index="{{ $index }}">
                            <input type="hidden" name="messages[{{ $index }}][id]" value="{{ $message->id }}">
                            <div class="form-group">
                                <label>Message Text:</label>
                                <textarea 
                                    class="form-control @error('messages.'.$index.'.message') is-invalid @enderror" 
                                    name="messages[{{ $index }}][message]" 
                                    rows="3" 
                                    required>{{ old('messages.'.$index.'.message', $message->message) }}</textarea>
                                @error('messages.'.$index.'.message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-message" style="{{ $loop->first && $loop->count == 1 ? 'display: none;' : '' }}">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="message-item" data-index="0">
                            <div class="form-group">
                                <label>Message Text:</label>
                                <textarea class="form-control" name="messages[0][message]" rows="3" required></textarea>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-danger btn-sm remove-message" style="display: none;">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-message">
                    <i class="fas fa-plus"></i> Add Another Message
                </button> -->
            </div>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Template</button>
                <a href="{{ route('template.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    let messageIndex = {{ $template->templateMessages->count() }};

    $('#add-message').click(function () {
        const messageHtml = `
            <div class="message-item" data-index="${messageIndex}">
                <div class="form-group">
                    <label>Message Text:</label>
                    <textarea class="form-control" name="messages[${messageIndex}][message]" rows="3" required></textarea>
                </div>
                <div class="text-right">
                    <button type="button" class="btn btn-danger btn-sm remove-message">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        $('#messages-list').append(messageHtml);
        messageIndex++;
        updateRemoveButtons();
    });

    $(document).on('click', '.remove-message', function () {
        $(this).closest('.message-item').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const messageCount = $('.message-item').length;
        if (messageCount > 1) {
            $('.remove-message').show();
        } else {
            $('.remove-message').hide();
        }
    }
});
</script>
</body>
</html>
