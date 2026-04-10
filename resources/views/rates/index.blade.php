@extends('adminlte::page')

@section('title', 'Rates')

@section('content_header')
    <h1>Rates Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Rates</h3>
            <div class="card-tools">
                <a href="{{ route('rates.create') }}" class="btn btn-primary btn-sm">Add New Rate</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Parameter</th>
                        <th>Base Price</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                        <tr>
                            <td>{{ $rate->id }}</td>
                            <td>{{ $rate->parameter->name }}</td>
                            <td>{{ $rate->base_price }}</td>
                            <td>{{ $rate->version }}</td>
                            <td>
                                @if($rate->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($rate->is_active)
                                    <a href="{{ route('rates.edit', $rate->id) }}" class="btn btn-info btn-sm">Update</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No rates found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
