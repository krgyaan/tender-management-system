@extends('layouts.app')

@section('page-title', 'Industries')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addIndustryBtn">Create New Industry</button>
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
                                @foreach ($industries as $industry)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $industry->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-xs editIndustryBtn"
                                                data-id="{{ $industry->id }}" data-name="{{ $industry->name }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $industry->id }}').submit();"
                                                class="btn btn-danger btn-xs">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('industries.destroy', $industry->id) }}" method="POST"
                                                id="deleteForm{{ $industry->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $industry->id }}">
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

    <!-- Add/Edit Industry Modal -->
    <div class="modal fade" id="industryModal" tabindex="-1" role="dialog" aria-labelledby="industryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="industryModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="industryForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="industry_name">Industry Name</label>
                            <input type="text" class="form-control" id="industry_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveIndustryBtn">Save Industry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Open the modal for adding a new Industry
            $('#addIndustryBtn').click(function() {
                $('#industryModalLabel').text('Add Industry');
                $('#industryForm').attr('action', '{{ route('industries.store') }}');
                $('#industryForm').trigger('reset');
                $('#industryModal').modal('show');
            });

            // Open the modal for editing an existing Industry
            $('.editIndustryBtn').click(function() {
                var industryId = $(this).data('id');
                var industryName = $(this).data('name');
                $('#industryModalLabel').text('Edit Industry');
                $('#industryForm').attr('action', '/admin/industries/' + industryId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#industry_name').val(industryName);
                $('#industryModal').modal('show');
            });
        });
    </script>
@endpush
