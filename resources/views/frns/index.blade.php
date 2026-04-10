@extends('adminlte::page')

@section('title', 'FRNs')

@section('content_header')
    <h1>Farmer Receipt Notes</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of FRNs</h3>
            <div class="card-tools">
                <a href="{{ route('frns.create') }}" class="btn btn-primary btn-sm">Capture New FRN</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>FRN Number</th>
                        <th>Lot Number</th>
                        <th>Arrival Date</th>
                        <th>Gross Wt.</th>
                        <th>Vehicle No.</th>
                        <th>Entry Instruction Status (FR4)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($frns as $frn)
                        <tr>
                            <td>{{ $frn->frn_number }}</td>
                            <td>{{ $frn->lot_number }}</td>
                            <td>{{ $frn->arrival_date }}</td>
                            <td>{{ $frn->gross_weight }}</td>
                            <td>{{ $frn->vehicle_number ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $instruction = \App\Models\EntryInstruction::where('lot_number', $frn->lot_number)->first();
                                @endphp
                                @if($instruction)
                                    <span class="badge badge-info">{{ $instruction->status }}</span>
                                @else
                                    <span class="badge badge-secondary">Not Created</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No FRNs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
