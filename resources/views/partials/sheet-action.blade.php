@if ($tender->sheet)
    <a href="{{ $tender->sheet->driveid }}" target="_blank" class="btn btn-primary btn-xs">Open</a>
    <button type="button" class="btn btn-warning btn-xs" data-id="{{ $tender->sheet->id }}" data-bs-toggle="modal"
        data-bs-target="#submit_sheet">
        Submit
    </button>
    <!--<a href="" class="btn btn-info btn-xs">View</a>-->
@else
    <form action="{{ asset('admin/googletoolssave') }}" method="post" class="d-inline">
        @csrf
        <input type="hidden" name="TenderInfo" value="{{ $tender->id }}">
        <input type="hidden" name="title" value="{{ $tender->tender_name }}">
        <button type="submit" class="btn btn-info btn-xs">
            Create Sheet
        </button>
    </form>
@endif
