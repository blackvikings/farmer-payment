@extends('adminlte::page')

@section('title', 'Quality Validation')

@section('content_header')
    <h1>Quality Validation (QC)</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pending and Completed Lots for Quality Check</h3>
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
                        <th>Lot Number</th>
                        <th>Initial Quantity</th>
                        <th>Final Quantity</th>
                        <th>Process Loss (%)</th>
                        <th>QC Status</th>
                        <th>Debit Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lots as $lot)
                        <tr>
                            <td>{{ $lot->lot_number }}</td>
                            <td>{{ $lot->quantity }}</td>
                            <td>{{ $lot->final_quantity ?? 'N/A' }}</td>
                            <td>{{ $lot->process_loss ? number_format($lot->process_loss, 2) . '%' : 'N/A' }}</td>
                            <td>
                                @if($lot->qc_status == 'Pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($lot->qc_status == 'Accepted')
                                    <span class="badge badge-success">Accepted</span>
                                @elseif($lot->qc_status == 'Conditional')
                                    <span class="badge badge-info">Conditional</span>
                                @else
                                    <span class="badge badge-danger">{{ $lot->qc_status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($lot->debit_note_id)
                                    @if($lot->debit_override)
                                        <span class="badge badge-success">Approved</span>
                                    @else
                                        <span class="badge badge-warning">Pending Approval</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">No Debit</span>
                                @endif
                            </td>
                            <td>
                                @if(!$lot->qc_status || $lot->qc_status == 'Pending')
                                    <a href="{{ route('quality.check', $lot->lot_number) }}" class="btn btn-primary btn-sm">Perform QC</a>
                                @endif

                                @if($lot->debit_note_id && !$lot->debit_override)
                                    <form action="{{ route('debit-note.approve', $lot->debit_note_id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Approve Debit</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No lots available for quality validation.</td>
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
