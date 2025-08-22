@extends('layouts.app')
@section('page-title', 'WO Dashboard')
@section('content')
<div class="container-fluid content-inner p-0">
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <!--<a href="" class="btn btn-primary">WO Detail Add</a>-->
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
                                        <th>Timer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($wo_data as $key => $row)
                                        @php
                                            $matchedData = $basic_data->where('id', $row->basic_detail_id)->first();
                                             $tenderdata = $tendername->where('id', $matchedData->tender_name_id)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $matchedData ? \Carbon\Carbon::parse($matchedData->date)->format('d-m-Y') : '' }}</td>
                                            <td>{{ $tenderdata ? Str::limit($tenderdata->tender_name, 20) : 'N/A' }}</td>
                                            <td> {{ $matchedData ? $matchedData->number : '' }}</td>
                                            <td> {{ $matchedData ? $matchedData->par_gst : '' }}</td>
                                            <td>{{\Carbon\Carbon::parse($row->ldstartdate)->format('d-m-Y')}}</td>
                                            <td>{{\Carbon\Carbon::parse($row->maxlddate)->format('d-m-Y')}}</td>
                                            <td>{{ $row->pbg_applicable_status == 1 ? 'Yes' : 'No' }}</td>
                                            <td>{{ $row->contract_agreement_status == 1 ? 'Yes' : 'No' }}</td>
    
                                         
                                           
                                            <td>
                                               
                                                <a  href="{{ asset('admin/woviewbuttenfoa/' . Crypt::encrypt($row->id)) }}"  class="btn btn-warning btn-sm btn-icon">View Button</a>
                                                <a  href="" class="btn btn-warning btn-sm btn-icon">WO Details Form</a><br>
                                                <a href="{{ asset('admin/wodetailupdate/' . Crypt::encrypt($row->id)) }}" class="mt-1 btn btn-warning btn-sm btn-icon ">
                                                    WO Uplode Form
                                                </a>
                                                
                                                <a  href="" class=" mt-1 btn btn-warning btn-sm btn-icon">WO Acceptance(for TL, CEO only)</a>
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
</div>

@endsection
