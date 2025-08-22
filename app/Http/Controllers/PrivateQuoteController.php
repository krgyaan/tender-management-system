<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use App\Models\PrivateQuote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class PrivateQuoteController extends Controller
{
    public function index()
    {
        return view('crm.quote.index');
    }

    public function getQuoteData(Request $request, $type)
    {
        Log::info("getQuoteData called with type=$type");

        $query = Enquiry::with(['costingSheets' => fn($q) => $q->where('status', 'Approved')])
            ->whereHas('costingSheets', fn($q) => $q->where('status', 'Approved'))
            ->orderBy('created_at', 'desc');

        // Optionally filter by $type if needed
        if ($type === 'pending') {
            $query->whereDoesntHave('privateQuote');
        } elseif ($type === 'submitted') {
            $query->whereHas('privateQuote');
        }

        return DataTables::of($query)
            ->addColumn('enquiry_no', fn($enquiry) => 'ENQ-' . str_pad($enquiry->id, 5, '0', STR_PAD_LEFT))
            ->addColumn('enquiry_name', fn($enquiry) => $enquiry->enq_name)
            ->addColumn('approx_value', fn($enquiry) => format_inr($enquiry->approx_value))
            ->addColumn('final_price', function ($enquiry) {
                $finalPrice = optional($enquiry->costingSheets->first())->final_price ?? 0;
                return format_inr($finalPrice);
            })
            ->addColumn('status', fn($enquiry) => (
                $enquiry->privateQuote->status ?? 'Submission Pending'
            ))
            ->addColumn('timer', function ($enquiry) {
                $deadline = optional($enquiry->created_at)->addDays(7); // example timer
                return '<span class="timer" data-deadline="' . $deadline . '">Pending</span>';
            })
            ->addColumn('action', fn($enquiry) => view('partials.pvt-quote-action', compact('enquiry'))->render())
            ->rawColumns(['status', 'timer', 'action'])
            ->make(true);
    }

    public function submitQuote(Request $request)
    {
        Log::info('submitQuote called');

        $request->validate([
            'enquiry_id' => 'required|integer|exists:enquiries,id',
            'quote_submission_datetime' => 'required|date',
            'submitted_documents.*' => 'nullable|file|max:10240',
            'contacts' => 'required|array',
        ]);

        $uploadedFiles = [];
        if ($request->hasFile('submitted_documents')) {
            foreach ($request->file('submitted_documents') as $file) {
                $filename = time() . '_submitted_quote' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('quotes/submitted_documents'), $filename);
                $uploadedFiles[] = $filename;
                Log::info('File uploaded successfully: ' . end($uploadedFiles));
            }
        }

        try {
            $quote = PrivateQuote::updateOrCreate(
                ['enquiry_id' => $request->enquiry_id],
                [
                    'quote_submission_datetime' => $request->quote_submission_datetime,
                    'submitted_documents' => json_encode($uploadedFiles),
                    'contacts' => json_encode($request->contacts),
                    'status' => 'Quotation Submitted',
                ]
            );

            Log::info("Quote submitted successfully: $quote");

            return redirect()->back()->with('success', 'Quote submitted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong while saving the data.');
        }
    }


    public function dropped(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required|integer|exists:enquiries,id',
            'missed_reason' => 'required|string|max:255',
            'oem_name' => 'nullable|string|max:255',
            'prevent_repeat' => 'required|string',
            'tms_improvement' => 'nullable|string',
        ]);

        Log::info('Validation passed for dropping quotation', $request->all());

        try {
            $quote = PrivateQuote::updateOrCreate(
                ['enquiry_id' => $request->enquiry_id],
                [
                    'missed_reason' => $request->missed_reason,
                    'oem_name' => $request->missed_reason === 'Not allowed by OEM' ? $request->oem_name : null,
                    'prevent_repeat' => $request->prevent_repeat,
                    'tms_improvement' => $request->tms_improvement,
                    'status' => 'Quotation Dropped',
                ]
            );

            Log::info('Quotation drop reason saved successfully', ['quote_id' => $quote->id]);

            return redirect()->back()->with('success', 'Quotation dropped reason submitted.');
        } catch (\Exception $e) {
            Log::error('Error saving quotation drop reason', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Something went wrong while saving the data.');
        }
    }


    public function show(PrivateQuote $privateQuote) {}

    public function edit(PrivateQuote $privateQuote) {}

    public function update(Request $request, PrivateQuote $privateQuote) {}

    public function destroy(PrivateQuote $privateQuote) {}
}
