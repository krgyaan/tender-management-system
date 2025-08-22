@extends('layouts.app')
@section('page-title', 'Assign Followup')
@section('content')
    @php
        $areas = [
            '1' => 'PG Personal',
            '2' => 'Accounts',
            '3' => 'AC Team',
            '4' => 'DC team',
        ];
    @endphp
    <section>
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('followups.index') }}" class="btn btn-outline-danger btn-sm">Go Back</a>
                <div class="card">
                    <div class="card-body">
                        @include('partials.messages')
                        <form action="{{ route('followups.store') }}" method="POST" class="needs-validation" novalidate>
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <input type="hidden" name="created_by" value="{{ auth()->user()->id }}">
                                    <label class="form-label" for="area">Area</label>
                                    <select name="area" id="area" class="form-control" required>
                                        <option value="">choose</option>
                                        @foreach ($areas as $key => $area)
                                            <option value="{{ $area }}">{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="party_name">Organisation Name</label>
                                    <input type="text" name="party_name" id="party_name" class="form-control" required
                                        value="{{ old('party_name') }}">
                                </div>
                            </div>
                            <div class="row" id="popfollowup">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="form-label">Contact details:</label>
                                    <a href="javascript:void(0)" class="addDdFollowup">Add Person</a>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group" id="ddfollowups">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <input type="text" name="fp[name][0]" class="form-control" id="name"
                                                    placeholder="Name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="email" name="fp[email][0]" class="form-control"
                                                    id="email" placeholder="Email">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <input type="number" name="fp[phone][0]" class="form-control"
                                                    id="phone" placeholder="Phone">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="followup_for">Followup For</label>
                                    <select name="followup_for" id="followup_for" class="form-control">
                                        <option value="">choose</option>
                                        @foreach ($reasons as $key => $reason)
                                            <option {{ old('followup_for') == $reason ? 'selected' : '' }}
                                                value="{{ $reason->name }}">{{ $reason->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="amount">Amount Involved</label>
                                    <input type="number" step="any" name="amount" id="amount" class="form-control"
                                        required value="{{ old('amount') }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="assigned_to">Followup Assigned to</label>
                                    <select name="assigned_to" id="assigned_to" class="form-control" required>
                                        <option value="">choose</option>
                                        @foreach ($users as $user)
                                            <option {{ old('assigned_to') == $user->id ? 'selected' : '' }}
                                                value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label" for="comment">Comment</label>
                                    <textarea row="3" class="form-control" name="comment" id="comment" placeholder="Add comment for assignee."></textarea>
                                </div>
                                <div class="form-group col-md-12 text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
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
            let fp = 1;
            let html = `
            <div class="row">
                <div class="col-md-4 form-group">
                    <input type="text" name="fp[name][${fp}]" class="form-control" id="name" placeholder="Name">
                </div>
                <div class="col-md-4 form-group">
                    <input type="email" name="fp[email][${fp}]" class="form-control" id="email" placeholder="Email">
                </div>
                <div class="col-md-4 form-group">
                    <input type="number" name="fp[phone][${fp}]" class="form-control" id="phone" placeholder="Phone">
                </div>
            </div>
            `;
            $(document).on('click', '.addDdFollowup', function(e) {
                $('#ddfollowups').append(html);
                fp++;
            });
        });
    </script>
@endpush
