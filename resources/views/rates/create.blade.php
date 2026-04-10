@extends('adminlte::page')

@section('title', 'Add Rate')

@section('content_header')
    <h1>Add New Rate</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Rate Details</h3>
        </div>

        <form action="{{ route('rates.store') }}" method="POST">
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
                    <label>Parameter *</label>
                    <select class="form-control" name="parameter_id" required>
                        <option value="">Select a Parameter</option>
                        @foreach($parameters as $parameter)
                            <option value="{{ $parameter->id }}" {{ old('parameter_id') == $parameter->id ? 'selected' : '' }}>
                                {{ $parameter->name }} ({{ ucfirst($parameter->type) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="base_price">Base Price *</label>
                    <input type="number" step="0.01" name="base_price" class="form-control" id="base_price" placeholder="Enter base price" value="{{ old('base_price') }}" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('rates.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop
