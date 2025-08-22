<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PhyDocs;
use App\Models\Documents;
use App\Models\DocketSlip;
use App\Models\TenderInfo;
use App\Helpers\MailHelper;
use App\Mail\PhydocsCreated;
use Illuminate\Http\Request;
use App\Models\PhydocsPerson;
use App\Models\Clintdirectory;
use App\Services\TimerService;
use App\Models\CourierDashboard;
use Yajra\DataTables\DataTables;
use App\Models\DocumentSubmitted;
use App\Models\TenderInformation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class PhyDocsController extends Controller
{
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }
    
    public function index()
    {
        return view('phydocs.index');
    }

    public function phydocsData(Request $request, $type)
    {
        $user = Auth::user();
        $team = $request->input('team');

        $query = TenderInfo::with(['info', 'users', 'phydocs'])
            ->where('tlStatus', '1')
            ->whereNotIn( 'status', ['8', '9', '10', '11', '12', '13', '14', '15', '38', '39'])
            ->where('deleteStatus', '0')
            ->whereHas('info', function ($q) {
                $q->where('phyDocs', 'Yes');
            });

        // Team filtering
        if (!in_array($user->role, ['admin'])) {
            if (in_array($user->role, ['team-leader', 'coordinator'])) {
                $query->where('team', $user->team);
            } else {
                $query->where('team_member', $user->id);
            }
        } else if($team) {
            $query->where('team', $team);
        }

        // Filter by physical doc status
        if ($type === 'pending') {
            $query->whereDoesntHave('phydocs');
        } elseif ($type === 'sent') {
            $query->whereHas('phydocs');
        }

        // Order by due_date
        if (!$request->filled('order')) {
            $query->orderByDesc('due_date');
        }

        // Global search
        if ($request->has('search') && !empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('tender_name', 'like', "%{$search}%")
                    ->orWhere('tender_no', 'like', "%{$search}%")
                    ->orWhere('gst_values', 'like', "%{$search}%")
                    ->orWhere('due_date', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return DataTables::of($query)
            ->addColumn('tender_name', function ($tender) {
                return "<strong>{$tender->tender_name}</strong> <br>
                <span class='text-muted'>{$tender->tender_no}</span>";
            })
            ->addColumn('users.name', function ($tender) {
                return optional($tender->users)->name ?? 'N/A';
            })
            ->addColumn('due_date', function ($tender) {
                return '<span class="d-none">' . strtotime($tender->due_date) . '</span>' .
                    date('d-m-Y', strtotime($tender->due_date)) . '<br>' .
                    date('h:i A', strtotime($tender->due_time));
            })
            ->addColumn('courier_date', function ($tender) {
                $phydoc = $tender->phydocs?->first();
                return $phydoc && $phydoc->created_at ? '<span class="d-none">' . strtotime($phydoc->created_at) . '</span>' . date('d-m-Y', strtotime($phydoc->created_at)) : '-';
            })
            ->addColumn('timer', function ($tender) {
                return view('partials.phydocs-timer', ['info' => $tender])->render();
            })
            ->addColumn('action', function ($tender) {
                return view('partials.phydocs-action', ['info' => $tender])->render();
            })
            ->rawColumns(['due_date', 'action', 'courier_date', 'tender_name', 'timer'])
            ->make(true);
    }
    
    public function create()
    {
        $tenders = TenderInfo::with('info')->latest()->get();
        $tenders = $tenders->where('info.phyDocs', '=', 'Yes');
        $couriers = CourierDashboard::latest()->get();
        $docs = DocumentSubmitted::latest()->get();
        return view('phydocs.create', compact('tenders', 'couriers', 'docs'));
    }
    
    public function store(Request $request)
    {
        try {
            $v = $request->validate([
                'tender_id' => 'required|integer',
                'client' => 'required|array',
                'client.name.*' => 'required',
                'client.email.*' => 'nullable',
                'client.phone.*' => 'nullable',
                'courier_no' => 'required|string|max:50',
                'submitted_docs' => 'nullable|array',
            ]);

            $attachments = [];
            $phyDocs = new PhyDocs();
            $phyDocs->tender_id = $request->tender_id;
            $phyDocs->courier_no = $request->courier_no;
            // submitted_docs is select2 array
            if ($request->has('submitted_docs') && is_array($request->submitted_docs)) {
                $phyDocs->submitted_docs = json_encode($request->submitted_docs);
                foreach ($request->submitted_docs as $doc) {
                    $attachments[] = DocumentSubmitted::findOrFail($doc)->name;
                }
            }
            $phyDocs->save();
            
            if ($request->has('client') && is_array($request->client)) {
                if ($request->client['name'] != null || $request->client['email'] != null || $request->client['phone'] != null) {
                    foreach ($request->client['name'] as $key => $client) {
                        $existingDir = Clintdirectory::where('email', $request->client['email'][$key])
                            ->orWhere('phone_no', $request->client['phone'][$key])
                            ->first();

                        if (!$existingDir) {
                            $dir = new Clintdirectory();
                            $dir->name = $request->client['name'][$key];
                            $dir->phone_no = $request->client['phone'][$key];
                            $dir->email = $request->client['email'][$key];
                            $dir->save();
                        } else {
                            Log::warning("Duplicate entry found in Clintdirectory.");
                        }

                        $person = new PhydocsPerson();
                        $person->phydoc_id = $phyDocs->id;
                        $person->name = $request->client['name'][$key];
                        $person->email = $request->client['email'][$key];
                        $person->phone = $request->client['phone'][$key];
                        $person->save();
                    }
                }
                Log::info("PhyDocs Persons: " . json_encode($phyDocs->persons));
            }

            if ($this->sendMail($phyDocs, $attachments)) {
                return redirect()->route('phydocs.index')->with('success', 'Physical Docs Created and Mail sent successfully.');
            } else {
                return redirect()->route('phydocs.index')->with('error', 'Physical Docs Created but Mail not sent.');
            }
        } catch (\Throwable $th) {
            Log::error("PhyDocs Error: " . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function show($id)
    {
        $phyDocs = PhyDocs::with('courier', 'persons')->where('tender_id', $id)->get();
        return view('phydocs.show', compact('phyDocs'));
    }
    
    public function edit(string $id)
    {
        $tender = TenderInfo::with('info')->where('id', $id)->first();
        $info = TenderInformation::where('tender_id', $id)->first();
        $couriers = CourierDashboard::latest()->get();
        $docs = DocumentSubmitted::latest()->get();
        
        return view('phydocs.edit', compact('info', 'couriers', 'docs', 'tender'));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $v = $request->validate([
                'tender_id' => 'required|integer',
                'client' => 'required|array',
                'client.name.*' => 'required',
                'client.email.*' => 'nullable',
                'client.phone.*' => 'nullable',
                'courier_no' => 'required|string|max:50',
                'submitted_docs' => 'nullable|array',
            ]);

            $attachments = [];
            $phyDocs = PhyDocs::find($id);
            if (!$phyDocs) {
                $phyDocs = new PhyDocs();
            }
            $phyDocs->tender_id = $request->tender_id;
            $phyDocs->courier_no = $request->courier_no;
            // submitted_docs is select2 array
            if ($request->has('submitted_docs') && is_array($request->submitted_docs)) {
                $phyDocs->submitted_docs = json_encode($request->submitted_docs);
                foreach ($request->submitted_docs as $doc) {
                    $attachments[] = DocumentSubmitted::findOrFail($doc)->name;
                }
            }
            $phyDocs->save();

            // Delete existing PhydocsPerson records for this PhyDocs
            PhydocsPerson::where('phydoc_id', $phyDocs->id)->delete();

            if ($request->has('client') && is_array($request->client)) {
                if ($request->client['name'] != null || $request->client['email'] != null || $request->client['phone'] != null) {
                    foreach ($request->client['name'] as $key => $client) {
                        $person = new PhydocsPerson();
                        $person->phydoc_id = $phyDocs->id;
                        $person->name = $request->client['name'][$key];
                        $person->email = $request->client['email'][$key];
                        $person->phone = $request->client['phone'][$key];
                        $person->save();
                    }
                }
                Log::info("PhyDocs Persons: " . json_encode($phyDocs->persons));
            }
            
            // Stop timer for the current PhyDocs
            $tender = TenderInfo::where('id', $phyDocs->tender_id)->first();
            if ($tender) {
                $this->timerService->stopTimer($tender, 'physical_docs');
            }

            if ($this->sendMail($phyDocs, $attachments)) {
                return redirect()->route('phydocs.index')->with('success', 'Physical Docs Updated and Mail sent successfully.');
            } else {
                return redirect()->route('phydocs.index')->with('error', 'Physical Docs Updated but Mail not sent.');
            }
        } catch (\Throwable $th) {
            Log::error("PhyDocs Error: " . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $phyDocs = PhyDocs::find($id);
            $phyDocs->delete();
            return redirect()->route('phydocs.index')->with('success', 'Physical Docs deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('phydocs.index')->with('error', $th->getMessage());
        }
    }
    
    public function deleteDoc($id)
    {
        try {
            $docs = Documents::find($id);
            $docs->delete();
            return redirect()->back()->with('success', 'Document deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
    
    public function deleteSlip($id)
    {
        try {
            $slips = DocketSlip::find($id);
            $slips->delete();
            return redirect()->back()->with('success', 'Slip deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /* ====== SEND MAIL ====== */

    public function sendMail(PhyDocs $phyDocs, array $docs)
    {
        try {
            $adminMail = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tlMail = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $cooMail = User::where('role', 'coordinator')->first()->email ?? 'gyanprakashk55@gmail.com';
            $due = TenderInformation::where('tender_id', $phyDocs->tender_id);
            $dueDate = date('d-m-Y', strtotime($due->first()->dead_date));
            $tender = TenderInfo::where('id', $phyDocs->tender_id)->first();
            $member = User::find($tender->team_member);
            $name = $member->name ?? 'gyanprakash';
            $password = $member->app_password ?? 'password';
            $email = $member->email ?? 'gyanprakashk55@gmail.com';
            MailHelper::configureMailer($email, $password, $name);
            // MailHelper::configureMailer('socialgyan69@gmail.com', 'rpscyifkeucxaiih', 'Denji');
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $phyDocs->courier = CourierDashboard::where('id', $phyDocs->courier_no)->first();
            if (!$phyDocs->courier) {
                Log::error("Courier not found for id: " . $phyDocs->courier_no);
                return response()->json(['success' => false, 'error' => 'Courier not found']);
            } else {
                Log::info("Courier found: " . json_encode($phyDocs->courier));
            }

            $data = [
                'tender_no' => $phyDocs->tender->tender_no,
                'due_date' => $dueDate,
                'courier_provider' => $phyDocs->courier->courier_provider,
                'docket_no' => $phyDocs->courier->docket_no,
                'delivery_time' => $phyDocs->courier->del_date,
                'tender_executive' => $name,
                'docketslip' => $phyDocs->courier->docket_slip,
                'docs' => $docs
            ];

            if ($phyDocs->persons->isEmpty()) {
                Log::error("No persons found for PhyDocs ID: " . $phyDocs->id);
                return response()->json(['success' => false, 'error' => 'No persons found']);
            } else {
                Log::info("Persons found: " . json_encode($phyDocs->persons));
            }
            foreach ($phyDocs->persons as $person) {
                $data['client_name'] = $person->name;

                Log::info("Mail Data: " . json_encode($data));
                $mail = Mail::mailer($mailer)
                    ->to($person->email)
                    ->cc([$cooMail, $tlMail, $adminMail])
                    ->send(new PhydocsCreated($data));
                if ($mail) {
                    Log::info("PhyDocs Courier Email sent successfully");
                } else {
                    Log::error("PhyDocs Courier Email failed to send");
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("PhyDocs Error: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
}
