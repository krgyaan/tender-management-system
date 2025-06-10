@extends('layouts.app')
@section('page-title', 'RFQ and Quotation Details')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card m-auto">
                <div class="card-body" id="print">
                    <div class="text-center text-white fw-bold">
                        <h5 class="card-title text-center">RFQ Section</h5>
                    </div>
                    @if ($rfq && $rfq->requirementss)
                        <table class="table-bordered w-100">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Requirement</th>
                                    <th>Unit</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (is_string($rfq->requirements))
                                    <tr>
                                        <td>{{ $rfq->requirements }}</td>
                                        <td>{{ $rfq->unit }}</td>
                                        <td>{{ $rfq->qty }}</td>
                                    </tr>
                                @else
                                    @foreach ($rfq->requirementss as $req)
                                        <tr>
                                            <td>{{ $req->requirement }}</td>
                                            <td>{{ $req->unit }}</td>
                                            <td>{{ $req->qty }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    @endif
                    <table class="table-bordered w-100 my-4">
                    <tbody>
                            <tr>
                                <th>Scope of Work</th>
                                <td>
                                    @if ($rfq->scopes)
                                        @foreach ($rfq->scopes as $item)
                                            <a href="{{ asset('uploads/rfqdocs/' . $item->file_path) }}" target="_blank"
                                                rel="noopener noreferrer">
                                                {!! nl2br(wordwrap($item->name, 50, "\n")) !!}</a>
                                        @endforeach
                                    @endif
                                </td>
                                <th>Technical Specifications</th>
                                <td>
                                    @if ($rfq->technicals)
                                        @foreach ($rfq->technicals as $item)
                                            <a href="{{ asset('uploads/rfqdocs/' . $item->file_path) }}" target="_blank"
                                                rel="noopener noreferrer">
                                                {!! nl2br(wordwrap($item->name, 50, "\n")) !!}</a>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Detailed BOQ</th>
                                <td>
                                    @if ($rfq->boqs)
                                        @foreach ($rfq->boqs as $item)
                                            <a href="{{ asset('uploads/rfqdocs/' . $item->file_path) }}" target="_blank"
                                                rel="noopener noreferrer">
                                                {!! nl2br(wordwrap($item->name, 50, "\n")) !!}
                                            </a>
                                        @endforeach
                                    @endif
                                </td>
                                <th>MAF Format</th>
                                <td>
                                    @if ($rfq->mafs)
                                        @foreach ($rfq->mafs as $item)
                                            <a href="{{ asset('uploads/rfqdocs/' . $item->file_path) }}" target="_blank"
                                                rel="noopener noreferrer">
                                                {!! nl2br(wordwrap($item->name, 50, "\n")) !!}</a>
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>MII Format</th>
                                <td>
                                    @if ($rfq->miis)
                                        @foreach ($rfq->miis as $item)
                                            <a href="{{ asset('uploads/rfqdocs/' . $item->file_path) }}" target="_blank"
                                                rel="noopener noreferrer">{{ $item->name }}</a>
                                        @endforeach
                                    @endif
                                </td>
                                <th>Other Documents needed</th>
                                <td>
                                    @if ($rfq->docs_list)
                                        {{ $rfq->docs_list }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($rfq && $rfq->rfqVendors)
                        <table class="table-bordered w-100">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Organisation</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rfq->rfqVendors as $vendor)
                                    <tr>
                                        <td>{{ $vendor->vendorss->name }}</td>
                                        <td>{{ $vendor->vendorss->vendorOrg->name }}</td>
                                        <td>{{ $vendor->vendorss->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($rfq->rfqResponse)
                        <div class="text-center text-white fw-bold mt-5">
                            <h5 class="card-title text-center">Quotation Section</h5>
                        </div>
                        <table class="table-bordered w-100">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Sr.No.</th>
                                    <th>Item Name</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Unit Price</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rfq->rfqResponse->items as $it)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $it->itemName->name }}</td>
                                        <td>{{ $it->quantity }}</td>
                                        <td>{{ $it->unit }}</td>
                                        <td>{{ format_inr($it->unit_price) }}</td>
                                        <td>{{ format_inr($it->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table-bordered w-100 mt-5">
                            <tbody>
                                <tr>
                                    <th>GST %age</th>
                                    <td>{{ $rfq->rfqResponse->gst_percentage }}</td>
                                    <th>GST Type</th>
                                    <td>{{ $rfq->rfqResponse->gst_type }}</td>
                                </tr>
                                <tr>
                                    <th>Delivery Time</th>
                                    <td>{{ $rfq->rfqResponse->delivery_time }}</td>
                                    <th>Freight</th>
                                    <td>{{ $rfq->rfqResponse->freight_type }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-center">Uploaded Documents</td>
                                </tr>
                                <tr>
                                    <th>Quotation</th>
                                    <td>
                                        @if ($rfq->rfqResponse->quotation_document)
                                            <a href="{{ asset('uploads/rfqdocs/' . $rfq->rfqResponse->quotation_document) }}"
                                                target="_blank">View</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                    <th>Technical documents</th>
                                    <td>
                                        @if ($rfq->rfqResponse->technical_documents)
                                            <a href="{{ asset('uploads/rfqdocs/' . $rfq->rfqResponse->technical_documents) }}"
                                                target="_blank">View</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Signed MAF</th>
                                    <td>
                                        @if ($rfq->rfqResponse->maf_document)
                                            <a href="{{ asset('uploads/rfqdocs/' . $rfq->rfqResponse->maf_document) }}"
                                                target="_blank">View</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                    <th>Signed MII</th>
                                    <td>
                                        @if ($rfq->rfqResponse->mii_document)
                                            <a href="{{ asset('uploads/rfqdocs/' . $rfq->rfqResponse->mii_document) }}"
                                                target="_blank">View</a>
                                        @else
                                            Not uploaded
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        th,
        td {
            padding: 8px;
            font-size: 14px;
        }
    </style>
@endpush
