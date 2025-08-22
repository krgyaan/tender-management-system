@extends('layouts.app')
@section('page-title', 'Edit Employee Imprest')
@section('content')
    @php
        $projects = App\Models\Project::all();
    @endphp
    <section class="row">
        <div class="col-md-12 mx-auto">
            <div>
                <a class="btn btn-outline-danger btn-xs" href="{{ url('/admin/employeeimprest') }}">Go Back</a>
            </div>
            @include('partials.messages')
            <div class="card">
                <div class="card-body p-4">
                    <form method="post" action="{{ route('employeeimprest_update') }}" enctype="multipart/form-data"
                        class="row g-3 needs-validation" novalidate>
                        @csrf
                        <input type="hidden" value="{{ $imprest->id }}" name="id" class="form-control">
                        <div class="col-md-6">
                            <label for="" class="form-label">Name</label>
                            @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                <select name="name_id" class="form-control" required>
                                    <option value="">Select Option</option>
                                    @foreach ($user as $key => $userItem)
                                        <option {{ $imprest->name_id == $userItem->id ? 'selected' : '' }}
                                            value="{{ $userItem->id }}">{{ $userItem->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="name_id" class="form-control" value="{{ Auth::user()->id }}">
                                <input type="text" name="" readonly class="form-control"
                                    value="{{ Auth::user()->name }}">
                            @endif
                        </div>

                        <div class="col-md-6" id="someOptional1">
                            <label for="party_name" class="form-label">Party Name</label>
                            <input type="text" name="party_name" class="form-control" id="party_name"
                                value="{{ $imprest->party_name }}">
                        </div>
                        <div class="col-md-6" id="someOptional2">
                            <label for="project_name" class="form-label">Project Name</label>
                            <select name="project_name" class="form-control" id="project_name" required>
                                <option value="">Select Project</option>
                                @foreach ($projects as $pro)
                                    <option {{ $imprest->project_name == $pro->project_name ? 'selected' : '' }}
                                        value="{{ $pro->project_name }}">
                                        {{ $pro->project_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" name="amount" class="form-control" required
                                value="{{ $imprest->amount }}">
                        </div>

                        <div class="col-md-6">
                            <label for="Category" class="form-label">Category</label>
                            <select name="category" class="form-control" id="category" required>
                                <option value="">Select Option</option>
                                @foreach ($category as $key => $category)
                                    <option value="{{ $category->id }}"
                                        {{ $imprest->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6" id="name-container" style="display:none;">
                            <label for="name" class="form-label">Team Name</label>
                            <select name="team_id" id="bsValidation8" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($user as $key => $userItem)
                                    <option {{ Auth::user()->id == $userItem->id ? 'disabled' : '' }}
                                        {{ $imprest->team_id == $userItem->id ? 'selected' : '' }}
                                        value="{{ $userItem->id }}">{{ $userItem->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="invoice_proof" class="form-label">Invoice/Proof</label>
                            <input type="file" name="invoice_proof[]" id="proof" class="form-control" multiple>
                            <ul class="list-unstyled d-flex gap-2 flex-wrap">
                                @if ($imprest->invoice_proof)
                                    @foreach (json_decode($imprest->invoice_proof) as $key => $proof)
                                        <li class="border p-1">
                                            <a href="{{ asset("uploads/employeeimprest/$proof") }}">
                                                Proof-{{ $key + 1 }}
                                            </a>
                                            <a href="javascript:void(0);"
                                                class="ms-1 btn btn-danger btn-xs delete-proof-btn"
                                                data-id="{{ $imprest->id }}" data-proof="{{ $proof }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        <div class="col-md-12">
                            <label for="remark" class="form-label">Remarks</label>
                            <textarea name="remark" id="" cols="80" rows="4" class="form-control" required>{{ $imprest->remark }}</textarea>
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
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
                    $('#someOptional1 input, #someOptional2 input').removeAttr(
                        'required');
                }
            });
        });

        $(document).on('click', '.delete-proof-btn', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this proof?')) return;
            var btn = $(this);
            $.ajax({
                url: "{{ route('delete_proof') }}",
                type: "POST",
                data: {
                    id: btn.data('id'),
                    proof: btn.data('proof'),
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        btn.closest('li').remove();
                    } else {
                        alert(response.message || 'Failed to delete proof.');
                    }
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || 'Error deleting proof.');
                }
            });
        });
    </script>
@endpush
