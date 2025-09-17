@include('template.header')
@extends('layouts.layout')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <!-- <div class="card-header bg-primary text-white text-center">
                    <h2>Maker Status</h2>
                </div> -->
                <div class="card-body">
                    <table class="table table-striped table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>Template Name</th>
                                <th>Message Details</th>
                                <th>Status</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($makerStatus as $status)
                                <tr>
                                    <td>{{ $status->template_name }}</td>
                                    <td>{{ $status->msg_details }}</td>
                                    <td>
                                        <span class="badge {{ $status->status_label == 'success' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($status->status_label) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($status->timestamp)->format('d-m-Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
