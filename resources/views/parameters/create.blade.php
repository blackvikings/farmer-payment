@extends('adminlte::page')

@section('title', 'Add Parameter')

@section('content_header')
    <h1>Add New Parameter</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Parameter Details</h3>
        </div>

        <form action="{{ route('parameters.store') }}" method="POST">
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
                    <label for="name">Parameter Name *</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter parameter name (e.g., Moisture)" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="type">Type *</label>
                    <select name="type" class="form-control" id="type" required>
                        <option value="quality" {{ old('type') == 'quality' ? 'selected' : '' }}>Quality</option>
                        <option value="production" {{ old('type') == 'production' ? 'selected' : '' }}>Production</option>
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('parameters.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
