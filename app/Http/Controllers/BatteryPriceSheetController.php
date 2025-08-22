<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item_model;
use App\Models\Battery_item_price;
use App\Models\Battery_installation;
use App\Models\Battery_accessories;
use App\Models\Battery_installation_view_sheet;
use App\Models\Battery_accessories_view_sheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Crypt;

class BatteryPriceSheetController extends Controller
{
    //

    function batteryprice()
    {


        $data['batteryitem'] = Battery_item_price::get();
        return view('batterysample.batteryprice', $data);
    }
    function batterypriceadd()
    {
        $data['itemmodel'] = Item_model::get();
        return view('batterysample.batterypriceadd', $data);
    }


    public function batterypricecreate(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'freight_age' => 'required',

        ]);


        foreach ($request->item_name as $index => $itemName) {
            $batterydata = new Battery_item_price();
            $batterydata->freight_age = $request->freight_age;
            $batterydata->bg = $request->bg;
            $batterydata->cash_margin = $request->cash_margin;
            $batterydata->buyback = $request->buyback;
            $batterydata->actual_buyback = $request->actual_buyback;
            $batterydata->gst_on_battery = $request->gst_on_battery;
            $batterydata->gst_on_ic = $request->gst_on_ic;
            $batterydata->gst_on_buyback = $request->gst_on_buyback;


            $batterydata->item_name = $itemName;
            $batterydata->item_model = $request->item_model[$index];
            $batterydata->ah = $request->ah[$index];
            $batterydata->cells_per_bank = $request->cells_per_bank[$index];
            $batterydata->spare_cells = $request->spare_cells[$index];
            $batterydata->price_ah = $request->price_ah[$index];
            $batterydata->bidding_installtion_cost = $request->bidding_installtion_cost[$index];
            $batterydata->no_of_banks = $request->no_of_banks[$index];
            $batterydata->old_battery_bank = $request->old_battery_bank[$index];
            $batterydata->ip = $_SERVER['REMOTE_ADDR'];
            $batterydata->strtotiem = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
            $batterydata->save();
        }

        return redirect()->route('batteryprice')->with('success', 'Battery prices added successfully.');
    }


    function batterypricedelete(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        //   $idd =datadelete($id, '1', 0);
        $batterydelete = Battery_item_price::findOrFail($id);
        $batterydelete->delete();
        return redirect()->back()->with('success', 'Battery Price  successfully delete.');
    }




    public function houseajexbatteryprice(Request $request)
    {
        $draw = $request->draw;
        $rows = $request->start;
        $columnIndex = $request->order[0]['column'];
        $columnName = $request->columns[$columnIndex]['data'];
        $columnSortOrder = $request->order[0]['dir'];
        $filltersearchValue = $request->columns[$columnIndex]['search']['value'];
        $searchValue = $request->search['value'];
        $length = $request->length;

        $housesurveydata = Battery_item_price::houseajexbatteryprice($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 1, $length);
        $housesurveycount = Battery_item_price::houseajexbatteryprice($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 0, $length);


        $i = 1;
        $data = array();
        $user_id = session('SUPERADMIN_ID');
        $superadminRole = session('SUPERADMIN_ROLE');

        foreach ($housesurveydata as $row) {
            $action = '<ul class="action d-flex " style="list-style-type: none; ">';


            $action .= '<li class="list-unstyled" style="margin: 0;">

                  <a href="' . asset('superadmin/employeedelete/' . Crypt::encrypt($row->id)) . '" class="btn btn-info btn-sm">
                                                                    <svg class="svg-inline--fa fa-pencil" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pencil" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                        <path fill="currentColor" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                                    </svg>
                                                                </a>

             </li>';


            $action .= '<li class="list-unstyled" >
                                                                <a onclick="return check_delete()" href="' . asset('admin/batterypricedelete/' . Crypt::encrypt($row->id)) . '" class="btn btn-danger btn-sm">
                                                                    <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                                        <path fill="currentColor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                                                                    </svg>
                                                                </a>



            </li>';


            $action .= '</ul>';



            $installation = '<ul class="action d-flex" style="list-style-type: none;">
                   <li class="list-unstyled">
                        <a  href="' . asset('admin/batteryinstallationview/' . Crypt::encrypt($row->id)) . '" class="btn btn-warning btn-sm">
                            <svg class="svg-inline--fa fa-wrench" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="wrench" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                <path fill="currentColor" d="M481.2 96.5c10.2-26.2 3.5-56.5-17.2-77.1s-50.9-27.4-77.1-17.2c-11.5 4.5-21.5 12.1-29 21.7l46.3 46.3c6.2 6.2 6.2 16.4 0 22.6s-16.4 6.2-22.6 0L335.2 46.4c-9.7 7.5-17.2 17.5-21.7 29-10.2 26.2-3.5 56.5 17.2 77.1s50.9 27.4 77.1 17.2c11.5-4.5 21.5-12.1 29-21.7l51.5 51.5c-3.1 8.5-4.8 17.7-4.8 27.3c0 44.2 35.8 80 80 80s80-35.8 80-80s-35.8-80-80-80c-9.6 0-18.8 1.7-27.3 4.8l-51.5-51.5c7.5-9.7 17.5-17.2 29-21.7zM96 416l64-64 80 80-64 64H96v-80z"/>
                            </svg>
                        </a>
                    </li>
                 </ul>';
            $Accessories = '<ul class="action d-flex" style="list-style-type: none;">
                   <li class="list-unstyled">
                        <a  href="' . asset('admin/batteryaccessoriesview/' . Crypt::encrypt($row->id)) . '"
                            class="btn btn-sm  btn-info"
                            >
                            <svg class="svg-inline--fa fa-wrench" aria-hidden="true" focusable="false"
                                data-prefix="fas" data-icon="wrench" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                width="16" height="16">
                                <path fill="white" d="M481.2 96.5c10.2-26.2 3.5-56.5-17.2-77.1s-50.9-27.4-77.1-17.2c-11.5 4.5-21.5 12.1-29 21.7l46.3 46.3c6.2 6.2 6.2 16.4 0 22.6s-16.4 6.2-22.6 0L335.2 46.4c-9.7 7.5-17.2 17.5-21.7 29-10.2 26.2-3.5 56.5 17.2 77.1s50.9 27.4 77.1 17.2c11.5-4.5 21.5-12.1 29-21.7l51.5 51.5c-3.1 8.5-4.8 17.7-4.8 27.3c0 44.2 35.8 80 80 80s80-35.8 80-80s-35.8-80-80-80c-9.6 0-18.8 1.7-27.3 4.8l-51.5-51.5c7.5-9.7 17.5-17.2 29-21.7zM96 416l64-64 80 80-64 64H96v-80z"/>
                            </svg>
                        </a>
                    </li>
                 </ul>';





            if ($row->status === '1') {
                $checked = 'checked';
            }

            $status = '<div class="form-check form-switch form-check-success"><input onclick="round_success_noti()" name="status" ' . ($row->status == 1 ? 'checked' : '') . '  data-id="' . $row->id . '" class="form-check-input js-switch" type="checkbox" role="switch"></div>';


            $data[] = array(
                "id" => $i++,
                "item_name" => $row->item_name,
                "installation" => $installation,
                "accessories" => $Accessories,

                "action" => $action
            );
        }

        $query = DB::table('battery_item_prices')->orderby('id', 'DESC');

        $tot_rows = $query->count();

        $datas = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rows,
            "recordsFiltered" => $housesurveycount,
            "data" => $data
        );

        echo json_encode($datas);
    }




    public function batteryinstallation()
    {
        return view('batterysample.batteryinstallation');
    }

    public function batteryinstallationadd(Request $request)
    {
        $batteryinstall = new Battery_installation;
        $batteryinstall->title = $request->title;
        $batteryinstall->ip = $_SERVER['REMOTE_ADDR'];
        $batteryinstall->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $batteryinstall->save();
        return redirect()->back()->with('success', 'Battery Installation Add Successfully');
    }


    public function houseajexbatteryinstallation(Request $request)
    {
        $draw = $request->draw;
        $rows = $request->start;
        $columnIndex = $request->order[0]['column'];
        $columnName = $request->columns[$columnIndex]['data'];
        $columnSortOrder = $request->order[0]['dir'];
        $filltersearchValue = $request->columns[$columnIndex]['search']['value'];
        $searchValue = $request->search['value'];
        $length = $request->length;

        $housesurveydata = Battery_installation::houseajexbatteryinstallation($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 1, $length);
        $housesurveycount = Battery_installation::houseajexbatteryinstallation($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 0, $length);


        $i = 1;
        $data = array();



        foreach ($housesurveydata as $row) {
            $action = '<ul class="action d-flex " style="list-style-type: none; ">';

            // Edit Button
            $action .= '<li class="list-unstyled" style="margin: 0;">

            <a href="javascript:void(0);"
                   onClick="categoryupdate(' .

                $row->id . ', \'' .
                addslashes($row->title) . '\');" class="btn btn-info btn-sm">
                                                                    <svg class="svg-inline--fa fa-pencil" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pencil" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                        <path fill="currentColor" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                                    </svg>
                                                                </a>

            </li>';


            // Delete Button

            $action .= '<li class="list-unstyled" >
                                                                <a onclick="return check_delete()" href="' . asset('admin/batteryinstallationdelete/' . Crypt::encrypt($row->id)) . '" class="btn btn-danger btn-sm">
                                                                    <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                                        <path fill="currentColor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                                                                    </svg>
                                                                </a>



            </li>';


            $action .= '</ul>';




            if ($row->status === '1') {
                $checked = 'checked';
            }

            $status = '<div class="form-check form-switch form-check-success"><input onclick="round_success_noti()" name="status" ' . ($row->status == 1 ? 'checked' : '') . '  data-id="' . $row->id . '" class="form-check-input js-switch" type="checkbox" role="switch"></div>';


            $data[] = array(
                "id" => $i++,

                "title" => $row->title,

                "action" => $action
            );
        }


        $query = DB::table('battery_installations')->orderby('id', 'DESC');

        $tot_rows = $query->count();

        $datas = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rows,
            "recordsFiltered" => $housesurveycount,
            "data" => $data
        );

        echo json_encode($datas);
    }



    public function batteryinstallationdelete(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        //   $idd =datadelete($id, '1', 0);
        $batterydelete = Battery_installation::findOrFail($id);
        $batterydelete->delete();
        return redirect()->back()->with('success', 'Battery Installation  successfully delete.');
    }

    public function batteryinstallationupdate(Request $request)
    {
        $batteryupdate = Battery_installation::where('id', $request->id)->first();
        $batteryupdate->ip = $_SERVER['REMOTE_ADDR'];
        $batteryupdate->title = $request->title;
        $batteryupdate->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $batteryupdate->save();
        return redirect()->back()->with('success', 'Battery Installation Update Successfully');
    }


    public function batteryaccessories()
    {
        return view('batterysample.batteryaccessories');
    }

    public function batteryaccessoriesadd(Request $request)
    {
        $batteryinstall = new Battery_accessories;
        $batteryinstall->title = $request->title;
        $batteryinstall->ip = $_SERVER['REMOTE_ADDR'];
        $batteryinstall->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $batteryinstall->save();
        return redirect()->back()->with('success', 'Battery Accessories Add Successfully');
    }




    public function batteryaccessoriesdelete(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        //   $idd =datadelete($id, '1', 0);
        $batterydelete = Battery_accessories::findOrFail($id);
        $batterydelete->delete();
        return redirect()->back()->with('success', 'Battery Accessories  successfully delete.');
    }

    public function batteryaccessoriesupdate(Request $request)
    {
        $batteryupdate = Battery_accessories::where('id', $request->id)->first();
        $batteryupdate->ip = $_SERVER['REMOTE_ADDR'];
        $batteryupdate->title = $request->title;
        $batteryupdate->strtotime = Carbon::parse($request->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $batteryupdate->save();
        return redirect()->back()->with('success', 'Battery Accessories Update Successfully');
    }

    public function houseajexbatteryaccessoriestion(Request $request)
    {
        $draw = $request->draw;
        $rows = $request->start;
        $columnIndex = $request->order[0]['column'];
        $columnName = $request->columns[$columnIndex]['data'];
        $columnSortOrder = $request->order[0]['dir'];
        $filltersearchValue = $request->columns[$columnIndex]['search']['value'];
        $searchValue = $request->search['value'];
        $length = $request->length;

        $housesurveydata = Battery_accessories::houseajexbatteryaccessoriestion($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 1, $length);
        $housesurveycount = Battery_accessories::houseajexbatteryaccessoriestion($rows, $searchValue, $columnSortOrder, $columnName, $columnIndex, $filltersearchValue, 0, $length);


        $i = 1;
        $data = array();



        foreach ($housesurveydata as $row) {
            $action = '<ul class="action d-flex " style="list-style-type: none; ">';

            // Edit Button
            $action .= '<li class="list-unstyled" style="margin: 0;">

            <a href="javascript:void(0);"
                   onClick="categoryupdate(' .

                $row->id . ', \'' .
                addslashes($row->title) . '\');" class="btn btn-info btn-sm">
                                                                    <svg class="svg-inline--fa fa-pencil" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pencil" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                        <path fill="currentColor" d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1 0 32c0 8.8 7.2 16 16 16l32 0zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                                                    </svg>
                                                                </a>

            </li>';


            // Delete Button

            $action .= '<li class="list-unstyled" >
                                                                <a onclick="return check_delete()" href="' . asset('admin/batteryaccessoriesdelete/' . Crypt::encrypt($row->id)) . '" class="btn btn-danger btn-sm">
                                                                    <svg class="svg-inline--fa fa-trash" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                                        <path fill="currentColor" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                                                                    </svg>
                                                                </a>



            </li>';


            $action .= '</ul>';




            if ($row->status === '1') {
                $checked = 'checked';
            }

            $status = '<div class="form-check form-switch form-check-success"><input onclick="round_success_noti()" name="status" ' . ($row->status == 1 ? 'checked' : '') . '  data-id="' . $row->id . '" class="form-check-input js-switch" type="checkbox" role="switch"></div>';


            $data[] = array(
                "id" => $i++,

                "title" => $row->title,

                "action" => $action
            );
        }


        $query = DB::table('battery_accessories')->orderby('id', 'DESC');

        $tot_rows = $query->count();

        $datas = array(
            "draw" => $draw,
            "recordsTotal" => $tot_rows,
            "recordsFiltered" => $housesurveycount,
            "data" => $data
        );

        echo json_encode($datas);
    }


    public function batterypriceview()
    {

        $data['batteryprice'] = Battery_item_price::with('itemModel')->get();

        $data['batteryinstallation'] = Battery_installation::get();
        $data['batteryaccessories'] = Battery_accessories::get();

        return view('batterysample.batterypriceview', $data);
    }



    public function batteryinstallationview(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        $data['batteryprice'] = Battery_item_price::where('id', $id)->with('itemModel')->first();
        $data['batterinstall'] = Battery_installation::get();

        return view('batterysample.batteryinstallationview', $data);
    }


    public function batteryinstallationviewadd(Request $request)
    {


        foreach ($request->batteryinstallationId as $index => $installationId) {

            $value = isset($request["batteryinstallationValue{$installationId}"]) ? $request["batteryinstallationValue{$installationId}"] : 0;


            $btrinstallview = Battery_installation_view_sheet::where('batteryinstallation_id', $installationId)
                ->where('itemid', $request->batterypriceId)
                ->first();

            if ($btrinstallview) {

                $btrinstallview->batteryinstallation_value = $value;
            } else {

                $btrinstallview = new Battery_installation_view_sheet;
                $btrinstallview->itemid = $request->batterypriceId;
                $btrinstallview->batteryinstallation_id = $installationId;
                $btrinstallview->batteryinstallation_value = $value;
                $btrinstallview->ip = request()->ip();
                $btrinstallview->strtotime = Carbon::now()->timezone('Asia/Kolkata')->timestamp;
            }

            $btrinstallview->save();
        }

        return redirect()->back()->with('success', 'Battery Installation values updated successfully.');
    }

    function batteryaccessoriesview(Request $request)
    {
        $id = Crypt::decrypt($request->id);
        $data['batteryprice'] = Battery_item_price::where('id', $id)->with('itemModel')->first();
        $data['batteryaccessories'] = Battery_accessories::get();
        return view('batterysample.batteryaccessoriesview', $data);
    }




    public function batteryaccessoriesviewadd(Request $request)
    {

        foreach ($request->batteryaccessoriesId as $index => $accessoriesId) {
            $value = isset($request["batteryaccessoriesValue{$accessoriesId}"]) ? $request["batteryaccessoriesValue{$accessoriesId}"] : 0;

            $btrinstallview = Battery_accessories_view_sheet::where('batteryaccessories_id', $accessoriesId)
                ->where('itemid', $request->batterypriceId)
                ->first();

            if ($btrinstallview) {

                $btrinstallview->batteryaccessories_value = $value;
            } else {

                $btrinstallview = new Battery_accessories_view_sheet;
                $btrinstallview->itemid = $request->batterypriceId;
                $btrinstallview->batteryaccessories_id = $accessoriesId;
                $btrinstallview->batteryaccessories_value = $value;
                $btrinstallview->ip = request()->ip();
                $btrinstallview->strtotime = Carbon::now()->timezone('Asia/Kolkata')->timestamp;
            }


            $btrinstallview->save();
        }

        return redirect()->back()->with('success', 'Battery Accessories values updated successfully.');
    }
}
