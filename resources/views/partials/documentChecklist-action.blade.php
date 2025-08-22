@php
    $submittedDocs = $tender->checklist;
@endphp

<button class="btn btn-info btn-sm upload-result-btn1" data-tender-id="{{ $tender->id }}"
    data-docs='@json($submittedDocs)'>
    Upload Checklist
</button>
