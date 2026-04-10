@extends('adminlte::page')

@section('title', 'Add Bonus Rule')

@section('content_header')
    <h1>Add New Bonus Rule</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Bonus Rule Details</h3>
        </div>

        <form action="{{ route('bonus-rules.store') }}" method="POST">
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
                    <input type="text" name="rule_name" class="form-control" id="rule_name" placeholder="e.g., High Quality Bonus" value="{{ old('rule_name') }}" required>
                </div>

                <div class="form-group">
                    <label for="condition">Condition *</label>
                    <input type="text" name="condition" class="form-control" id="condition" placeholder="e.g., > 90" value="{{ old('condition') }}" required>
                </div>

                <div class="form-group">
                    <label for="bonus_amount">Bonus Amount *</label>
                    <input type="number" step="0.01" name="bonus_amount" class="form-control" id="bonus_amount" placeholder="Enter bonus amount" value="{{ old('bonus_amount') }}" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('bonus-rules.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop
