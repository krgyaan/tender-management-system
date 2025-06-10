@extends('layouts.app')

@section('page-title', 'Statuses')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addStatusBtn">Create New Status</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="statuses table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($statuses as $status)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $status->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editStatusBtn"
                                                data-id="{{ $status->id }}" data-name="{{ $status->name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $status->id }}').submit();"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('statuses.destroy', $status->id) }}" method="POST"
                                                id="deleteForm{{ $status->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $status->id }}">
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

    <!-- Add/Edit Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="statusModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="status_name">Status Name</label>
                            <input type="text" class="form-control" id="status_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveStatusBtn">Save Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open the modal for adding a new status
            $('#addStatusBtn').click(function() {
                $('#statusModalLabel').text('Add Status');
                $('#statusForm').attr('action', '{{ route('statuses.store') }}');
                $('#statusForm').trigger('reset');
                $('#statusModal').modal('show');
            });

            // Open the modal for editing an existing status
            $('.editStatusBtn').click(function() {
                var statusId = $(this).data('id');
                var statusName = $(this).data('name');
                $('#statusModalLabel').text('Edit Status');
                $('#statusForm').attr('action', '/admin/statuses/' + statusId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#status_name').val(statusName);
                $('#statusModal').modal('show');
            });
        });
    </script>
@endpush
