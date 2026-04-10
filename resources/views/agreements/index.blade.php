@extends('adminlte::page')

@section('title', 'Agreements')

@section('content_header')
    <h1>Agreements Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Agreements</h3>
            <div class="card-tools">
                <a href="{{ route('agreements.create') }}" class="btn btn-primary btn-sm">Add New Agreement</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Farmer Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Rate</th>
                        <th>Bonus Rule</th>
                        <th>Loss Rule</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agreements as $agreement)
                        <tr>
                            <td>{{ $agreement->id }}</td>
                            <td>{{ optional($agreement->farmer)->name }}</td>
                            <td>{{ $agreement->start_date->format('Y-m-d') }}</td>
                            <td>{{ $agreement->end_date->format('Y-m-d') }}</td>
                            <td>{{ optional($agreement->rate)->base_price ?? 'N/A' }}</td>
                            <td>{{ optional($agreement->bonusRule)->rule_name ?? 'N/A' }}</td>
                            <td>{{ optional($agreement->lossRule)->rule_name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No agreements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
