@extends('layouts.app')
@section('page-title', 'All Receipts Received')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('rfq.index') }}" class="btn btn-primary btn-sm">RFQ Dashboard</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="table-responsive">
                            <table class="table " id="allUsers">
                                <thead class="">
                                    <tr>
                                        <th>Tender No.</th>
                                        <th>Tender Name</th>
                                        <th>Item</th>
                                        <th>Vendor Name</th>
                                        <th>Tender Due Date & Time</th>
                                        <th>Receipt Date & Time</th>
                                        <th>RFQ Timer</th>
                                        <th>Quotation Timer</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($receipts as $receipt)
                                        <tr>
                                            <td>
                                                {{ $receipt->rfq->tender->tender_no }}
                                            </td>
                                            <td>
                                                {{ $receipt->rfq->tender->tender_name }}
                                            </td>
                                            <td>
                                                @if ($receipt->items)
                                                    @foreach ($receipt->items as $item)
                                                        {{ $item->itemName->name }} <br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @foreach (explode(',', $receipt->rfq->tender->rfq_to) as $v)
                                                    @if (App\Models\Vendor::where('id', $v)->exists())
                                                        {{ App\Models\Vendor::find($v)->name }} <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ date('d-m-Y h:i A', strtotime($receipt->rfq->tender->due_date . ' ' . $receipt->rfq->tender->due_time)) }}
                                            </td>
                                            <td>
                                                {{ date('d-m-Y h:i A', strtotime($receipt->receipt_datetime)) }}
                                            </td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <a href="{{ route('rfq.show', $receipt->rfq->id) }}"
                                                    class="btn btn-info btn-sm">Quotation</a>
                                                <a href="#" class="btn btn-secondary btn-sm">FollowUp Frequency</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const timers = document.querySelectorAll('.timer');
            timers.forEach(startCountdown);
        });
    </script>
@endpush
