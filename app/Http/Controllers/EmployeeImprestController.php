<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Employeeimprest;
use App\Models\Employeeimprestamount;
use App\Models\Pqr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmployeeImprestController extends Controller
{
    public function employeeimprest()
    {
        if (Auth::user()->role == 'admin' || Auth::user()->role == 'account') {
            $data['employeeamount'] = EmployeeImprest::sum('amount');
            $data['amountrecevied'] = Employeeimprestamount::sum('amount');
            $data['amountapproved'] = EmployeeImprest::where('buttonstatus', '1')->sum('amount');
            $data['amountspent'] = $data['amountapproved'] - $data['amountrecevied'];
        } else {
            $data['employeeamount'] = EmployeeImprest::where('name_id', Auth::user()->id)->sum('amount');
            $data['amountrecevied'] = Employeeimprestamount::where('name_id', Auth::user()->id)->sum('amount');
            $data['amountapproved'] = EmployeeImprest::where('name_id', Auth::user()->id)->where('buttonstatus', '1')->sum('amount');
            $data['amountspent'] = $data['amountapproved'] - $data['amountrecevied'];
        }

        // $data['employeeimprest'] = Employeeimprest::with('user')->with('category')->with('team')->get();
        $data['employeeimprest'] = Employeeimprest::with('user')->with('category')->with('team')->orderBy('id', 'desc')->get();
        return view('employeeimprest.employee_imprest_view', $data);
    }

    public function employeeimprest_add()
    {
        $category = Category::where('status', '1')->get();
        $user = User::where('status', '1')->get();
        return view('employeeimprest.employee_imprest', ['category' => $category, 'user' => $user]);
    }

    public function employeeimprest_post(Request $request)
    {
        Log::info('Starting employee imprest post process', $request->all());
        $request->validate([
            'name_id' => 'required',
            'party_name' => 'nullable|string|max:255',
            'project_name' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0',
            'category' => 'required|exists:categories,id',
            'remark' => 'nullable|string|max:500',
        ]);

        try {
            Log::info('Starting employee imprest post process', $request->all());

            if ($request->category == '22' && $request->team_id) {
                $name = User::where('id', $request->team_id)->first()->name ?? 'Unknown';
                $from = User::where('id', $request->name_id)->first()->name ?? 'Unknown';
                $employeeaccount_add = new Employeeimprestamount();
                $employeeaccount_add->name_id = $request->team_id;
                $employeeaccount_add->date = date('Y-m-d');
                $employeeaccount_add->team_member_name = $name;
                $employeeaccount_add->amount = $request->amount;
                $employeeaccount_add->project_name = "Transfered from $from";
                $employeeaccount_add->ip = $_SERVER['REMOTE_ADDR'];
                $employeeaccount_add->strtotime = Carbon::parse($employeeaccount_add->strtotime)->timezone('Asia/Kolkata')->timestamp;
                $employeeaccount_add->save();
                Log::info('Employee imprest amount added for team transfer', ['team_id' => $request->team_id]);
            }

            $employeeimprest_add = new Employeeimprest();
            $employeeimprest_add->name_id = $request->name_id;
            $employeeimprest_add->party_name = $request->category == 22 ? null : $request->party_name;
            $employeeimprest_add->project_name = $request->category == 22 ? null : $request->project_name;
            $employeeimprest_add->amount = $request->amount;
            $employeeimprest_add->category_id = $request->category;
            $employeeimprest_add->team_id = $request->team_id;
            $employeeimprest_add->remark = $request->remark;
            $employeeimprest_add->ip = $_SERVER['REMOTE_ADDR'];
            $employeeimprest_add->strtotime = Carbon::parse($employeeimprest_add->strtotime)->timezone('Asia/Kolkata')->timestamp;

            if ($request->hasFile('invoice_proof')) {
                $invoiceProofFiles = $request->file('invoice_proof');
                $imagePaths = [];

                foreach ($invoiceProofFiles as $file) {
                    $img = time() . rand(1000, 9999) . '.' . $file->extension();
                    $file->move(public_path('/uploads/employeeimprest/'), $img);
                    $imagePaths[] = $img;
                }
                $employeeimprest_add->invoice_proof = json_encode($imagePaths);
                Log::info('Invoice proof files uploaded', ['invoice_proof' => $imagePaths]);
            }

            $employeeimprest_add->save();
            Log::info('Employee imprest record saved successfully', ['name_id' => $request->name_id]);

            return redirect()->back()->with('success', 'Employee Imprest added successfully');
        } catch (\Exception $e) {
            Log::error('Error in employee imprest post process', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function employeeimprest_delete($id)
    {
        $employeeimprest_delete = Employeeimprest::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'Employee Imprest deleted successfully');
    }

    public function employeeimprest_edit(Request $request)
    {
        try {
            Log::info('Starting employee imprest edit process by ', [Auth::user()->name]);
            $category = Category::where('status', '1')->get();
            $user = User::where('status', '1')->get();
            $employeeimprest_update = Employeeimprest::where('id', $request->id)->first();

            if (!$employeeimprest_update) {
                Log::error('Error in employee imprest edit process', ['error' => 'Employee record not found.']);
                return redirect()->back()->with('error', 'Employee record not found.');
            }

            return view(
                'employeeimprest.employee_imprest_edit',
                ['imprest' => $employeeimprest_update, 'user' => $user, 'category' => $category]
            );
        } catch (\Exception $e) {
            Log::error('Error in employee imprest edit process', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function employeeimprest_update(Request $request)
    {
        Log::info('Starting employee imprest update process by ', [Auth::user()->name]);
        try {
            $update = Employeeimprest::where('id', $request->id)->first();

            if (!$update) {
                Log::error('Error in employee imprest update process', ['error' => 'Employee record not found.']);
                return redirect()->back()->with('error', 'Employee record not found.');
            }

            $update->name_id = $request->name_id ?? $update->name_id;
            $update->party_name = $request->party_name ?? $update->party_name;
            $update->project_name = $request->project_name ?? $update->project_name;
            $update->amount = $request->amount ?? $update->amount;
            $update->category_id = $request->category ?? $update->category_id;
            $update->team_id = $request->team_id ?? $update->team_id;
            $update->remark = $request->remark ?? $update->remark;

            if ($request->hasFile('invoice_proof')) {
                Log::info('Invoice proof files received', ['invoice_proof' => $request->invoice_proof]);
                $invoiceProofFiles = $request->file('invoice_proof');
                $existingProofs = $update->invoice_proof ? json_decode($update->invoice_proof, true) : [];
                $imagePaths = $existingProofs;

                foreach ($invoiceProofFiles as $file) {
                    if (!$file) {
                        continue;
                    }
                    $img = time() . rand(1000, 9999) . '.' . $file->extension();
                    $file->move(public_path('/uploads/employeeimprest/'), $img);
                    $imagePaths[] = $img;
                }
                Log::info('Invoice proof files uploaded', ['invoice_proof' => $imagePaths]);
                $update->invoice_proof = json_encode($imagePaths);
            }

            $update->save();
            Log::info('Employee imprest record updated successfully', ['name_id' => $request->name_id]);
            return redirect()->back()->with('success', 'Employee Imprest updated successfully');
        } catch (\Exception $e) {
            Log::error('Error in employee imprest update process', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function deleteProof(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'proof' => 'required|string',
        ]);

        $imprest = Employeeimprest::find($request->id);
        if (!$imprest) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $proofs = $imprest->invoice_proof ? json_decode($imprest->invoice_proof, true) : [];
        $proofToDelete = $request->proof;

        if (($key = array_search($proofToDelete, $proofs)) !== false) {
            unset($proofs[$key]);
            // Remove file from storage if needed
            $filePath = public_path("uploads/employeeimprest/{$proofToDelete}");
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            $imprest->invoice_proof = json_encode(array_values($proofs));
            $imprest->save();
            Log::info('Proof deleted by ', [Auth::user()->name]);
            return response()->json(['success' => true, 'message' => 'Proof deleted.']);
        }

        return response()->json(['success' => false, 'message' => 'Proof not found.'], 404);
    }

    public function employeeimprest_account(Request $request)
    {
        $data['employeeamount'] = EmployeeImprest::sum('amount');
        $data['amountrecevied'] = Employeeimprestamount::sum('amount');
        $data['amountapproved'] = EmployeeImprest::where('buttonstatus', '1')->sum('amount');
        $data['amountspent'] = $data['amountapproved'] - $data['amountrecevied'];
        $data['employeeimprest'] = EmployeeImprest::select('name_id')->groupBy('name_id')->get();
        return view('employeeimprest.employee_imprest_account_view', $data);
    }

    public function employeeimprest_amount_post(Request $request)
    {
        Log::info('Employee imprest paying to ', ['name_id' => $request->name_id, 'amount' => $request->amount]);
        try {
            $validated = $request->validate([
                'name_id' => 'required',
                'date' => 'required',
                'team_member_name' => 'required',
                'amount' => 'required',
                'project_name' => 'nullable',
            ]);

            $employeeaccount_add = new Employeeimprestamount();
            $employeeaccount_add->name_id = $request->name_id;
            $employeeaccount_add->date = $request->date;
            $employeeaccount_add->team_member_name = $request->team_member_name;
            $employeeaccount_add->amount = $request->amount;
            $employeeaccount_add->project_name = $request->project_name ?? 'Transferred to ' . $request->name_id;
            $employeeaccount_add->ip = $_SERVER['REMOTE_ADDR'];
            $employeeaccount_add->strtotime = Carbon::parse($employeeaccount_add->strtotime)->timezone('Asia/Kolkata')->timestamp;

            if ($employeeaccount_add->save()) {
                Log::info('Employee imprest amount added successfully', ['name_id' => $request->name_id]);
                return redirect()->back()->with('success', 'Employee Imprest Account added successfully');
            } else {
                Log::error('Error in employee imprest amount post process', ['error' => 'Unable to add employee imprest amount']);
                return redirect()->back()->with('error', 'Unable to add employee imprest amount');
            }
        } catch (\Exception $e) {
            Log::error('Error in employee imprest amount post process', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function employeeimprest_account_delete($id)
    {
        $employeeimprest_account_delete = Employeeimprestamount::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'Employee Imprest Account deleted successfully');
    }

    public function employeeimprest_account_edit(Request $request)
    {
        $employeeimprest = User::where('status', '1')->get();
        $employeeimprest_update = Employeeimprestamount::where('id', Crypt::decrypt($request->id))->first();
        return view('employeeimprest.employee_imprest_account_edit', ['employeeimprest_update' => $employeeimprest_update, 'employeeimprest' => $employeeimprest]);
    }

    public function employeeimprest_account_update(Request $request)
    {
        $employeeupdate = Employeeimprestamount::where('id', $request->id)->first();
        $employeeupdate->name_id = $request->name_id;
        $employeeupdate->date = $request->date;
        $employeeupdate->team_member_name = $request->team_member_name;
        $employeeupdate->amount = $request->amount;
        $employeeupdate->project_name = $request->project_name;
        $employeeupdate->save();
        return redirect()->back()->with('success', 'Employee Imprest Account updated successfully');
    }

    public function employeeimprest_dashboard($id)
    {
        $data['employee'] = EmployeeImprest::where('name_id', $id)->with('project')->orderBy('id', 'desc')->get();
        $data['name_id'] = $id;
        $data['name'] = User::where('id', $id)->first()->name;
        $data['amtReceived'] = EmployeeImprest::getAmtReceived($id);
        $data['amtSpent'] = EmployeeImprest::getAmtSpent($id);
        $data['amtApproved'] = EmployeeImprest::getAmtApproved($id);
        $data['amtLeft'] = EmployeeImprest::getAmtLeft($id);
        return view('employeeimprest.view_dashboard', $data);
    }

    public function employee_status(Request $request)
    {
        $employeeId = $request->input('id');
        $status = $request->input('buttonstatus');
        $project = EmployeeImprest::find($employeeId);

        if ($project) {
            $project->buttonstatus = $status;
            $project->approved_date = Carbon::now();
            $project->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Project not found.']);
    }

    public function tally_status(Request $request)
    {
        $employeeId = $request->input('id');
        $status = $request->input('tallystatus');
        $project = EmployeeImprest::find($employeeId);

        if ($project) {
            $project->tallystatus = $status;
            $project->save();
            return response()->json(['success' => true, 'message' => 'Tally Status updated successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Project not found.']);
    }

    public function proof_status(Request $request)
    {
        $employeeId = $request->input('id');
        $status = $request->input('proofstatus');
        $project = EmployeeImprest::find($employeeId);

        if ($project) {
            $project->proofstatus = $status;
            $project->save();
            return response()->json(['success' => true, 'message' => 'Proof Filled Status updated successfully']);
        }
        return response()->json(['success' => false, 'message' => 'Project not found.']);
    }

    public function updateStatus(Request $request)
    {
        $imprest = EmployeeImprest::find($request->id);
        if ($imprest) {
            $imprest->status = $request->status;
            $imprest->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Imprest not found']);
    }

    public function get_proof($id)
    {
        $employeeImprest = Employeeimprest::find($id);
        if (!$employeeImprest) {
            return response()->json(['error' => 'Employee record not found.'], 404);
        }
        $existingInvoiceProof = $employeeImprest->invoice_proof ? json_decode($employeeImprest->invoice_proof) : [];
        return response()->json(['proofs' => $existingInvoiceProof]);
    }

    public function add_proof(Request $request)
    {
        $update = Employeeimprest::where('id', $request->id)->first();

        if (!$update) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }
        $existingInvoiceProof = $update->invoice_proof ? json_decode($update->invoice_proof) : [];
        $imagePaths = $existingInvoiceProof;

        if ($request->hasFile('invoice_proof')) {
            $invoiceProofFiles = $request->file('invoice_proof');
            foreach ($invoiceProofFiles as $file) {
                $img = time() . rand(1000, 9999) . '.' . $file->extension();
                $file->move(public_path('/uploads/employeeimprest/'), $img);
                $imagePaths[] = $img;
            }
        }
        $update->invoice_proof = json_encode($imagePaths);
        $update->save();
        return redirect()->back()->with('success', 'Proofs updated successfully.');
    }

    public function employeeimprest_amount_project(Request $request)
    {
        $projects = Employeeimprest::where('name_id', $request->name_id)->get();
        $option = "";
        foreach ($projects as $project) {
            $option .= '<option value="' . $project->id . '">' . $project->project_name . '</option>';
        }
        echo $option;
    }

    public function employeeimprest_remark(Request $request)
    {
        try {
            $remark = EmployeeImprest::where('id', $request->id)->first();
            $remark->acc_remark = $request->acc_remark;
            $remark->save();
            return redirect()->back()->with('success', 'Remark updated successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function pqr_dashboard()
    {
        $ac_data = Pqr::where('team_name', 'AC')->get();
        $dc_data = Pqr::where('team_name', 'DC')->get();
        return view('employeeimprest.pqr_dashboard_view', ['ac_data' => $ac_data, 'dc_data' => $dc_data]);
    }

    public function pqr_dashboard_add()
    {
        return view('employeeimprest.pqr_dashboard_add');
    }

    public function pqr_dashboard_post(Request $request)
    {
        $pqr_add = new Pqr();
        $pqr_add->team_name = $request->team_name;
        $pqr_add->project_name = $request->project_name;
        $pqr_add->value = $request->value;
        $pqr_add->item = $request->item;
        $pqr_add->po_date = $request->po_date;
        if ($request->uplode_po) {
            $img1 = time() . '.' . $request->uplode_po->extension();
            $request->uplode_po->move(public_path('uploads/pqr/'), $img1);
            $pqr_add->uplode_po = $img1;
            $pqr_add->save();
        }

        $pqr_add->sap_gem_po_date = $request->sap_gem_po_date;
        if ($request->uplode_sap_gem_po) {
            $img2 = time() . '.' . $request->uplode_sap_gem_po->extension();
            $request->uplode_sap_gem_po->move(public_path('uploads/pqr/'), $img2);
            $pqr_add->uplode_sap_gem_po = $img2;
            $pqr_add->save();
        }

        $pqr_add->completion_date = $request->completion_date;

        if ($request->uplode_completion) {
            $img3 = time() . '.' . $request->uplode_completion->extension();
            $request->uplode_completion->move(public_path('uploads/pqr/'), $img3);
            $pqr_add->uplode_completion = $img3;
            $pqr_add->save();
        }
        if ($request->performace_cretificate) {
            $img4 = time() . '.' . $request->performace_cretificate->extension();
            $request->performace_cretificate->move(public_path('uploads/pqr/'), $img4);
            $pqr_add->performace_cretificate = $img4;
            $pqr_add->save();
        }
        $pqr_add->remarks = $request->remarks;
        $pqr_add->ip = $_SERVER['REMOTE_ADDR'];
        $pqr_add->strtotime = Carbon::parse($pqr_add->strtotime)->timezone('Asia/Kolkata')->timestamp;
        $pqr_add->save();
        return redirect(route('pqr_dashboard'))->with('success', 'PQR added successfully');
    }

    public function pqr_delete($id)
    {
        $pqr_delete = Pqr::where('id', Crypt::decrypt($id))->delete();
        return redirect()->back()->with('success', 'PQR deleted successfully');
    }

    public function pqr_edit(Request $request)
    {
        $pqr_edit = Pqr::where('id', Crypt::decrypt($request->id))->first();
        return view('employeeimprest.pqr_dashboard_edit', ['pqr_edit' => $pqr_edit]);
    }

    public function pqr_dashboard_edit(Request $request)
    {
        $pqrupdate = Pqr::where('id', $request->id)->first();

        $pqrupdate->team_name = $request->team_name;
        $pqrupdate->project_name = $request->project_name;
        $pqrupdate->value = $request->value;
        $pqrupdate->item = $request->item;
        $pqrupdate->po_date = $request->po_date;
        if ($request->uplode_po) {
            $img1 = time() . '.' . $request->uplode_po->extension();
            $request->uplode_po->move(public_path('uploads/pqr/'), $img1);
            $pqrupdate->uplode_po = $img1;
            $pqrupdate->save();
        }

        $pqrupdate->sap_gem_po_date = $request->sap_gem_po_date;
        if ($request->uplode_sap_gem_po) {
            $img2 = time() . '.' . $request->uplode_sap_gem_po->extension();
            $request->uplode_sap_gem_po->move(public_path('uploads/pqr/'), $img2);
            $pqrupdate->uplode_sap_gem_po = $img2;
            $pqrupdate->save();
        }

        $pqrupdate->completion_date = $request->completion_date;

        if ($request->uplode_completion) {
            $img3 = time() . '.' . $request->uplode_completion->extension();
            $request->uplode_completion->move(public_path('uploads/pqr/'), $img3);
            $pqrupdate->uplode_completion = $img3;
            $pqrupdate->save();
        }
        if ($request->performace_cretificate) {
            $img4 = time() . '.' . $request->performace_cretificate->extension();
            $request->performace_cretificate->move(public_path('uploads/pqr/'), $img4);
            $pqrupdate->performace_cretificate = $img4;
            $pqrupdate->save();
        }
        $pqrupdate->remarks = $request->remarks;

        $pqrupdate->save();
        return redirect(route('pqr_dashboard'))->with('success', 'PQR updated successfully');
    }

    public function upload_po(Request $request)
    {
        $upload_po = Pqr::where('id', $request->pq_id)->first();
        if ($request->hasFile('upload_po')) {
            $existingImages = $upload_po->uplode_po ? json_decode($upload_po->uplode_po, true) : [];
            foreach ($request->file('upload_po') as $file) {
                $imgName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/pqr/'), $imgName);
                $existingImages[] = $imgName;
            }

            $upload_po->uplode_po = json_encode($existingImages);
            $upload_po->save();
        }
        return redirect()->back()->with('success', 'PO uploaded successfully.');
    }

    public function ac_upload_po(Request $request)
    {
        $upload_po = Pqr::where('id', $request->ac_id)->first();

        if ($request->hasFile('upload_po')) {
            $existingImages = $upload_po->uplode_po ? json_decode($upload_po->uplode_po, true) : [];
            foreach ($request->file('upload_po') as $file) {
                $imgName = time() . '_' . uniqid() . '.' . $file->extension();
                $file->move(public_path('uploads/pqr/'), $imgName);
                $existingImages[] = $imgName;
            }
            $upload_po->uplode_po = json_encode($existingImages);
            $upload_po->save();
        }
        return redirect()->back()->with('success', 'PO uploaded successfully.');
    }

    public function categories()
    {
        $categorydata =  Category::get();
        return view('master.categories', ['categorydata' => $categorydata]);
    }

    public function categories_add(Request $request)
    {
        $request->validate([
            'category' => 'required|unique:categories,category',
            'heading' => 'required',
        ], [
            'category.unique' => 'The category name has already been taken. Please choose a different one.',
        ]);
        $category = new Category();
        $category->category = $request->category;
        $category->heading = $request->heading;
        $category->ip = $_SERVER['REMOTE_ADDR'];

        $category->save();
        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function category_del($id)
    {
        $categorydel = Category::where('id', Crypt::decrypt($id))->first();
        $categorydel->status = $categorydel->status == 0 ? 1 : 0;
        $categorydel->save();
        return redirect()->back()->with('success', 'Category status updated successfully');
    }

    public function category_edit(Request $request)
    {
        $update = Category::where('id', $request->id)->first();
        $update->category = $request->category;
        $update->heading = $request->heading;
        $update->save();
        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function dateFilter(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'name_id' => 'nullable|exists:users,id',
            ], [
                'start_date.required' => 'Please select start date',
                'end_date.required' => 'Please select end date',
                'end_date.after_or_equal' => 'End date must be greater than or equal to start date',
                'name_id.exists' => 'Invalid user selected',
            ]);

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $name_id = $request->name_id;

            if ($start_date == null || $end_date == null) {
                return redirect()->back()->with('error', 'Please select start and end date');
            }

            $data['employeeamount'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])
                ->where('name_id', $name_id)
                ->sum('amount');

            $data['amountrecevied'] = Employeeimprestamount::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])
                ->where('name_id', $name_id)
                ->sum('amount');

            $data['amountapproved'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])
                ->where('buttonstatus', '1')
                ->where('name_id', $name_id)
                ->sum('amount');

            $data['amountspent'] = $data['amountapproved'] - $data['amountrecevied'];

            $data['employeeimprest'] = Employeeimprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])
                ->with('user')
                ->with('category')
                ->with('team')
                ->get();

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            return view('employeeimprest.employee_imprest_view', $data);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function dateFilterAcc(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'name_id' => 'nullable|exists:users,id',
            ], [
                'start_date.required' => 'Please select start date',
                'end_date.required' => 'Please select end date',
                'end_date.after_or_equal' => 'End date must be greater than or equal to start date',
                'name_id.exists' => 'Invalid user selected',
            ]);

            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $id = $request->name_id;

            if ($start_date == null || $end_date == null) {
                return redirect()->back()->with('error', 'Please select start and end date');
            }

            $data['employee'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])
                ->where('name_id', $id)->with('project')->get();
            $data['name_id'] = $id;
            $data['name'] = User::where('id', $id)->first()->name;
            $data['amtReceived'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])->where('name_id', $id)->sum('amount');
            $data['amtSpent'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])->where('name_id', $id)->sum('amount');
            $data['amtApproved'] = EmployeeImprest::whereBetween('strtotime', [strtotime($start_date), strtotime($end_date)])->where('name_id', $id)->where('buttonstatus', '1')->sum('amount');
            $data['amtLeft'] = $data['amtApproved'] - $data['amtReceived'];

            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            return view('employeeimprest.view_dashboard', $data);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function paymentHistory($id = null)
    {
        try {
            if ($id) {
                $data['transactions'] = Employeeimprestamount::where('name_id', $id)->get();
            } else {
                $data['transactions'] = EmployeeImprestamount::all();
            }
            return view('employeeimprest.payment-history', $data);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function imprestVoucher($id = null)
    {
        try {
            $query = EmployeeImprest::query();

            if ($id) {
                $query->where('name_id', $id);
            }

            $vouchers = $query->selectRaw('
                name_id,
                WEEKOFYEAR(COALESCE(approved_date, created_at)) as week,
                DATE_FORMAT(MIN(COALESCE(approved_date, created_at)), "%Y-%m-%d") as start_date,
                DATE_FORMAT(ADDDATE(MIN(COALESCE(approved_date, created_at)), 6 - WEEKDAY(MIN(COALESCE(approved_date, created_at)))), "%Y-%m-%d") as end_date,
                SUM(amount) as total_amount,
                GROUP_CONCAT(invoice_proof) as all_invoice_proofs
            ')->groupBy('name_id', 'week')->orderBy('week', 'desc')->get();

            //  dd($vouchers);

            return view('employeeimprest.vouchers', ['vouchers' => $vouchers]);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function voucherView(Request $request)
    {
        try {
            $request->validate([
                'from' => 'required|date',
                'to' => 'required|date|after_or_equal:from',
                'name_id' => 'required|exists:users,id',
            ]);

            $from = $request->from;
            $to = $request->to;
            $name_id = $request->name_id;

            $year = $this->getFinancialYearStartYear();

            // Check if the voucher already exists
            $existingVoucher = Voucher::where('name_id', $name_id)
                ->where('from', $from)
                ->where('to', $to)
                ->first();

            if ($existingVoucher) {
                $voucher_id = $existingVoucher->voucher_id;
            } else {
                $last = Voucher::selectRaw('MAX(CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(voucher_id, "/", -1), "V", -1) AS UNSIGNED)) as max_id')
                    ->whereRaw("voucher_id LIKE 'VE/{$year}/V%'")
                    ->first();
                $nextId = $last && $last->max_id ? $last->max_id + 1 : 1;
                $voucher_id = 'VE/' . $year . '/V' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

                $voucher = EmployeeImprest::whereBetween(DB::raw('COALESCE(approved_date, created_at)'), [$from, $to])
                    ->where('name_id', $name_id)
                    ->get();

                $amount = $voucher->sum('amount');

                Voucher::create([
                    'voucher_id' => $voucher_id,
                    'name_id' => $name_id,
                    'from' => $from,
                    'to' => $to,
                    'amount' => $amount
                ]);
            }

            $query = EmployeeImprest::whereDate('approved_date', '>=', $from)
                ->whereDate('approved_date', '<=', $to)
                ->where('buttonstatus', '1')
                ->where('name_id', $name_id);

            $voucher = $query->get();

            // dd($query->toSql(), $query->getBindings(), $voucher);

            // $abcd = EmployeeImprest::whereBetween(DB::raw('COALESCE(approved_date, created_at)'), [$from, $to])
            //     ->where('name_id', $name_id)
            //     ->get();


            $abc = User::where('id', $name_id)->first();

            return view(
                'employeeimprest.voucher-view',
                [
                    'abc' => $abc,
                    'vo' => $voucher,
                    'from' => $from,
                    'to' => $to,
                    'last' => $voucher_id,
                    'voucher' => $existingVoucher
                ]
            );
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function getFinancialYearStartYear()
    {
        $currentDate = Carbon::now();
        $financialYearStart = $currentDate->month >= 4 ? $currentDate->year : $currentDate->year - 1;

        return substr($financialYearStart, -2);
    }

    public function adminSignedVoucher(Request $request, $vid)
    {
        $id = str_replace('-', '/', $vid);
        Log::info('VID = ' . $id);
        try {
            $user = Auth::user();
            Log::info('Attempting to sign voucher by admin', ['user_id' => $user->id, 'voucher_id' => $id]);

            $remark = $request->input('remark');
            $approveIt = $request->input('approve_it');

            $voucher = Voucher::where('voucher_id', $id)->first();
            if (!$voucher) {
                Log::warning('Voucher not found', ['voucher_id' => $id]);
                return response()->json(['success' => false, 'message' => 'Voucher not found']);
            }

            // Always update the remark
            $voucher->admin_remark = $remark;

            if ($user->role == 'admin') {
                if ($approveIt) {
                    if ($voucher->admin_sign != null) {
                        Log::info('Voucher already signed by admin', ['voucher_id' => $id]);
                        $voucher->save(); // Save remark anyway
                        return response()->json(['success' => false, 'message' => 'Voucher already signed']);
                    }
                    $voucher->admin_sign = $user->sign;
                    $voucher->admin_sign_date = Carbon::now();
                }
                $voucher->save();
                Log::info('Voucher signed/remarked successfully by admin', ['voucher' => $voucher]);
                return response()->json(['success' => true, 'message' => $approveIt ? 'Voucher signed successfully' : 'Remark saved successfully']);
            } else {
                Log::warning('Unauthorized attempt to sign voucher', ['user_id' => $user->id, 'voucher_id' => $id]);
                return response()->json(['success' => false, 'message' => 'You are not authorized to sign the voucher']);
            }
        } catch (\Throwable $th) {
            Log::error('Error signing voucher by admin', ['error' => $th->getMessage(), 'voucher_id' => $id]);
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    // function to sign the voucher by account
    public function accountSignedVoucher(Request $request, $vid)
    {
        $id = str_replace('-', '/', $vid);
        Log::info('VID = ' . $id);
        try {
            $user = Auth::user();
            Log::info('Attempting to sign voucher by account', ['user_id' => $user->id, 'voucher_id' => $id]);
            $remark = $request->input('remark');
            $approveIt = $request->input('approve_it');

            $voucher = Voucher::where('voucher_id', $id)->first();
            if (!$voucher) {
                Log::warning('Voucher not found', ['voucher_id' => $id]);
                return response()->json(['success' => false, 'message' => 'Voucher not found']);
            }

            // Always update the remark
            $voucher->acc_remark = $remark;

            if (Str::startsWith($user->role, 'account')) {
                if ($approveIt) {
                    if ($voucher->acc_sign) {
                        Log::info('Voucher already signed by account', ['voucher_id' => $id]);
                        $voucher->save(); // Save remark anyway
                        return response()->json(['success' => false, 'message' => 'Voucher already signed']);
                    }
                    $voucher->acc_sign = $user->sign;
                    $voucher->acc_sign_date = Carbon::now();
                }
                $voucher->save();
                Log::info('Voucher signed/remarked successfully by account', ['voucher' => $voucher]);
                return response()->json(['success' => true, 'message' => $approveIt ? 'Voucher signed successfully' : 'Remark saved successfully']);
            } else {
                Log::warning('Unauthorized attempt to sign voucher', ['user_id' => $user->id, 'voucher_id' => $id]);
                return response()->json(['success' => false, 'message' => 'You are not authorized to sign the voucher']);
            }
        } catch (\Throwable $th) {
            Log::error('Error signing voucher by account', ['error' => $th->getMessage(), 'voucher_id' => $id]);
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function imprestDelete($id)
    {
        try {
            $imprest = EmployeeImprest::find($id);
            if ($imprest) {
                $imprest->delete();
                return redirect()->back()->with(['message' => 'Imprest deleted successfully']);
            }
            return redirect()->back()->with(['message' => 'Imprest not found']);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => $th->getMessage()]);
        }
    }

    public function historyDelete($id)
    {
        try {
            $imprest = EmployeeImprestamount::find($id);
            if ($imprest) {
                $imprest->delete();
                return redirect()->back()->with(['message' => 'Imprest deleted successfully']);
            }
            return redirect()->back()->with(['message' => 'Imprest not found']);
        } catch (\Throwable $th) {
            return redirect()->back()->with(['error' => $th->getMessage()]);
        }
    }
}
