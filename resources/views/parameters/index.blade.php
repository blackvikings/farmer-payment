@extends('adminlte::page')

@section('title', 'Parameters')

@section('content_header')
    <h1>Parameters Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Parameters</h3>
            <div class="card-tools">
                <a href="{{ route('parameters.create') }}" class="btn btn-primary btn-sm">Add New Parameter</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($parameters as $parameter)
                        <tr>
                            <td>{{ $parameter->id }}</td>
                            <td>{{ $parameter->name }}</td>
                            <td>{{ ucfirst($parameter->type) }}</td>
                            <td>
                                <a href="#" class="btn btn-info btn-sm">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No parameters found.</td>
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
