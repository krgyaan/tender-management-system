<?php

namespace App\Http\Controllers;

use File;
use Carbon\Carbon;
use App\Models\Dueemi;
use App\Models\Tdsrecovery;
use App\Models\Loanadvances;
use App\Models\Loanpartname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanAdvancesController extends Controller
{
    public function loanadvances()
    {
        if (Auth::user()->role == 'admin') {
            // Get the total_loan, total_paid group by loanparty_name.
            $data['loan_summary'] = Loanadvances::selectRaw('loanadvances.bank_name as bank_name, SUM(loanamount) as total_loan, SUM(dueemis.principle_paid) as total_paid')
                ->leftJoin('dueemis', 'loanadvances.id', '=', 'dueemis.loneid')
                ->leftJoin('loanpartnames', 'loanadvances.loanparty_name', '=', 'loanpartnames.id')
                ->groupBy('loanadvances.bank_name')
                ->get();
        }

        $data['loanadvances'] = Loanadvances::where('status', '1')->with('loanadvances', 'dueemi')->get();
        return view('loanadvances.loanadvances', $data);
    }

    public function loanadvancesadd()
    {
        $data['loanpartname'] =  Loanpartname::where('status', '1')->get();
        return view('loanadvances.loanadvancesadd', $data);
    }

    public function loanadvancescreate(Request $request)
    {
        $request->validate([
            'loanparty_name' => 'required',
            'bank_name' => 'required',
            'typeof_loan' => 'required',
            'loac_acc_no' => 'required',
            'loanamount' => 'required|numeric',
            'sanctionletter_date' => 'required|date',
            'emipayment_date' => 'required|date',
            'lastemi_date' => 'required|date',
            'sanction_letter' => 'required|mimes:jpg,png,xlsx,xls,csv,pdf|max:10240',
            'bankloan_schedule' => 'required|mimes:jpg,png,xlsx,xls,csv,pdf|max:10240',
            // Only require loan_schedule if schedule_type is 'file'
            'loan_schedule' => $request->schedule_type === 'file' ? 'required|mimes:jpg,png,xlsx,xls,csv,pdf|max:10240' : 'nullable',
            // Only require loan_schedule_url if schedule_type is 'url'
            'loan_schedule_url' => $request->schedule_type === 'url' ? 'required|url' : 'nullable',
            'chargemca_website' => 'nullable|string',
            'tdstobedeductedon_interest' => 'nullable|string',
        ]);

        try {
            $loanadvances = new Loanadvances();
            $loanadvances->loanparty_name = $request->loanparty_name;
            $loanadvances->bank_name = $request->bank_name;
            $loanadvances->loac_acc_no = $request->loac_acc_no;
            $loanadvances->typeof_loan = $request->typeof_loan;
            $loanadvances->loanamount = $request->loanamount;
            $loanadvances->sanctionletter_date = $request->sanctionletter_date;
            $loanadvances->emipayment_date = $request->emipayment_date;
            $loanadvances->lastemi_date = $request->lastemi_date;
            $loanadvances->chargemca_website = $request->chargemca_website;
            $loanadvances->tdstobedeductedon_interest = $request->tdstobedeductedon_interest;

            if ($request->hasFile('sanction_letter')) {
                $img = time() . 'sanction_letter.' . $request->sanction_letter->extension();
                $request->sanction_letter->move(public_path('upload/loanadvances'), $img);
                $loanadvances->sanction_letter = $img;
            }

            if ($request->hasFile('bankloan_schedule')) {
                $img = time() . 'bankloan_schedule.' . $request->bankloan_schedule->extension();
                $request->bankloan_schedule->move(public_path('upload/loanadvances'), $img);
                $loanadvances->bankloan_schedule = $img;
            }

            // Handle loan schedule based on type
            if ($request->schedule_type === 'file' && $request->hasFile('loan_schedule')) {
                $img = time() . 'loan_schedule.' . $request->loan_schedule->extension();
                $request->loan_schedule->move(public_path('upload/loanadvances'), $img);
                $loanadvances->loan_schedule = $img;
            } elseif ($request->schedule_type === 'url') {
                $loanadvances->loan_schedule = $request->loan_schedule_url;
            }

            $loanadvances->ip = $request->ip();
            $loanadvances->strtotime = Carbon::now()->timezone('Asia/Kolkata')->timestamp;
            $loanadvances->save();

            return redirect(route('loanadvances'))->with('success', 'Data successfully added.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    public function loanadvancesdelete($id)
    {
        try {
            $loanadvancesdelete = Loanadvances::findOrFail($id);

            // Delete the loan advance record
            $loanadvancesdelete->delete();

            // Delete the associated files if they exist
            $image_path_loan_schedule = public_path("upload/loanadvances/{$loanadvancesdelete->loan_schedule}");
            $image_path_bankloan_schedule = public_path("upload/loanadvances/{$loanadvancesdelete->bankloan_schedule}");
            $image_path_sanction_letter = public_path("upload/loanadvances/{$loanadvancesdelete->sanction_letter}");

            if (isset($loanadvancesdelete->loan_schedule) && File::exists($image_path_loan_schedule) && !File::isDirectory($image_path_loan_schedule)) {
                unlink($image_path_loan_schedule);
            }

            if (isset($loanadvancesdelete->bankloan_schedule) && File::exists($image_path_bankloan_schedule) && !File::isDirectory($image_path_bankloan_schedule)) {
                unlink($image_path_bankloan_schedule);
            }

            if (isset($loanadvancesdelete->sanction_letter) && File::exists($image_path_sanction_letter) && !File::isDirectory($image_path_sanction_letter)) {
                unlink($image_path_sanction_letter);
            }

            return redirect()->back()->with('success', 'Data successfully Delete.');;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function loanadvancesupdate(Request $request)
    {
        $data['loanpartname'] =  Loanpartname::where('status', '1')->get();
        $data['loanadvances'] = Loanadvances::where('id', $request->id)->first();
        return view('loanadvances.loanadvancesedit', $data);
    }

    public function loanadvancesedit(Request $request)
    {
        $loanadvances = Loanadvances::find($request->id);
        if (!$loanadvances) {
            return redirect()->back()->with('error', 'Loan advance not found.');
        }

        $loanadvances->loanparty_name = $request->loanparty_name;
        $loanadvances->bank_name = $request->bank_name;
        $loanadvances->loac_acc_no = $request->loac_acc_no;
        $loanadvances->typeof_loan = $request->typeof_loan;
        $loanadvances->loanamount = $request->loanamount;
        $loanadvances->sanctionletter_date = $request->sanctionletter_date;
        $loanadvances->emipayment_date = $request->emipayment_date;
        $loanadvances->lastemi_date = $request->lastemi_date;
        $loanadvances->chargemca_website = $request->chargemca_website;
        $loanadvances->tdstobedeductedon_interest = $request->tdstobedeductedon_interest;

        try {
            if ($request->hasFile('sanction_letter')) {
                $img = time() . 'sanction_letter.' . $request->sanction_letter->extension();
                $request->sanction_letter->move(public_path('upload/loanadvances'), $img);
                $loanadvances->sanction_letter = $img;
            }

            if ($request->hasFile('bankloan_schedule')) {
                $img = time() . 'bankloan_schedule.' . $request->bankloan_schedule->extension();
                $request->bankloan_schedule->move(public_path('upload/loanadvances'), $img);
                $loanadvances->bankloan_schedule = $img;
            }

            // Handle loan schedule based on type
            if ($request->schedule_type === 'file' && $request->hasFile('loan_schedule')) {
                $img = time() . 'loan_schedule.' . $request->loan_schedule->extension();
                $request->loan_schedule->move(public_path('upload/loanadvances'), $img);
                $loanadvances->loan_schedule = $img;
            } elseif ($request->schedule_type === 'url') {
                $loanadvances->loan_schedule = $request->loan_schedule_url;
            }

            $loanadvances->save();
            return redirect(route('loanadvances'))->with('success', 'Data successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating data: ' . $e->getMessage());
        }
    }

    public function dueview($id)
    {
        $loneid = Loanadvances::where('id', $id)->first();
        $data = Dueemi::where('status', '1')->where('loneid', $id)->get();
        return view('loanadvances.dueview', ['due_id' => $id, 'viewdata' => $data, 'loneid' => $loneid]);
    }


    public function dueemiadd(Request $request)
    {

        if (!$loneid = Loanadvances::where('id', $request->loneid)->first()) {
            return redirect()->back()->with('error', 'Loan advance not found.');
        }

        try {
            $loneid->emipayment_date = $request->emi_date;
            $loneid->save();

            $dueemi = new Dueemi();
            $dueemi->loneid = $request->loneid;
            $dueemi->emi_date = $request->emi_date;
            $dueemi->principle_paid = $request->principle_paid;
            $dueemi->interest_paid = $request->interest_paid;
            $dueemi->tdstobe_recovered = $request->tdstobe_recovered;
            $dueemi->penal_charges_paid = $request->penal_charges_paid;
            $dueemi->ip = $_SERVER['REMOTE_ADDR'];
            $dueemi->strtotime = Carbon::parse($dueemi->strtotime)->timezone('Asia/Kolkata')->timestamp;
            $dueemi->save();
            return redirect()->back()->with('success', 'Data successfully added.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while adding data: ' . $e->getMessage());
        }
    }

    public function dueemiupdate($id)
    {
        $datafirst = Dueemi::where('id', $id)->first();
        $data = Dueemi::where('status', '1')->where('loneid', $datafirst->loneid)->get();
        $loneid = Loanadvances::where('id', $datafirst->loneid)->first();
        return view('loanadvances.dueview', ['updatedata' => $datafirst, 'viewdata' => $data, 'loneid' => $loneid]);
    }

    public function dueemiupdatepost(Request $request, $id)
    {
        $datafirst = Dueemi::where('id', $id)->first();
        if (!$datafirst) {
            return redirect()->back()->with('error', 'Due EMI not found.');
        }

        $loneid = Loanadvances::where('id', $datafirst->loneid)->first();
        if (!$loneid) {
            return redirect()->back()->with('error', 'Loan Advance not found.');
        }

        try {
            $loneid->emipayment_date = $request->emi_date;
            $loneid->save();

            $datafirst->emi_date = $request->emi_date;
            $datafirst->principle_paid = $request->principle_paid;
            $datafirst->interest_paid = $request->interest_paid;
            $datafirst->tdstobe_recovered = $request->tdstobe_recovered;
            $datafirst->penal_charges_paid = $request->penal_charges_paid;
            $datafirst->save();

            return redirect()->route('dueview', ['id' => $datafirst->loneid])->with('success', 'Data successfully Update.');;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating data: ' . $e->getMessage());
        }
    }

    public function dueemidelete($id)
    {
        $duedelete = Dueemi::findOrFail($id);
        $duedelete->delete();
        return redirect()->back()->with('success', 'Data successfully Delete.');;
    }

    public function tdsrecoveryview($id)
    {
        $data = Tdsrecovery::where('status', '1')->where('loneid', $id)->get();
        return view('loanadvances.tdsrecoveryview', ['due_id' => $id, 'viewdata' => $data]);
    }

    public function tdsrecoveryadd(Request $request)
    {
        $tdsrecovery = new Tdsrecovery();
        $tdsrecovery->loneid = $request->loneid;
        $tdsrecovery->tds_amount = $request->tds_amount;

        if ($request->hasFile('tds_document')) {
            $tds_document = $request->file('tds_document');
            if ($tds_document->isValid()) {
                $img = time() . 'tdsdocument.' . $tds_document->extension();
                $tds_document->move(public_path('upload/tdsrecovery'), $img);
                $tdsrecovery->tds_document = $img;
            } else {
                return redirect()->back()->with('error', 'File is not valid.');
            }
        }

        $tdsrecovery->tds_date = $request->tds_date;
        $tdsrecovery->tdsrecoverybank_details = $request->tdsrecoverybank_details;

        try {
            $tdsrecovery->ip = $_SERVER['REMOTE_ADDR'];
            $tdsrecovery->strtotime = Carbon::parse($tdsrecovery->strtotime)->timezone('Asia/Kolkata')->timestamp;
            $tdsrecovery->save();

            return redirect()->back()->with('success', 'Data successfully added.');;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while adding data: ' . $e->getMessage());
        }
    }


    public function tdsrecoveryupdate($id)
    {
        $datafirst = Tdsrecovery::where('id', $id)->first();
        $data = Tdsrecovery::where('status', '1')->where('loneid', $datafirst->loneid)->get();

        return view('loanadvances.tdsrecoveryview', ['updatedata' => $datafirst, 'viewdata' => $data]);
    }


    public function tdsrecoveryupdatepost(Request $request, $id)
    {
        $datafirst = Tdsrecovery::where('id', $id)->first();

        if (!$datafirst) {
            return redirect()->back()->with('error', 'TDS Recovery not found.');
        }

        $datafirst->loneid = $request->loneid;
        $datafirst->tds_amount = $request->tds_amount;

        if ($request->hasFile('tds_document')) {
            $tds_document = $request->file('tds_document');
            if ($tds_document->isValid()) {
                $img = time() . 'tdsdocument.' . $tds_document->extension();
                $tds_document->move(public_path('upload/tdsrecovery'), $img);
                $datafirst->tds_document = $img;
            } else {
                return redirect()->back()->with('error', 'File is not valid.');
            }
        }

        $datafirst->tds_date = $request->tds_date;
        $datafirst->tdsrecoverybank_details = $request->tdsrecoverybank_details;

        try {
            $datafirst->save();
            return redirect()->route('tdsrecoveryview', ['id' => $datafirst->loneid])->with('success', 'Data successfully Update.');;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating data: ' . $e->getMessage());
        }
    }

    public function tdsrecoverydelete($id)
    {
        $duedelete = Tdsrecovery::findOrFail($id);
        $duedelete->delete();
        return redirect()->back()->with('success', 'Data successfully Delete.');;
    }

    public function loancloseupdate($id)
    {
        $data = Loanadvances::where('id', $id)->first();
        return view('loanadvances.loancloseupdate', ['loanclose' => $data]);
    }

    public function loancloseupdate_post(Request $request)
    {
        $loanclose = Loanadvances::find($request->id);

        if (!$loanclose) {
            return redirect()->back()->with('error', 'Loan record not found.');
        }

        $loanclose->loan_close_status = '1';

        try {
            if ($request->hasFile('banknoc_document')) {
                $banknoc_document = $request->file('banknoc_document');
                $img = time() . 'loanclose.' . $banknoc_document->extension();
                $banknoc_document->move(public_path('upload/loanclose'), $img);
                $loanclose->banknoc_document = $img;
            }

            if ($request->hasFile('closurecreated_mca')) {
                $closurecreated_mca = $request->file('closurecreated_mca');
                $img = time() . 'loanclose.' . $closurecreated_mca->extension();
                $closurecreated_mca->move(public_path('upload/loanclose'), $img);
                $loanclose->closurecreated_mca = $img;
            }

            $loanclose->save();
            return redirect(route('loanadvances'))->with('success', 'Data successfully updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating data: ' . $e->getMessage());
        }
    }
}
