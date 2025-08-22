@extends('layouts.app')
@section('page-title', 'Battery Price Sheet ')
@section('content')
    <section>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="post" action="{{ asset('/admin/batterypricecreate') }}" enctype="multipart/form-data"
                            id="formatDistrict-update" class="row g-3 needs-validation" novalidate>
                            @csrf
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">Freight %age<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="freight_age" id="input36" required>

                                @if ($errors->has('freight_age'))
                                    <span class="text-danger">
                                        @error('freight_age')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">BG<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="bg" id="input36" required>

                                @if ($errors->has('bg'))
                                    <span class="text-danger">
                                        @error('bg')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">Cash Margin<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="cash_margin" id="input36" required>

                                @if ($errors->has('cash_margin'))
                                    <span class="text-danger">
                                        @error('cash_margin')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">Buyback Rs./Kg<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="buyback" id="input36" required>

                                @if ($errors->has('buyback'))
                                    <span class="text-danger">
                                        @error('buyback')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">Actual Buyback<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="actual_buyback" id="input36" required>

                                @if ($errors->has('actual_buyback'))
                                    <span class="text-danger">
                                        @error('actual_buyback')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>

                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">GST on Battery<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="gst_on_battery" id="input36" required>

                                @if ($errors->has('gst_on_battery'))
                                    <span class="text-danger">
                                        @error('gst_on_battery')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">GST on I&C<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="gst_on_ic" id="input36" required>

                                @if ($errors->has('gst_on_ic'))
                                    <span class="text-danger">
                                        @error('gst_on_ic')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-3">
                                <label for="input35" class=" col-form-label">GST on Buyback<span style="color:#d2322d">
                                        *</span></label>
                                <input type="text" class="form-control" name="gst_on_buyback" id="input36" required>

                                @if ($errors->has('gst_on_buyback'))
                                    <span class="text-danger">
                                        @error('gst_on_buyback')
                                            {{ $message }}
                                        @enderror
                                    </span>
                                @endif

                            </div>
                            <div class="col-md-12">

                                <div class="col-md-12 text-right mt-3">
                                    <p id="add_row" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <rect x="10" y="4" width="4" height="16" />
                                            <rect x="4" y="10" width="16" height="4" />
                                        </svg>
                                    </p>
                                    <p id="delete_row" class="btn btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="currentColor" viewBox="0 0 24 24">
                                            <rect x="4" y="10" width="16" height="4" />
                                        </svg>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-12" style="overflow: auto;">
                                <table class="table table-bordered table-hover table-responsive" id="tab_logic">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Sr.No</th>
                                            <th class="text-center">Item Name</th>
                                            <th class="text-center">Model</th>
                                            <th class="text-center">AH</th>
                                            <th class="text-center">Cells per Bank</th>
                                            <th class="text-center">Spare Cells</th>
                                            <th class="text-center">Price/AH</th>
                                            <th class="text-center">Bidding Installtion Cost</th>
                                            <th class="text-center">No. of Banks</th>
                                            <th class="text-center">Dry Weight of Old Battery bank (Kg)</th>
                                        </tr>
                                    </thead>
                                    <tbody>


                                        <tr id="addr0">
                                            <td>1</td>
                                            <td>

                                                <input type="text" name="item_name[]" value="0" placeholder=""
                                                    class="form-control" required>
                                            </td>
                                            <td>
                                                <select name="item_model[]" class="form-control" required>

                                                    <option value="">Select Option</option>
                                                    @foreach ($itemmodel as $key => $data)
                                                        <option value="{{ $data->id }}">{{ $data->model }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="ah[]" value="0" class="form-control"
                                                    required>
                                            </td>
                                            <td>
                                                <input type="text" name="cells_per_bank[]" value="0"
                                                    placeholder="" class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="spare_cells[]" value="0" placeholder=""
                                                    class="form-control" required>
                                            </td>
                                            <td>
                                                <input type="text" name="price_ah[]" value="0" placeholder=""
                                                    class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="bidding_installtion_cost[]" value="0"
                                                    placeholder="" class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="no_of_banks[]" value="0"
                                                    class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="old_battery_bank[]" value="0"
                                                    class="form-control">
                                            </td>
                                        </tr>

                                        <tr id="addr1"></tr>
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3 justify-content-end">

                            <button type="submit" class="btn btn-primary px-4">Submit</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var i = 1;

                $("#add_row").click(function() {
                    var b = i - 1;
                    $('#addr' + i).html($('#addr' + b).html()).find('td:first-child').html(i + 1);
                    $('#tab_logic').append('<tr id="addr' + (i + 1) + '"></tr>');
                    i++;
                });

                $("#delete_row").click(function() {
                    if (i > 1) {
                        $("#addr" + (i - 1)).html('');
                        i--;
                    }
                });

                $('#tab_logic tbody').on('keyup change', function() {
                    calc();
                });

                $('#tax').on('keyup change', function() {
                    calc_total();
                });
            });
        </script>
    @endpush
@endsection
