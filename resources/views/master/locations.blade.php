@extends('layouts.app')
@section('page-title', 'Locations')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addLocBtn">Add New Location</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="table-responsive">
                        <table class="table" id="locations table">
                            <thead class="">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Address</th>
                                    <th>Acronym</th>
                                    <th>State</th>
                                    <th>Region</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($locations as $loc)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $loc->address }}</td>
                                        <td>{{ $loc->acronym }}</td>
                                        <td>{{ $loc->state }}</td>
                                        <td>{{ $loc->region }}</td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm editLocBtn"
                                                data-id="{{ $loc->id }}" data-address="{{ $loc->address }}" data-acronym="{{ $loc->acronym }}"
                                                data-state="{{ $loc->state }}" data-region="{{ $loc->region }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-{{ $loc->status ? 'success' : 'danger' }} btn-sm"
                                                data-toggle="button" aria-pressed="false" autocomplete="off"
                                                onclick="event.preventDefault(); document.getElementById('deleteForm{{ $loc->id }}').submit();">
                                                @if ($loc->status)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                            </button>
                                            <form action="{{ route('locations.destroy', $loc->id) }}" method="POST"
                                                id="deleteForm{{ $loc->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="id" value="{{ $loc->id }}">
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
                    <h5 class="modal-title" id="locModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="locForm" method="POST">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="loc_addr">Address</label>
                            <textarea class="form-control" id="loc_addr" name="address" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="loc_acronym">Acronym</label>
                            <input type="text" class="form-control" id="loc_acronym" name="acronym" required
                                onblur="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="form-group">
                            <label for="loc_state">State</label>
                            <select name="state" class="form-control" id="loc_state">
                                @foreach ($states as $id => $name)
                                    <option value="{{ $name }}" {{ old('state') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="loc_region">Region</label>
                            <select name="region" class="form-control" id="loc_region">
                                @foreach ($regions as $id => $name)
                                    <option value="{{ $name }}" {{ old('region') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveWebBtn">Save Location</button>
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
                $('#locModalLabel').text('Add Location');
                $('#locForm').attr('action', '{{ route('locations.store') }}');
                $('#locForm').trigger('reset');
                $('#locModal').modal('show');
            });

            $('.editLocBtn').click(function() {
                var locId = $(this).data('id');
                var address = $(this).data('address');
                var acronym = $(this).data('acronym');
                var state = $(this).data('state');
                var region = $(this).data('region');
                
                $('#locModalLabel').text('Edit Location');
                $('#locForm').attr('action', '/admin/locations/' + locId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#loc_addr').val(address);
                $('#loc_acronym').val(acronym);
                $('#loc_state').val(state);
                $('#loc_region').val(region);
                $('#locModal').modal('show');
            });
        });
    </script>
@endpush
