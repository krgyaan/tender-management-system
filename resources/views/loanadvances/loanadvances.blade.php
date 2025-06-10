@extends('layouts.app')
@section('page-title', 'Loan & Advances')
@section('content')
    <div class="container-fluid content-inner p-0">
        <section>
            <div class="row">
                <div class="col-md-12 m-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('loanadvancesadd') }}" class="btn btn-primary">Add</a>
                    </div>
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table dataTable" id="allUsers">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            <th>Loan Party<br> Name</th>
                                            <th>Bank/NBFC<br> Name</th>
                                            <th>Loan<br> Amount</th>
                                            <th>Sanction <br>letter date</th>
                                            <th>Image</th>
                                            <th>EMI Payment<br> date</th>
                                            <th>Last EMI<br> date</th>
                                            <th>No. of <br>EMIs Paid</th>
                                            <th>Interest <br>paid</th>
                                            <th>Penal Charges<br> paid</th>
                                            <th> Principle<br> Outstanding</th>
                                            <th>TDS to be<br> recovered</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanadvances as $key => $loanadvancesData)
                                            @php
                                                $principle_paid = App\Models\Dueemi::where(
                                                    'loneid',
                                                    $loanadvancesData->id,
                                                )->sum('principle_paid');
                                                $interest_paid = App\Models\Dueemi::where(
                                                    'loneid',
                                                    $loanadvancesData->id,
                                                )->sum('interest_paid');
                                                $penal_charges_paid = App\Models\Dueemi::where(
                                                    'loneid',
                                                    $loanadvancesData->id,
                                                )->sum('penal_charges_paid');

                                                $remainingAmount = $loanadvancesData->loanamount - $principle_paid;
                                            @endphp

                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if (isset($loanadvancesData->loanadvances))
                                                        {{ $loanadvancesData->loanadvances->loanparty_name }}
                                                    @endif
                                                </td>
                                                <td>{{ $loanadvancesData->bank_name }}</td>
                                                <td>

                                                    @php
                                                        $amount = $loanadvancesData->loanamount;

                                                        // Create a number formatter for Indian currency without decimals or currency symbol
                                                        $locale = 'en_IN';
                                                        $fmt = new NumberFormatter($locale, NumberFormatter::DECIMAL);

                                                        // Set the minimum and maximum fraction digits to 0 to remove decimals
                                                        $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
                                                        $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

                                                        // Format the amount
                                                        $formattedAmount = $fmt->format($amount);

                                                        echo $formattedAmount;
                                                    @endphp
                                                    <br><span class="text-danger">
                                                        @php
                                                            $amount = $remainingAmount;

                                                            // Create a number formatter for Indian currency without decimals or currency symbol
                                                            $locale = 'en_IN';
                                                            $fmt = new NumberFormatter(
                                                                $locale,
                                                                NumberFormatter::DECIMAL,
                                                            );

                                                            // Set the minimum and maximum fraction digits to 0 to remove decimals
                                                            $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
                                                            $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

                                                            // Format the amount
                                                            $formattedAmount = $fmt->format($amount);

                                                            echo $formattedAmount;
                                                        @endphp
                                                    </span>

                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($loanadvancesData->sanctionletter_date)->format('d-m-Y') }}
                                                </td>
                                                <td>
                                                    <a href="{{ asset('upload/loanadvances/' . $loanadvancesData->sanction_letter) }}"
                                                        data-fancybox="image" class="whitespace-nowrap"
                                                        title="Sanction Letter">
                                                        Letter-1
                                                    </a>
                                                    <br>
                                                    <a href="{{ asset('upload/loanadvances/' . $loanadvancesData->bankloan_schedule) }}"
                                                        data-fancybox="image" class="whitespace-nowrap"
                                                        title="Bank Loan Schedule">
                                                        Sheet-1
                                                    </a>
                                                    <br>
                                                    @php
                                                        $filePath = public_path(
                                                            'upload/loanadvances/' . $loanadvancesData->loan_schedule,
                                                        );
                                                        $fileMime = mime_content_type($filePath);
                                                    @endphp

                                                    @if (strpos($fileMime, 'pdf') !== false)
                                                        <a href="{{ asset('upload/loanadvances/' . $loanadvancesData->loan_schedule) }}"
                                                            data-fancybox="gallery" class="whitespace-nowrap"
                                                            title="Loan Schedule">
                                                            Sheet-2
                                                        </a>
                                                    @elseif (strpos($fileMime, 'excel') !== false || strpos($fileMime, 'spreadsheetml') !== false)
                                                        <a href="{{ asset('upload/loanadvances/' . $loanadvancesData->loan_schedule) }}"
                                                            data-fancybox="gallery" class="whitespace-nowrap"
                                                            title="Loan Schedule">
                                                            Sheet-2
                                                        </a>
                                                    @else
                                                        <a href="{{ asset('upload/loanadvances/' . $loanadvancesData->loan_schedule) }}"
                                                            class="whitespace-nowrap" title="Loan Schedule">
                                                            Sheet-2
                                                        </a>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($loanadvancesData->emipayment_date)->format('d-m-Y') }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($loanadvancesData->lastemi_date)->format('d-m-Y') }}
                                                </td>
                                                <td></td>
                                                <td>
                                                    @if ($loanadvancesData->dueemi)
                                                        @php
                                                            $amounts = $interest_paid;

                                                            // Create a number formatter for Indian currency without decimals or currency symbol
                                                            $locale = 'en_IN';
                                                            $fmt = new NumberFormatter(
                                                                $locale,
                                                                NumberFormatter::DECIMAL,
                                                            );

                                                            // Set the minimum and maximum fraction digits to 0 to remove decimals
                                                            $fmt->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 0);
                                                            $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

                                                            // Format the amount
                                                            $formattedAmount = $fmt->format($amounts);
                                                        @endphp

                                                        <!-- Output the formatted amount -->
                                                        {{ $formattedAmount }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($loanadvancesData->dueemi)
                                                        {{ $penal_charges_paid }}
                                                    @endif
                                                </td>
                                                <td></td>
                                                <td>{{ $loanadvancesData->tdstobedeductedon_interest }}</td>
                                                <td>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <a href="{{ asset('admin/loanadvancesupdate/' . Crypt::encrypt($loanadvancesData->id)) }}"
                                                            class="btn btn-info btn-xs">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a onclick="return check_delete()"
                                                            href="{{ asset('admin/loanadvancesdelete/' . Crypt::encrypt($loanadvancesData->id)) }}"
                                                            class="btn btn-danger btn-xs">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        <a href="{{ asset('admin/dueview/' . Crypt::encrypt($loanadvancesData->id)) }}"
                                                            class="btn btn-warning btn-xs" data-bs-target="#emidata">
                                                            {{ $loanadvancesData->emipayment_date < date('Y-m-d') ? 'DUE' : 'Paid' }}
                                                        </a>
                                                        @if (strtotime($loanadvancesData->lastemi_date) < strtotime(date('d-m-Y')))
                                                            <a href="{{ asset('admin/loancloseupdate/' . Crypt::encrypt($loanadvancesData->id)) }}"
                                                                class="btn btn-warning btn-xs" data-bs-target="#emidata">
                                                                Loan Close
                                                            </a>
                                                        @endif
                                                        <a href="{{ asset('admin/tdsrecoveryview/' . Crypt::encrypt($loanadvancesData->id)) }}"
                                                            class="btn btn-primary btn-xs" data-bs-target="#tdsrecovery">
                                                            TDS
                                                        </a>
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
    </div>

    <div class="modal fade" id="tdsrecovery" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> TDS Recovery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ asset('/admin/tdsrecoveryadd') }}" enctype="multipart/form-data"
                        class="row">
                        @csrf
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS Amount recovered<span style="color:#d2322d">
                                    *</span></label>
                            <input type="text" value="" class="form-control" name="tds_amount" id="input36"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1')";
                                required>
                            <input type="text" id="lone_id" class="form-control" name="loneid" hidden readonly>
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">Upload TDS return document<span
                                    style="color:#d2322d"> *</span></label>
                            <input type="file" value="" class="form-control" name="tds_document" id="input36"
                                required>
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS recovery date<span style="color:#d2322d">
                                    *</span></label>
                            <input type="date" value="" class="form-control" name="tds_date" id="input36">
                        </div>
                        <div class="col-md-12">
                            <label for="input36" class=" col-form-label">TDS recovery Bank Transaction details<span
                                    style="color:#d2322d"> *</span></label>
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
