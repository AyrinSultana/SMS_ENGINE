@include('template.header')
@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm rounded">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h2 class="text-success mb-0">
                        <i class="fas fa-sliders-h me-2"></i> SMS History
                    </h2>
                    <form action="{{ route('sms.history') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-success bg-transparent text-dark" 
                                   placeholder="Search by any field..."
                                   value="{{ request('search') }}"
                                   style="min-width: 280px;">
                            <button class="btn btn-outline-success" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('sms.history') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    @if(request('search'))
                        <div class="alert alert-info">
                            <strong>Search Results for:</strong> "{{ request('search') }}" 
                            <small>({{ $groupedHistory->total() }} result{{ $groupedHistory->total() != 1 ? 's' : '' }} found)</small>
                        </div>
                    @endif

                    @if($groupedHistory->isEmpty())
                        <div class="alert alert-warning text-center">
                            @if(request('search'))
                                No SMS history found matching your search criteria.
                            @else
                                No SMS history found.
                            @endif
                        </div>
                    @else
                        <div class="accordion" id="smsHistoryAccordion">
    @foreach($groupedHistory as $group)
        @php
            $templateId = $group->template_id;
            $templateName = $group->template_name;
            $templateMessages = $messagesByTemplate[$templateId] ?? collect();
        @endphp
        
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center" id="heading{{ $templateId }}">
                <div>
                    <h5 class="mb-0">
                        <strong>{{ $templateName }}</strong>
                        <span class="badge bg-light text-dark ms-2">ID: {{ $templateId }}</span>
                    </h5>
                    <small class="text-muted">{{ $group->total_messages }} message{{ $group->total_messages != 1 ? 's' : '' }} • Last modified: {{ $group->last_modified ? date('d M, Y H:i', strtotime($group->last_modified)) : 'N/A' }}</small>
                </div>
                <button class="btn btn-link text-success" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapse{{ $templateId }}" aria-expanded="false" 
                        aria-controls="collapse{{ $templateId }}">
                     <i class="nav-icon fas fa-list-alt text-success"></i>
                    <p>View Templates</p>
                </button>
            </div>

            <div id="collapse{{ $templateId }}" class="collapse" aria-labelledby="heading{{ $templateId }}" 
                 data-parent="#smsHistoryAccordion">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Message</th>
                                    <th>Recipient</th>
                                    <th>Mobile</th>
                                    <th>Status</th>
                                    <th>Modified At</th>
                                    <th>Source</th>
                                    <th>Authorizer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($templateMessages as $sms)
                                    <tr>
                                        <td>{{ $sms->message ? Str::limit($sms->message, 255) : 'N/A' }}</td>
                                        <td>
                                            @if(is_numeric($sms->recipient))
                                                <span class="badge bg-info text-dark">{{ $sms->recipient }}</span>
                                            @else
                                                {{ $sms->recipient }}
                                            @endif
                                        </td>
                                        <td>{{ $sms->mobile_no ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusValue = $sms->status ? ($sms->status instanceof \UnitEnum ? $sms->status->value : $sms->status) : 'unknown';

                                                if ($statusValue == 'pending') {
                                                    $statusClass = 'bg-warning text-dark';
                                                } elseif ($statusValue == 'rejected') {
                                                    $statusClass = 'bg-danger';
                                                } elseif ($statusValue == 'approved') {
                                                    $statusClass = 'bg-success';
                                                } elseif ($statusValue == 'sent') {
                                                    $statusClass = 'bg-primary';
                                                } else {
                                                    $statusClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($statusValue) }}
                                            </span>
                                        </td>
                                        <td>{{ $sms->modified_at ? date('d-m-Y H:i:s', strtotime($sms->modified_at)) : 'N/A' }}</td>
                                        <td>
                                            <span class="badge 
                                                {{ $sms->source == 'Excel/CSV Record' ? 'bg-primary' : 
                                                   ($sms->source == 'File Upload' ? 'bg-info text-dark' : 'bg-secondary') }}">
                                                {{ $sms->source }}
                                            </span>
                                        </td>
                                        <td>{{ $sms->authorizer ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $groupedHistory->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.form.submit();
                }
            });
        }
    });
</script>
@endsection