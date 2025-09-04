@switch($complaint->status)
    @case(1)
        Allotted Engineer
    @break

    @case(2)
        Conference Call Completed
    @break

    @case(3)
        Service Visit
    @break

    @case(4)
        Closed
    @break

    @default
        Engineer Not Alloted
@endswitch
