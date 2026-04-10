@extends('adminlte::page')

@section('title', 'Add Loss Rule')

@section('content_header')
    <h1>Add New Loss Rule</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Loss Rule Details</h3>
        </div>

        <form action="{{ route('loss-rules.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="form-group">
                    <label for="rule_name">Rule Name *</label>
                    <input type="text" name="rule_name" class="form-control" id="rule_name" placeholder="e.g., Standard Moisture Loss" value="{{ old('rule_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="max_allowable_loss_percentage">Max Allowable Loss (%) *</label>
                    <input type="number" step="0.01" name="max_allowable_loss_percentage" class="form-control" id="max_allowable_loss_percentage" placeholder="e.g., 2.5" value="{{ old('max_allowable_loss_percentage') }}" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('loss-rules.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop
