@extends('layouts.app')
@section('page-title', 'Create GST 3B Form')
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

                        <form method="POST" action="{{ route('gst3b.store') }}" enctype="multipart/form-data">
                            @csrf


                            {{-- 2. Tally Data Link --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="tally_data_link" class="form-label">Tally Data Link </label>
                                    <input type="url"
                                        class="form-control @error('tally_data_link') is-invalid @enderror"
                                        id="tally_data_link" name="tally_data_link" value="{{ old('tally_data_link') }}"
                                        placeholder="https://drive.google.com/..." required>
                                    <small class="text-muted">Google Drive link to the Tally data</small>
                                    @error('tally_data_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 3. GST 2A File --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="gst_2a_file" class="form-label">GST 2A File </label>
                                    <input type="file" class="form-control @error('gst_2a_file') is-invalid @enderror"
                                        id="gst_2a_file" name="gst_2a_file" required>
                                    <small class="text-muted">Accepted formats: XLS, XLSX, PDF</small>
                                    @error('gst_2a_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 4. GST TDS File --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="gst_tds_file" class="form-label">GST TDS File </label>
                                    <input type="file" class="form-control @error('gst_tds_file') is-invalid @enderror"
                                        id="gst_tds_file" name="gst_tds_file" required>
                                    <small class="text-muted">Accepted formats: XLS, XLSX, PDF</small>
                                    @error('gst_tds_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 5. GST TDS Accepted --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input @error('gst_tds_accepted') is-invalid @enderror"
                                            type="checkbox" id="gst_tds_accepted" name="gst_tds_accepted" value="1"
                                            {{ old('gst_tds_accepted') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gst_tds_accepted">GST TDS Accepted</label>
                                        @error('gst_tds_accepted')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- 6. GST TDS Amount --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="gst_tds_amount" class="form-label">GST TDS Amount</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('gst_tds_amount') is-invalid @enderror"
                                        id="gst_tds_amount" name="gst_tds_amount" value="{{ old('gst_tds_amount') }}"
                                        placeholder="0.00">
                                    @error('gst_tds_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 7. GST Paid --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input @error('gst_paid') is-invalid @enderror"
                                            type="checkbox" id="gst_paid" name="gst_paid" value="1"
                                            {{ old('gst_paid') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gst_paid">GST Paid</label>
                                        @error('gst_paid')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- 8. Amount --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="amount" class="form-label">Amount </label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('amount') is-invalid @enderror" id="amount"
                                        name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary" type="submit">Submit</button>
                                <a href="{{ route('gst3b.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
