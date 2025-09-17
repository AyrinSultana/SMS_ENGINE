@include('template.header')
@extends('layouts.layout')

@section('content')
<style>
    h2.text-success {
        margin-bottom: 30px;
    }

    /* SMS Method Buttons - Different Colors */
    .btn-method {
        border-radius: 6px;
        padding: 10px 18px;
        font-weight: 600;
        color: #fff;
        border: none;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .btn-db {
        background-color: #0d6efd;
    }

    .btn-comma {
        background-color: #6610f2;
    }

    .btn-excel {
        background-color: #fd7e14;
    }

    .btn-method:hover {
        opacity: 0.9;
    }

    /* Transparent Inputs with Green Hover/Focus */
    .form-control {
        background-color: transparent;
        border: 1.5px solid #ccc;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 1rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
    }

    .form-label {
        font-weight: 600;
        margin-top: 10px;
        color: #343a40;
    }

    .btn-submit {
        margin-top: 20px;
        background-color: transparent;
        color: #28a745;
        border: 2px solid #28a745;
        font-weight: 600;
        padding: 10px 24px;
        border-radius: 6px;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #28a745;
        color: white;
    }

    .form-section {
        margin-bottom: 20px;
    }

    .btn-link {
        margin-top: 5px;
        display: inline-block;
    }

    .btn-method {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: 2px solid #28a745;
    background-color: transparent;
    color: #28a745;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 14px;
}

.btn-method i {
    font-size: 16px;
}

.btn-method:hover {
    background-color: #28a745;
    color: #fff;
}

.btn-check:checked + .btn-method {
    background-color: #28a745;
    color: #fff;
}
</style>

<div class="container mt-4">
    <h2 class="text-success">
        <i class="fas fa-paper-plane me-2"></i> Select SMS Sending Method
    </h2>

    {{-- Validation & Session Messages --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data" action="{{ route('send.sms') }}">
        @csrf

        <div class="form-section">
            <input type="radio" class="btn-check" name="sms_method" id="all_users" value="all_users" autocomplete="off" required>
<label class="btn-method" for="all_users">
    <i class="fas fa-database"></i> All Users in Database
</label>

<input type="radio" class="btn-check" name="sms_method" id="comma_separated" value="comma_separated" autocomplete="off" required>
<label class="btn-method" for="comma_separated">
    <i class="fas fa-list"></i> Comma-Separated Numbers
</label>

<input type="radio" class="btn-check" name="sms_method" id="upload_excel" value="upload_excel" autocomplete="off" required>
<label class="btn-method" for="upload_excel">
    <i class="fas fa-file-excel"></i> Upload Excel Numbers
</label>
        </div>

        <div class="form-section">
            <label for="templateDropdown" class="form-label">Choose a Template:</label>
            <select class="form-control" id="templateDropdown" name="templateDropdown" onchange="fetchTemplateMessages(this.value)" required>
                <option value="">Select Template</option>
                @foreach($templateNames as $templateName)
                    <option value="{{ $templateName }}" {{ old('templateDropdown') == $templateName ? 'selected' : '' }}>
                        {{ $templateName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-section">
            <label for="messageDropdown" class="form-label">Choose a Message:</label>
            <select class="form-control" id="messageDropdown" name="messageDropdown" required>
                <option value="">Select Message</option>
                {{-- Messages will be loaded via AJAX --}}
            </select>
        </div>

        <div id="comma_separated_section" class="form-section" style="display:none;">
            <label class="form-label">Enter Numbers (comma-separated):</label>
            <input type="text" class="form-control" name="numbers" value="{{ old('numbers') }}">
        </div>

        <div id="upload_excel_section" class="form-section" style="display:none;">
            <label class="form-label">Upload Excel File:</label>
            <input type="file" class="form-control" name="excel_file">
            <a href="{{ asset('downloads/sample.csv') }}" download="sample.csv" class="btn btn-link">Download Sample Excel Format</a>
        </div>

        <div class="form-section">
            <label for="authorizer" class="form-label">Choose an Authorizer <span class="text-danger">*</span></label>
            <select class="form-control" id="authorizer" name="authorizer" required>
                <option value="">-- Select Authorizer --</option>
                @if($employees && $employees->count())
                    @foreach($employees as $employee)
                        <!-- <option value="{{ $employee->email_address }}" {{ old('authorizer') == $employee->email_address ? 'selected' : '' }}>
                            {{ $employee->email_address }}
                        </option> -->

                          <option value="{{ $employee->email_address }}">
                                            {{ $employee->full_name }} ({{ $employee->email_address }})
                        </option>
                    @endforeach
                @else
                    <option disabled>No authorizers available</option>
                @endif
            </select>
        </div>

        <button type="submit" class="btn btn-submit" id="submitBtn">Send SMS</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('input[name="sms_method"]').forEach((input) => {
        input.addEventListener('change', function () {
            document.getElementById('comma_separated_section').style.display = this.value === 'comma_separated' ? 'block' : 'none';
            document.getElementById('upload_excel_section').style.display = this.value === 'upload_excel' ? 'block' : 'none';
        });
    });

    window.addEventListener('DOMContentLoaded', function () {
        let selectedMethod = '{{ old('sms_method') }}';
        if (selectedMethod === 'comma_separated') {
            document.getElementById('comma_separated_section').style.display = 'block';
        } else if (selectedMethod === 'upload_excel') {
            document.getElementById('upload_excel_section').style.display = 'block';
        }
    });

    function fetchTemplateMessages(templateName) {
        if (templateName === "") {
            document.getElementById("messageDropdown").innerHTML = '<option value="">Select Message</option>';
            return;
        }

        $.ajax({
            url: '{{ route('template.fetchTemplateMessages') }}',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { templateName: templateName },
            success: function(response) {
                let messageDropdown = document.getElementById("messageDropdown");
                messageDropdown.innerHTML = '<option value="">Select Message</option>';
                if (Array.isArray(response.data) && response.data.length > 0) {
                    response.data.forEach(function(message) {
                        let option = document.createElement("option");
                        option.value = message.message;
                        option.text = message.title + " - " + message.message.substring(0, 50) + (message.message.length > 50 ? '...' : '');
                        messageDropdown.appendChild(option);
                    });
                } else {
                    console.log("No messages found for this template.");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching messages:', error);
            }
        });
    }

    document.querySelector('form').addEventListener('submit', function () {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerText = 'Sending...';
    });
</script>
@endsection
