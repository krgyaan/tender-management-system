@extends('layouts.app')
@section('page-title', 'Finance Dashboard')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('finance_add') }}" class="btn btn-primary btn-sm">Add Financial Document</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Document Name</th>
                                        <th>Document Type</th>
                                        <th>Financial Type</th>
                                        <th>File</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($financedata as $key => $financedata)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $financedata->document_name }}</td>
                                            <td>{{ $financedata->documenttype->document_type }}</td>
                                            <td>{{ $financedata->financialyear->financial_year }}</td>
                                            <td>
                                                @if ($financedata->image)
                                                    @php
                                                        $files =
                                                            json_decode($financedata->image) ??
                                                            explode(',', $financedata->image);
                                                    @endphp
                                                    @foreach ($files as $index => $file)
                                                        <a href="{{ asset('upload/finance/' . $file) }}" target="_blank">
                                                            <i class="fa fa-file-text fs-5" aria-hidden="true"></i>
                                                        </a><br>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ asset('admin/finance_edit/' . Crypt::encrypt($financedata->id)) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/finance_delete/' . Crypt::encrypt($financedata->id)) }}"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </a>
                                                <a href="" data-bs-toggle="modal" data-bs-target="#uploadPQ"
                                                    class="btn btn-warning btn-sm"
                                                    onClick="update('{{ $financedata->id }}');">
                                                    <i class="fa fa-file" aria-hidden="true"></i>
                                                </a>
                                                <a href="" data-bs-toggle="modal" data-bs-target="#finance_email"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                                </a>
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

    <!-- Modal for Upload Financial Document -->
    <div class="modal fade" id="uploadPQ" tabindex="-1" aria-labelledby="uploadPQLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPQLabel">Upload Financial Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadPQForm" action="{{ asset('admin/image_uplode') }}" method="POST"
                        enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <input type="hidden" name="ac_id" id="ac_id">
                        <div class="mb-3">
                            <label for="upload_po" class="form-label">Select Images</label>
                            <input type="file" class="form-control" name="image[]" id="upload_po" multiple required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Finance Email -->
    <div class="modal fade" id="finance_email" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ asset('admin/email') }}" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="mb-12">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" name="to_email" required>
                        </div>
                        <div class="mb-12">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="mb-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="message" id="" cols="80" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function update(id) {
            $('#ac_id').val(id)
        }
    </script>

@endsection
