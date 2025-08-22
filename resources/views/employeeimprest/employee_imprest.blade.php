@extends('layouts.app')
@section('page-title', 'Add Employee Imprests')
@section('content')
    @php
        $projects = App\Models\Project::all();
    @endphp
    <div class="page-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12 mx-auto">
                    <div>
                        <a class="btn btn-outline-danger btn-xs" href="{{ url('/admin/employeeimprest') }}">Go Back</a>
                    </div>
                    @include('partials.messages')
                    <div class="card">
                        <div class="card-header px-4 py-3">
                            <h5 class="mb-0">Add Employee Imprest</h5>
                        </div>
                        <div class="card-body p-4">
                            <form method="post" action="{{ asset('employeeimprest_post') }}" enctype="multipart/form-data"
                                id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                                @csrf
                                <div class="col-md-4">
                                    <label for="" class="form-label">Name</label>
                                    @if (in_array(Auth::user()->role, ['admin']))
                                        <select name="name_id" class="form-control" required>
                                            <option value="">Select Option</option>
                                            @foreach ($user as $key => $userItem)
                                                <option value="{{ $userItem->id }}">{{ $userItem->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="name_id" class="form-control"
                                            value="{{ Auth::user()->id }}">
                                        <input type="text" name="" readonly class="form-control"
                                            value="{{ Auth::user()->name }}">
                                    @endif
                                    @error('name_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4" id="someOptional1">
                                    <label for="party_name" class="form-label">Party Name</label>
                                    <input type="text" name="party_name" class="form-control" id=""
                                        placeholder="Party Name">
                                    @error('party_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4" id="someOptional2">
                                    <label for="project_name" class="form-label">Project Name</label>
                                    <select name="project_name" class="form-control" id="project_name" required>
                                        <option value="">Select Project</option>
                                        @foreach ($projects as $pro)
                                            <option value="{{ $pro->project_name }}">{{ $pro->project_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('project_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" name="amount" class="form-control"placeholder=" Amount" required>
                                    @error('amount')
                                        <div class="text-danger">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="Category" class="form-label">Category</label>
                                    <select name="category" class="form-control" id="category" required>
                                        <option value="">Select Option</option>
                                        @foreach ($category as $key => $category)
                                            <option value="{{ $category->id }}">{{ $category->category }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4" id="name-container" style="display:none;">
                                    <label for="name" class="form-label">Team Name</label>
                                    <select name="team_id" id="bsValidation8" class="form-control">
                                        <option value="">Select Option</option>
                                        @foreach ($user as $key => $userItem)
                                            <option {{ Auth::user()->id == $userItem->id ? 'disabled' : '' }}
                                                value="{{ $userItem->id }}">{{ $userItem->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('team_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="invoice_proof" class="form-label">Invoice/Proof</label>
                                    <input type="file" name="invoice_proof[]" id="proof" class="form-control"
                                        multiple>
                                </div>

                                <div class="col-md-12">
                                    <label for="remark" class="form-label">Remarks</label>
                                    <textarea name="remark" id="" cols="80" rows="4" class="form-control" required></textarea>
                                    @error('remark')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#project_name').select2({
            placeholder: 'Select Project',
            allowClear: true,
            width: '100%',
            height: 38,
        });
        
        $('#proof').filepond({
            allowMultiple: true,
            storeAsFile: true,
            credits: false,
        });
        $('#category').change(function() {
            var id = $(this).val();
            console.log('Selected Value:', id);

            $('#name-container').hide();
            $('#someOptional1, #someOptional2').show();
            $('#someOptional1 input, #someOptional2 input').attr('required', 'required');

            if (id == '22') {
                $('#someOptional1, #someOptional2').hide();
                $('#name-container').show();
                $('#someOptional1 input, #someOptional2 select').removeAttr('required');
            }
        });
    });
</script>
@endpush
