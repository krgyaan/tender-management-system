@extends('layouts.app')
@section('page-title', ' TQ Type')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary">
                        New TQ Type
                    </a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th></th>
                                        <th></th>
                                        <th>Name</th>
                                        <th></th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tq_type as $key => $categorydata)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ $categorydata->tq_type }}</td>
                                            <td></td>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#updatecategories"
                                                    onClick="acupdate('{{ $categorydata->tq_type }}','{{ $categorydata->id }}');"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/tq_type_delete/' . Crypt::encrypt($categorydata->id)) }}"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot></tfoot>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add TQ Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/tq_type_add') }}" novalidate>
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">TQ type <span class="text-danger">*</span></label>
                            <input type="text" name="tq_type" class="form-control" placeholder="TQ Type Name" required>
                            @error('tq_type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updatecategories" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update TQ Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/tq_type_update') }}" novalidate>
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">TQ type <span class="text-danger">*</span></label>
                            <input type="text" name="tq_type" class="form-control" id="categorydata" placeholder=""
                                required>

                            <input type="hidden" name="id" class="form-control" id="id">

                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function acupdate(tq_type, id) {
            $('#id').val(id);
            $('#categorydata').val(tq_type);
        }
    </script>
@endsection
