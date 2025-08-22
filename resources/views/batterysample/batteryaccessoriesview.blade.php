@extends('layouts.app')
@section('page-title', 'Battery Accessories View Sheet ')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-4 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-12">
                                <p><b>Item Name : </b>{{ $batteryprice->item_name }}</p>
                                <p><b>Item Model :</b>
                                    @if (isset($batteryprice->itemModel))
                                        {{ $batteryprice->itemModel->model }}
                                    @endif
                                </p>
                                <p><b>AH :</b> {{ $batteryprice->ah }}</p>
                                <p><b>Cells per Bank : </b>{{ $batteryprice->cells_per_bank }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/batteryaccessoriesviewadd') }}"
                            enctype="multipart/form-data" id="formatDistrict-update" class="row g-3 needs-validation"
                            novalidate>
                            @csrf
                            <div class="row mt-2">
                                @foreach ($batteryaccessories as $key => $batteryaccessoriesData)
                                    <div class="col-md-6 mt-3">
                                        <label for="input{{ $key }}" class="col-form-label">
                                            {{ $batteryaccessoriesData->title }} <span style="color:#d2322d"> *</span>
                                        </label>
                                        <input type="hidden" name="batteryaccessoriesId[]"
                                            value="{{ $batteryaccessoriesData->id }}">
                                        @php
                                            $existingValue = App\Models\Battery_accessories_view_sheet::where(
                                                'batteryaccessories_id',
                                                $batteryaccessoriesData->id,
                                            )
                                                ->where('itemid', $batteryprice->id)
                                                ->first();
                                        @endphp

                                        <input type="text" class="form-control"
                                            name="batteryaccessoriesValue{{ $batteryaccessoriesData->id }}"
                                            value="{{ $existingValue ? $existingValue->batteryaccessories_value : 0 }}"
                                            required>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="batterypriceId" value="{{ $batteryprice->id }}">
                            <div class="col-md-12">
                                <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">
                                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
