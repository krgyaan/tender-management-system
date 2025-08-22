@extends('layouts.app')

@section('page-title', 'Websites')

@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addWebBtn">Add New Website</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="websites table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($websites as $web)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $web->name }}</td>
                                        <td>{{ $web->url }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editWebBtn"
                                                data-id="{{ $web->id }}" data-name="{{ $web->name }}"
                                                data-url="{{ $web->url }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $web->id }}').submit();"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form action="{{ route('websites.destroy', $web->id) }}" method="POST"
                                                id="deleteForm{{ $web->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $web->id }}">
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

    <!-- Add/Edit Website Modal -->
    <div class="modal fade" id="webModal" tabindex="-1" role="dialog" aria-labelledby="webModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title text-white" id="webModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="webForm" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" id="webId" name="id">
                        <div class="form-group">
                            <label for="web_name">Name</label>
                            <input type="text" class="form-control" id="web_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="web_url">Url</label>
                            <input type="text" class="form-control" id="web_url" name="url">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveWebBtn">Save Website</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#addWebBtn').click(function() {
                $('#webModalLabel').text('Add Website');
                $('#webForm').attr('action', '{{ route('websites.store') }}');
                $('#webForm').trigger('reset');
                $('#webModal').modal('show');
            });

            $('.editWebBtn').click(function() {
                var webId = $(this).data('id');
                var webName = $(this).data('name');
                var webUrl = $(this).data('url');
                $('#webModalLabel').text('Edit Website');
                $('#webForm').attr('action', '/admin/websites/' + webId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#web_name').val(webName);
                $('#webId').val(webId);
                $('#web_url').val(webUrl);
                $('#webModal').modal('show');
            });
        });
    </script>
@endpush
