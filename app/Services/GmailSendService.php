<?php

namespace App\Services;

use Google\Service\Gmail as GmailService;
use Google\Service\Gmail\Message as GmailMessage;
use App\Models\ConversationMap;

/**
 * High-level sender service (API you’ll call)
 * Given {user_id, to[], subject, html/text, conversation_key?}, send a message;
 * if conversation_key maps to a thread_id, append to that thread.
 */

class GmailSendService
{
    public function __construct(private GmailTokenGuard $guard) {}

    public function send(array $payload): array
    {
        $userId = $payload['user_id'];

        // 1) Resolve threadId via ConversationMap
        $threadId = null;
        $prevMsgId = null;
        if (!empty($payload['conversation_key'])) {
            $map = ConversationMap::where('user_id', $userId)
                ->where('conversation_key', $payload['conversation_key'])->first();
            if ($map) {
                $threadId = $map->thread_id;
                $prevMsgId = $map->last_message_id;
            }
        }

        // 2) Build MIME (simple alt mime; use a library if you prefer)
        $raw = $this->buildRawMime(
            to: $payload['to'] ?? [],
            cc: $payload['cc'] ?? [],
            bcc: $payload['bcc'] ?? [],
            subject: $payload['subject'] ?? '',
            text: $payload['text'] ?? null,
            html: $payload['html'] ?? null,
            conversationKey: $payload['conversation_key'] ?? null,
            inReplyTo: $prevMsgId
        );

        // 3) Send via Gmail API
        $client = $this->guard->clientForUser($userId);
        $gmail  = new GmailService($client);

        $msg = new GmailMessage();
        $msg->setRaw($raw);
        if ($threadId) $msg->setThreadId($threadId);

        $resp = $gmail->users_messages->send('me', $msg);

        // 4) Upsert ConversationMap
        if (!empty($payload['conversation_key'])) {
            ConversationMap::updateOrCreate(
                ['user_id' => $userId, 'conversation_key' => $payload['conversation_key']],
                ['thread_id' => $resp->getThreadId(), 'last_message_id' => $resp->getId()]
            );
        }

        return ['status' => 'SENT', 'threadId' => $resp->getThreadId(), 'messageId' => $resp->getId()];
    }

    private function buildRawMime(array $to, array $cc, array $bcc, string $subject, ?string $text, ?string $html, ?string $conversationKey, ?string $inReplyTo): string
    {
        $boundary = 'b_' . bin2hex(random_bytes(8));
        $alt      = 'alt_' . bin2hex(random_bytes(8));

        $headers = array_values(array_filter([
            'MIME-Version: 1.0',
            'To: ' . implode(', ', $to),
            $cc ? 'Cc: ' . implode(', ', $cc) : null,
            $bcc ? 'Bcc: ' . implode(', ', $bcc) : null,
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            $conversationKey ? "X-MyApp-Conversation-Key: $conversationKey" : null,
            $inReplyTo ? "In-Reply-To: <$inReplyTo>" : null,
            $inReplyTo ? "References: <$inReplyTo>" : null,
            "Content-Type: multipart/alternative; boundary=\"$alt\"",
        ]));

        $parts = [];
        if ($text) $parts[] = "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . rtrim(base64_encode($text));
        if ($html) $parts[] = "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . rtrim(base64_encode($html));

        $altBody = "--$alt\r\n" . implode("\r\n--$alt\r\n", $parts) . "\r\n--$alt--";
        $raw = implode("\r\n", $headers) . "\r\n\r\n" . $altBody;

        // Base64url
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
