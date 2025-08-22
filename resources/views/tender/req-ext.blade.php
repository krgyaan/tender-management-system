@extends('layouts.app')
@section('page-title', 'Request Extension for Tender ' . $id->tender_no)
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('tender.index') }}" class="btn btn-danger btn-sm">Back</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <table class="table-bordered table-hover w-100 mb-4">
                            <tr>
                                <td><strong>Tender No:</strong></td>
                                <td>{{ $id->tender_no }}</td>
                                <td><strong>Tender Name:</strong></td>
                                <td>{{ $id->tender_name }}</td>
                            </tr>
                        </table>

                        <form action="{{ route('extension.store', ['id' => $id->id]) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="days" class="form-label">Days of Extension</label>
                                    <input type="number" class="form-control" id="days" name="days" required
                                        min="1">
                                </div>
                                <input type="hidden" name="tender_id" value="{{ $id->id }}">

                                <div class="col-md-9 mb-3">
                                    <label for="reason" class="form-label">Reason for Extension</label>
                                    <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <h5 for="client_contact" class="form-label">Client Contact Details</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="client_org" class="form-label">Organisation</label>
                                            <input type="text" class="form-control" id="client_org" name="client_org"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="client_name" class="form-label">Contact Person Name</label>
                                            <input type="text" class="form-control" id="client_name" name="client_name"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="client_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" id="client_email" name="client_email"
                                                required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="client_phone" class="form-label">Contact Phone</label>
                                            <input type="text" class="form-control" id="client_phone" name="client_phone"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary">Submit Extension Request</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
