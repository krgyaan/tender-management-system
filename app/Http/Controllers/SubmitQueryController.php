<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SubmitQuery;
use App\Models\TenderInfo;
use Illuminate\Http\Request;
use App\Helpers\MailHelper;
use App\Mail\SubmitQueryMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SubmitQueryController extends Controller
{
    public function create(Request $request, TenderInfo $id)
    {
        return view('tender.submit-query', ['tender' => $id]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Starting SubmitQueryController: store() method. by ' . Auth::user()->name);
            // Validate the request
            $request->validate([
                'tender_id' => 'required|exists:tender_infos,id',
                'queries' => 'required|array',
                'queries.*.page_no' => 'required',
                'queries.*.clause_no' => 'required',
                'queries.*.query_type' => 'required|in:technical,commercial,bec,price_bid',
                'queries.*.current_statement' => 'required',
                'queries.*.requested_statement' => 'required',
                'client_org' => 'required',
                'client_name' => 'required',
                'client_email' => 'required|email',
                'client_phone' => 'required'
            ]);

            $submitQuery = SubmitQuery::create([
                'tender_id' => $request->tender_id,
                'client_org' => $request->client_org,
                'client_name' => $request->client_name,
                'client_email' => $request->client_email,
                'client_phone' => $request->client_phone,
            ]);

            // Insert query details in submit_queries_lists table
            foreach ($request->queries as $query) {
                $submitQuery->queryLists()->create([
                    'page_no' => $query['page_no'],
                    'clause_no' => $query['clause_no'],
                    'query_type' => $query['query_type'],
                    'current_statement' => $query['current_statement'],
                    'requested_statement' => $query['requested_statement']
                ]);
            }

            Log::info('SubmitQueryController: store() method completed successfully. by ' . Auth::user()->name);
            // Send email notification
            $mailSent = $this->sendMail($submitQuery->id);

            if ($mailSent) {
                return redirect()->route('tender.index')
                    ->with('success', 'Query submitted successfully and notification email sent.');
            }
            return redirect()->route('tender.index')
                ->with('success', 'Query submitted successfully but email notification failed.');
        } catch (\Throwable $th) {
            Log::error('Submit Query Error: ' . $th->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to submit query. Please try again.')
                ->withInput();
        }
    }

    public function sendMail($queryId)
    {
        try {
            $query = SubmitQuery::with(['tender', 'queryLists'])->find($queryId);
            $tender = $query->tender;
            $adminMail = User::where('role', 'admin')->where('team', 'DC')->first()->email ?? config('mail.admin_email');
            $tlMail = User::where('role', 'team-leader')->where('team', 'DC')->first()->email ?? config('mail.tl_email');
            $coo = User::where('role', 'coordinator')->where('team', 'DC')->first();

            $data = [
                'tender_no' => $tender->tender_no,
                'tender_name' => $tender->tender_name,
                'queries' => $query->queryLists->map(function ($item) {
                    return [
                        'page_no' => $item->page_no,
                        'clause_no' => $item->clause_no,
                        'query_type' => $item->query_type,
                        'current_statement' => $item->current_statement,
                        'requested_statement' => $item->requested_statement
                    ];
                })->toArray(),
                'client_details' => [
                    'name' => $query->client_name,
                ],
                'assignee' => $tender->users->name,
                'te_mobile' => $tender->users->mobile,
                'te_email' => $tender->users->email,
                've_address' => 'B1/D8 2nd Floor, Mohan Cooperative Industrial Estate, New Delhi - 110044',
            ];

            Log::info("Query Submission Mail Data: " . json_encode($data));

            // Configure mailer
            MailHelper::configureMailer($tender->users->email, $tender->users->app_password, $tender->users->name);
            $mailer = Config::has('mail.mailers.dynamic') ? 'dynamic' : 'smtp';
            $mail = Mail::mailer($mailer)
                ->to($query->client_email)
                ->cc([$tlMail, $adminMail, $coo->email])
                ->send(new SubmitQueryMail($data));

            if ($mail) {
                Log::info("Query Submission Email sent successfully using " . $mailer);
                return true;
            } else {
                Log::error("Query Submission Email failed to send");
                return false;
            }
        } catch (\Throwable $th) {
            Log::error("Query Submission Mail Error: " . $th->getMessage());
            return false;
        }
    }
}
