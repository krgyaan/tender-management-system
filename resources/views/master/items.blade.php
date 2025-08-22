@extends('layouts.app')
@section('page-title', 'Items')
@section('content')
    <div class="row">
        <div class="col-md-12 m-auto">
            <div class="d-flex justify-content-between align-items-center">
                <button class="btn btn-primary" id="addItemBtn">Create New Item</button>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('partials.messages')
                    <div class="bd-example">
                        <nav>
                            <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                <button class="nav-link {{ Auth::user()->team == 'AC' ? 'active' : '' }}" id="nav-home-tab"
                                    data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab"
                                    aria-controls="nav-home" aria-selected="true">Team AC Items</button>
                                <button class="nav-link {{ Auth::user()->team == 'DC' ? 'active' : '' }}"
                                    id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button"
                                    role="tab" aria-controls="nav-profile" aria-selected="false">Team DC Items</button>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade {{ Auth::user()->team == 'AC' ? 'show active' : '' }}" id="nav-home"
                                role="tabpanel" aria-labelledby="nav-home-tab">
                                <div class="table-responsive">
                                    <table class="table" id="items table">
                                        <thead class="">
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Name</th>
                                                <th>Heading</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($acItems as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->heading }}</td>
                                                    <td>
                                                        <span
                                                            class="text-{{ $item->status == '1' ? 'success' : 'danger' }}">
                                                            {{ $item->status == '1' ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-xs editItemBtn"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            data-team="{{ $item->team }}"
                                                            data-heading="{{ $item->heading }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('items.delete', $item->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @if (Auth::user()->designation == 'CEO' && $item->status != '1')
                                                            <form action="{{ route('items.approve', $item->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs">
                                                                    <i class="fa fa-check"></i> Approve
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade {{ Auth::user()->team == 'DC' ? 'show active' : '' }}"
                                id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                <div class="table-responsive">
                                    <table class="table" id="items table">
                                        <thead class="">
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Name</th>
                                                <th>Heading</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dcItems as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->name }}</td>
                                                    <td>{{ $item->heading }}</td>
                                                    <td>
                                                        <span
                                                            class="text-{{ $item->status == '1' ? 'success' : 'danger' }}">
                                                            {{ $item->status == '1' ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-xs editItemBtn"
                                                            data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                            data-team="{{ $item->team }}"
                                                            data-heading="{{ $item->heading }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('items.delete', $item->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger btn-xs">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @if (Auth::user()->designation == 'CEO' && $item->status != '1')
                                                            <form action="{{ route('items.approve', $item->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-xs">
                                                                    <i class="fa fa-check"></i> Approve
                                                                </button>
                                                            </form>
                                                        @endif
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
            </div>
        </div>
    </div>

    <!-- Add/Edit Status Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header m-0 py-0 border-0">
                    <h5 class="modal-title" id="itemModalLabel"></h5>
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span class="text-white fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="itemForm" method="POST"
                    onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <span id="update-method"></span>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="team">Team</label>
                            <select class="form-control" id="team" name="team" required>
                                <option value="" selected disabled>Select Team</option>
                                <option value="DC">DC</option>
                                <option value="AC">AC</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="heading">Heading</label>
                            <div class="input-group">
                                <select class="form-control" id="heading" name="heading" required>
                                    <option value="" selected disabled>Select Heading</option>
                                </select>
                                <div class="input-group-append">
                                    <a class="btn btn-outline-info" href="{{ route('items.add-heading') }}"
                                        id="addHeadingBtn">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="item_name">Item Name</label>
                            <input type="text" class="form-control" id="item_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" id="saveItemBtn">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function changeHeading(team) {
            console.log('Fetching headings for team:', team);
            $.ajax({
                url: "{{ route('items.get-headings') }}",
                type: 'GET',
                data: { team: team },
                success: function(headings) {
                    $('#heading').empty().append('<option value="" selected disabled>Select Heading</option>');
                    headings.forEach(function(heading) {
                        $('#heading').append($('<option>', {
                            value: heading,
                            text: heading
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching headings:', error);
                    alert('Error loading headings. Please try again.');
                }
            });
        }

        $(document).ready(function() {
            $('#addItemBtn').click(function() {
                $('#itemModalLabel').text('Add Item');
                $('#itemForm').attr('action', '{{ route('items.store') }}');
                $('#itemForm').trigger('reset');
                $('#itemModal').modal('show');
            });

            $('#team').on('change', function() {
                console.log('Team changed:', this.value);
                changeHeading(this.value);
            });

            $('.editItemBtn').click(function() {
                var itemId = $(this).data('id');
                var itemName = $(this).data('name');
                var team = $(this).data('team');
                var heading = $(this).data('heading');
                
                $('#itemModalLabel').text('Edit Item');
                $('#itemForm').attr('action', '/admin/items/' + itemId);
                $('#update-method').html('<input type="hidden" name="_method" value="PUT">');
                $('#item_name').val(itemName);
                $('#team option[value="' + team + '"]').prop('selected', true);
                
                changeHeading(team);
                setTimeout(function() {
                    $('#heading option[value="' + heading + '"]').prop('selected', true);
                }, 500);
                
                $('#itemModal').modal('show');
            });
        });
    </script>
@endpush
