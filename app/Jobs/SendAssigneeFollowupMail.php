<?php

namespace App\Jobs;

use App\Models\FollowUps;
use App\Models\User;
use App\Services\GmailSendService;
use App\Support\MailRender;
use App\Mail\FollowupAssigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendAssigneeFollowupMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public $backoff = [60, 300, 900];
    public int $timeout = 60;

    public function __construct(public int $followupId)
    {
        //
    }

    public function handle(): void
    {
        $gmail = app(GmailSendService::class);
        $fup = FollowUps::findOrFail($this->followupId);

        $assigneeUser = User::findOrFail($fup->assigned_to);
        $initiator    = User::findOrFail($fup->created_by);
        $admin        = User::where('role', 'admin')->first();
        $cooMail      = optional(User::where('role', 'coordinator')->first())->email ?? null;

        $to   = [$assigneeUser->email];
        $cc   = array_values(array_filter([$admin?->email, $cooMail]));
        $bcc  = [];

        $data = [
            'team_member'         => $assigneeUser->name,
            'organization_name'   => $fup->party_name,
            'follow_up_for'       => $fup->followup_for,
            'form_link'           => route('followups.edit', $fup->id),
            'follow_up_initiator' => $initiator->name,
        ];

        $html = MailRender::html(new FollowupAssigned($data));
        $subject = 'Follow Up Assigned - ' . $fup->followup_for;

        $conversationKey = "followup:{$fup->id}:assigned";

        $result = $gmail->send([
            'user_id'          => $initiator->id,
            'to'               => $to,
            'cc'               => $cc,
            'bcc'              => $bcc,
            'subject'          => $subject,
            'html'             => $html,
            'conversation_key' => $conversationKey,
            'idempotency_key'  => (string) Str::uuid(),
        ]);

        Log::info('Queued assignee followup mail sent', [
            'followup_id' => $fup->id,
            'to' => $to,
            'result' => $result,
        ]);
    }

    public function tags(): array
    {
        return ["followup:{$this->followupId}", 'mail', 'assignee'];
    }
}
