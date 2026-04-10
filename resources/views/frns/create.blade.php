@extends('adminlte::page')

@section('title', 'Capture FRN')

@section('content_header')
    <h1>Capture FRN</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">FRN Details</h3>
        </div>

        <form action="{{ route('frns.store') }}" method="POST">
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
                    <label>Lot Number *</label>
                    <small class="text-muted d-block mb-2">Only accepted lots without an FRN are shown</small>
                    <select class="form-control" name="lot_number" required>
                        <option value="">Select a Lot</option>
                        @foreach($lots as $lot)
                            <option value="{{ $lot->lot_number }}" {{ old('lot_number') == $lot->lot_number ? 'selected' : '' }}>
                                {{ $lot->lot_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="arrival_date">Arrival Date *</label>
                    <input type="date" name="arrival_date" class="form-control" id="arrival_date" value="{{ old('arrival_date', date('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label for="gross_weight">Gross Weight *</label>
                    <input type="number" step="0.01" name="gross_weight" class="form-control" id="gross_weight" placeholder="Enter gross weight" value="{{ old('gross_weight') }}" required>
                </div>

                <div class="form-group">
                    <label for="vehicle_number">Vehicle Number</label>
                    <input type="text" name="vehicle_number" class="form-control" id="vehicle_number" placeholder="Enter vehicle number" value="{{ old('vehicle_number') }}">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Capture FRN</button>
                <a href="{{ route('frns.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
