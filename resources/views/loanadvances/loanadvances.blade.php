@extends('layouts.app')
@section('page-title', 'Loan & Advances')
@php
    use App\Models\Dueemi;
@endphp
@section('content')
    <section>
        <div class="row">
            @if (Auth::user()->role == 'admin')
                <div class="col-md-12 text-center">
                    <h5>Loan Summary</h5>
                    <table class="table-bordered w-50 m-auto">
                        <thead>
                            <tr>
                                <th class="fw-bold p-2 fs-6">Loan Party Name</th>
                                <th class="fw-bold p-2 fs-6">Total Loan</th>
                                <th class="fw-bold p-2 fs-6">Total Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan_summary as $summary)
                                <tr>
                                    <td class="text-left fs-6 p-1">{{ $summary->bank_name }}</td>
                                    <td class="text-left fs-6 p-1">{{ format_inr($summary->total_loan) }}</td>
                                    <td class="text-left fs-6 p-1">{{ format_inr($summary->total_paid) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('loanadvancesadd') }}" class="btn btn-primary btn-sm">Add New Loan & Advance</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers">
                                <thead>
                                    <tr>
                                        <th>Loan Party<br> Name</th>
                                        <th>Bank/NBFC<br> Name</th>
                                        <th>Loan Account No.</th>
                                        <th>Loan Amount</th>
                                        <th>Sanction <br>letter date</th>
                                        <th>Related documents</th>
                                        <th>Latest EMI<br> Paid</th>
                                        <th>Last EMI<br> date</th>
                                        <th>No. of <br>EMIs Paid</th>
                                        <th>Interest <br>paid</th>
                                        <th>Penal Charges<br> paid</th>
                                        <th>TDS to be<br> recovered</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loanadvances as $key => $loan)
                                        @php
                                            $principle_paid = Dueemi::where('loneid', $loan->id)->sum('principle_paid');
                                            $interest_paid = Dueemi::where('loneid', $loan->id)->sum('interest_paid');
                                            $penal_charges_paid = Dueemi::where('loneid', $loan->id)->sum(
                                                'penal_charges_paid',
                                            );
                                            $tds = Dueemi::where('loneid', $loan->id)->sum('tdstobe_recovered');

                                            $remainingAmount = (float) $loan->loanamount - (float) $principle_paid;

                                            $totalEmisPaid = Dueemi::where('loneid', $loan->id)->count();
                                        @endphp

                                        <tr>
                                            <td>
                                                @if (isset($loan->loanadvances))
                                                    {{ $loan->loanadvances->loanparty_name }}
                                                @endif
                                            </td>
                                            <td>{{ $loan->bank_name }}</td>
                                            <td>{{ $loan->loac_acc_no }}</td>
                                            <td>
                                                <span data-bs-toggle="tooltip" title="Remaining Amount">
                                                    {{ format_inr($remainingAmount) }}
                                                </span>
                                                <br>
                                                <span class="text-danger" data-bs-toggle="tooltip" title="Loan Amount">
                                                    {{ format_inr($loan->loanamount) }}
                                                </span>

                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($loan->sanctionletter_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    // Sanction Letter
                                                    $sanctionPath = public_path(
                                                        'upload/loanadvances/' . $loan->sanction_letter,
                                                    );
                                                    $sanctionExists = file_exists($sanctionPath);
                                                    $sanctionStyle = $sanctionExists ? '' : 'style="color:white"';

                                                    // Bank Loan Schedule
                                                    $bankSchedulePath = public_path(
                                                        'upload/loanadvances/' . $loan->bankloan_schedule,
                                                    );
                                                    $bankScheduleExists = file_exists($bankSchedulePath);
                                                    $bankScheduleStyle = $bankScheduleExists
                                                        ? ''
                                                        : 'style="color:white"';

                                                    // Loan Schedule
                                                    $loanSchedulePath = public_path(
                                                        'upload/loanadvances/' . $loan->loan_schedule,
                                                    );
                                                    $loanScheduleExists = false;
                                                    $loanScheduleMime = '';

                                                    if (file_exists($loanSchedulePath)) {
                                                        $loanScheduleMime = mime_content_type($loanSchedulePath);
                                                        $loanScheduleExists = true;
                                                    } elseif (filter_var($loan->loan_schedule, FILTER_VALIDATE_URL)) {
                                                        $headers = @get_headers($loan->loan_schedule, 1);
                                                        if ($headers && isset($headers['Content-Type'])) {
                                                            $loanScheduleMime = is_array($headers['Content-Type'])
                                                                ? $headers['Content-Type'][0]
                                                                : $headers['Content-Type'];
                                                            $loanScheduleExists = true;
                                                        }
                                                    }

                                                    $loanScheduleStyle = $loanScheduleExists
                                                        ? ''
                                                        : 'style="color:white"';
                                                @endphp

                                                {{-- Sanction Letter --}}
                                                <a href="{{ asset('upload/loanadvances/' . $loan->sanction_letter) }}"
                                                    title="Sanction Letter" {!! $sanctionStyle !!}>
                                                    Sanction Letter
                                                </a>
                                                <br>

                                                {{-- Bank Loan Schedule --}}
                                                <a href="{{ asset('upload/loanadvances/' . $loan->bankloan_schedule) }}"
                                                    title="Bank Loan Schedule" {!! $bankScheduleStyle !!}>
                                                    Bank Loan Schedule
                                                </a>
                                                <br>

                                                {{-- Loan Schedule --}}
                                                @if (strpos($loanScheduleMime, 'pdf') !== false)
                                                    <a href="{{ asset('upload/loanadvances/' . $loan->loan_schedule) }}"
                                                        title="Loan Schedule" {!! $loanScheduleStyle !!}>
                                                        Loan Schedule
                                                    </a>
                                                @elseif (strpos($loanScheduleMime, 'excel') !== false || strpos($loanScheduleMime, 'spreadsheetml') !== false)
                                                    <a href="{{ asset('upload/loanadvances/' . $loan->loan_schedule) }}"
                                                        title="Loan Schedule" {!! $loanScheduleStyle !!}>
                                                        Loan Schedule
                                                    </a>
                                                @else
                                                    <a href="{{ $loan->loan_schedule }}" target="_blank"
                                                        class="whitespace-nowrap" title="Loan Schedule"
                                                        {!! $loanScheduleStyle !!}>
                                                        Loan Schedule
                                                    </a>
                                                @endif
                                            </td>

                                            <td>
                                                {{ \Carbon\Carbon::parse($loan->emipayment_date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($loan->lastemi_date)->format('d-m-Y') }}
                                            </td>
                                            <td>{{ $totalEmisPaid }}</td>
                                            <td>
                                                @if ($loan->dueemi)
                                                    {{ format_inr($interest_paid) }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($loan->dueemi)
                                                    {{ $penal_charges_paid }}
                                                @endif
                                            </td>
                                            <td>{{ format_inr($tds) }}</td>
                                            <td>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="{{ asset('admin/loanadvancesupdate/' . $loan->id) }}"
                                                        class="btn btn-info btn-xs">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="{{ asset('admin/dueview/' . $loan->id) }}"
                                                        class="btn btn-warning btn-xs" data-bs-target="#emidata">
                                                        Due
                                                    </a>
                                                    @if (strtotime($loan->lastemi_date) < strtotime(date('d-m-Y')))
                                                        <a href="{{ asset('admin/loancloseupdate/' . $loan->id) }}"
                                                            class="btn btn-warning btn-xs" data-bs-target="#emidata">
                                                            Closure
                                                        </a>
                                                    @endif
                                                    <a href="{{ asset('admin/tdsrecoveryview/' . $loan->id) }}"
                                                        class="btn btn-primary btn-xs" data-bs-target="#tdsrecovery">
                                                        TDS Recover
                                                    </a>
                                                    @if (Auth::user()->role == 'admin')
                                                        <a onclick="return check_delete()"
                                                            href="{{ asset('admin/loanadvancesdelete/' . $loan->id) }}"
                                                            class="btn btn-danger btn-xs">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    @endif
                                                </div>
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

    <div class="modal fade" id="tdsrecovery" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> TDS Recovery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ asset('/admin/tdsrecoveryadd') }}" enctype="multipart/form-data"
                        class="row" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS Amount recovered</label>
                            <input type="text" value="" class="form-control" name="tds_amount" id="input36"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')";
                                required>
                            <input type="text" id="lone_id" class="form-control" name="loneid" hidden readonly>
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">Upload TDS return document</label>
                            <input type="file" value="" class="form-control" name="tds_document" id="input36"
                                required>
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS recovery date</label>
                            <input type="date" value="" class="form-control" name="tds_date" id="input36">
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS recovery Bank Transaction details</label>
                            <input type="text" value="" class="form-control" name="tdsrecoverybank_details"
                                id="input36">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateId(id) {
            $('#id').val(id);
        }

        function loneId(id) {
            $('#lone_id').val(id);
        }
    </script>
@endpush
