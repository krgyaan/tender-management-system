@extends('layouts.app')
@section('page-title', 'Accounts Performance')
@section('content')
    <div class="row">
        <div class="card">
            <div class="card-body">
                @include('partials.messages')
                <div class="new-user-info">
                    <form method="POST" action="">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="form-label" for="address">Team Member:</label>
                                <select name="team_member" class="form-control" id="team_member" required>
                                    <option value="">Select Team Member</option>
                                    @foreach ($users as $user)
                                        <option {{ $team_member == $user->id ? 'selected' : '' }}
                                            value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->team }})
                                        </option>
                                    @endforeach
                                </select>
                                <small>
                                    <span class="text-danger">{{ $errors->first('team_member') }}</span>
                                </small>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="from_date">From Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="from_date" class="form-control" id="from_date"
                                            value="{{ old('from_date') ?? ($_POST['from_date'] ?? '') }}">
                                    </div>
                                    <small>
                                        <span class="text-danger">{{ $errors->first('from_date') }}</span>
                                    </small>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <div class="profile-img-edit position-relative">
                                    <label class="form-label" for="to_date">To Date:</label>
                                    <div class="input-group">
                                        <input type="date" name="to_date" class="form-control" id="to_date"
                                            value="{{ old('to_date') ?? ($_POST['to_date'] ?? '') }}">
                                    </div>
                                    <small>
                                        <span class="text-danger">{{ $errors->first('to_date') }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" id="submit" name="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
