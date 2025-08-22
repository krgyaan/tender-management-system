@extends('layouts.app')
@section('page-title', 'All Employees Imprest')
@section('content')
    <section>
        <div class="row mb-2">
            <div class="col p-4 rounded shadow border">
                <h6>Amount Received</h6>
                <p>{{ format_inr($amountrecevied) }}</p>
            </div>
            <div class="col p-4 rounded shadow border">
                <h6>Amount Spent</h6>
                <p>{{ format_inr($employeeamount) }}</p>
            </div>
            <div class="col p-4 rounded shadow border">
                <h6>Amount Approved</h6>
                <p>{{ format_inr($amountapproved) }}</p>
            </div>
            <div class="col p-4 rounded shadow border">
                <h6>Amount Left</h6>
                <p>{{ format_inr($amountspent) }}</p>
            </div>
        </div>
        @include('partials.messages')
        <div class="row mt-4">
            <div class="col-md-12 pb-3">
                <form action="{{ route('dateFilter') }}" method="POST" class="row g-3" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <div class="col-md-4 form-group">
                        <input type="hidden" name="name_id" class="form-control" value="{{ Auth::user()->id }}">
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
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('employeeimprest_add') }}" class="btn btn-primary btn-sm">Add Imprest</a>
                    <a href="{{ route('payment-history', Auth::user()->id) }}" class="btn btn-secondary btn-sm">Payment History</a>
                    <a href="{{ route('imprest-voucher', Auth::user()->id) }}" class="btn btn-sm btn-outline-success">Imprest Voucher</a>
                    <form method="GET" action="{{ url('/download-employee-imprest') }}">
                        <input type="hidden" name="start_date" value="{{ $start_date ?? '' }}">
                        <input type="hidden" name="end_date" value="{{ $end_date ?? '' }}">
                        <button type="submit" class="btn btn-success btn-sm mt-3">Download Excel</button>
                    </form>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table " id="allUsers">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Name</th>
                                        <th>Party Name</th>
                                        <th>Project Name</th>
                                        <th>Amount</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Remarks</th>
                                        <th>Account Team <br> Remarks</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($employeeimprest)
                                        @foreach ($employeeimprest as $key => $employeeimprest)
                                            @if ($employeeimprest->name_id == Auth::user()->id || $employeeimprest->team_id == Auth::user()->id || in_array(Auth::user()->role, ['admin', 'account']))
                                                <tr>
                                                    <td>
                                                        {{ \Carbon\Carbon::parse($employeeimprest->created_at)->format('d-m-Y') }}
                                                    </td>
                                                    <td>{{ $employeeimprest->user->name }}</td>
                                                    <td>{{ $employeeimprest->party_name }}</td>
                                                    <td>{{ $employeeimprest->project_name }}</td>
                                                    <td>{{ format_inr($employeeimprest->amount) }}</td>
                                                    <td>
                                                        {{ $employeeimprest->category->category }}
                                                        @if($employeeimprest->category_id == 22)
                                                            <br>
                                                            {{ $employeeimprest->team->name }}
                                                        @else
                                                            {{ '' }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($employeeimprest->invoice_proof)
                                                            @php
                                                                $files = json_decode($employeeimprest->invoice_proof, true);
                                                            @endphp
    
                                                            @if (is_array($files))
                                                                @foreach ($files as $index => $file)
                                                                    @php
                                                                        $fileExtension = strtolower(
                                                                            pathinfo($file, PATHINFO_EXTENSION),
                                                                        ); // Get the file extension
                                                                    @endphp
    
                                                                    @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                        <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $file }}"
                                                                            data-fancybox="gallery"
                                                                            target="_blank">IMG-{{ $index + 1 }}</a><br>
                                                                    @elseif(in_array($fileExtension, ['pdf']))
                                                                        <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $file }}"
                                                                            data-fancybox="gallery"
                                                                            target="_blank">PDF-{{ $index + 1 }}
                                                                        </a><br>
                                                                    @else
                                                                    @endif
                                                                @endforeach
                                                            @else
                                                                @php
                                                                    $fileExtension = strtolower(
                                                                        pathinfo(
                                                                            $employeeimprest->invoice_proof,
                                                                            PATHINFO_EXTENSION,
                                                                        ),
                                                                    );
                                                                @endphp
    
                                                                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                                    <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeeimprest->invoice_proof }}"
                                                                        data-fancybox="gallery" target="_blank">IMG-1</a>
                                                                @elseif(in_array($fileExtension, ['pdf']))
                                                                    <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeeimprest->invoice_proof }}"
                                                                        data-fancybox="gallery" target="_blank">PDF-1</a>
                                                                @else
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ $employeeimprest->remark }}</td>
                                                    <td>
                                                        {{ $employeeimprest->acc_remark }}
                                                    </td>
                                                    <td>
                                                        @if($employeeimprest->buttonstatus == 1)
                                                            <b class="text-success">Approved</b><br>
                                                            @if ($employeeimprest->approved_date)
                                                                on {{ date('d M', strtotime($employeeimprest->approved_date)) }}
                                                            @endif
                                                        @else
                                                            <b class="text-warning">Pending</b><br>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ asset('admin/employeeimprest_edit/' . Crypt::encrypt($employeeimprest->id)) }}"
                                                            class="btn btn-info btn-xs d-none">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a onclick="confirm('Do You Want To Delete!')"
                                                            href="{{ asset('admin/employeeimprest_delete/' . Crypt::encrypt($employeeimprest->id)) }}"
                                                            class="btn btn-danger btn-xs d-none">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-primary btn-xs"
                                                            data-bs-toggle="modal"
                                                            onclick="update('{{ $employeeimprest->id }}')"
                                                            data-bs-target="#exampleModal">
                                                            Add Proof
                                                        </button>
                                                        @if (in_array(Auth::user()->role, ['admin,account']) || Auth::user()->id == $employeeimprest->name_id)
                                                            <form
                                                                action="{{ route('imprest.delete', $employeeimprest->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-danger"
                                                                    onclick="return confirm('Are you sure you want to delete this record?')">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
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
                        id="uploadInvoiceForm" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
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
@endsection

@push('scripts')
    <script>
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
