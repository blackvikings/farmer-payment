@extends('adminlte::page')

@section('title', 'Loss Rules')

@section('content_header')
    <h1>Loss Rules Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Loss Rules</h3>
            <div class="card-tools">
                <a href="{{ route('loss-rules.create') }}" class="btn btn-primary btn-sm">Add New Loss Rule</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rule Name</th>
                        <th>Max Allowable Loss %</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lossRules as $rule)
                        <tr>
                            <td>{{ $rule->id }}</td>
                            <td>{{ $rule->rule_name }}</td>
                            <td>{{ $rule->max_allowable_loss_percentage }}%</td>
                            <td>{{ $rule->version }}</td>
                            <td>
                                @if($rule->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($rule->is_active)
                                    <a href="#" class="btn btn-info btn-sm">Update</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No loss rules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
