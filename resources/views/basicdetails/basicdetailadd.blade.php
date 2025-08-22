@extends('layouts.app')
@section('page-title', 'Add Basic Details')
@section('content')
    <section>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-4">
                    <form method="post" action="{{ url('/admin/basicdetailaddpost') }}" enctype="multipart/form-data"
                        id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                        @csrf

                        @if(isset($basicDetail) || $basicDetail != null)
                            <input type="hidden" name="id" value="{{ $basicDetail?->id }}">
                        @endif
                        @if(isset($tender) && $tender)
                            <div class="col-md-4">
                                <label for="name_id" class="form-label">Tender Name</label>
                                <input type="hidden" name="tender_name_id" id="name_id" value="{{ $tender->id }}" required>
                                <input type="text" class="form-control" name="tender_name" id="name"
                                    value="{{ $tender->tender_name }}" required readonly>
                                @error('tender_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="col-md-4">
                                <label for="name_id" class="form-label">Tender Name</label>
                                <select class="select2 form-control" name="tender_name_id" id="name_id" required>
                                    <option value="">Select Tender</option>
                                    @if(isset($alltenders) && count($alltenders))
                                        @foreach($alltenders as $t)
                                            <option value="{{ $t->id }}" {{ old('tender_name_id', $basicDetail?->tender_name_id ?? '') == $t->id ? 'selected' : '' }}>
                                                {{ $t->tender_name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('tender_name_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="col-md-4">
                            <label for="number" class="form-label">WO Number</label>
                            <input type="text" class="form-control" name="number" id="number" required
                                value="{{ old('number', $basicDetail?->number ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="form-label">WO Date</label>
                            <input type="date" class="form-control" name="date" id="date" required
                                value="{{ old('date', $basicDetail?->date ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pre_gst" class="form-label">WO Value (Pre-GST)</label>
                            <input type="text" class="form-control" name="pre_gst" id="pre_gst" required
                                value="{{ old('pre_gst', $basicDetail?->par_gst ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="pre_amt" class="form-label">WO Value (GST Amt.)</label>
                            <input type="text" class="form-control" name="pre_amt" id="pre_amt" required
                                value="{{ old('pre_amt', $basicDetail?->par_amt ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="image" class="form-label">LOA/GEM PO/LOI/Draft WO</label>
                            <input type="file" class="form-control" name="image" id="image" {{ isset($basicDetail) ? '' : 'required' }}>
                            @if(isset($basicDetail) && $basicDetail?->image)
                                <div class="mt-2">
                                    <a href="{{ asset('upload/basicdetails/' . $basicDetail?->image) }}" target="_blank">View
                                        current file</a>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endpush
