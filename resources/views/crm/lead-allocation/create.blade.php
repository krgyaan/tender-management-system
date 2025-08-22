@extends('layouts.app')
@section('page-title', 'Allocate to TE')
@section('content')
    <section>
        <div class="card">
            <div class="card-body">
                @include('partials.messages')

                <form method="POST" action="{{ route('lead-allocations.store') }}">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                            <label for="te_id" class="form-label">Select Technical Executive</label>
                            <select class="form-select" id="te_id" name="te_id" required>
                                <option value="">-- Select TE --</option>
                                @foreach ($technicalExecutives as $te)
                                    <option value="{{ $te->id }}">{{ $te->name }} ({{ $te->team }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="allocation_notes" class="form-label">Allocation Notes</label>
                            <textarea class="form-control" id="allocation_notes" name="allocation_notes" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Allocate TE</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
