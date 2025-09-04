<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton{{ $amc->id }}"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $amc->id }}">

        <!-- View -->
        <li>
            <a class="dropdown-item" href="{{ route('amc.show', $amc->id) }}">
                View
            </a>
        </li>

        <!-- Sample Service Report Download -->
        <li>
            <a class="dropdown-item" href="#">
                Sample Service Report Download
            </a>
        </li>

        <!-- Upload Filled Service Report -->
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadFilledReportModal"
                data-amc-id="{{ $amc->id }}">
                Upload Filled Service Report
            </a>
        </li>

        <!-- Upload Signed Service Report -->
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#uploadSignedReportModal"
                data-amc-id="{{ $amc->id }}">
                Upload Signed Service Report
            </a>
        </li>


        <li>
            <a class="dropdown-item" href="{{ route('amc.edit', $amc->id) }}">
                Edit
            </a>
        </li>


    </ul>
</div>
