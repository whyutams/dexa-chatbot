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
        if ($allowedUrl === '') {
            return $next($request);
        }

        $allowedHost = $this->extractHost($allowedUrl);
        if ($allowedHost === '') {
            return $next($request);
        }

        $referer = (string) $request->headers->get('referer');
        $origin = (string) $request->headers->get('origin');
        $requestHost = (string) $request->getHost();

        foreach ([$referer, $origin, $requestHost] as $source) {
            if ($source === '') {
                continue;
            }

            $sourceHost = $this->extractHost($source);
            if ($sourceHost !== '' && $sourceHost === $allowedHost) {
                return $next($request);
            }
        }

        abort(404);
    }

    private function extractHost(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (! str_contains($url, '://')) {
            $url = 'http://' . $url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) ? strtolower($host) : '';
    }
}
