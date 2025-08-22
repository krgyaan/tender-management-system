@extends('layouts.app')
@section('page-title', 'TDS Recovery View')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form
                            action="{{ isset($updatedata) ? route('tdsrecoveryupdatepost', $updatedata->id) : route('tdsrecoveryadd') }}"
                            enctype="multipart/form-data" method="POST" novalidate class="needs-validation">
                            @csrf
                            @if (isset($updatedata))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="input36" class=" col-form-label">TDS Amount recovered<span
                                            style="color:#d2322d"> *</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($updatedata) ? $updatedata->tds_amount : '' }}" name="tds_amount"
                                        id="input36"
                                        oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')";
                                        required>
                                    <input type="text" id="id" class="form-control"
                                        value="{{ isset($due_id) ? $due_id : '' }}" name="loneid" hidden readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="input36" class="col-form-label">Upload TDS return document<span
                                            style="color:#d2322d"> {{ isset($updatedata) ? '' : '*' }} </span></label>
                                    <div class="input-group">
                                        <input type="file" class="form-control"
                                            value="{{ isset($updatedata) ? $updatedata->tds_document : '' }}"
                                            name="tds_document" id="input36" {{ isset($updatedata) ? '' : 'required' }}>
                                        @if (isset($updatedata) && $updatedata->tds_document)
                                            <div class="input-group-append" style="position: relative;">
                                                <a href="{{ asset('upload/tdsrecovery/' . $updatedata->tds_document) }}"
                                                    data-fancybox="images" data-caption="">
                                                    <img src="{{ asset('upload/tdsrecovery/' . $updatedata->tds_document) }}"
                                                        class="input-group-text"
                                                        style="position: absolute; top: 50%; transform: translateY(-50%); right: 10px; height: 30px; width: 30px;">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="input36" class=" col-form-label">TDS recovery date<span
                                            style="color:#d2322d">
                                            *</span></label>
                                    <input type="date" class="form-control"
                                        value="{{ isset($updatedata) ? $updatedata->tds_date : '' }}" name="tds_date"
                                        id="input36" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="input36" class=" col-form-label">TDS recovery Bank Transaction details<span
                                            style="color:#d2322d"> *</span></label>
                                    <input type="text" class="form-control"
                                        value="{{ isset($updatedata) ? $updatedata->tdsrecoverybank_details : '' }}"
                                        name="tdsrecoverybank_details" id="input36" required>
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
        </div>
        <div class="container mt-5">

            <table class="table table-bordered table-hover table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>Sr.No</th>
                        <th>tds amount</th>
                        <th>TDS return document </th>
                        <th>TDS recovery date </th>
                        <th>Bank Transaction details </th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($viewdata as $num => $row)
                        <tr>
                            <td class="border">{{ $num + 1 }}</td>
                            <td class="border">{{ $row->tds_amount }}</td>
                            <td class="border text-center">
                                <a href="{{ asset('upload/tdsrecovery/' . $row->tds_document) }}" data-fancybox="images"
                                    data-caption="">
                                    <img src="{{ asset('upload/tdsrecovery/' . $row->tds_document) }}" class="  "
                                        style="height: 30px;width: 30px;">
                                </a>
                            </td>
                            <td class="border">{{ $row->tds_date }}</td>
                            <td class="border">{{ $row->tdsrecoverybank_details }}</td>
                            <td class="border">
                                <a href="{{ asset('admin/tdsrecoveryupdate/' . $row->id) }}"
                                    class="btn btn-info btn-sm">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a onclick="return check_delete()"
                                    href="{{ asset('admin/tdsrecoverydelete/' . $row->id) }}"
                                    class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
