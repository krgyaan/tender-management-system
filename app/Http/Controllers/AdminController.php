<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\FollowUps;
use App\Models\TenderInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public $teams = [
        'AC' => 'AC',
        'DC' => 'DC',
        'Accounts' => 'Accounts',
        'Sales' => 'Sales',
        'Common' => 'Common',
    ];

    public $designations = [
        'CEO' => 'CEO',
        'coo' => 'COO',
        'common-coordinator' => 'Common Coordinator',
        'ac-coordinator' => 'AC Coordinator',
        'dc-coordinator' => 'DC Coordinator',
        'ac-team-leader' => 'AC Tender Team Leader',
        'dc-team-leader' => 'DC Tender Team Leader',
        'ac-tender-executive' => 'AC Tender Executive',
        'dc-tender-executive' => 'DC Tender Executive',
        'dc-operation-leader' => 'DC Operations Team Leader',
        'dc-operation-executive' => 'DC Operations Executive',
        'dc-operation-engineer' => 'DC Operations Engineer',
        'ac-operation-leader' => 'AC Operations Team Leader',
        'ac-operation-executive' => 'AC Operations Executive',
        'ac-operation-engineer' => 'AC Operations Engineer',
        'account-leader' => 'Account Team Leader',
        'account-executive' => 'Account Executive',
        'accountant' => 'Accountant',
        'field' => 'Field',
    ];

    public $roles = [
        'admin' => 'Admin',
        'coordinator' => 'Team Coordinator',
        'common-coordinator' => 'Common Coordinator',
        'team-leader' => 'Tender Team Leader',
        'tender-executive' => 'Tender Executive',
        'operation-leader' => 'Operations Team Leader',
        'operation-executive' => 'Operations Executive',
        'operation-engineer' => 'Operations Engineer',
        'account-leader' => 'Account Team Leader',
        'account-executive' => 'Account Executive',
        'accountant' => 'Accountant',
        'field' => 'Field',
    ];

    public $permissions = [
        'all' => 'All',
        'tender-create' => 'Tender Create',
        'tender-info' => 'Tender Info',
        'tender-approval' => 'Tender Approval',
        'request-emd' => 'Request EMD',
        'rfq' => 'RFQ',
        'phy-docs' => 'Physical Docs',
        'costing-sheet' => 'Costing Sheet',
        'costing-approval' => 'Costing Approval',
        'results' => 'Results',
        'tq-mgmt' => 'TQ Management',
        'ra-mgmt' => 'RA Management',
        'bid-submission' => 'Bid Submission',
        'pqr-dashboard' => 'PQR Dashboard',
        'bg-emds-dashboard' => 'BG Dashboard',
        'dd-emds-dashboard' => 'DD Dashboard',
        'bt-emds-dashboard' => 'BT Dashboard',
        'pop-emds-dashboard' => 'POP Dashboard',
        'chq-emds-dashboard' => 'CHEQUE Dashboard',
        'fdr-emds-dashboard' => 'FDR Dashboard',
        'tender-fees' => 'Tender Fees',
        'follow-up' => 'Follow Up',
        'courier' => 'Courier',
        'employee-imprest' => 'Employee Imprest',
        'finance-docs' => 'Finance Documents',
        'loan-advances' => 'Loan & Advance',
        'projects' => 'Project Dashboard',
        'client-directory' => 'Client Directory',
        'wo-dashboard' => 'WO Dashboard',
        'kickoff-meeting' => 'Kick Off Meeting',
        'contract-agreement' => 'Contract Agreement',
        'rent-agreement' => 'Rent Agreement',
        'account-checklist' => 'Account Checklist',
        'expense-checklist' => 'Expense Checklist',
        'gstr1-checklist' => 'GSTR1 Checklist',
        'gst3b-checklist' => 'GST3B Checklist',
        'tds-checklist' => 'TDS Checklist',
        'admin' => 'Admin',
        'organizations' => 'Organizations',
        'statuses' => 'Statuses',
        'submitteddocs' => 'Document Submitted',
        'items' => 'Items',
        'vendors' => 'Vendors',
        'websites' => 'Websites',
        'locations' => 'Locations',
        'categories' => 'Imprest Category',
        'documenttype' => 'Document Type',
        'financialyear' => 'Financial Year',
        'followupcategories' => 'Followup Categories',
        'te-dashboard' => 'TE Dashboard',
        'tl-dashboard' => 'TL Dashboard',
        'operation-dashboard' => 'Operation Dashboard',
        'account-dashboard' => 'Account Dashboard',
        'oem-dashboard' => 'OEM Dashboard',
        'business-dashboard' => 'Business Dashboard',
        'customer-dashboard' => 'Customer Dashboard',
        'location-dashboard' => 'Location Dashboard',
        'customer-service' => 'Customer Service',
        'service-visit' => 'Service Visit',
        'conference-call' => 'Conference Call',
        'amc' => 'AMC',
        'amc-billing' => 'AMC Billing',
        'leads' => 'Leads',
        'enquiries' => 'Enquiry',
        'pvt-costing' => 'Private Costing Approval',
        'pvt-quotes' => 'Private Quotation',
    ];

    public function index()
    {
        $userCount = User::where('role', '!=', 'admin')->where('status', 1)->count();
        $tenderInfoCount = TenderInfo::where('deleteStatus', '0')->count();

        $user = Auth::user();
        if ($user->role == 'admin') {
            $tender_info = TenderInfo::all();
            $follow_ups = FollowUps::where('assign_initiate', 'Followup Initiated')->get();
        } else {
            $tender_info = TenderInfo::where('team_member', $user->id)->get();
            $follow_ups = FollowUps::where('assign_initiate', 'Followup Initiated')->where('assigned_to', $user->id)->get();
        }

        return view('adminDashboard', compact('userCount', 'tenderInfoCount', 'tender_info', 'follow_ups'));
    }

    public function createUser(Request $request)
    {
        if ($request->isMethod('get')) {
            $teams = $this->teams;
            $designations = $this->designations;
            $roles = $this->roles;
            $permissions = $this->permissions;
            return view('user.createUser', compact('teams', 'designations', 'roles', 'permissions'));
        }

        if ($request->isMethod('post')) {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
                'role' => 'required',
                'mobile' => 'required',
                'designation' => 'required',
                'address' => 'required',
                'permissions' => 'required',
                'image' => 'required|image',
                'id_proof' => 'required|image',
            ]);

            try {
                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                $user->role = $request->role;
                $user->mobile = $request->mobile;
                $user->designation = $request->designation;
                $user->address = $request->address;
                $user->permissions = implode(',', $request->permissions);
                $user->status = '1';
                $user->team = $request->team;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads'), $filename);
                    $user->image = $filename;
                }

                if ($request->hasFile('id_proof')) {
                    $image = $request->file('id_proof');
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads'), $filename);
                    $user->id_proof = $filename;
                }

                $user->save();

                return redirect()->back()->with('success', 'User created successfully');
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
                return redirect()->back()->with('error', $ex->getMessage());
            }
        }
    }

    public function allUsers(Request $request)
    {
        if ($request->isMethod('get')) {
            $users = User::orderBy('status', 'ASC')->get();
            return view('user.index', compact('users'));
        }
    }

    public function editUser(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $user = User::find($id);
            $teams = $this->teams;
            $designations = $this->designations;
            $roles = $this->roles;
            $permissions = $this->permissions;
            return view('user.edit', compact('user', 'teams', 'designations', 'roles', 'permissions'));
        }

        if ($request->isMethod('post')) {

            $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'mobile' => 'required',
                'designation' => 'required',
                'address' => 'required',
                'permissions' => 'required',
                'team' => 'required',
                'image' => 'image',
                'id_proof' => 'image',
            ]);

            try {
                $user = User::find($id);
                $user->name = $request->name;
                $user->email = $request->email;
                $user->role = $request->role;
                $user->mobile = $request->mobile;
                $user->designation = $request->designation;
                $user->address = $request->address;
                $user->permissions = implode(',', $request->permissions);
                $user->status = $request->status ?? '1';
                $user->team = $request->team;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $filename1 = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads'), $filename1);
                    $user->image = $filename1;
                }

                if ($request->hasFile('id_proof')) {
                    $image = $request->file('id_proof');
                    $filename = time() . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads'), $filename);
                    $user->id_proof = $filename;
                }

                $user->save();

                return redirect()->back()->with('success', 'User updated successfully');
            } catch (Exception $ex) {
                return redirect()->back()->with('error', $ex->getMessage());
            }
        }
    }

    public function deleteUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = User::find($request->id);
            if ($user) {
                $user->status = '0';
                $user->save();
                return redirect()->back()->with('success', 'User Deleted successfully');
            } else {
                return redirect()->back()->with('error', 'User not found');
            }
        }
        return redirect()->back()->with('error', 'Invalid request method');
    }
}
