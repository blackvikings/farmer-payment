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
                        @foreach($farmers as $id => $name)
                            <option value="{{ $id }}" {{ old('farmer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
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
                    <label for="rate">Rate</label>
                    <input type="text" name="rate" class="form-control" id="rate" value="{{ old('rate') }}" placeholder="Enter Rate">
                </div>

                <div class="form-group">
                    <label for="bonus">Bonus</label>
                    <input type="text" name="bonus" class="form-control" id="bonus" value="{{ old('bonus') }}" placeholder="Enter Bonus">
                </div>

                <div class="form-group">
                    <label>Loss Rule</label>
                    <button type="button" class="btn btn-secondary" id="add_loss_rule">Add Input</button>
                    <div id="loss_rule_inputs" class="mt-2">
                        <!-- Dynamic inputs will be added here -->
                    </div>
                </div>

                <div class="form-group">
                    <label>Parameters</label>
                    <button type="button" class="btn btn-secondary" id="add_parameter">Add Parameter</button>
                    <div id="parameter_inputs" class="mt-2">
                        <!-- Dynamic inputs will be added here -->
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('agreements.index') }}" class="btn btn-default float-right">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            let lossRuleIndex = 0;
            $('#add_loss_rule').click(function() {
                $('#loss_rule_inputs').append(`
                    <div class="input-group mt-2">
                        <input type="text" name="loss_rules[${lossRuleIndex}][name]" class="form-control" placeholder="Enter Loss Rule Name">
                        <input type="text" name="loss_rules[${lossRuleIndex}][value]" class="form-control" placeholder="Enter Loss Rule Value">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-loss-rule" type="button">Remove</button>
                        </div>
                    </div>
                `);
                lossRuleIndex++;
            });

            $(document).on('click', '.remove-loss-rule', function() {
                $(this).closest('.input-group').remove();
            });

            let parameterIndex = 0;
            $('#add_parameter').click(function() {
                $('#parameter_inputs').append(`
                    <div class="input-group mt-2">
                        <input type="text" name="parameters[${parameterIndex}][name]" class="form-control" placeholder="Enter Parameter Name">
                        <input type="text" name="parameters[${parameterIndex}][value]" class="form-control" placeholder="Enter Parameter Value">
                        <div class="input-group-append">
                            <button class="btn btn-danger remove-parameter" type="button">Remove</button>
                        </div>
                    </div>
                `);
                parameterIndex++;
            });

            $(document).on('click', '.remove-parameter', function() {
                $(this).closest('.input-group').remove();
            });
        });
    </script>
@stop
