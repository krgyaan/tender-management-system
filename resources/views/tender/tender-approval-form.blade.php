@extends('layouts.app')
@section('page-title', 'Tender Info Approval Form')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <h4 class="card-title text-center pb-4">
                            Approval Form for {{ $tenderInfo->tender->tender_name }} [{{ $tenderInfo->tender->tender_no }}]
                        </h4>
                        <form action="{{ route('tlapproved') }}" method="POST" enctype="multipart/form-data" class="row">
                            @csrf
                            <div class="form-group col-md-4 mb-3">
                                <label for="te" class="form-label">TE Recommendation</label>
                                <input type="hidden" name="id" id="id" value="{{ $tenderInfo->id }}">
                                <input type="text" name="te" id="te" class="form-control" readonly
                                    value="{{ $tenderInfo->is_rejectable == 1 ? 'No' : 'Yes' }}">
                            </div>
                            <div class="form-group col-md-4 mb-3">
                                <label for="status" class="form-label">
                                    TL's decision to bid on the tender
                                </label>
                                <select name="status" id="status" class="form-control" data-toggle-watch="true" required>
                                    <option value="">Select Status</option>
                                    <option {{ $tenderInfo->tender->tlStatus == '1' ? 'selected' : '' }} value="1">Yes</option>
                                    <option {{ $tenderInfo->tender->tlStatus == '2' ? 'selected' : '' }} value="2">No</option>
                                    <option {{ $tenderInfo->tender->tlStatus == '3' ? 'selected' : '' }} value="3">Tender Sheet Incomplete</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <div class="row" id="no" data-show-if="status:2">
                                    <div class="form-group mb-3 col-md-4">
                                        <label for="tender_status" class="form-label">Tender Status</label>
                                        <select name="tender_status" id="tender_status" class="form-control" data-toggle-oem="true" data-oem-value="10" data-required-if="status:2">
                                            <option value="" disabled>Select Status</option>
                                            @php
                                                $statuses = App\Models\Status::whereBetween('id', [9, 15])->orWhereBetween('id', [31, 35])->get();
                                            @endphp
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ $status->id == $tenderInfo->tender->status ? 'selected' : '' }}>
                                                    {{ $status->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 col-md-4" id="who_rejected" data-show-if="tender_status:10" data-required-if="tender_status:10">
                                        <label for="oem" class="form-label">OEM who didn't allow</label>
                                        <select name="oem_who_denied[]" id="oem" class="form-control w-100" multiple data-placeholder="Select OEMs">
                                            @php
                                                $vendors = App\Models\VendorOrg::all();
                                            @endphp
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}"
                                                    {{ in_array($vendor->id, explode(',', $tenderInfo->tender->oem_who_denied ?? '')) ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 col-md-4">
                                        <label for="rej_remark" class="form-label">Rejection Remark</label>
                                        <textarea name="rej_remark" id="rej_remark" class="form-control" data-required-if="status:2">{{ $tenderInfo->rej_remark }}</textarea>
                                    </div>
                                </div>
                                <div class="row" id="yes" data-show-if="status:1">
                                    <div class="form-group mb-3 col-md-4" id="rfqTo">
                                        <label for="rfq_to" class="form-label">Send RFQ to</label>
                                        <select name="rfq_to[]" id="rfq_to" class="form-control w-100" multiple data-required-if="status:1"
                                            data-placeholder="Select Vendor for RFQ">
                                            @php
                                                $vendors = App\Models\VendorOrg::all();
                                            @endphp
                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}"
                                                    {{ in_array($vendor->id, explode(',', $tenderInfo->tender->rfq_to ?? '')) ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 col-md-4" id="tenderFee">
                                        <label for="tender_fees" class="form-label">Select Mode of Tender Fees</label>
                                        <select name="tender_fees[]" id="tender_fees" class="form-control" multiple data-required-if="status:1"
                                            data-placeholder="Select Tender Fees Mode">
                                            @foreach (explode(',', $tenderInfo->tender_fees ?? '') as $key => $value)
                                                @if (array_key_exists($value, $tenderFees))
                                                    <option value="{{ $value }}"
                                                        {{ in_array($value, explode(',', $tenderInfo->tender_fees ?? '')) ? 'selected' : '' }}>
                                                        {{ $tenderFees[$value] }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 col-md-4" id="emdMode">
                                        <label for="emd_mode" class="form-label">Select Mode of EMD</label>
                                        <select name="emd_mode[]" id="emd_mode" class="form-control" multiple data-required-if="status:1"
                                            data-placeholder="Select EMD Mode">
                                            @foreach (explode(',', $tenderInfo->emd_opt) as $key => $value)
                                                @if (array_key_exists($value, $emdOpt))
                                                    <option value="{{ $value }}"
                                                        {{ in_array($value, explode(',', $tenderInfo->emd_opt)) ? 'selected' : '' }}>
                                                        {{ $emdOpt[$value] }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-3 col-md-6" id="pqr">
                                        <p>Approve PQR Selection</p>
                                        <ul class="list-group list-group-flush">
                                            @forelse ($tenderInfo->workOrder as $item)
                                                <li class="list-group-item">
                                                    {{ optional($item->woName)->project_name }}</li>
                                            @empty
                                                <li class="list-group-item">No documents available</li>
                                            @endforelse
                                        </ul>
                                        <div class="d-flex gap-4 pt-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pqr_eligible"
                                                    id="pqr_eligible_yes" value="1">
                                                <label class="form-check-label" for="pqr_eligible_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="pqr_eligible"
                                                    id="pqr_eligible_no" value="0">
                                                <label class="form-check-label" for="pqr_eligible_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3 col-md-6" id="finance">
                                        <p>Approve Finance Doc Selection</p>
                                        <ul class="list-group list-group-flush">
                                            @forelse ($tenderInfo->eligibleDocs as $item)
                                                <li class="list-group-item">
                                                    {{ optional($item->docName)->document_name }}</li>
                                            @empty
                                                <li class="list-group-item">No documents available</li>
                                            @endforelse
                                        </ul>
                                        <div class="d-flex gap-4 pt-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="fin_eligible"
                                                    id="fin_eligible_yes" value="1">
                                                <label class="form-check-label" for="fin_eligible_yes">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="fin_eligible"
                                                    id="fin_eligible_no" value="0" >
                                                <label class="form-check-label" for="fin_eligible_no">
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-6" id="newPqr" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table-bordered w-100">
                                        <tr class="text-end bg-transparent">
                                            <td colspan="3" class="p-0">
                                                <button type="button" class="btn btn-xs btn-secondary"
                                                    id="addWorkOrder">Add New</button>
                                            </td>
                                        </tr>
                                        <tbody id="workOrderTable">
                                            <tr>
                                                <td colspan="2">
                                                    <select name="wo[0][wo_name]" class="form-select" id="workOrder">
                                                        <option value="">Select PQR</option>
                                                        @foreach ($pqr as $it)
                                                            <option value="{{ $it->id }}">
                                                                {{ $it->project_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group mb-3 col-md-6" id="newFin" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table-bordered w-100">
                                        <tr class="text-end bg-transparent">
                                            <td colspan="3" class="p-0">
                                                <button type="button" class="btn btn-xs btn-secondary"
                                                    id="addDoc">Add</button>
                                            </td>
                                        </tr>
                                        <tbody id="documentstable">
                                            <tr>
                                                <td colspan="2">
                                                    <select name="docs[0][doc_name]" class="form-select" id="doc_name">
                                                        <option value="">Select Finance Docs</option>
                                                        @foreach ($finance as $it)
                                                            <option value="{{ $it->id }}">
                                                                {{ $it->document_name }}
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-6" id="rej_rem" data-show-if="status:3">
                                <div class="form-group mb-3 w-100">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea name="remarks" id="remarks" class="form-control" data-required-if="status:3">{{ $tenderInfo->tender->tlRemarks }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function setupConditionalToggles() {
                $('[data-show-if]').each(function () {
                    const $target = $(this);
                    const condition = $target.data('show-if');
                    const [sourceId, showVal] = condition.split(':');
            
                    const $source = $('#' + sourceId);
                    if (!$source.length) return;
            
                    function evaluateToggle() {
                        const currentVal = $source.val();
                        $target.toggle(currentVal == showVal);
                    }
            
                    evaluateToggle();
            
                    $source.on('change', evaluateToggle);
                });
            }
            
            setupConditionalToggles();
            
            function updateRequiredFields() {
                $('[data-required-if]').each(function () {
                    const $field = $(this);
                    const condition = $field.data('required-if');
                    const [sourceId, requiredVal] = condition.split(':');
                    const $source = $('#' + sourceId);
            
                    if (!$source.length) return;
            
                    const isRequired = $source.val() == requiredVal;
                    $field.prop('required', isRequired);
                });
            }
            
            updateRequiredFields();
            $('#status, #tender_status').on('change', updateRequiredFields);

            $('#rfq_to,#tender_fees,#emd_mode,#oem').each(function() {
                $(this).select2({
                    width: '100%',
                    placeholder: $(this).data('placeholder'),
                    allowClear: true
                });
            });

            $('input[name="pqr_eligible"]').on('change', function() {
                $('#newPqr').toggle(this.value == 0);
            });

            $('input[name="fin_eligible"]').on('change', function() {
                $('#newFin').toggle(this.value == 0);
            });
        });
    
        $(document).ready(function() {
            // Add new row for documents onclick #addDoc
            let docc = 1;

            $('#addDoc').click(function() {
                let html = '';
                html += '<tr>';
                html +=
                    '<td><select name="docs[' + docc +
                    '][doc_name]" class="form-select" id="documents"><option value="">Select Finance Docs</option>';
                @foreach ($finance as $i)
                    html +=
                        '<option value="{{ $i->id }}">{{ $i->document_name }}</option>';
                @endforeach
                html += '</select></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-xs" id="removeDoc"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('tbody#documentstable').append(html);
                docc++;
            });

            $('#documentstable').on('click', '#removeDoc', function() {
                $(this).closest('tr').remove();
            });

            // Add new row for workOrderTable onclick #addWorkOrder
            let woc = 1;
            $('#addWorkOrder').click(function() {
                let html = '';
                html += '<tr>';
                html += '<td><select name="wo[' + woc +
                    '][wo_name]" class="form-select" id="workorder"><option value="" disabled >Select PQR</option>';
                @foreach ($pqr as $it)
                    html +=
                        '<option value="{{ $it->id }}">{{ $it->project_name }}</option>';
                @endforeach
                html += '</select></td>';
                html +=
                    '<td><button type="button" class="btn btn-danger btn-xs" id="removeWorkOrder"><i class="fa fa-minus"></i></button></td>';
                html += '</tr>';
                $('tbody#workOrderTable').append(html);
                woc++;
            });

            $('#workOrderTable').on('click', '#removeWorkOrder', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        .select2-selection,
        .select2-selection__choice {
            background: transparent !important;
        }
    </style>
@endpush
