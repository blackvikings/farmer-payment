@extends('adminlte::page')

@section('title', 'Farmers')

@section('content_header')
    <h1>Farmers Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Farmers</h3>
            <div class="card-tools">
                <a href="{{ route('farmers.create') }}" class="btn btn-primary btn-sm">Add New Farmer</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Organizer</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmers as $farmer)
                        <tr>
                            <td>{{ $farmer->id }}</td>
                            <td>{{ $farmer->name }}</td>
                            <td>{{ $farmer->organizer->name }}</td>
                            <td>{{ $farmer->phone_number }}</td>
                            <td>{{ $farmer->address }}</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No farmers found.</td>
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
