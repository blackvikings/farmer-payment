@extends('adminlte::page')

@section('title', 'Perform Quality Check')

@section('content_header')
    <h1>Perform Quality Check for Lot: {{ $lot->lot_number }}</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Enter QC Values</h3>
        </div>

        <form action="{{ route('quality.submit', $lot->lot_number) }}" method="POST">
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

                <p><strong>Initial Lot Quantity:</strong> {{ $lot->quantity }}</p>

                <div class="form-group row">
                    <label for="final_quantity" class="col-sm-3 col-form-label">Final Quantity</label>
                    <div class="col-sm-9">
                        <input type="number" step="0.01" class="form-control" name="final_quantity" id="final_quantity" placeholder="Enter final quantity" required>
                    </div>
                </div>

                <h4 class="mt-4">Quality Parameters</h4>
                <hr>

                @forelse($parameters as $index => $parameter)
                    <div class="form-group row">
                        <label for="param_{{ $parameter->id }}" class="col-sm-3 col-form-label">
                            {{ $parameter->name }} <br>
                            <small class="text-muted">Expected Value: {{ $parameter->value }}</small>
                        </label>
                        <div class="col-sm-9">
                            <input type="hidden" name="checks[{{ $index }}][parameter_id]" value="{{ $parameter->id }}">
                            <input type="number" step="0.01" class="form-control" name="checks[{{ $index }}][observed_value]" id="param_{{ $parameter->id }}" placeholder="Enter observed value for {{ $parameter->name }}" required>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">No parameters defined for this agreement.</p>
                @endforelse
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" {{ $parameters->isEmpty() ? 'disabled' : '' }}>Submit Quality Validation</button>
                <a href="{{ route('quality.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
