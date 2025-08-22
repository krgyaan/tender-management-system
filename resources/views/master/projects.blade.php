@extends('layouts.app')
@section('page-title', 'Add Projects')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProjectModal"
                        id="addProject">
                        Add New Project
                    </button>
                    <a href="{{ route('download-projects') }}" class="btn btn-success btn-sm">
                        Download Excel
                    </a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <div class="bd-example">
                            <nav>
                                <div class="nav nav-tabs mb-3 justify-content-center" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">Team AC</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Team DC</button>
                                    <button class="nav-link" id="nav-ho-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-ho" type="button" role="tab"
                                        aria-controls="nav-ho" aria-selected="false">Team HO</button>
                                    <button class="nav-link" id="nav-bd-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-bd" type="button" role="tab"
                                        aria-controls="nav-bd" aria-selected="false">Team BD</button>
                                </div>
                            </nav>
                            <div class="tab-content" id="nav-tabContent">
                                <div class="tab-pane fade show active" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Project Name</th>
                                                    <th>Project Code</th>
                                                    <th>Location</th>
                                                    <th>PO No.</th>
                                                    <th>PO Date</th>
                                                    <th>PO Uploaded</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($acProjects as $key => $project)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $project->project_name }}</td>
                                                        <td>{{ $project->project_code }}</td>
                                                        <td>{{ $project->location->address }}</td>
                                                        <td>{{ $project->po_no }}</td>
                                                        <td>
                                                            <span class="d-none">{{ $project->po_date }}</span>
                                                            {{ \Carbon\Carbon::parse($project->po_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($project->po_upload)
                                                                <a href="{{ asset('uploads/projects/' . $project->po_upload) }}"
                                                                    target="_blank" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" data-id="{{ $project->id }}"
                                                                class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                                data-bs-target="#editProjectModal">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                <a href="{{ route('projects.destroy', $project->id) }}"
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) document.getElementById('deleteForm{{ $project->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('projects.destroy', $project->id) }}"
                                                                    method="POST" id="deleteForm{{ $project->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $project->id }}">
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Project Name</th>
                                                    <th>Project Code</th>
                                                    <th>Location</th>
                                                    <th>PO No.</th>
                                                    <th>PO Date</th>
                                                    <th>PO Uploaded</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dcProjects as $key => $project)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $project->project_name }}</td>
                                                        <td>{{ $project->project_code }}</td>
                                                        <td>{{ $project->location->address }}</td>
                                                        <td>{{ $project->po_no }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($project->po_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($project->po_upload)
                                                                <a href="{{ asset('uploads/projects/' . $project->po_upload) }}"
                                                                    target="_blank" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" data-id="{{ $project->id }}"
                                                                class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                                data-bs-target="#editProjectModal">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                <a href="{{ route('projects.destroy', $project->id) }}"
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) document.getElementById('deleteForm{{ $project->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('projects.destroy', $project->id) }}"
                                                                    method="POST" id="deleteForm{{ $project->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $project->id }}">
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-ho" role="tabpanel"
                                    aria-labelledby="nav-ho-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Project Name</th>
                                                    <th>Project Code</th>
                                                    <th>Location</th>
                                                    <th>PO No.</th>
                                                    <th>PO Date</th>
                                                    <th>PO Uploaded</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($hoProjects as $key => $project)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $project->project_name }}</td>
                                                        <td>{{ $project->project_code }}</td>
                                                        <td>{{ $project->location->address }}</td>
                                                        <td>{{ $project->po_no }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($project->po_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($project->po_upload)
                                                                <a href="{{ asset('uploads/projects/' . $project->po_upload) }}"
                                                                    target="_blank" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" data-id="{{ $project->id }}"
                                                                class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                                data-bs-target="#editProjectModal">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                <a href="{{ route('projects.destroy', $project->id) }}"
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) document.getElementById('deleteForm{{ $project->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('projects.destroy', $project->id) }}"
                                                                    method="POST" id="deleteForm{{ $project->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $project->id }}">
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="nav-bd" role="tabpanel"
                                    aria-labelledby="nav-bd-tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>S.No.</th>
                                                    <th>Project Name</th>
                                                    <th>Project Code</th>
                                                    <th>Location</th>
                                                    <th>PO No.</th>
                                                    <th>PO Date</th>
                                                    <th>PO Uploaded</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bdProjects as $key => $project)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ $project->project_name }}</td>
                                                        <td>{{ $project->project_code }}</td>
                                                        <td>{{ $project->location->address }}</td>
                                                        <td>{{ $project->po_no }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($project->po_date)->format('d-m-Y') }}
                                                        </td>
                                                        <td>
                                                            @if ($project->po_upload)
                                                                <a href="{{ asset('uploads/projects/' . $project->po_upload) }}"
                                                                    target="_blank" class="btn btn-info btn-xs">
                                                                    <i class="fa fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button type="button" data-id="{{ $project->id }}"
                                                                class="btn btn-warning btn-xs" data-bs-toggle="modal"
                                                                data-bs-target="#editProjectModal">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                                                <a href="{{ route('projects.destroy', $project->id) }}"
                                                                    onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this project?')) document.getElementById('deleteForm{{ $project->id }}').submit();"
                                                                    class="btn btn-danger btn-xs">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                                <form
                                                                    action="{{ route('projects.destroy', $project->id) }}"
                                                                    method="POST" id="deleteForm{{ $project->id }}"
                                                                    style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $project->id }}">
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
    </section>
    <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add Project</h5>
                    <button type="button" class="btn btn-outline-danger btn-xs" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data" class="row">
                        @csrf
                        <div class="form-group col-md-6 mb-2">
                            <label for="team_name">Team Name</label>
                            <select class="form-control" id="team_name" name="team_name" required>
                                <option value="">Select Team Name</option>
                                <option value="AC">AC</option>
                                <option value="DC">DC</option>
                                <option value="HO">HO</option>
                                <option value="BD">BD</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="organisation">Organisation</label>
                            <select class="form-control" id="organisation" name="organisation" required>
                                <option value="">Select Organisation</option>
                                @foreach ($organisations->sortBy('name') as $organisation)
                                    <option value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="item">Item</label>
                            <select class="form-control" id="item" name="item" required>
                                <option value="">Select Item</option>
                                @foreach ($items->sortBy('name') as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="location">Location</label>
                            <select class="form-control" id="location" name="location" required>
                                <option value="">Select Location</option>
                                @foreach ($locations->sortByDesc('acronym') as $location)
                                    <option data-code="{{ $location->acronym }}" data-name="{{ $location->address }}"
                                        value="{{ $location->id }}">
                                        {{ $location->acronym }} - {{ $location->address }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="po_no">PO No.</label>
                            <input type="text" class="form-control" id="po_no" name="po_no" required>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="po_date">PO Date</label>
                            <input type="date" class="form-control" id="po_date" name="po_date" required>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="po_upload">PO Upload</label>
                            <input type="file" class="form-control" id="po_upload" name="po_upload"
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="project_code">Project Code</label>
                            <input type="text" class="form-control" id="project_code" name="project_code" readonly>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="project_name">Project Name</label>
                            <input type="text" class="form-control" id="project_name" name="project_name" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" data-bs-backdrop="static"
        data-bs-keyboard="false" aria-labelledby="editProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProjectModalLabel">Edit Project</h5>
                    <button type="button" class="btn btn-outline-danger btn-xs" data-bs-dismiss="modal" aria-label="Close">
                        <span class="fs-4" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" enctype="multipart/form-data" class="row"
                        id="editProjectForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_project_id" name="project_id">
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_team_name">Team Name</label>
                            <select class="form-control" id="edit_team_name" name="team_name" required>
                                <option value="">Select Team Name</option>
                                <option value="AC">AC</option>
                                <option value="DC">DC</option>
                                <option value="HO">HO</option>
                                <option value="BD">BD</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_organisation">Organisation</label>
                            <select class="form-control" id="edit_organisation" name="organisation" required>
                                <option value="">Select Organisation</option>
                                @foreach ($organisations as $organisation)
                                    <option value="{{ $organisation->id }}">{{ $organisation->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_item">Item</label>
                            <select class="form-control" id="edit_item" name="item" required>
                                <option value="">Select Item</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_location">Location</label>
                            <select class="form-control" id="edit_location" name="location" required>
                                <option value="">Select Location</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" data-code="{{ $location->acronym }}"
                                        data-name="{{ $location->address }}">
                                        {{ $location->acronym }} - {{ $location->address }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_po_no">PO No.</label>
                            <input type="text" class="form-control" id="edit_po_no" name="po_no" required>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_po_date">PO Date</label>
                            <input type="date" class="form-control" id="edit_po_date" name="po_date" required>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_po_upload">PO Upload</label>
                            <input type="file" class="form-control" id="edit_po_upload" name="po_upload">
                            <a href="" class="text-primary pt-2" id="edit_po_upload_link"
                                target="_blank">View</a>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_project_code">Project Code</label>
                            <input type="text" class="form-control" id="edit_project_code" name="project_code"
                                readonly>
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <label for="edit_project_name">Project Name</label>
                            <input type="text" class="form-control" id="edit_project_name" name="project_name"
                                readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#editProjectModal').on('show.bs.modal', function(event) {
                let button = $(event.relatedTarget);
                let projectId = button.data('id');
                let projects = @json($projects);

                // Find the project by ID
                let project = projects.find(ele => ele.id === projectId);
                console.log(project);

                if (project) {
                    // Set form values
                    $('#editProjectForm').attr('action', `projects/${project.id}`);

                    $('#edit_project_id').val(project.id);
                    $('#edit_team_name').val(project.team_name);
                    $('#edit_organisation').val(project.organisation_id);
                    $('#edit_item').val(project.item_id);
                    $('#edit_location').val(project.location_id);
                    $('#edit_po_no').val(project.po_no);
                    $('#edit_project_code').val(project.project_code);
                    $('#edit_project_name').val(project.project_name);
                    let poDate = new Date(project.po_date);
                    $('#edit_po_date').val(
                        `${poDate.getFullYear()}-${('0' + (poDate.getMonth() + 1)).slice(-2)}-${('0' + poDate.getDate()).slice(-2)}`
                    );

                    // Set PO upload link
                    let poUploadLink = `/uploads/projects/${project.po_upload}`;
                    $('#edit_po_upload_link').attr('href', poUploadLink);
                }
            });

            // Add project name update functionality to edit form
            function updateEditProjectName() {
                const teamName = $('#edit_team_name').val();
                const poDate = new Date($('#edit_po_date').val());
                const year = getFinancialYear(poDate);
                const organisationName = $('#edit_organisation option:selected').text();
                const itemName = $('#edit_item option:selected').text();
                const locationCode = $('#edit_location option:selected').data('code');
                const locationName = $('#edit_location option:selected').data('name');
                const poNo = $('#edit_po_no').val().slice(-4);

                $('#edit_project_code').val(
                    `${teamName}/${year}/${organisationName}/${itemName}/${locationCode}/${poNo}`
                );

                $('#edit_project_name').val(
                    `${organisationName} ${itemName} ${locationName}`
                );
            }

            // Add event listeners to edit form fields
            $('#edit_team_name').on('change', updateEditProjectName);
            $('#edit_po_date').on('change', updateEditProjectName);
            $('#edit_organisation').on('change', updateEditProjectName);
            $('#edit_item').on('change', updateEditProjectName);
            $('#edit_location').on('change', updateEditProjectName);
            $('#edit_po_no').on('input', updateEditProjectName);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const projectCodeInput = document.getElementById('project_code');
            const projectNameInput = document.getElementById('project_name');
            const teamNameSelect = document.getElementById('team_name');
            const poDateInput = document.getElementById('po_date');
            const organisationSelect = document.getElementById('organisation');
            const itemSelect = document.getElementById('item');
            const locationSelect = document.getElementById('location');
            const poNoInput = document.getElementById('po_no');

            function updateProjectName() {
                const teamName = teamNameSelect.value;
                const poDate = new Date(poDateInput.value);
                const year = getFinancialYear(poDate);
                const organisationName = organisationSelect.options[organisationSelect.selectedIndex].text;
                const itemName = itemSelect.options[itemSelect.selectedIndex].text;
                const locationCode = $('#location option:selected').data('code');
                const locationName = $('#location option:selected').data('name');
                const poNo = poNoInput.value.slice(-4);

                projectCodeInput.value =
                    `${teamName}/${year}/${organisationName}/${itemName}/${locationCode}/${poNo}`;

                projectNameInput.value =
                    `${organisationName} ${itemName} ${locationName}`;
            }

            teamNameSelect.addEventListener('change', updateProjectName);
            poDateInput.addEventListener('change', updateProjectName);
            organisationSelect.addEventListener('change', updateProjectName);
            itemSelect.addEventListener('change', updateProjectName);
            locationSelect.addEventListener('change', updateProjectName);
            poNoInput.addEventListener('input', updateProjectName);
        });

        // Function to get Financial Year last two digits and current year last two digits by date.
        // Financial year =  1 April - 31 March

        function getFinancialYear(date) {
            let year = date.getFullYear();
            let y = year.toString().slice(-2);
            let month = date.getMonth() + 1;
            if (month >= 4) {
                f = year.toString().slice(-2);
            } else {
                f = (year - 1).toString().slice(-2);
            }

            return f + '' + (parseInt(f) + 1).toString().padStart(2, '0');
        }
    </script>
@endpush
