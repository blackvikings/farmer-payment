@extends('adminlte::page')

@section('title', 'Add Farmer')

@section('content_header')
    <h1>Add New Farmer</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Farmer Details</h3>
        </div>

        <form action="{{ route('farmers.store') }}" method="POST">
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
                    <label for="name">Full Name *</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter farmer name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Organizer *</label>
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
                    <label for="phone_number">Phone Number *</label>
                    <input type="text" name="phone_number" class="form-control" id="phone_number" placeholder="Enter phone number" value="{{ old('phone_number') }}" required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <textarea name="address" class="form-control" id="address" rows="3" placeholder="Enter address" required>{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('farmers.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
