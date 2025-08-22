@extends('layouts.app')
@section('page-title', ' Document Tpye')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary">
                        Add Document Type
                    </a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Document Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documenttype as $key => $documenttypeData)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $documenttypeData->document_type }}</td>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#updatedocument"
                                                    onclick="dataupdate('{{ $documenttypeData->id }}','{{ $documenttypeData->document_type }}')"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/documenttype_del/' . Crypt::encrypt($documenttypeData->id)) }}"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
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

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Document Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/documenttype_add') }}" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Document Type <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="document_type" class="form-control" placeholder="" required>
                            @error('document_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updatedocument" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Document Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" method="post" action="{{ asset('admin/documenttype_edit') }}"
                        novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Document Type <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="document_type" class="form-control" id="document_type"
                                placeholder="" required>
                            <input type="hidden" name="id" class="form-control" id="id">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function dataupdate(id, document_type) {
            $('#id').val(id);
            $('#document_type').val(document_type);
        }
    </script>
@endsection
