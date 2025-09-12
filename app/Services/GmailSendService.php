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

        $map = null;
        $threadId = null;
        $prevRfcId = null;
        $referencesChain = [];

        if (!empty($payload['conversation_key'])) {
            $map = ConversationMap::where('user_id', $userId)
                ->where('conversation_key', $payload['conversation_key'])
                ->with('references:id,conversation_map_id,rfc_message_id')
                ->first();

            if ($map) {
                $threadId   = $map->thread_id;
                $prevRfcId  = $map->last_message_rfc_id;
                $referencesChain = $map->references
                    ->pluck('rfc_message_id')
                    ->all();
            }
        }

        $mime = $this->buildRawMime(
            to: $payload['to'] ?? [],
            cc: $payload['cc'] ?? [],
            bcc: $payload['bcc'] ?? [],
            subject: $payload['subject'] ?? '',
            text: $payload['text'] ?? null,
            html: $payload['html'] ?? null,
            conversationKey: $payload['conversation_key'] ?? null,
            inReplyTo: $prevRfcId,
            references: $referencesChain,
            attachments: $payload['attachments'] ?? []
        );

        $client = $this->guard->clientForUser($userId);
        $gmail  = new GmailService($client);
        $msg = new GmailMessage();

        $msg->setRaw($mime['raw']);
        if ($threadId) $msg->setThreadId($threadId);

        $resp = $gmail->users_messages->send('me', $msg);

        if (!empty($payload['conversation_key'])) {
            if (!$map) {
                $map = ConversationMap::create([
                    'user_id'           => $userId,
                    'conversation_key'  => $payload['conversation_key'],
                    'thread_id'         => $resp->getThreadId(),
                    'last_message_api_id' => $resp->getId(),
                    'last_message_rfc_id' => $mime['message_id'],
                ]);
            } else {
                $map->update([
                    'thread_id'           => $resp->getThreadId(),
                    'last_message_api_id' => $resp->getId(),
                    'last_message_rfc_id' => $mime['message_id'],
                ]);
            }

            $map->references()->create(['rfc_message_id' => $mime['message_id']]);
        }

        return [
            'status'     => 'SENT',
            'threadId'   => $resp->getThreadId(),
            'messageId'  => $resp->getId(),
            'rfcMessageId' => $mime['message_id'],
        ];
    }

    private function buildRawMime(
        array $to,
        array $cc,
        array $bcc,
        string $subject,
        ?string $text,
        ?string $html,
        ?string $conversationKey,
        ?string $inReplyTo,
        array $references = [],
        array $attachments = []
    ): array {
        $boundaryMixed = 'mixed_' . bin2hex(random_bytes(8));
        $boundaryAlt   = 'alt_' . bin2hex(random_bytes(8));

        // Always generate a fresh RFC Message-ID
        $messageId = sprintf('<%s@volksenergie.in>', bin2hex(random_bytes(16)));

        // Build References header: full chain + last
        $refsHeader = null;
        if ($inReplyTo) {
            // Gmail is happy with just the last id, but the full chain is more robust
            $chain = array_values(array_unique(array_filter(array_merge($references, [$inReplyTo]))));
            $refsHeader = 'References: ' . implode(' ', $chain);
        }

        $headers = array_values(array_filter([
            'MIME-Version: 1.0',
            'To: ' . implode(', ', $to),
            $cc ? 'Cc: ' . implode(', ', $cc) : null,
            $bcc ? 'Bcc: ' . implode(', ', $bcc) : null,
            'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=',
            $conversationKey ? "X-MyApp-Conversation-Key: $conversationKey" : null,
            $inReplyTo ? "In-Reply-To: $inReplyTo" : null,   // NOTE: already contains <...>
            $refsHeader,
            "Message-ID: $messageId",
            "Content-Type: multipart/mixed; boundary=\"$boundaryMixed\"",
        ]));
        // --- Alternative part (text + html) ---
        $parts = [];
        if ($text) {
            $parts[] = "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($text));
        }
        if ($html) {
            $parts[] = "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
                . chunk_split(base64_encode($html));
        }

        $altBody = "--$boundaryAlt\r\n" . implode("\r\n--$boundaryAlt\r\n", $parts) . "\r\n--$boundaryAlt--";

        $body = "--$boundaryMixed\r\n"
            . "Content-Type: multipart/alternative; boundary=\"$boundaryAlt\"\r\n\r\n"
            . $altBody . "\r\n";

        // --- Attachments ---
        foreach ($attachments as $attachment) {
            $filename = $attachment['filename'] ?? 'file.bin';
            $mimeType = $attachment['mime'] ?? 'application/octet-stream';
            $content  = $attachment['content'] ?? '';

            $body .= "--$boundaryMixed\r\n";
            $body .= "Content-Type: $mimeType; name=\"$filename\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($content)) . "\r\n";
        }
        $body .= "--$boundaryMixed--";

        $raw = implode("\r\n", $headers) . "\r\n\r\n" . $body;

        return [
            'raw'        => rtrim(strtr(base64_encode($raw), '+/', '-_'), '='),
            'message_id' => $messageId,
        ];
    }
}
