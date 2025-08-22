@extends('layouts.app')
@section('page-title', ' Financial Year')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary">
                        Add Financial Year
                    </a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Financail Year</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($financialyear as $key => $financialyearData)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $financialyearData->financial_year }}</td>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#updatefinancial"
                                                    onclick="dataupdate('{{ $financialyearData->id }}','{{ $financialyearData->financial_year }}')"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/financialyear_del/' . Crypt::encrypt($financialyearData->id)) }}"
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Financial Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/financialyear_add') }}" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Financial Year <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="financial_year" class="form-control" placeholder="" required>
                            @error('financial_year')
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
    <!-- Modal -->
    <div class="modal fade" id="updatefinancial" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Financial Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/financialyear_edit') }}" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Financial Year <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="financial_year" class="form-control" id="financial_year"
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
        function dataupdate(id, financial_year) {
            $('#id').val(id);
            $('#financial_year').val(financial_year);
        }
    </script>
@endsection
