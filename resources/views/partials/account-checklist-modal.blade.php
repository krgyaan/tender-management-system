<!-- Responsibility Modal -->
<div class="modal fade" id="respModal{{ $checklist->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    aria-labelledby="respModalLabel{{ $checklist->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('checklists.resp.remark', $checklist->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="respModalLabel{{ $checklist->id }}">Responsibility Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resp_remark">Responsibility Remark</label>
                        <textarea name="resp_remark" class="form-control" required style="min-height: 100px;" id="resp_remark"
                            placeholder="Brief what you've done.">{{ old('resp_remark') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="resp_result_file">Upload Proof (if any)</label>
                        <input type="file" name="resp_result_file" class="form-control" id="resp_result_file"
                            accept=".pdf,.doc,.docx, image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Accountability Modal -->
<div class="modal fade" id="acctModal{{ $checklist->id }}" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" aria-labelledby="acctModalLabel{{ $checklist->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('checklists.acct.remark', $checklist->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="acctModalLabel{{ $checklist->id }}">Accountability Remark</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="acc_remark">Accountability Remark</label>
                        <textarea name="acc_remark" class="form-control" style="min-height: 100px;" required id="acc_remark"
                            placeholder="Brief what you've checked.">{{ old('acc_remark') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="acc_result_file">Upload Proof (if any)</label>
                        <input type="file" name="acc_result_file" class="form-control" id="acc_result_file"
                            accept=".pdf,.doc,.docx, image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
