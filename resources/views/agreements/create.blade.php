@extends('adminlte::page')

@section('title', 'Add Agreement')

@section('content_header')
    <h1>Add New Agreement</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Agreement Details</h3>
        </div>

        <form action="{{ route('agreements.store') }}" method="POST">
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
                    <label>Farmer *</label>
                    <select class="form-control" name="farmer_id" required>
                        <option value="">Select a Farmer</option>
                        @foreach($farmers as $farmer)
                            <option value="{{ $farmer->id }}" {{ old('farmer_id') == $farmer->id ? 'selected' : '' }}>
                                {{ $farmer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_date">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date') }}" required>
                </div>

                <div class="form-group">
                    <label for="end_date">End Date *</label>
                    <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date') }}" required>
                </div>

                <div class="form-group">
                    <label for="terms">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" id="terms" rows="4" placeholder="Enter agreement terms">{{ old('terms') }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('agreements.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop
