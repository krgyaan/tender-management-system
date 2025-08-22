@extends('layouts.app')
@section('page-title', 'Locations')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addLocBtn">Add New Followup Category</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="locations table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Categoriy</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fors as $for)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $for->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editLocBtn"
                                                data-id="{{ $for->id }}" data-name="{{ $for->name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $for->id }}').submit();"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('followup-categories-destroy', $for->id) }}" method="POST"
                                                id="deleteForm{{ $for->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $for->id }}">
                                            </form>
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

    <!-- Add/Edit Location Modal -->
    <div class="modal fade" id="locModal" tabindex="-1" role="dialog" aria-labelledby="locModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="locModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="locForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="hidden" class="form-control" id="id" name="id" required>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveWebBtn">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#addLocBtn').click(function() {
                $('#locModalLabel').text('Add Followup Category');
                $('#locForm').attr('action', '{{ route('followup-categories-add') }}');
                $('#locForm').trigger('reset');
                $('#locModal').modal('show');
            });

            $('.editLocBtn').click(function() {
                var locId = $(this).data('id');
                var name = $(this).data('name');
                $('#locModalLabel').text('Edit Followup Category');
                $('#locForm').attr('action', 'followup-categories/update');
                $('#name').val(name);
                $('#id').val(locId);
                $('#locModal').modal('show');
            });
        });
    </script>
@endpush
