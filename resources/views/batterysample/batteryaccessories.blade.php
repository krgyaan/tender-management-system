@extends('layouts.app')
@section('page-title', 'Battery Accessories')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header px-2 py-2 text-capitalize ">
                        <h6 class="mb-0">Battery Accessories Add</h6>
                    </div>
                    <div class="card-body p-4">
                        <form class="row g-3 needs-validation" id="formatDistrict-update" method="POST"
                            action="{{ asset('admin/batteryaccessoriesadd') }}" novalidate>
                            @csrf
                            <div class="col-md-12">
                                <label for="Vehicletitle" class="form-label">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="hidden" name="id" class="form-control" id="id">
                                <input type="text" name="title" class="form-control" id="title" required>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3">
                                    <button type="submit" name="submit" value="submit"
                                        class="btn btn-primary submitbtn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header  text-capitalize">
                        <h6 class="mb-0">Battery Accessories View</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic21" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr.no.</th>
                                        <th>Title</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            if ($.fn.DataTable.isDataTable("table.table")) {
                $("table.table").DataTable().clear().destroy();
            }

            function categoryupdate(id, title) {
                var url = "{{ asset('admin/batteryaccessoriesupdate/') }}";
                $('#formatDistrict-update').attr('action', url);

                $('#id').val(id);
                $('#title').val(title);
            }

            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                });

                $("#basic21").DataTable({
                    processing: true,
                    serverSide: true,
                    serverMethod: "POST",
                    ajax: {
                        url: "{{ asset('admin/houseajexbatteryaccessoriestion') }}",
                    },
                    columns: [{
                            data: "id",
                            title: "Sr. No."
                        },
                        {
                            data: "title",
                            title: "Title"
                        },

                        {
                            data: "action",
                            title: "Action"
                        }
                    ],
                });
            });
        </script>
    @endpush

@endsection
