<?php

namespace App\Services;

class ChatContextService
{
    private const MAX_HISTORY = 6;

    public function sanitizeHistory(mixed $rawHistory): array
    {
        if (!is_array($rawHistory)) {
            return [];
        }

        $sanitized = [];
        $recentHistory = array_slice($rawHistory, -self::MAX_HISTORY);

        foreach ($recentHistory as $msg) {
            if (!is_array($msg) || !isset($msg['role'], $msg['content'])) {
                continue;
            }

            $role = in_array($msg['role'], ['user', 'assistant'], true) ? $msg['role'] : 'user';
            $content = mb_substr(strip_tags(trim((string)$msg['content'])), 0, 300);

            if ($content !== '') {
                $sanitized[] = ['role' => $role, 'content' => $content];
            }
        }

        return $sanitized;
    }

    public function resolveAnaphora(string $currentQuery, array $history): string
    {
        $cleanQuery = mb_strtolower(trim($currentQuery));
        $anaphoraPattern = '/^(?:siapa|bagaimana|apa|prodi|fakultas|angkatan)\s+(?:dia|yang\s+tadi|itu|mahasiswa\s+tadi)\b/i';

        if (preg_match($anaphoraPattern, $cleanQuery)) {
            foreach (array_reverse($history) as $msg) {
                if (($msg['role'] ?? '') === 'user' && ($msg['content'] ?? '') !== $currentQuery) {
                    return $msg['content'];
                }
            }
        }

        return $currentQuery;
    }
}
