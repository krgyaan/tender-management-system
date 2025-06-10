@extends('layouts.app')
@section('page-title', 'Employee Dashboard')
@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="{{ route('tender.index') }}" class="stretched-link text-white fw-bold fs-5 me-2">Assigned Tenders</a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">
                            @php
                                $count = 0;
                                foreach ($tenderInfo as $item) {
                                    if (Auth::user()->id == $item->team_member) {
                                        $count++;
                                    }
                                }
                            @endphp
                            {{ $count }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shining-card">
                <div class="card-body">
                    <a href="" class="stretched-link text-white fw-bold fs-5 me-2">Total Bids</a>
                    <div class="progress-detail pt-3">
                        <h4 class="counter text-success" style="visibility: visible;">0</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
