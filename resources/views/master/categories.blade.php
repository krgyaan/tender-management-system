@extends('layouts.app')
@section('page-title', ' Add Categories')
@section('content')

    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-primary">Create
                        New category</a>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table dataTable" id="allUsers">
                                <thead>
                                    <tr>
                                        <th>Sr.No.</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categorydata as $key => $categorydata)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $categorydata->category }}</td>
                                            <td>
                                                <a data-bs-toggle="modal" data-bs-target="#updatecategories"
                                                    onClick="acupdate('{{ $categorydata->category }}','{{ $categorydata->id }}');"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a onclick="return check_delete()"
                                                    href="{{ asset('admin/category_del/' . Crypt::encrypt($categorydata->id)) }}"
                                                    class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/categories_add') }}" novalidate>
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" placeholder="" required>
                            @error('category')
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

    <div class="modal fade" id="updatecategories" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Categories</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3 needs-validation" id="formatDistrict-update" method="post"
                        action="{{ asset('admin/category_edit') }}" novalidate>
                        @csrf
                        <div class="col-md-12">
                            <label for="Vehicletitle" class="form-label">Category <span class="text-danger">*</span></label>
                            <input type="text" name="category" class="form-control" id="categorydata" placeholder=""
                                required>
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
        function acupdate(category, id) {
            $('#id').val(id);
            $('#categorydata').val(category);
        }
    </script>
@endsection
