@extends('layouts.app')
@section('page-title', 'Due EMI View')
@section('content')
    <section>
        <div class="row">
            @if (strtotime($loneid->emipayment_date) < strtotime(date('d-m-Y')))
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-body p-4">
                            <form
                                action="{{ isset($updatedata) ? route('dueemiupdatepost', $updatedata->id) : route('dueemiadd') }}"
                                method="POST" novalidate class="needs-validation">
                                @csrf
                                @if (isset($updatedata))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="input36" class=" col-form-label">EMI Payment date<span
                                                style="color:#d2322d"> *</span></label>
                                        <input type="Date" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->emi_date : '' }}" name="emi_date"
                                            id="input36" required>
                                        <input type="text" id="id" class="form-control"
                                            value="{{ isset($due_id) ? $due_id : '' }}" name="loneid" hidden readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input36" class=" col-form-label">Principle paid<span
                                                style="color:#d2322d">
                                                *</span></label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->principle_paid : '' }}"
                                            name="principle_paid" id="input36" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input36" class=" col-form-label">Interest Paid<span
                                                style="color:#d2322d">
                                                *</span></label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->interest_paid : '' }}"
                                            name="interest_paid" id="input36" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input36" class=" col-form-label">TDS to be recovered<span
                                                style="color:#d2322d"> *</span></label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->tdstobe_recovered : '' }}"
                                            name="tdstobe_recovered" id="input36" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="input36" class=" col-form-label">Penal Charges paid<span
                                                style="color:#d2322d"> *</span></label>
                                        <input type="text" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->penal_charges_paid : '' }}"
                                            name="penal_charges_paid" id="input36" required>
                                    </div>
                                </div>

                                <div class="mt-3 ">
                                    <button type="submit" class="btn btn-primary ">
                                        {{ isset($updatedata) ? 'Update' : 'Submit' }}
                                    </button>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
            @endif
            <div class="container mt-5">

                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Sr.No</th>
                            <!--<th>EMI date</th>-->
                            <th>Principle paid </th>
                            <th>Interest Paid </th>
                            <th>TDS to be recovered </th>
                            <th>Penal Charges paid </th>
                            @if (strtotime($loneid->emipayment_date) < strtotime(date('d-m-Y')))
                                <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($viewdata as $num => $row)
                            <tr>
                                <td class="border">{{ $num + 1 }}</td>
                                <!--<td class="border" >{{ $row->emi_date }}</td>-->
                                <td class="border">{{ $row->principle_paid }}</td>
                                <td class="border">{{ $row->interest_paid }}</td>
                                <td class="border">{{ $row->tdstobe_recovered }}</td>
                                <td class="border">{{ $row->penal_charges_paid }}</td>
                                @if (strtotime($loneid->emipayment_date) < strtotime(date('d-m-Y')))
                                    <td class="border">
                                        <a href="{{ asset('admin/dueemiupdate/' . Crypt::encrypt($row->id)) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a onclick="return check_delete()"
                                            href="{{ asset('admin/dueemidelete/' . Crypt::encrypt($row->id)) }}"
                                            class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                @endif

                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
