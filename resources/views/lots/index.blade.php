@extends('adminlte::page')

@section('title', 'Lots')

@section('content_header')
    <h1>Lots / Farmer Production (FR2)</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">List of Lots</h3>
            <div class="card-tools">
                <a href="{{ route('lots.create') }}" class="btn btn-primary btn-sm">Receive New Lot</a>
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
                        <th>Lot Number</th>
                        <th>Farmer</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>QC Status</th>
                        <th>Net Payable</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lots as $lot)
                        <tr>
                            <td>{{ $lot->lot_number }}</td>
                            <td>{{ optional(optional($lot->agreement)->farmer)->name }}</td>
                            <td>{{ $lot->quantity }}</td>
                            <td>
                                @if($lot->status == 'accepted')
                                    <span class="badge badge-success">Accepted</span>
                                @elseif($lot->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst($lot->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $lot->qc_status ?? '-' }}</td>
                            <td>{{ $lot->net_payable ?? '-' }}</td>
                            <td>
                                @if($lot->payment_status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($lot->pricing_approved)
                                    <span class="badge badge-info">Approved</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($lot->status == 'accepted' && $lot->qc_status && !$lot->pricing_approved && !$lot->payment_blocked)
                                    <form action="{{ route('lots.calculate-pricing', $lot) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary">Calculate</button>
                                    </form>
                                @endif

                                @if(!is_null($lot->net_payable) && !$lot->pricing_approved && !$lot->payment_blocked)
                                     <form action="{{ route('lots.approve-pricing', $lot) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                    </form>
                                @endif

                                @if($lot->pricing_approved && $lot->payment_status != 'paid')
                                    <form action="{{ route('lots.process-payment', $lot) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Pay</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No lots found.</td>
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
