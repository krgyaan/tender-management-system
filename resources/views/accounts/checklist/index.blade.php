@extends('layouts.app')
@section('page-title', 'Account Checklists')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('checklists.create') }}" class="btn btn-primary btn-sm">+ Add New Checklist</a>
                </div>
                <div class="card">
                    @include('partials.messages')
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Task Name</th>
                                        <th>Frequency</th>
                                        <th>Responsibility</th>
                                        <th>Timer</th>
                                        <th>Accountability</th>
                                        <th>Timer</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($checklists->count())
                                        @foreach ($checklists as $checklist)
                                            <tr>
                                                <td>{{ $checklist->task_name }}</td>
                                                <td>{{ $checklist->frequency }}</td>
                                                <td>{{ $checklist->responsibleUser->name ?? 'N/A' }}</td>
                                                <td></td>
                                                <td>{{ $checklist->accountableUser->name ?? 'N/A' }}</td>
                                                <td></td>
                                                <td style="white-space: nowrap;">
                                                    <a href="{{ route('checklists.show', $checklist->id) }}"
                                                        class="btn btn-sm btn-secondary mb-1">View</a>
                                                    <button class="btn btn-sm btn-info mb-1" data-bs-toggle="modal"
                                                        data-bs-target="#respModal{{ $checklist->id }}">RESP</button>
                                                    <button class="btn btn-sm btn-warning mb-1" data-bs-toggle="modal"
                                                        data-bs-target="#acctModal{{ $checklist->id }}">ACCT</button>
                                                    <button class="btn btn-sm btn-success mb-1" data-bs-toggle="modal"
                                                        data-bs-target="#uploadModal{{ $checklist->id }}">Upload</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal templates -->
    @foreach ($checklists as $checklist)
        <!-- Responsibility Modal -->
        <div class="modal fade" id="respModal{{ $checklist->id }}" tabindex="-1"
            aria-labelledby="respModalLabel{{ $checklist->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('checklists.resp.remark', $checklist->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="respModalLabel{{ $checklist->id }}">Responsibility Remark</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="responsibility_remark" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Accountability Modal -->
        <div class="modal fade" id="acctModal{{ $checklist->id }}" tabindex="-1"
            aria-labelledby="acctModalLabel{{ $checklist->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('checklists.acct.remark', $checklist->id) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="acctModalLabel{{ $checklist->id }}">Accountability Remark</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="accountability_remark" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Upload Modal -->
        <div class="modal fade" id="uploadModal{{ $checklist->id }}" tabindex="-1"
            aria-labelledby="uploadModalLabel{{ $checklist->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('checklists.upload.result', $checklist) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel{{ $checklist->id }}">Upload File</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="result_file" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
