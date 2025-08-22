@extends('layouts.app')
@section('page-title', 'Battery Price Sheet')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="row">
                    <div class="col-md-1">
                        <a href="{{ route('batterypriceadd') }}" class="btn btn-primary">Add</a>
                    </div>
                    <div class="col-md-2 ms-2 text-start">
                        <a href="{{ route('batterypriceview') }}" class="btn btn-primary">View</a>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="table-responsive">

                            <div class="table-responsive">
                                <table id="basic21" class="table table-striped dataTable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr.no.</th>
                                            <th>Item Name</th>
                                            <th>Installation</th>
                                            <th>Accessories</th>
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
        </div>
    </section>

    @push('scripts')
        <script>
            // destroy datatable
            if ($.fn.DataTable.isDataTable("table.table")) {
                $("table.table").DataTable().clear().destroy();
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
                        url: "{{ asset('admin/houseajexbatteryprice') }}",
                    },
                    columns: [{
                            data: "id",
                            title: "Sr. No."
                        },
                        {
                            data: "item_name",
                            title: "Item Name"
                        },
                        {
                            data: "installation",
                            title: "Installation "
                        },
                        {
                            data: "accessories",
                            title: "Accessories "
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
