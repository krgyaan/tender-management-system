<div class="dropdown">
    <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" id="dropdownMenuButton1"
        data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
        <li>
            <a class="dropdown-item" href="{{ asset('admin/view_butten/' . $row->id) }}">
                View
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ asset('admin/tq_received_form/' . Crypt::encrypt($row->id)) }}">
                TQ Received
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ asset('admin/tq_replied_form/' . Crypt::encrypt($row->id)) }}">
                TQ Replied
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ asset('admin/tq_missed_form/' . Crypt::encrypt($row->id)) }}">
                TQ Missed
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="">
                Qualified
            </a>
        </li>
    </ul>
</div>
