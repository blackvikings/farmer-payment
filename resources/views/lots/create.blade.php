@extends('adminlte::page')

@section('title', 'Receive Lot')

@section('content_header')
    <h1>Receive Lot Production (FR2)</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Lot Reception Details</h3>
        </div>

        <form action="{{ route('lots.store') }}" method="POST">
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
                    <label for="lot_number">Lot Number * (Unique)</label>
                    <input type="text" name="lot_number" class="form-control" id="lot_number" placeholder="Enter lot number" value="{{ old('lot_number') }}" required>
                </div>

                <div class="form-group">
                    <label>Agreement (Farmer) *</label>
                    <select class="form-control" name="agreement_id" required>
                        <option value="">Select an Agreement</option>
                        @foreach($agreements as $agreement)
                            <option value="{{ $agreement->id }}" {{ old('agreement_id') == $agreement->id ? 'selected' : '' }}>
                                ID: {{ $agreement->id }} - {{ optional($agreement->farmer)->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Declared Organizer *</label>
                    <small class="text-muted d-block mb-2">Used to validate mapping against the agreement</small>
                    <select class="form-control" name="organizer_id" required>
                        <option value="">Select an Organizer</option>
                        @foreach($organizers as $organizer)
                            <option value="{{ $organizer->id }}" {{ old('organizer_id') == $organizer->id ? 'selected' : '' }}>
                                {{ $organizer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity (Kg/Ton) *</label>
                    <input type="number" step="0.01" name="quantity" class="form-control" id="quantity" placeholder="Enter quantity" value="{{ old('quantity') }}" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Process Reception</button>
                <a href="{{ route('lots.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
