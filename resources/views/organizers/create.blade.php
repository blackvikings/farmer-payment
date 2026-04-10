@extends('adminlte::page')

@section('title', 'Add Organizer')

@section('content_header')
    <h1>Add New Organizer</h1>
@stop

@section('content')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Organizer Details</h3>
        </div>

        <form action="{{ route('organizers.store') }}" method="POST">
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
                    <label for="name">Organizer Name *</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Enter organizer name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="contact_info">Contact Info</label>
                    <input type="text" name="contact_info" class="form-control" id="contact_info" placeholder="Enter contact info (optional)" value="{{ old('contact_info') }}">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('organizers.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
