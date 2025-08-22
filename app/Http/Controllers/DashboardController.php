<?php

namespace App\Http\Controllers;

use App\Models\BidSubmission;
use App\Models\TenderInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public $teams = [
        'AC' => 'AC',
        'DC' => 'DC',
    ];
    public $designations = [
        'CEO' => 'CEO',
        'coo' => 'COO',
        'ac-coordinator' => 'AC Co-ordinator',
        'dc-coordinator' => 'DC Co-ordinator',
        'ac-team-leader' => 'AC Tender Team Leader',
        'dc-team-leader' => 'DC Tender Team Leader',
        'ac-tender-executive' => 'AC Tender Executive',
        'dc-tender-executive' => 'DC Tender Executive',
        'dc-operation-leader' => 'DC Opreration Team Leader',
        'dc-operation-executive' => 'DC Opreration Executive',
        'dc-operation-engineer' => 'DC Opreration Engineer',
        'ac-operation-leader' => 'AC Opreration Team Leader',
        'ac-operation-executive' => 'AC Opreration Executive',
        'ac-operation-engineer' => 'AC Opreration Engineer',
        'account-leader' => 'Account Team Leader',
        'account-executive' => 'Account Executive',
        'accountant' => 'Accountant',
        'common-coordinator' => 'Common Coordinator',
        'field' => 'Field',
    ];
    public $roles = [
        'admin' => 'Admin',
        'common-coordinator' => 'Common Coordinator',
        'coordinator' => 'Common Coordinator',
        'coordinator' => 'Co-ordinator',
        'team-leader' => 'Tender Team Leader',
        'tender-executive' => 'Tender Executive',
        'operation-leader' => 'Opreration Team Leader',
        'operation-executive' => 'Oprerator Executive',
        'operation-engineer' => 'Oprerator Engineer',
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
    ];
    public function index()
    {
        $user = Auth::user();
        if (!array_key_exists($user->role, $this->roles)) {
            return redirect()->back()->with('error', 'Please contact the admin to update your role and permission.');
        }
        $data['role'] = $this->roles[$user->role];
        $data['userCount'] = User::where('role', '!=', 'admin')->where('status', 1)->count();
        $tenderInfoQuery = TenderInfo::query()->where('deleteStatus', '0');
        
        if ($user->role == 'admin') {
            // Admins can see everything — no query filter needed
        } elseif (in_array($user->role, ['team-leader', 'coordinator'])) {
            $tenderInfoQuery->where('team', $user->team);
        } else {
            $tenderInfoQuery->where('team_member', $user->id);
        }

        // Eager load tq_received
        $data['tender_info'] = $tenderInfoQuery->with('tq_received')->get();
        
        $data['tenderInfoCount'] = $data['tender_info']->count();
        $data['follow_ups'] = DB::table('follow_ups')
            ->where('assign_initiate', 'Followup Initiated')
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->get();

        $data['bided'] = BidSubmission::with('tenderdue')->where('status', 'Bid Submitted')
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->whereHas('tenderdue', function ($query) use ($user) {
                    $query->where('team_member', $user->id);
                });
            })
            ->count();

        // Add: Pass all active users (except admin) for admin dropdown
        $activeUsers = [];
        if ($user->role == 'admin') {
            $activeUsers = User::where('role', '!=', 'admin')->where('status', 1)->get();
        } else if(in_array($user->role, ['team-leader', 'coordinator'])) {
            $activeUsers = User::where('role', '!=', 'admin')->where('team', $user->team)->where('status', 1)->get();
        }

        return view('dashboard', compact('user', 'data', 'activeUsers'));
    }
}
