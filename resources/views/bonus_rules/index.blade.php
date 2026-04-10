@extends('adminlte::page')

@section('title', 'Bonus Rules')

@section('content_header')
    <h1>Bonus Rules Management</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Bonus Rules</h3>
            <div class="card-tools">
                <a href="{{ route('bonus-rules.create') }}" class="btn btn-primary btn-sm">Add New Bonus Rule</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Rule Name</th>
                        <th>Condition</th>
                        <th>Amount</th>
                        <th>Version</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bonusRules as $rule)
                        <tr>
                            <td>{{ $rule->id }}</td>
                            <td>{{ $rule->rule_name }}</td>
                            <td>{{ $rule->condition }}</td>
                            <td>{{ $rule->bonus_amount }}</td>
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
                            <td colspan="7" class="text-center">No bonus rules found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
