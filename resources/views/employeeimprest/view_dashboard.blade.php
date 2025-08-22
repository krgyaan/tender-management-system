@extends('layouts.app')
@section('page-title', "Imprest Details of $name")
@section('content')
    <section>
        <div class="row">
            <div class="col-md-3 p-4 rounded shadow border">
                <h6>Amount Received</h6>
                <p>{{ $amtReceived }}</p>
            </div>
            <div class="col-md-3 p-4 rounded shadow border">
                <h6>Amount Spent</h6>
                <p>{{ $amtSpent }}</p>
            </div>
            <div class="col-md-3 p-4 rounded shadow border">
                <h6>Amount Approved</h6>
                <p>{{ $amtApproved }}</p>
            </div>
            <div class="col-md-3 p-4 rounded shadow border">
                <h6>Amount Left</h6>
                <p>{{ $amtLeft }}</p>
            </div>
            @include('partials.messages')
            <div class="col-md-12 m-auto mt-3">
                <div class="col-md-12 pb-3">
                    <form action="{{ route('dateFilterAcc') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-4 form-group">
                            <input type="hidden" name="name_id" class="form-control"
                                value="{{ optional($employee->first())->name_id }}">
                            <label for="start_date">From Date</label>
                            <input type="date" name="start_date" class="form-control" id="start_date"
                                value="{{ $start_date ?? '' }}">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="end_date">To Date</label>
                            <input type="date" name="end_date" class="form-control" id="end_date"
                                value="{{ $end_date ?? '' }}">
                        </div>
                        <div class="col-md-4 form-group d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </div>
                    </form>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('employeeimprest_account') }}" class="btn btn-outline-danger btn-sm">Back</a>
                    <button data-bs-toggle="modal" data-bs-target="#payImprest" class="btn btn-sm btn-primary">Pay Imprest</button>
                    <a href="{{ route('imprest-voucher', optional($employee->first())->name_id) }}" class="btn btn-sm btn-success">Imprest Voucher</a>
                    <form method="GET" action="{{ url('/download-employee-imprest') }}">
                        <input type="hidden" name="name_id" value="{{ optional($employee->first())->name_id }}">
                        <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}">
                        <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}">
                        <button type="submit" class="btn btn-outline-success btn-sm mt-3">Download Excel</button>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table class="table dataTable" id="allUsers">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Party Name</th>
                                        <th>Project Name</th>
                                        <th>Amount</th>
                                        <th>Category</th>
                                        <th>Proof</th>
                                        <th>Remarks</th>
                                        <th>Account Team <br> Remarks</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($employee as $employeedata)
                                        <tr>
                                            <td>{{ date('d-m-Y', $employeedata->strtotime) }}</td>
                                            <td>{{ $employeedata->user->name }}</td>
                                            <td>{{ $employeedata->party_name }}</td>
                                            <td>{{ $employeedata->project_name }}</td>
                                            <td>{{ format_inr($employeedata->amount) }}</td>
                                            <td>
                                                {{ $employeedata->category->category }}
                                                @if ($employeedata->category_id == 22)
                                                    <br>
                                                    {{ $employeedata->team->name }}
                                                @else
                                                    {{ '' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($employeedata->invoice_proof)
                                                    @php
                                                        $files = json_decode($employeedata->invoice_proof, true);
                                                    @endphp

                                                    @if (is_array($files))
                                                        @foreach ($files as $index => $file)
                                                            @php
                                                                $fileExtension = strtolower(
                                                                    pathinfo($file, PATHINFO_EXTENSION),
                                                                );
                                                            @endphp

                                                            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                <a href="{{ config('app.url') }}uploads/employeeimprest/{{ $file }}"
                                                                    data-fancybox="gallery"
                                                                    target="_blank">IMG-{{ $index + 1 }}</a><br>
                                                            @elseif(in_array($fileExtension, ['pdf']))
                                                                <a href="{{ config('app.url') }}uploads/employeeimprest/{{ $file }}"
                                                                    data-fancybox="gallery"
                                                                    target="_blank">PDF-{{ $index + 1 }}
                                                                </a><br>
                                                            @else
                                                                <p>No proof found</p>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $fileExtension = strtolower(
                                                                pathinfo(
                                                                    $employeedata->invoice_proof,
                                                                    PATHINFO_EXTENSION,
                                                                ),
                                                            );
                                                        @endphp

                                                        @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                            <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeedata->invoice_proof }}"
                                                                data-fancybox="gallery" target="_blank">IMG-1</a>
                                                        @elseif(in_array($fileExtension, ['pdf']))
                                                            <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeedata->invoice_proof }}"
                                                                data-fancybox="gallery" target="_blank">PDF-1</a>
                                                        @else
                                                            <p>No proof found</p>
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $employeedata->remark }}</td>
                                            <td>{{ $employeedata->acc_remark }}</td>
                                            <td class="d-flex gap-2 flex-wrap">
                                                <button class="btn btn-info btn-xs" name="buttonstatus"
                                                    title="Approved Status" id="approvedBtn-{{ $employeedata->id }}"
                                                    onclick="markApproved({{ $employeedata->id }}, this)">
                                                    @if ($employeedata->buttonstatus == 1)
                                                        ✅
                                                    @else
                                                        pending
                                                    @endif
                                                </button>
                                                <button class="btn btn-warning btn-xs" name="tallybtn" title="Tally Status"
                                                    id="tallyBtn-{{ $employeedata->id }}"
                                                    onclick="tallyApproved({{ $employeedata->id }}, this)">
                                                    @if ($employeedata->tallystatus == 1)
                                                        ✅
                                                    @else
                                                        Entered in Tally
                                                    @endif
                                                </button>
                                                <button class="btn btn-danger btn-xs" name="proofbtn" title="Proof Status"
                                                    id="proofBtn-{{ $employeedata->id }}"
                                                    onclick="proofApproved({{ $employeedata->id }}, this)">
                                                    @if ($employeedata->proofstatus == 1)
                                                        ✅
                                                    @else
                                                        Proofs filled
                                                    @endif
                                                </button>
                                                <button type="button" class="btn btn-primary btn-xs" data-bs-toggle="modal"
                                                    onclick="update('{{ $employeedata->id }}')"
                                                    data-bs-target="#exampleModal">
                                                    Add Proof
                                                </button>
                                                <button type="button" class="btn btn-secondary btn-xs addRemarkBtn"
                                                    data-eiId="{{ $employeedata->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#remarkModal">
                                                    Add Remarks
                                                </button>
                                                @if (Auth::user()->role == 'admin' || Str::startsWith(Auth::user()->role, 'account'))
                                                    <form action="{{ route('imprest.delete', $employeedata->id) }}"
                                                        method="POST" class="">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this record?')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="{{ route('employeeimprest_edit', $employeedata->id) }}"
                                                        class="btn btn-info btn-xs">
                                                        Edit
                                                    </a>
                                                @endif
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Proof</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to upload new proof -->
                    <form action="{{ route('add_proof') }}" method="POST" enctype="multipart/form-data"
                        id="uploadInvoiceForm">
                        @csrf
                        <input type="hidden" name="id" id="employee_id" value="">

                        <label for="invoice_proof" class="form-label">Invoice/Proof<span
                                class="text-danger">*</span></label>
                        <input type="file" name="invoice_proof[]" class="form-control" multiple id="invoice_proof">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" form="uploadInvoiceForm">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="payImprest" tabindex="-1" aria-labelledby="payImprestLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="card-title" id="payImprestLabel">Pay Imprest To {{ $name }}</h5>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ asset('/admin/employeeimprest_amount_post') }}"
                        id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <div id="additional-fields">
                            <div class="row">
                                <!-- Date Input -->
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" id="date"
                                        value="{{ date('Y-m-d') }}" placeholder="Date" required>
                                    @error('date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="team_member_name" class="form-label">
                                        Team Member Name
                                    </label>
                                    <input type="hidden" name="name_id" class="form-control" id="name_id"
                                        value="{{ $name_id }}" required>
                                    <input type="text" name="team_member_name" class="form-control"
                                        id="team_member_name" readonly value="{{ $name }}">

                                    @error('team_member_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">
                                        Amount
                                    </label>
                                    <input type="text" name="amount" class="form-control" id="amount" required>
                                    @error('amount')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="project_name" class="form-label">
                                        Project Name
                                    </label>
                                    <select name="project_name" id="project_name" class="form-select">
                                        <option value="">Select Projects</option>
                                        @foreach ($employee as $item)
                                            <option value="{{ $item->project_name }}">{{ $item->project_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('project_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="remarkModal" tabindex="-1" aria-labelledby="remarkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="card-title" id="remarkModalLabel">Remarks for {{ $name }}</h5>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ asset('/admin/employeeimprest_remark') }}"
                        id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                        @csrf
                        <div class="col-md-12">
                            <label for="remark">Remarks</label>
                            <input type="hidden" name="id" value="">
                            <textarea name="acc_remark" rows="3" id="remark" class="form-control"></textarea>
                        </div>
                        <div class="col-md-12">
                            <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        $(document).ready(function() {
            $(document).on('show.bs.modal', '#remarkModal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('eiid');
                $(this).find('.modal-body input[name="id"]').val(id);
            });
        });


        function tallyApproved(id, button) {
            var currentStatus = button.innerText.trim().toLowerCase();
            var newStatus = (currentStatus === '✅') ? '0' : '1';

            // Disable the button to prevent multiple clicks
            button.disabled = true;

            $.ajax({
                url: '{{ url('admin/tally_status') }}', // Use 'url' helper for cleaner path
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    tallystatus: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert('Status updated successfully!');
                        button.innerText = (newStatus === '1') ? '✅' : 'Entered in Tally';
                    } else {
                        alert('Failed to update status.');
                        button.disabled = false; // Re-enable the button if the update fails
                    }
                },
                error: function() {
                    alert('An error occurred while updating the status.');
                    button.disabled = false; // Re-enable the button on error
                }
            });
        }

        function proofApproved(id, button) {
            var currentStatus = button.innerText.trim().toLowerCase();
            var newStatus = (currentStatus === '✅') ? '0' : '1';

            // Disable the button to prevent multiple clicks
            button.disabled = true;

            $.ajax({
                url: '{{ url('admin/proof_status') }}', // Use 'url' helper for cleaner path
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    proofstatus: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert('Status updated successfully!');
                        button.innerText = (newStatus === '1') ? '✅' : 'Proofs filled';
                    } else {
                        alert('Failed to update status.');
                        button.disabled = false; // Re-enable the button if the update fails
                    }
                },
                error: function() {
                    alert('An error occurred while updating the status.');
                    button.disabled = false; // Re-enable the button on error
                }
            });
        }

        function markApproved(id, button) {
            var currentStatus = button.innerText.trim().toLowerCase();
            var newStatus = (currentStatus === '✅') ? '0' : '1';

            // Disable the button to prevent multiple clicks
            button.disabled = true;

            $.ajax({
                url: '{{ url('admin/employee_status') }}', // Use 'url' helper for cleaner path
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    buttonstatus: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        alert('Status updated successfully!');
                        button.innerText = (newStatus === '1') ? '✅' : 'pending';
                    } else {
                        alert('Failed to update status.');
                        button.disabled = false; // Re-enable the button if the update fails
                    }
                },
                error: function() {
                    alert('An error occurred while updating the status.');
                    button.disabled = false; // Re-enable the button on error
                }
            });
        }

        function update(employeeId) {
            document.getElementById('employee_id').value = employeeId;
            fetch(`/admin/get_proof/${employeeId}`)
                .then(response => response.json())
                .then(data => {
                    let proofsContainer = document.getElementById('existing_proofs');
                    proofsContainer.innerHTML = ''; // Clear previous content

                    if (data.proofs && data.proofs.length > 0) {
                        data.proofs.forEach(proof => {
                            let proofElement = document.createElement('div');
                            proofElement.classList.add('existing-proof');
                            proofElement.innerHTML =
                                `<a href="https://tms.volksenergie.in/uploads/employeeimprest/${proof}" target="_blank">${proof}</a>`;
                            proofsContainer.appendChild(proofElement);
                        });
                    } else {
                        proofsContainer.innerHTML = '<p>No existing proofs found.</p>';
                    }
                });
        }
    </script>
@endpush
