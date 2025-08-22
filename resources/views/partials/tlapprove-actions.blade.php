<a href="{{ route('tender.show', $info->tender_id) }}" class="btn btn-primary btn-xs">
    <i class="fa fa-eye"></i>
</a>
<a href="{{ route('tlApprovalForm', $info->id) }}"
    class="btn btn-{{ [
        1 => 'success',
        2 => 'danger',
        3 => 'warning',
    ][$info->tender->tlStatus] ?? 'primary' }} btn-xs">
    {{ [
        1 => 'Approved',
        2 => 'Rejected',
        3 => 'Incomplete Sheet',
    ][$info->tender->tlStatus] ?? 'Pending' }}
</a>
