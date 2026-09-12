<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAllowedAccessUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('local')) {
            return $next($request);
        }

        $allowedUrl = trim((string) config('services.access.url'));
        $referer = (string) $request->headers->get('referer');
        $origin = (string) $request->headers->get('origin');

        if ($allowedUrl === '' || ! $this->matchesAllowedUrl($allowedUrl, $referer, $origin)) {
            abort(404);
        }

        return $next($request);
    }

    private function matchesAllowedUrl(string $allowedUrl, string ...$sources): bool
    {
        $allowed = parse_url($allowedUrl);
        if (! is_array($allowed) || empty($allowed['host'])) {
            return false;
        }

        foreach ($sources as $source) {
            if ($source === '') {
                continue;
            }

            $parsed = parse_url($source);
            if (is_array($parsed) && ($parsed['host'] ?? null) === $allowed['host']) {
                return true;
            }
        }

        return false;
    }
}
