@extends('layouts.app')

@section('page-title', 'GST R1 Uploads')

@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">

                {{-- Align Add New button to the left, outside the card --}}
                <div class="d-flex justify-content-start align-items-center mb-2">
                    <a href="{{ route('gstr1.create') }}" class="btn btn-sm btn-primary">Add New</a>
                </div>

                <div class="card shadow-sm rounded">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>GstR1 Sheet</th>
                                    <th>Tally Data Link</th>
                                    <th>Confirmation</th>
                                    <th>Return File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($gstR1s as $gstR1)
                                    <tr>
                                        <td>{{ $gstR1->id }}</td>
                                        <td>
                                            <a href="{{ $gstR1->gst_r1_sheet_path }}">
                                                <button class="btn btn-sm btn-primary"> View Sheet</button></a>
                                        </td>
                                        <td>
                                            <a href="{{ $gstR1->tally_data_link }}" target="_blank">
                                                <button class="btn btn-sm btn-secondary"> View Tally Data
                                                </button>
                                            </a>
                                        </td>
                                        <td>{{ $gstR1->confirmation ? 'Yes' : 'No' }}</td>
                                        <td>
                                            @if ($gstR1->return_file_path)
                                                <a href="{{ $gstR1->return_file_path }}" target="_blank">
                                                    <button class="btn btn-sm secondary-btn">View Return
                                                        File</button>
                                                </a>
                                            @else
                                                Not Available
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('gstr1.edit', $gstR1->id) }}"
                                                class="btn btn-sm btn-warning">Edit</a>
                                            <a href="{{ route('gstr1.show', $gstR1->id) }}"
                                                class="btn btn-sm btn-secondary"> Show </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
