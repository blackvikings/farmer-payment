@extends('adminlte::page')

@section('title', 'Organizers')

@section('content_header')
    <h1>Organizers Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Organizers</h3>
            <div class="card-tools">
                <a href="{{ route('organizers.create') }}" class="btn btn-primary btn-sm">Add New Organizer</a>
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
                        <th>Name</th>
                        <th>Contact Info</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organizers as $organizer)
                        <tr>
                            <td>{{ $organizer->id }}</td>
                            <td>{{ $organizer->name }}</td>
                            <td>{{ $organizer->contact_info ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No organizers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
