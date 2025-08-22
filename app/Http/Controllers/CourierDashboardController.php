<?php

namespace App\Http\Controllers;

use App\Helpers\MailHelper;
use App\Mail\CourierCreated;
use App\Mail\CourierDespatched;
use App\Mail\CourierStatusMail;
use App\Models\CourierDashboard;
use App\Models\User;
use App\Services\TimerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class CourierDashboardController extends Controller
{
    public $status = [
        1 => 'In Transit',
        2 => 'Out for delivery',
        3 => 'Address incorrect/Not delivered/Returned',
        4 => 'Delivered',
        5 => 'Rejected',
    ];
    protected $timerService;

    public function __construct(TimerService $timerService)
    {
        $this->timerService = $timerService;
    }

    public function index()
    {
        $status = $this->status;
        return view('courier.index', compact('status'));
    }

    public function getCourierData(Request $request, $type)
    {
        try {
            $user = Auth::user();
            $statusMap = [
                'dispatched' => ['1'],
                'not_delivered' => ['2', '3'],
                'delivered' => ['4'],
                'rejected' => ['5'],
            ];

            if (!in_array($type, array_merge(array_keys($statusMap), ['pending']))) {
                throw new \InvalidArgumentException('Invalid courier type');
            }

            Log::info("Fetching $type couriers");

            $query = CourierDashboard::with('courier_from');
            if ($type === 'pending') {
                $query->whereNull('status');
            } else {
                $query->whereIn('status', $statusMap[$type]);
            }

            $query->orderBy('created_at', 'desc');
            // Handle search
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('to_name', 'LIKE', "%{$searchValue}%")
                        ->orWhere('to_org', 'LIKE', "%{$searchValue}%")
                        ->orWhere('courier_provider', 'LIKE', "%{$searchValue}%")
                        ->orWhere('docket_no', 'LIKE', "%{$searchValue}%")
                        ->orWhere('to_addr', 'LIKE', "%{$searchValue}%");
                });
            }

            return DataTables::of($query)
                ->addColumn('from_name', function ($courier) {
                    return $courier->courier_from->name ?? '';
                })
                ->addColumn('timer', function ($courier) {
                    return view('partials.courier-timer', compact('courier'))->render();
                })
                ->addColumn('action', function ($courier) {
                    return view('partials.courier-actions', compact('courier'))->render();
                })
                ->editColumn('created_at', function ($courier) {
                    return $courier->created_at
                        ? '<span class="d-none">' . strtotime($courier->created_at) . '</span>' . $courier->created_at->format('d-m-Y h:i A')
                        : '';
                })
                ->editColumn('del_date', function ($courier) {
                    return $courier->del_date
                        ? '<span class="d-none">' . strtotime($courier->del_date) . '</span>' . date('d-m-Y', strtotime($courier->del_date))
                        : '';
                })
                ->editColumn('pickup_date', function ($courier) {
                    return $courier->pickup_date
                        ? '<span class="d-none">' . strtotime($courier->pickup_date) . '</span>' . date('d-m-Y h:i A', strtotime($courier->pickup_date))
                        : '';
                })
                ->editColumn('delivery_date', function ($courier) {
                    return $courier->delivery_date
                        ? '<span class="d-none">' . strtotime($courier->delivery_date) . '</span>' . date('d-m-Y h:i A', strtotime($courier->delivery_date))
                        : ( $courier->status ? $this->status[$courier->status] : '' );
                })
                ->rawColumns(['action', 'timer', 'created_at', 'del_date', 'pickup_date', 'delivery_date'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'Error loading data',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        $status = $this->status;
        $employees = User::where('role', '!=', 'admin')->where('status', '1')->get();
        return view('courier.create', compact('employees', 'status'));
    }

    public function store(Request $request)
    {
        Log::info('Courier Create Request: ' . json_encode($request->all()));

        try {
            $request->validate([
                'to_org' => 'required',
                'to_name' => 'required',
                'to_addr' => 'required',
                'to_pin' => 'required',
                'to_mobile' => 'required',
                'emp_from' => 'required',
                'del_date' => 'required',
                'urgency' => 'required',
                'courier_docs' => 'nullable|array',
                'courier_docs.*' => 'nullable|file',
            ]);

            $attachment = [];

            if ($request->hasFile('courier_docs')) {
                foreach ($request->file('courier_docs') as $file) {
                    $name = str_replace(' ', '_', $file->getClientOriginalName());
                    $fileName = $name . '-' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/courier_docs'), $fileName);
                    $attachment[] = $fileName;
                }
            }

            Log::info('Courier Create Attachment: ' . json_encode($attachment));

            $courier = CourierDashboard::create([
                'to_org' => $request->to_org,
                'to_name' => $request->to_name,
                'to_addr' => $request->to_addr,
                'to_pin' => $request->to_pin,
                'to_mobile' => $request->to_mobile,
                'emp_from' => $request->emp_from,
                'del_date' => $request->del_date,
                'urgency' => $request->urgency,
                'courier_docs' => $attachment ? json_encode($attachment) : null,
            ]);

            Log::info('Courier Created: ' . $courier->id);

            // Calculate countdown till 9 PM based on urgency, skipping Sundays.
            $tomorrow = date('Y-m-d', strtotime('tomorrow'));
            $isSunday = date('w', strtotime($tomorrow)) == 0;
            $currentHour = date('H');

            if ($request->urgency == 1) {
                // Same day delivery: countdown till 9 PM of the same day
                $remainingHrs = 21 - $currentHour;
            } elseif ($request->urgency == 2) {
                // Next day delivery: countdown till 9 PM of the next day, skipping Sundays
                $remainingHrs = $isSunday ? (24 * 2 + 21 - $currentHour) : (24 + 21 - $currentHour);
            } else {
                $remainingHrs = 0;
            }

            Log::info('Courier Timer Started: ' . $remainingHrs . ' hours');

            $this->timerService->startTimer($courier, 'courier_created', $remainingHrs);

            if ($this->courierCreatedMail($courier, $attachment)) {
                return redirect()->route('courier.index')->with('success', 'Courier created and mail sent successfully');
            } else {
                return redirect()->route('courier.index')->with('error', 'Courier created successfully but mail not sent');
            }
        } catch (\Throwable $th) {
            Log::error('Courier Create Error: ' . $th->getMessage());
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function show(CourierDashboard $courier)
    {
        return view('courier.show', compact('courier'));
    }

    public function destroy($id)
    {
        Log::info('Deleting courier with id: ' . $id . ' by' . Auth::user()->name);

        try {
            $courierDashboard = CourierDashboard::findOrFail($id);

            // Collect all file paths to delete
            $filesToDelete = [];

            if ($courierDashboard->courier_docs) {
                $docs = json_decode($courierDashboard->courier_docs, true) ?? [];
                foreach ($docs as $doc) {
                    $filesToDelete[] = public_path('uploads/courier_docs/' . $doc);
                }
            }

            if ($courierDashboard->docket_slip) {
                $filesToDelete[] = public_path('uploads/courier_docs/' . $courierDashboard->docket_slip);
            }

            if ($courierDashboard->delivery_pod) {
                $filesToDelete[] = public_path('uploads/courier_docs/' . $courierDashboard->delivery_pod);
            }

            // Delete all collected files
            foreach ($filesToDelete as $filePath) {
                if (file_exists($filePath)) {
                    Log::info('Deleting file: ' . $filePath);
                    unlink($filePath);
                }
            }

            // Delete the courier record
            $courierDashboard->delete();

            Log::info('Courier deleted successfully', ['id' => $courierDashboard->id]);
            return redirect()->route('courier.index')->with('success', 'Courier deleted successfully');
        } catch (\Throwable $th) {
            Log::error('Error deleting courier:', ['error' => $th->getMessage()]);
            return redirect()->back()->with('error', 'Error deleting courier: ' . $th->getMessage());
        }
    }

    public function despatch(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'courier_provider' => 'required',
                'pickup_date' => 'required',
                'docket_no' => 'required',
                'docket_slip' => 'required',
            ]);

            $attachment = [];

            if ($request->hasFile('docket_slip')) {
                $file = $request->file('docket_slip');
                $fileName = explode('.', $file->getClientOriginalName())[0];
                $fileName = $fileName . '-' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/courier_docs'), $fileName);
                $attachment[] = $fileName;
            }

            CourierDashboard::find($id)->update([
                'courier_provider' => $request->courier_provider,
                'pickup_date' => $request->pickup_date,
                'docket_no' => $request->docket_no,
                'docket_slip' => $fileName,
                'status' => 1,
            ]);

            $courier = CourierDashboard::find($id);

            // Stop timer for courier_created stage
            $this->timerService->stopTimerOnDifferentTime($courier, 'courier_created', $request->pickup_date);

            if ($this->courierDespatchedMail($courier, $attachment)) {
                return redirect()->route('courier.index')->with('success', 'Courier despatched and mail sent successfully');
            } else {
                return redirect()->route('courier.index')->with('error', 'Courier despatched successfully but mail not sent');
            }
        } else {
            return view('courier.despatch', compact('id'));
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required',
            ]);

            if ($request->status == 4) {
                $request->validate([
                    'delivery_date' => 'required',
                    'delivery_pod' => 'required',
                ]);
            } else {
                $request->validate([
                    'delivery_date' => '',
                    'delivery_pod' => '',
                ]);
            }

            $fileName = '';
            $attachment = [];
            if ($request->status == 4) {
                $request->validate([
                    'delivery_pod' => 'required',
                ]);

                if ($request->hasFile('delivery_pod')) {
                    $file = $request->file('delivery_pod');
                    $fileName = explode('.', $file->getClientOriginalName())[0];
                    $fileName = $fileName . '-' . time() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('uploads/courier_docs'), $fileName);
                    $attachment[] = $fileName;
                }
            }

            CourierDashboard::find($request->id)->update([
                'status' => $request->status,
                'delivery_date' => $request->delivery_date,
                'delivery_pod' => $fileName,
                'within_time' => $request->within_time,
            ]);
            $courier = CourierDashboard::find($request->id);
            if ($courier->status == 3 || $courier->status == 4) {
                if ($this->courierStatusMail($courier)) {
                    Log::info("Courier status updated and mail sent successfully $courier->id to $courier->status");
                    return redirect()->route('courier.index')->with('success', 'Courier status updated and mail sent successfully');
                } else {
                    Log::info("Courier status updated successfully but mail not sent $courier->id to $courier->status");
                    return redirect()->route('courier.index')->with('error', 'Courier status updated successfully but mail not sent');
                }
            } else {
                Log::info("Courier status updated successfully $courier->id to $courier->status");
            }

            return redirect()->route('courier.index')->with('success', 'Courier updated successfully');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    // ================================ Mail Functions ==============================

    public function courierStatusMail($courier)
    {
        try {
            $to = $courier->courier_from->email ?? 'gyanprakashk55@gmail.com';
            $admin = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'common-coordinator')->first();
            $data = [
                'from_name' => $courier->courier_from->name,
                'to_name' => $courier->to_name,
                'to_org' => $courier->to_org,
                'to_address' => $courier->to_addr,
                'status' => $this->status[$courier->status],
                'coordinator_name' => $coo->name,
                'req_no' => $courier->id,
                'provider' => $courier->courier_provider,
                'docket_no' => $courier->docket_no,
                'pickup' => $courier->pickup_date ? date('d-m-Y h:i A', strtotime($courier->pickup_date)) : '',
                'delivery' => $courier->delivery_date ? date('d-m-Y h:i A', strtotime($courier->delivery_date)) : '',
                'expected' => $courier->within_time == 1 ? 'Yes' : 'No',
                'pod' => [$courier->delivery_pod],
            ];
            Log::info("courierStatusMail: " . json_encode($data));
            MailHelper::configureMailer($coo->email, $coo->app_password, $coo->name);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($to)
                ->cc([$admin])
                ->send(new CourierStatusMail($data));
            if ($mail) {
                Log::info("Courier Status Mail sent successfully");
            } else {
                Log::error("Courier Status Mail not sent");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Courier Status Mail: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function courierCreatedMail($courier, $attachment)
    {
        try {
            $to = User::where('role', 'common-coordinator')->pluck('email')->toArray();
            $admin = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $from = User::find($courier->emp_from);
            $fromMail = $from->email ?? 'gyanprakashk55@gmail.com';
            $fromName = $from->name ?? 'gyanprakashk55@gmail.com';
            $fromPass = $from->app_password ?? 'password';
            $data = [
                'id' => $courier->id,
                'to_name' => $courier->to_name,
                'to_org' => $courier->to_org,
                'to_addr' => $courier->to_addr,
                'to_pin' => $courier->to_pin,
                'to_mobile' => $courier->to_mobile,
                'from_name' => $courier->courier_from->name,
                'expected_delivery_date' => $courier->del_date,
                'despatch_urgency' => $courier->urgency == 1 ? 'Same Day (Urgent)' : 'Next Day',
                'files' => $attachment,
            ];
            Log::info("courierCreatedMail: " . json_encode($data));

            MailHelper::configureMailer($fromMail, $fromPass, $fromName);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)->to($to)
                ->cc([$admin])
                ->send(new CourierCreated($data));

            if ($mail) {
                Log::info("Courier Created Mail sent successfully");
            } else {
                Log::error("Courier Created Mail not sent");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Courier Created Mail: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }

    public function courierDespatchedMail($courier, $attachment)
    {
        try {
            $to = $courier->courier_from->email ?? 'gyanprakashk55@gmail.com';
            $admin = User::where('role', 'admin')->first()->email ?? 'gyanprakashk55@gmail.com';
            $tl = User::where('role', 'team-leader')->first()->email ?? 'gyanprakashk55@gmail.com';
            $coo = User::where('role', 'common-coordinator')->first();
            $cooMail = $coo->email ?? 'gyanprakashk55@gmail.com';
            $cooName = $coo->name ?? 'gyanprakashk55@gmail.com';
            $password = $coo->app_password ?? 'password';
            $data = [
                'id' => $courier->id,
                'to_org' => $courier->to_org,
                'from_name' => $courier->courier_from->name,
                'courier_provider' => $courier->courier_provider,
                'pickup_date_time' => $courier->pickup_date,
                'docket_no' => $courier->docket_no,
                'coordinator_name' => $cooName,
                'files' => $attachment,
            ];
            Log::info("courierDespatchedMail: " . json_encode($data));
            MailHelper::configureMailer($cooMail, $password, $cooName);
            $mailer = Config::has('mail.mailers.dynamic') ?  'dynamic' : 'smtp';
            $newMail = new Mail();
            $mail = $newMail::mailer($mailer)->to($to)
                ->cc([$admin])
                ->send(new CourierDespatched($data));

            if ($mail) {
                Log::info("Courier Dispatch Mail sent successfully");
            } else {
                Log::error("Courier Dispatch Mail not sent");
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            Log::error("Courier Despatched Mail: " . $th->getMessage());
            return response()->json(['success' => false, 'error' => $th->getMessage()]);
        }
    }
}
