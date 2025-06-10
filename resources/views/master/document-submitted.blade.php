@extends('layouts.app')

@section('page-title', 'Submitted Documents For Physical Docs')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary btn-xs" id="addOrgBtn">Add New</button>
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
                                @foreach ($docs as $doc)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $doc->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editOrgBtn"
                                                data-id="{{ $doc->id }}" data-name="{{ $doc->name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $doc->id }}').submit();"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('submitteddocs.destroy', $doc->id) }}" method="POST"
                                                id="deleteForm{{ $doc->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $doc->id }}">
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

    <!-- Add/Edit Document Modal -->
    <div class="modal fade" id="docModal" tabindex="-1" role="dialog" aria-labelledby="docModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="docModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="docForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" id="id" name="id">
                            <label for="name">Document Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveDocBtn">Save Document</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open the modal for adding a new Document
            $('#addOrgBtn').click(function() {
                $('#docModalLabel').text('Add Document');
                $('#docForm').attr('action', '{{ route('submitteddocs.store') }}');
                $('#docForm').trigger('reset');
                $('#docModal').modal('show');
            });

            // Open the modal for editing an existing Document
            $('.editOrgBtn').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                $('#docModalLabel').text('Edit Document');
                $('#docForm').attr('action', '/admin/submitteddocs/' + id);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#name').val(name);
                $('#id').val(id);
                $('#docModal').modal('show');
            });
        });
    </script>
@endpush
