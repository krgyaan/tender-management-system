@extends('layouts.app')
@section('page-title', 'WO Dashboard')
@section('content')
<div class="container-fluid content-inner p-0">
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('basicdetailadd') }}" class="btn btn-primary">Basic Details</a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: max-content">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>WO Date</th>
                                        <th>Project Name</th>
                                        <th>WO Number</th>
                                        <th>WO Value(Pre-GST)</th>
                                        <th>LD Start Date</th>
                                        <th>Max. LD Date</th>
                                        <th>PBG Applicable</th>
                                        <th>Contract Agreement</th>
                                        <th>Action</th>
                                        <th>Action Butten</th>
                                        <th>Timer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($basic_data as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $row ? \Carbon\Carbon::parse($row->date)->format('d-m-Y') : '' }}</td>
                                       <td>{{ $tendername->firstWhere('id', $row->tender_name_id)->tender_name ?? 'N/A' }}</td>
                                        <td>{{ $row->number }}</td>
                                        <td>{{ $row->par_gst }}</td>
                                        <td>{{ $wodetails->firstWhere('basic_detail_id', $row->id)->ldstartdate ?? 'N/A' }}</td>
                                        <td>{{ $wodetails->firstWhere('basic_detail_id', $row->id)->maxlddate ?? 'N/A' }}</td>
                                        <td>@php
                                                $pbg_status = optional($wodetails->firstWhere('basic_detail_id', $row->id))->pbg_applicable_status;
                                            @endphp
                                            {{ $pbg_status === null ? 'N/A' : ($pbg_status == 1 ? 'Yes' : 'No') }}
                                        </td>
                                        <td>
                                            @php
                                                $con_agr_status = optional($wodetails->firstWhere('basic_detail_id', $row->id))->contract_agreement_status;
                                            @endphp
                                            {{ $con_agr_status === null ? 'N/A' : ($con_agr_status == 1 ? 'Yes' : 'No') }}
                                        </td>          
                                        <td>
                                            <a href="{{ asset('admin/basicdetailupdate/' . Crypt::encrypt($row->id)) }}" class="btn btn-info btn-sm">
                                                <svg class="svg-inline--fa fa-pencil" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pencil" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                    <path fill="currentColor" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                </svg>
                                            </a>
                                            <a onclick="return check_delete()" href="{{ asset('admin/basicdetaildelete/' . Crypt::encrypt($row->id)) }}" class="btn btn-danger btn-sm">
                                                <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path fill="currentColor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                                                </svg>
                                            </a>
                                            
                                           
                                            
                                        </td>
                                        <td>
                                             @php
                                                $detail = $wodetails->firstWhere('basic_detail_id', $row->id);
                                            @endphp
                                            
                                            <a href="{{ isset($wodetails) && $wodetails->where('basic_detail_id', $row->id)->isNotEmpty() ? asset('admin/wodetailupdate/' . Crypt::encrypt($detail->id)) : asset('admin/wodetailadd/' . Crypt::encrypt($row->id)) }}" class="btn btn-warning btn-sm btn-icon">
                                                WO Details({{ isset($wodetails) && $wodetails->where('basic_detail_id', $row->id)->isNotEmpty() ? 'Up' : 'Ad' }})
                                            </a> 
                                            
                                            <a href="{{ asset('admin/woacceptanceform/' . Crypt::encrypt($row->id)) }}" class="btn btn-warning btn-sm btn-icon">
                                                WO Acceptance
                                            </a><br>
                                            <a href="{{ asset('admin/woupdate/' . Crypt::encrypt($row->id)) }}" class=" mt-1 btn btn-warning btn-sm btn-icon">
                                                Wo Update
                                            </a>
                                           @if ($wodetails->contains('basic_detail_id', $row->id))
                                                <a href="{{ asset('admin/woviewbuttenfoa/' . Crypt::encrypt($row->id)) }}" class="mt-1 btn btn-warning btn-sm btn-icon">
                                                    View Button
                                                </a>
                                            @endif
                                        </td>
                                        <td></td>
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
</div>

@endsection
