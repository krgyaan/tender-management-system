@extends('layouts.app')
@section('page-title', 'All Employees Imprest')
@section('content')

    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-header px-4 py-3">
                            <h5 class="mb-0">Update Employee Imprest</h5>
                        </div>

                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('admin/employeeimprest_update') }}"
                                enctype="multipart/form-data" id="formatDistrict-update" class="row g-3 needs-validation"
                                novalidate>

                                @csrf
                                <input type="text" name="id" hidden value="{{ $employeeimprest_update->id }}"
                                    class="form-control" id="" placeholder="Party Name">


                                <div class="col-md-6">
                                    <label for="" class="form-label">Name<span class="text-danger">*</span></label>
                                    <select name="name_id" class="form-control" required>

                                        @foreach ($user as $key => $userItem)
                                            <option value="{{ $userItem->id }}"
                                                @if ($employeeimprest_update->name_id == $userItem->id) selected @endif>
                                                {{ $userItem->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-6">
                                    <label for="party_name" class="form-label">Party Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $employeeimprest_update->party_name }}"
                                        name="party_name" class="form-control" id="" placeholder="Party Name"
                                        required>

                                </div>
                                <div class="col-md-6">
                                    <label for="project_name" class="form-label">Project Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $employeeimprest_update->project_name }}"
                                        name="project_name" class="form-control" id="" placeholder="Project Name"
                                        required>

                                </div>

                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount<span
                                            class="text-danger">*</span></label>
                                    <input type="number" value="{{ $employeeimprest_update->amount }}" name="amount"
                                        class="form-control"placeholder=" Amount" required>

                                </div>

                                <div class="col-md-6">
                                    <label for="Category" class="form-label">Category<span
                                            class="text-danger">*</span></label>
                                    <select name="category" class="form-control" id="category" required>
                                        @foreach ($category as $key => $category)
                                            <option value="{{ $category->id }}"
                                                @if ($employeeimprest_update->category_id == $category->id) selected @endif>
                                                {{ $category->category }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="col-md-6" id="name-container"
                                    @if ($employeeimprest_update->category_id != '10') style="display:none;" @endif>
                                    <label for="name" class="form-label">Team Name<span
                                            class="text-danger">*</span></label>
                                    <select name="team_id" id="bsValidation8" class="form-control">
                                        @foreach ($user as $key => $userItem)
                                            <option value="{{ $userItem->id }}">{{ $userItem->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-6">
                                    <label for="invoice_proof" class="form-label">Invoice/Proof<span
                                            class="text-danger">*</span></label>

                                    <input type="file" name="invoice_proof[]" class="form-control" multiple>
                                    @if ($employeeimprest_update->invoice_proof)
                                        @php
                                            $files = json_decode($employeeimprest_update->invoice_proof, true);
                                        @endphp

                                        @if (is_array($files))
                                            @foreach ($files as $file)
                                                @php
                                                    $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                                @endphp

                                                @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <!-- Image for Fancybox -->
                                                    <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $file }}"
                                                        data-fancybox="gallery" data-caption="Invoice Image">
                                                        <img class="ms-4 mt-4" width="10%"
                                                            src="https://tms.volksenergie.in/uploads/employeeimprest/{{ $file }}"
                                                            alt="Document Image">
                                                    </a>
                                                @elseif($fileExtension == 'pdf')
                                                    <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $file }}"
                                                        target="_blank">View
                                                        PDF</a>
                                                @else
                                                @endif
                                            @endforeach
                                        @else
                                            @php
                                                $fileExtension = strtolower(
                                                    pathinfo(
                                                        $employeeimprest_update->invoice_proof,
                                                        PATHINFO_EXTENSION,
                                                    ),
                                                );
                                            @endphp

                                            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <a href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeeimprest_update->invoice_proof }}"
                                                    data-fancybox="gallery" data-caption="Invoice Image">
                                                    <img class="ms-4 mt-4" width="10%"
                                                        src="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeeimprest_update->invoice_proof }}"
                                                        alt="Document Image">
                                                </a>
                                            @elseif($fileExtension == 'pdf')
                                                <a class="ms-4 mt-5"
                                                    href="https://tms.volksenergie.in/uploads/employeeimprest/{{ $employeeimprest_update->invoice_proof }}"
                                                    target="_blank">View PDF</a>
                                            @else
                                            @endif
                                        @endif
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label for="remark" class="form-label">Remarks<span
                                            class="text-danger">*</span></label>
                                    <textarea name="remark" value="" id="" cols="80" rows="4" class="form-control">{{ $employeeimprest_update->remark }}</textarea>


                                </div>

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">

                                        <button type="submit" class="btn btn-primary px-4">update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>

@endsection
