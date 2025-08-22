@extends('layouts.app')
@section('page-title', 'Finance Dashboard')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('finance') }}" class="btn btn-outline-danger btn-sm">back</a>
                    </div>
                    <div class="card">
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('/admin/finance_update') }}" enctype="multipart/form-data"
                                id="formatDistrict-update" class="row g-3 needs-validation" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                @csrf
                                <input type="hidden" value="{{ $finance_edit->id }}" name="id" class="form-control"
                                    id="">
                                <div class="col-md-6">
                                    <label for="document_name" class="form-label">Document Name<span
                                            class="text-danger">*</span></label>
                                    <input type="text" value="{{ $finance_edit->document_name }}" name="document_name"
                                        class="form-control" id="" placeholder=" Document Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="document_type" class="form-label">Document Type<span
                                            class="text-danger">*</span></label>
                                    <select name="document_type" class="form-control" required>

                                        @foreach ($documenttype as $key => $documenttypeItem)
                                            <option value="{{ $documenttypeItem->id }}"
                                                @if ($finance_edit->id == $documenttypeItem->document_type) selected @endif>
                                                {{ $documenttypeItem->document_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="financial_year" class="form-label">Financial Year<span
                                            class="text-danger">*</span></label>
                                    <select name="financial_year" class="form-control" required>

                                        @foreach ($financialyear as $key => $financialyearItem)
                                            <option value="{{ $financialyearItem->id }}"
                                                @if ($finance_edit->id == $financialyearItem->financial_year) selected @endif>
                                                {{ $financialyearItem->financial_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="upload_file" class="form-label">Upload File<span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="image[]" class="form-control" multiple>
                                    @if ($finance_edit->image)
                                        @php
                                            $files = json_decode($finance_edit->image, true);
                                        @endphp
                                        @foreach ($files as $file)
                                            <img class="ms-4 mt-4" src="{{ asset('uploads/finance/' . $file) }}"
                                                alt="Image" width="50">
                                        @endforeach
                                    @endif
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

        </div>
    </div>
@endsection
