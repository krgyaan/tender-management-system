@extends('layouts.app')
@section('page-title', 'Coordinator Dashboard')
@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card shining-card">
            <div class="card-body">
                <a href="{{ route('tender.index') }}" class="stretched-link text-white fw-bold fs-5 me-2">Total Tenders</a>
                <div class="progress-detail pt-3">
                    <h4 class="counter text-success" style="visibility: visible;">{{ count($tenderInfo) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card shining-card">
            <div class="card-body">
                <a href="" class="stretched-link text-white fw-bold fs-5 me-2">Total Bids</a>
                <div class="progress-detail pt-3">
                    <h4 class="counter text-success" style="visibility: visible;">5</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
