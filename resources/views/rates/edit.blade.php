@extends('adminlte::page')

@section('title', 'Update Rate')

@section('content_header')
    <h1>Update Rate Version</h1>
@stop

@section('content')
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">Update Rate for {{ $rate->parameter->name }}</h3>
        </div>

        <form action="{{ route('rates.update', $rate->id) }}" method="POST">
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
                <p class="text-muted">This will deactivate Version {{ $rate->version }} and create Version {{ $rate->version + 1 }}.</p>

                <div class="form-group">
                    <label for="base_price">New Base Price *</label>
                    <input type="number" step="0.01" name="base_price" class="form-control" id="base_price" placeholder="Enter new base price" value="{{ old('base_price', $rate->base_price) }}" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-info">Update Version</button>
                <a href="{{ route('rates.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop
