@extends('layouts.app')
@section('page-title', 'Edit GST 3B Form')

@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">

                        {{-- Success Message --}}
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- Error Messages --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('gst3b.update', $gst3b->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="tally_data_link" class="form-label">Tally Data Link </label>
                                    <input type="url"
                                        class="form-control @error('tally_data_link') is-invalid @enderror"
                                        id="tally_data_link" name="tally_data_link"
                                        value="{{ old('tally_data_link', $gst3b->tally_data_link) }}" required>
                                    <small class="text-muted">Google Drive link to the Tally data</small>
                                    @error('tally_data_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="gst_2a_file" class="form-label">GST 2A File</label>
                                    @if ($gst3b->gst_2a_file_path)
                                        <div class="mb-2">
                                            <a href="{{ Storage::url($gst3b->gst_2a_file_path) }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                View Current File
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error('gst_2a_file') is-invalid @enderror"
                                        id="gst_2a_file" name="gst_2a_file">
                                    <small class="text-muted">Leave blank to keep existing file</small>
                                    @error('gst_2a_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gst_tds_accepted"
                                            name="gst_tds_accepted" value="1"
                                            {{ old('gst_tds_accepted', $gst3b->gst_tds_accepted) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gst_tds_accepted">
                                            GST TDS Accepted
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="gst_tds_amount" class="form-label">GST TDS Amount</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('gst_tds_amount') is-invalid @enderror"
                                        id="gst_tds_amount" name="gst_tds_amount"
                                        value="{{ old('gst_tds_amount', $gst3b->gst_tds_amount) }}" placeholder="0.00">
                                    @error('gst_tds_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gst_paid" name="gst_paid"
                                            value="1" {{ old('gst_paid', $gst3b->gst_paid) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gst_paid">
                                            GST Paid
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="amount" class="form-label">Amount </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('amount') is-invalid @enderror" id="amount"
                                        name="amount" value="{{ old('amount', $gst3b->amount) }}" placeholder="0.00"
                                        required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="status" class="form-label">Status </label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="pending"
                                            {{ old('status', $gst3b->status) == 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="approved"
                                            {{ old('status', $gst3b->status) == 'approved' ? 'selected' : '' }}>Approved
                                        </option>
                                        <option value="rejected"
                                            {{ old('status', $gst3b->status) == 'rejected' ? 'selected' : '' }}>Rejected
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('gst3b.show', $gst3b->id) }}"
                                    class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
