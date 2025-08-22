@extends('layouts.app')
@section('page-title', 'Employee Imprest Vouchers')
@section('content')
    <div class="page-wrapper">
        @include('partials.messages')
        <div class="page-content">
            <div class="row">
                <div class="col-md-12 mx-auto mt-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ URL::previous() }}" class="btn btn-outline-danger btn-sm">back</a>
                    </div>
                    <div class="card">
                        @include('partials.messages')
                        <div class="card-body">
                            <div class="row" id="printableArea">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="">
                                            <tbody>
                                                <tr>
                                                    <td colspan="4">
                                                        <h4>Volks Energie Pvt Ltd</h4>
                                                        <p @class(['pt-0', 'm-0', 'fs-6', 'pt-1'])>
                                                            Solar and Air Conditioning Contractor
                                                        </p>
                                                        <p @class(['pt-0', 'm-0', 'fs-6', 'pt-1'])>
                                                            New Delhi - 110044
                                                        </p>
                                                        <p @class(['pt-0', 'm-0', 'fs-6', 'pt-1'])>
                                                            Ph: +91- 8882591733 | E-mail : accounts@volksenergie.in
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        <h3 @class(['pt-3'])>Expense Report</h3>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p @class(['p-0', 'm-0', 'pt-1', 'fs-6'])>
                                                            FROM <b>{{ date('d M, Y', strtotime($from)) }}</b>
                                                            TO <b>{{ date('d M, Y', strtotime($to)) }}</b>
                                                        </p>
                                                    </td>
                                                    <td colspan="3">
                                                        <p @class(['p-0', 'm-0', 'pt-1', 'fs-6', 'font-bold'])>Voucher No:
                                                            {{ $last }}
                                                        </p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <p @class(['p-0', 'm-0', 'pt-1', 'fs-6'])>
                                                            Employee Name: <br>
                                                            <b>{{ $abc->name }}</b>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p @class(['p-0', 'm-0', 'pt-1', 'fs-6'])>
                                                            Employee ID: <br>
                                                            <b>ID00{{ $abc->id }}</b>
                                                        </p>
                                                    </td>
                                                    <td colspan="2">
                                                        <p @class(['p-0', 'm-0', 'pt-1', 'fs-6'])>
                                                            Team Name: <br>
                                                            <b>{{ $abc->team }}</b>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive pt-5">
                                        <table class="table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No.</th>
                                                    <th>Category</th>
                                                    <th>Project Code</th>
                                                    <th>Project Name</th>
                                                    <th>Remarks</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $total = 0;
                                                @endphp
                                                @if ($vo->isEmpty())
                                                    <tr>
                                                        <td colspan="5" class="text-center">No records found.</td>
                                                    </tr>
                                                @else
                                                    @foreach ($vo as $v)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{!! nl2br(wordwrap($v->category->category, 30, "\n")) !!}</td>
                                                            <td>{!! \App\Models\Project::where('project_name', $v->project_name)->first()?->project_code !!}
                                                            </td>
                                                            <td>{!! nl2br(wordwrap($v->project_name, 30, "\n")) !!}</td>
                                                            <td>{!! nl2br(wordwrap($v->remark, 40, "\n")) !!}</td>
                                                            <td>{{ format_inr($v->amount) }}</td>
                                                        </tr>
                                                        @php
                                                            $total += $v->amount;
                                                        @endphp
                                                    @endforeach
                                                @endif
                                                <tr>
                                                    <td colspan="5" class="text-end fs-6">Total</td>
                                                    <td class="fs-6">
                                                        {{ format_inr($total) }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive pt-5">
                                        <table class="w-50">
                                            <tbody>
                                                <tr>
                                                    <th style="width: 200px;">
                                                        <p @class(['p-0', 'fs-6'])>Prepared By: </p>
                                                    </th>
                                                    <td>
                                                        <p @class(['p-0', 'fs-6', 'text-start'])>{{ $abc->name }}</p>
                                                    </td>
                                                    <th>
                                                        <p @class(['p-0', 'fs-6'])>Date: </p>
                                                    </th>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 200px;">
                                                        <p @class(['p-0', 'fs-6'])>Checked By: </p>
                                                    </th>
                                                    <td>
                                                        @if (optional($voucher)->acc_sign != null)
                                                            <img src="{{ asset('uploads/signs/' . $voucher->acc_sign) }}"
                                                                alt="account-sign" height="40" width="120">
                                                        @else
                                                            <i>to be signed</i>
                                                        @endif
                                                    </td>
                                                    <th>
                                                        <p @class(['p-0', 'fs-6'])>Date: </p>
                                                    </th>
                                                    <td>
                                                        @if (optional($voucher)->acc_sign_date != null)
                                                            <p>
                                                                {{ date('d M, Y', strtotime($voucher->acc_sign_date)) }}
                                                            </p>
                                                        @else
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 200px;">
                                                        <p @class(['p-0', 'fs-6'])>Approved By: </p>
                                                    </th>
                                                    <td>
                                                        @if (optional($voucher)->admin_sign != null)
                                                            <img src="{{ asset('uploads/signs/' . $voucher->admin_sign) }}"
                                                                alt="admin-sign" height="40" width="120">
                                                        @else
                                                            <i>to be signed</i>
                                                        @endif
                                                    </td>
                                                    <th>
                                                        <p @class(['p-0', 'fs-6'])>Date: </p>
                                                    </th>
                                                    <td>
                                                        @if (optional($voucher)->admin_sign_date != null)
                                                            <p>
                                                                {{ date('d M, Y', strtotime($voucher->admin_sign_date)) }}
                                                            </p>
                                                        @else
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-center align-items-center gap-3">
                                    @if (Str::startsWith(Auth::user()->role, 'account'))
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#accSignModal">
                                            Approve by Accounts
                                        </button>
                                    @endif
                                    @if (auth()->user()->role == 'admin')
                                        <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#adminSignModal">
                                            Approve by CEO
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-primary btn-sm"
                                        onclick="printDiv('printableArea')">
                                        Print
                                    </button>
                                </div>
                            </div>
                            <div id="response"></div>

                            <!-- Account Approval Modal -->
                            <div class="modal fade" id="accSignModal" tabindex="-1" aria-labelledby="accSignModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="accSignModalLabel">Approve by Accounts</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="accSignForm">
                                                <div class="mb-3">
                                                    <label for="accRemark" class="form-label">Remark</label>
                                                    <textarea class="form-control" id="accRemark" name="remark"
                                                        rows="3"></textarea>
                                                </div>
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="accApproveIt" name="approve_it">
                                                    <label class="form-check-label" for="accApproveIt">
                                                        Approve it
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Admin Approval Modal -->
                            <div class="modal fade" id="adminSignModal" tabindex="-1" aria-labelledby="adminSignModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="adminSignModalLabel">Approve by CEO</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="adminSignForm">
                                                <div class="mb-3">
                                                    <label for="adminRemark" class="form-label">Remark</label>
                                                    <textarea class="form-control" id="adminRemark" name="remark"
                                                        rows="3"></textarea>
                                                </div>
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input" type="checkbox" value="1"
                                                        id="adminApproveIt" name="approve_it">
                                                    <label class="form-check-label" for="adminApproveIt">
                                                        Approve it
                                                    </label>
                                                </div>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#accSignForm').on('submit', function (e) {
                e.preventDefault();
                const voucherId = '{{ $last }}'; // VE/24/V114
                const safeVoucherId = voucherId.replaceAll('/', '-');
                const remark = $('#accRemark').val();
                const approveIt = $('#accApproveIt').is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/imprest/account-sign/' + safeVoucherId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remark: remark,
                        approve_it: approveIt
                    },
                    success: (response) => {
                        if (response.success) {
                            $('#accSignModal').modal('hide');
                            $('#accSignForm')[0].reset();
                            $('#response').html(response.message);
                            console.log(response);
                        } else {
                            $('#response').html(response.message);
                            console.log(response);
                        }
                    },
                    error: (xhr) => {
                        alert('An error occurred while updating the status.');
                        console.log(xhr.responseText);
                    }
                });
            });

            $('#adminSignForm').on('submit', function (e) {
                e.preventDefault();
                const voucherId = '{{ $last }}'; // VE/24/V114
                const safeVoucherId = voucherId.replaceAll('/', '-');
                const remark = $('#adminRemark').val();
                const approveIt = $('#adminApproveIt').is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/imprest/admin-sign/' + safeVoucherId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        remark: remark,
                        approve_it: approveIt
                    },
                    success: (response) => {
                        if (response.success) {
                            $('#adminSignModal').modal('hide');
                            $('#adminSignForm')[0].reset();
                            $('#response').html(response.message);
                            console.log(response);
                        } else {
                            $('#response').html(response.message);
                            console.log(response);
                        }
                    },
                    error: (xhr) => {
                        alert('An error occurred while updating the status.');
                        console.log(xhr.responseText);
                    }
                });
            });
        });

        function printDiv(printableArea) {
            const printContents = document.getElementById(printableArea).innerHTML;
            const originalContents = document.body.innerHTML;
            const originalStyles = document.body.getAttribute('style') || '';

            document.body.innerHTML = printContents;
            document.body.setAttribute('style', 'background: #fff; color: #000;');

            const style = document.createElement('style');
            style.innerHTML = `
                            h1, h2, h3, h4, h5, h6 {
                                color: #000;
                                font-weight: bold;
                            }
                            table, tr, td, th{
                                border: 1px solid #000;
                            }
                        `;
            document.head.appendChild(style);

            window.print();

            document.body.innerHTML = originalContents;
            document.body.setAttribute('style', originalStyles);
            document.head.removeChild(style);
        }
    </script>
@endpush

@push('styles')
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 5px;
        }

        table th {
            padding: 5px;
            font-size: 16px;
            font-weight: 600
        }
    </style>
@endpush
