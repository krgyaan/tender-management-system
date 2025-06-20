@extends('layouts.app')
@section('page-title', 'RA Management')

@section('content')
    @php
        $reason = [
            9 => 'OEM Bidders only',
            10 => 'Not allowed by OEM',
            11 => 'Not Eligible',
            12 => 'Product type bid',
            13 => 'Small Value Tender',
            14 => 'Product not available',
            15 => 'An electrical Contractor license needed',
        ];
        $commercial = [
            1 => 'Item Wise GST Inclusive',
            2 => 'Item Wise Pre GST',
            3 => 'Overall GST Inclusive',
            4 => 'Overall Pre GST',
        ];
        $maf = [
            1 => 'Yes (project specific)',
            2 => 'Yes (general)',
            3 => 'No',
        ];
        $tenderfees = [
            1 => 'Pay on Portal',
            2 => 'NEFT/RTGS',
            3 => 'DD',
            4 => 'Not Applicable',
        ];
        $emdReq = [
            1 => 'Yes',
            2 => 'No',
            3 => 'Exempt',
        ];
        $emdopt = [
            1 => 'Pay on Portal',
            2 => 'NEFT/RTGS',
            3 => 'DD',
            4 => 'BG',
            5 => 'Not Applicable',
        ];
        $revAuction = [
            1 => 'Yes',
            2 => 'No',
        ];
    @endphp
    <div class="row">
        <div class="col-md-12">
            <div class="card m-auto">
                <div class="card-body" id="print">
                    <div class="tender-info">
                        <div class="table-responsive">
                            <table class="table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="text-center text-white fw-bold">
                                            <h6 class="card-title text-center">{{ $tender->tender_name }}</h6>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th class="text-white fw-bold">Organisation</th>
                                        <td colspan="3">
                                            {{ $tender->organizations ? $tender->organizations->name : '' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">Tender Name</th>
                                        <td colspan="3">{{ $tender->tender_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">Tender No</th>
                                        <td colspan="3">{{ $tender->tender_no }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">Website</th>
                                        <td colspan="3">
                                            @if ($tender->websites)
                                                <a href="http://{{ $tender->websites->url }}" target="_blank"
                                                    rel="noopener noreferrer">
                                                    {{ $tender->websites->name }}
                                                    <i class="fa fa-external-link" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">Due Date & Time</th>
                                        <td colspan="3">{{ date('d-m-Y', strtotime($tender->due_date)) }}<br>
                                            {{ date('h:i A', strtotime($tender->due_time)) }}</td>
                                    </tr>
                                    <tr class="d-none">
                                        <th class="text-white fw-bold">Team Member</th>
                                        <td colspan="3">{{ $tender->users->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">Tender Fees</th>
                                        <td>{{ round($tender->tender_fees) }}</td>
                                        <th class="text-white fw-bold">Tender Items</th>
                                        <td>
                                            {{ $tender->itemName ? $tender->itemName->name : '' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">EMD</th>
                                        <td>{{ $tender->emd }}</td>
                                        <th class="text-white fw-bold">Remarks</th>
                                        <td>{{ $tender->remarks }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-white fw-bold">GST Values</th>
                                        <td>{{ $tender->gst_values }}</td>
                                        <th class="text-white fw-bold">Documents</th>
                                        <td>
                                            <ul>
                                                @foreach ($tender->docs as $doc)
                                                    <li>
                                                        <a href="/uploads/docs/{{ $doc->doc_path }}" target="_blank"
                                                            class="text-decoration-none">
                                                            Document - {{ $loop->iteration }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr class="d-none">
                                        <th class="text-white fw-bold">Location</th>
                                        <td colspan="3" class="text-capitalize">
                                            {{ $tender->locations ? $tender->locations->address : 'NA' }}
                                        </td>
                                    </tr>
                                    <tr class="d-none">
                                        <th class="text-white fw-bold">Status</th>
                                        <td colspan="3" class="text-uppercase">
                                            {{ $tender->statuses->name }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if ($tender->info)
                            <div class="table-responsive">
                                <table class="table-bordered w-100">
                                    <thead>
                                        <tr>
                                            <th colspan="8" class="text-center text-white fw-bold bg-">
                                                <h6 class="card-title text-center">Tender Information Sheet</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th class="text-white fw-bold">Recommendation By TE</th>
                                            <td>{{ $tender->info->is_rejectable == 0 ? 'Yes' : 'No' }}</td>
                                            <th class="text-white fw-bold">Reason</th>
                                            <td colspan="4">{{ $tender->info->reject_reason }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center" colspan="7">
                                                <b>Tender Information</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Tender Fees</th>
                                            <td>{{ format_inr($tender->tender_fees) }}</td>
                                            <td>Rs.</td>
                                            <th class="text-white fw-bold">Tender Fees in form of</th>
                                            <td colspan="3">
                                                {{ $tender->info->tender_fees ? $tenderfees[$tender->info->tender_fees] : 'NA' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">EMD</th>
                                            <td>{{ format_inr($tender->emd) }}</td>
                                            <td>Rs.</td>
                                            <th class="text-white fw-bold">EMD Required</th>
                                            <td colspan="3">
                                                {{ $tender->info->emd_req == 1 ? 'Yes' : 'No' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Tender Value (GST Incl.)</th>
                                            <td>{{ format_inr($tender->tender_value) }}</td>
                                            <td>Rs.</td>
                                            <th class="text-white fw-bold">EMD in form of</th>
                                            <td colspan="3">
                                                @php
                                                    if ($tender->info->emd_opt):
                                                        $emds = explode(',', $tender->info->emd_opt);
                                                        $emd = [];
                                                        foreach ($emds as $emd_id) {
                                                            $emd[] = $emdopt[$emd_id];
                                                        }
                                                        echo implode(', ', $emd);
                                                    endif;
                                                @endphp
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Bid Validity</th>
                                            <td>{{ $tender->info->bid_valid }}</td>
                                            <td>Days</td>
                                            <th class="text-white fw-bold">Commercial Evaluation</th>
                                            <td>{{ $tender->info->comm_eval == 1 ? 'Yes' : 'No' }}</td>
                                            <th class="text-white fw-bold">RA Applicable</th>
                                            <td>{{ $tender->info->rev_auction == 1 ? 'Yes' : 'No' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">MAF Required</th>
                                            <td colspan="2">{{ $tender->info->maf_req == 1 ? 'Yes' : 'No' }}</td>
                                            <th class="text-white fw-bold">Delivery Time (supply/total)</th>
                                            <td>{{ $tender->info->supply }} Days</td>
                                            <th class="text-white fw-bold">Delivery Time (I&C)</th>
                                            <td>{{ $tender->info->installation }} Days</td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">PBG %age</th>
                                            <td colspan="2">{{ $tender->info->pbg }}%</td>
                                            <th class="text-white fw-bold">Payment Terms (supply)</th>
                                            <td>{{ $tender->info->pt_supply }} Days</td>
                                            <th class="text-white fw-bold">Payment Terms (I&C)</th>
                                            <td>{{ $tender->info->pt_ic }} Days</td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">PBG Duration</th>
                                            <td colspan="2">{{ $tender->info->pbg_duration }} Months</td>
                                            <th class="text-white fw-bold">LD % (per week)</th>
                                            <td>{{ $tender->info->ldperweek }}%</td>
                                            <th class="text-white fw-bold">Max LD%</th>
                                            <td>{{ $tender->info->maxld }}%</td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Physical Docs Submission Required</th>
                                            <td>{{ $tender->info->phyDocs }}</td>
                                            <td></td>
                                            <th class="text-white fw-bold">Physical Docs Submission Deadline</th>
                                            <td colspan="3">
                                                @if ($tender->info->phyDocs)
                                                    {{ date('d-m-Y', strtotime($tender->info->dead_date)) }}<br>
                                                    {{ date('h:i A', strtotime($tender->info->dead_time)) }}
                                                @else
                                                    NA
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Eligibility Criterion</th>
                                            <td>{{ $tender->info->tech_eligible }}</td>
                                            <td> Age (in yrs)</td>
                                            <td colspan="4" class="text-center">
                                                <b>Financial Criterion</b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">3 Work Value</th>
                                            <td>{{ $tender->info->order3 }}</td>
                                            <td></td>
                                            <th class="text-white fw-bold">Annual Avg. Turnover</th>
                                            <td>{{ $tender->info->aat }}</td>
                                            <td>{{ $tender->info->aat ? format_inr($tender->info->aat_amt) : 'NA' }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">2 Work Value</th>
                                            <td>{{ $tender->info->order2 }}</td>
                                            <td></td>
                                            <th class="text-white fw-bold">Working Capital</th>
                                            <td>{{ $tender->info->wc }}</td>
                                            <td>{{ $tender->info->wc ? format_inr($tender->info->wc_amt) : 'NA' }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">1 Work Value</th>
                                            <td>{{ $tender->info->order1 }}</td>
                                            <td></td>
                                            <th class="text-white fw-bold">Net Worth</th>
                                            <td>{{ $tender->info->nw }}</td>
                                            <td>{{ $tender->info->nw ? format_inr($tender->info->nw_amt) : 'NA' }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Document for Technical Eligibility</th>
                                            <td colspan="2">
                                                <ul>
                                                    @forelse ($tender->info->workOrder as $item)
                                                        <li>
                                                            {{ optional($item->woName)->project_name }}
                                                        </li>
                                                    @empty
                                                        <li>No documents available</li>
                                                    @endforelse
                                                </ul>
                                            </td>
                                            <th class="text-white fw-bold">Solvency Certificate</th>
                                            <td>{{ $tender->info->sc }}</td>
                                            <td>{{ $tender->info->sc ? format_inr($tender->info->sc_amt) : 'NA' }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th class="text-white fw-bold">Tender Documents</th>
                                            <td colspan="3">
                                                <ul>
                                                    @forelse ($tender->docs as $item)
                                                        {{ $item->doc_path }}
                                                    @empty
                                                        {{ 'No documents available' }}
                                                    @endforelse
                                                </ul>
                                            </td>
                                            <th class="text-white fw-bold">Document for CommercialEligibility</th>
                                            <td colspan="3">
                                                <ul>
                                                    @forelse ($tender->info->eligibleDocs as $item)
                                                        <li>
                                                            {{ optional($item->docName)->document_name }}</li>
                                                    @empty
                                                        <li>No documents available</li>
                                                    @endforelse
                                                </ul>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="rfq">
                        <div class="text-center text-white fw-bold">
                            <h5 class="card-title text-center">RFQ Section</h5>
                        </div>
                        @if (isset($rfq) && $rfq->requirements)
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
                                        @foreach ($rfq->requirements as $req)
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
                        <table class="table-bordered w-100 mt-5">
                            <tbody>
                                <tr>
                                    <th>Scope of Work</th>
                                    <td>
                                        @if ($rfq->scopes)
                                            @foreach ($rfq->scopes as $item)
                                                <a href="{{ asset('upload/rfqdocs' . $item->file_path) }}"
                                                    target="_blank" rel="noopener noreferrer">{{ $item->name }}</a>
                                            @endforeach
                                        @endif
                                    </td>
                                    <th>Technical Specifications</th>
                                    <td>
                                        @if ($rfq->technicals)
                                            @foreach ($rfq->technicals as $item)
                                                <a href="{{ asset('upload/rfqdocs' . $item->file_path) }}"
                                                    target="_blank" rel="noopener noreferrer">{{ $item->name }}</a>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Detailed BOQ</th>
                                    <td>
                                        @if ($rfq->boqs)
                                            @foreach ($rfq->boqs as $item)
                                                <a href="{{ asset('upload/rfqdocs' . $item->file_path) }}"
                                                    target="_blank" rel="noopener noreferrer">{{ $item->name }}</a>
                                            @endforeach
                                        @endif
                                    </td>
                                    <th>MAF Format</th>
                                    <td>
                                        @if ($rfq->mafs)
                                            @foreach ($rfq->mafs as $item)
                                                <a href="{{ asset('upload/rfqdocs' . $item->file_path) }}"
                                                    target="_blank" rel="noopener noreferrer">{{ $item->name }}</a>
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>MII Format</th>
                                    <td>
                                        @if ($rfq->miis)
                                            @foreach ($rfq->miis as $item)
                                                <a href="{{ asset('upload/rfqdocs' . $item->file_path) }}"
                                                    target="_blank" rel="noopener noreferrer">{{ $item->name }}</a>
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
