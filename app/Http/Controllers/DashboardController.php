<?php

namespace App\Http\Controllers;

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
        'ac-operation_leader' => 'AC Opreration Team Leader',
        'dc-operation_executive' => 'DC Oprerator Executive',
        'account-leader' => 'Account Team Leader',
        'account-executive' => 'Account Executive',
        'accountant' => 'Accountant',
        'field' => 'Field',
    ];
    public $roles = [
        'admin' => 'Admin',
        'coordinator' => 'Co-ordinator',
        'team-leader' => 'Tender Team Leader',
        'tender-executive' => 'Tender Executive',
        'operation_leader' => 'Opreration Team Leader',
        'operation_executive' => 'Oprerator Executive',
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
        'rfq' => 'RFQ',
        'phy-docs' => 'Physical Docs',
        'pricing-sheet' => 'Pricing Sheet',
        'request-emd' => 'Request EMD',
        'emd-dashboard' => 'EMD Dashboard',
        'tender-fees' => 'Tender Fees',
        'follow-up' => 'Follow Up',
        'courier' => 'Courier',
        'employee-imprest' => 'Employee Imprest',
        'doc-dashboard' => 'Documents Dashboard',
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
        if ($user->role != 'admin') {
            $tenderInfoQuery->where('team_member', $user->id);
        }

        $data['tender_info'] = $tenderInfoQuery->get();
        $data['tenderInfoCount'] = $data['tender_info']->count();
        $data['follow_ups'] = DB::table('follow_ups')
            ->where('assign_initiate', 'Followup Initiated')
            ->when($user->role != 'admin', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            })
            ->get();

        return view('dashboard', compact('user', 'data'));
    }
}
