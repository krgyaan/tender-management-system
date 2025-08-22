@extends('layouts.app')
@section('page-title', 'View GST R1 Details')

@section('content')
    <div class="container">

        <table class="table table-bordered">
            <tr>
                <th>Tally Data Link</th>
                <td>
                    <a href="{{ $gstR1->tally_data_link }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Open
                    </a>
                </td>
            </tr>
            <tr>
                <th>GST R1 Sheet</th>
                <td>
                    <a href="{{ asset('storage/' . $gstR1->gst_r1_sheet_path) }}" target="_blank"
                        class="btn btn-sm btn-secondary ">
                        View
                    </a>
                </td>
            </tr>
            <tr>
                <th>Return File</th>
                @if ($gstR1->return_file_path)
                    <td>
                        <a href="{{ $gstR1->return_file_path }}" target="_blank">
                            <button class="btn btn-sm btn-secondary">View Return
                                File</button>
                        </a>
                    </td>
                @else
                    <td>Not Available</td>
                     @endif
            </tr>
           

            <tr>
                <th>GST confirmation</th>
                <td>{{ $gstR1->confirmation ? 'Yes' : 'No' }}</td>
            </tr>


        </table>
        <div class="col-md-12 text-end">
            <a href="{{ route('gstr1.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <div>

    </div>
@endsection
