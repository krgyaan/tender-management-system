@extends('layouts.app')
@section('page-title', 'Costing Sheet')
@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <iframe src="https://docs.google.com/spreadsheets/d/<?php echo $driveid; ?>/edit?usp=sharing" width="100%"
                        height="800"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
