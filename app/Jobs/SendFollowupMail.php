<?php

namespace App\Jobs;

use App\Mail\BgFollowupMail;
use App\Mail\BtFollowupMail;
use App\Mail\ChqFollowupMail;
use App\Mail\DdFollowupMail;
use App\Mail\FdrFollowupMail;
use App\Mail\FollowupPersonMail;
use App\Mail\PopFollowupMail;
use App\Models\Emds;
use App\Models\FollowUpPersons;
use App\Models\FollowUps;
use App\Models\User;
use App\Services\GmailSendService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendFollowupMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public $backoff = [60, 300, 900];
    public int $timeout = 120;

    public function __construct(public int $followupId) {}

    public function handle(): void
    {
        $gmail = app(GmailSendService::class);
        $fu = FollowUps::find($this->followupId);
        if (!$fu) {
            Log::warning('SendFollowupMail: FollowUp not found', ['id' => $this->followupId]);
            return;
        }

        $assignee = User::findOrFail($fu->assigned_to);

        $recipients = FollowUpPersons::where('follwup_id', $fu->id)
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        // Skip if start date is in the future
        $startDate = Carbon::parse($fu->start_from)->startOfDay();
        $today = Carbon::now()->startOfDay();
        if ($startDate->gt($today)) {
            Log::info('SendFollowupMail: Skipped due to future start date', [
                'followup_id' => $fu->id,
                'start_from' => $fu->start_from,
            ]);
            return;
        }
        $day = max(1, $startDate->diffInDays($today, false));

        $data = [
            'for' => $fu->followup_for,
            'since' => $day,
            'reminder' => $fu->reminder_no,
            'mail' => $fu->details,
            'files' => json_decode($fu->attachments, true) ?: [],
        ];

        $class = FollowupPersonMail::class; // default
        if ($fu->emd_id) {
            $emd = Emds::find($fu->emd_id);
            if (!$emd) {
                Log::warning('SendFollowupMail: Invalid EMD ID', ['emd_id' => $fu->emd_id]);
                return;
            }
            $ins = $emd->instrument_type;
            switch ($ins) {
                case 1:
                    $class = DdFollowupMail::class;
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['tenderNo'] = $fu->dd->emd->tender->tender_no ?? null;
                    $data['projectName'] = $fu->dd->emd->project_name ?? null;
                    $data['status'] = $fu->dd->emd->tender->statuses->name ?? null;
                    $data['amount'] = format_inr($fu->dd->amount ?? 0);
                    $data['date'] = date('d-m-Y', strtotime($fu->dd->transfer_date ?? now()));
                    $data['utr'] = $fu->dd->utr ?? null;
                    break;
                case 2:
                    $class = FdrFollowupMail::class;
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['amount'] = format_inr($fu->fdr->amount ?? 0);
                    $data['date'] = date('d-m-Y', strtotime($fu->fdr->transfer_date ?? now()));
                    $data['utr'] = $fu->fdr->utr ?? null;
                    break;
                case 3:
                    $class = ChqFollowupMail::class;
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['amount'] = format_inr($fu->chq->amount ?? 0);
                    $data['date'] = date('d-m-Y', strtotime($fu->chq->transfer_date ?? now()));
                    $data['utr'] = $fu->chq->utr ?? null;
                    break;
                case 4:
                    $class = BgFollowupMail::class;
                    $bg_purpose = [
                        'advance' => 'Advance Payment',
                        'deposit' => 'Security Bond/ Deposit',
                        'bid' => 'Bid Bond',
                        'performance' => 'Performance',
                        'financial' => 'Financial',
                        'counter' => 'Counter Guarantee',
                    ];
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['tenderNo'] = $fu->bg->emd->tender->tender_no ?? null;
                    $data['projectName'] = $fu->bg->emd->project_name ?? null;
                    $data['status'] = $fu->bg->emd->tender->statuses->name ?? null;
                    $data['amount'] = format_inr($fu->bg->bg_amount ?? 0);
                    $data['bg_no'] = $fu->bg->bg_no ?? null;
                    $data['purpose'] = isset($fu->bg->bg_purpose) ? ($bg_purpose[$fu->bg->bg_purpose] ?? '') : '';
                    $data['bg_validity'] = date('d-m-Y', strtotime($fu->bg->bg_expiry ?? now()));
                    $data['bg_claim_period_expiry'] = date('d-m-Y', strtotime($fu->bg->bg_claim ?? now()));
                    $data['favor'] = $fu->bg->bg_favor ?? null;
                    break;
                case 5:
                    $class = BtFollowupMail::class;
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['tenderNo'] = $fu->bt->emd->tender->tender_no ?? null;
                    $data['projectName'] = $fu->bt->emd->project_name ?? null;
                    $data['status'] = $fu->bt->emd->tender->statuses->name ?? null;
                    $data['amount'] = format_inr($fu->bt->bt_amount ?? 0);
                    $data['date'] = date('d-m-Y', strtotime($fu->bt->transfer_date ?? now()));
                    $data['utr'] = $fu->bt->utr ?? null;
                    break;
                case 6:
                    $class = PopFollowupMail::class;
                    $data['for'] = $fu->followup_for;
                    $data['name'] = $fu->party_name;
                    $data['tenderNo'] = $fu->pop->emd->tender->tender_no ?? null;
                    $data['projectName'] = $fu->pop->emd->project_name ?? null;
                    $data['status'] = $fu->pop->emd->tender->statuses->name ?? null;
                    $data['amount'] = format_inr($fu->pop->amount ?? 0);
                    $data['date'] = date('d-m-Y', strtotime($fu->pop->transfer_date ?? now()));
                    $data['utr'] = $fu->pop->utr ?? null;
                    $data['accountNo'] = '1234567890';
                    $data['ifsc'] = 'SBIN0000001';
                    break;
                default:
                    $class = FollowupPersonMail::class;
            }
        }

        $html = (new $class($data))->render();
        $subject = 'Follow Up for ' . ($data['for'] ?? 'Update');

        // Gmail size budgeting to avoid 35MB raw limit
        $MAX_RAW_BYTES = 35 * 1024 * 1024; // 35 MB
        $SAFETY_MARGIN = 2 * 1024 * 1024;  // 2 MB
        $B64_FACTOR = 4 / 3;
        $budget = $MAX_RAW_BYTES - $SAFETY_MARGIN;

        $attachmentsInput = $data['files'] ?? [];
        $attachments = [];
        $encodedSoFar = strlen(base64_encode($html)) + 4096;

        $skipped = [];
        foreach ($attachmentsInput as $file) {
            $path = public_path("uploads/accounts/$file");
            if (!is_file($path)) {
                $skipped[] = ['file' => $file, 'reason' => 'missing'];
                continue;
            }
            $content = file_get_contents($path);
            if ($content === false) {
                $skipped[] = ['file' => $file, 'reason' => 'unreadable'];
                continue;
            }

            $plainSize = strlen($content);
            $encodedSize = (int) ceil($plainSize * $B64_FACTOR) + 1024;
            if (($encodedSoFar + $encodedSize) > $budget) {
                $skipped[] = ['file' => $file, 'reason' => 'size_limit'];
                continue;
            }

            $attachments[] = [
                'filename' => basename($path),
                'content' => $content,
                'mime' => FileFacade::mimeType($path) ?: 'application/octet-stream',
            ];
            $encodedSoFar += $encodedSize;
        }

        if (!empty($skipped)) {
            $skippedList = implode(', ', array_map(fn($s) => $s['file'], $skipped));
            $html .= "<p><em>Note:</em> Some attachments were omitted due to size limits: {$skippedList}</p>";
        }

        $conversationKey = "followup:{$fu->id}:main";

        $result = $gmail->send([
            'user_id' => $assignee->id,
            'to' => $recipients,
            'bcc' => [],
            'subject' => $subject,
            'html' => $html,
            'attachments' => $attachments,
            'conversation_key' => $conversationKey,
            'idempotency_key' => (string) Str::uuid(),
        ]);

        // Increment reminder count after successful send
        $fu->reminder_no = ($fu->reminder_no ?? 0) + 1;
        $fu->save();

        Log::info('SendFollowupMail: Sent', [
            'followup_id' => $fu->id,
            'to' => $recipients,
            'result' => $result,
            'skipped_attachments' => $skipped,
        ]);
    }

    public function tags(): array
    {
        return ["followup:{$this->followupId}", 'mail'];
    }
}
