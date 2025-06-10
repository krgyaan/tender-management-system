<?php

namespace App\Http\Controllers;

use URL;
use File;
use Crypt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Clintdirectory;
use Validator, Redirect, Response;
use App\Http\Requests\StoreClientDirectoryRequest;
use App\Http\Requests\UpdateClientDirectoryRequest;

class ClientDirectoryController extends Controller
{
    public function clientdirectory()
    {
        $data['clientdirectory'] = Clintdirectory::where('status', '1')->get();
        return view('clientdirectory.clientdirectory', $data);
    }

    public function clientdirectoryadd()
    {
        return view('clientdirectory.clientdirectoryadd');
    }
    public function clientdirectorycreate(Request $request)
    {
        $request->validate([
            'phone_no' => 'required',
        ]);

        $clintdata = new Clintdirectory();
        $clintdata->organization = $request->organization;
        $clintdata->name = $request->name;
        $clintdata->designation = $request->designation;
        $clintdata->phone_no = $request->phone_no;
        $clintdata->email = $request->email;
        $clintdata->ip = $_SERVER['REMOTE_ADDR'];
        $clintdata->strtotime = Carbon::parse($clintdata->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $clintdata->save();

        return redirect(route('clientdirectory'));
    }
    public function clientdirectorydelete($id)
    {
        $id = Crypt::decrypt($id);
        $clientdirectorydelete = Clintdirectory::findOrFail($id);
        $clientdirectorydelete->delete();
        return redirect()->back();
    }

    public function clientdirectoryupdate(Request $request)
    {
        $data['clientdirectory'] = Clintdirectory::where('id', Crypt::decrypt($request->id))->first();
        return view('clientdirectory.clientdirectoryedit', $data);
    }
    public function clientdirectoryedit(Request $request)
    {
        $clientdirectory = Clintdirectory::where('id', $request->id)->first();
        $clientdirectory->organization = $request->organization;
        $clientdirectory->name = $request->name;
        $clientdirectory->designation = $request->designation;
        $clientdirectory->phone_no = $request->phone_no;
        $clientdirectory->email = $request->email;
        $clientdirectory->save();

        return redirect(route('clientdirectory'));
    }
}
