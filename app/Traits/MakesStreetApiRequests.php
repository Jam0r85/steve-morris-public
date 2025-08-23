<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Throwable;

trait MakesStreetApiRequests
{
    /**
     * Feed API (Property Feed) request helper.
     * $endpoint example: '/sales/search' or '/lettings/search'
     */
    protected function streetFeedRequest(string $method, string $endpoint, array $query = []): array
    {
        $baseUrl = mb_rtrim((string) config('services.street.feed.url'), '/');
        $token = (string) config('services.street.feed.token');

        // Normalise JSON:API page array into page[number], page[size]
        if (isset($query['page']) && is_array($query['page'])) {
            if (array_key_exists('number', $query['page'])) {
                $query['page[number]'] = $query['page']['number'];
            }
            if (array_key_exists('size', $query['page'])) {
                $query['page[size]'] = $query['page']['size'];
            }
            unset($query['page']);
        }

        $url = $baseUrl . $endpoint;

        try {
            $resp = Http::withToken($token)
                ->accept('application/vnd.api+json')
                ->retry(3, 500, throw: false)
                ->timeout(30)
                ->{$method}($url, $query);

            if ( ! $resp->successful()) {
                $ctx = [
                    'api' => 'feed',
                    'method' => $method,
                    'url' => $url,
                    'query' => $query,
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ];
                Log::error('Street feed API request failed', $ctx);
                $this->notifyStreetFailure('Street feed API request failed', $ctx);
                throw new RuntimeException("Street feed API request failed ({$resp->status()})");
            }

            return (array) $resp->json();
        } catch (Throwable $e) {
            $ctx = [
                'api' => 'feed',
                'method' => $method,
                'url' => $url,
                'query' => $query,
                'error' => $e->getMessage(),
            ];
            Log::error('Street feed API exception', $ctx);
            $this->notifyStreetFailure('Street feed API exception', $ctx);
            throw $e instanceof RuntimeException ? $e : new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Open API (general) GET helper.
     * $endpoint example: '/branches'
     */
    protected function streetOpenGet(string $endpoint, array $query = []): ?array
    {
        $baseUrl = mb_rtrim((string) config('services.street.feed.url'), '/');
        $token = (string) config('services.street.open.token');
        $url = $baseUrl . $endpoint;

        try {
            $resp = Http::withToken($token)
                ->accept('application/vnd.api+json')
                ->retry(3, 500, throw: false)
                ->timeout(30)
                ->get($url, $query);

            if ( ! $resp->successful()) {
                $ctx = [
                    'api' => 'open',
                    'method' => 'GET',
                    'url' => $url,
                    'query' => $query,
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                ];
                Log::error('Street open API request failed', $ctx);
                $this->notifyStreetFailure('Street open API request failed', $ctx);

                return null;
            }

            return (array) $resp->json();
        } catch (Throwable $e) {
            $ctx = [
                'api' => 'open',
                'method' => 'GET',
                'url' => $url,
                'query' => $query,
                'error' => $e->getMessage(),
            ];
            Log::error('Street open API exception', $ctx);
            $this->notifyStreetFailure('Street open API exception', $ctx);

            return null;
        }
    }

    /**
     * Sends a single email per unique failure signature for a short window to avoid spam.
     */
    protected function notifyStreetFailure(string $subject, array $context = [], int $suppressSeconds = 300): void
    {
        $to = (string) config('services.street.failed_jobs_email');
        if ( ! $to) {
            return;
        }

        // Build a small signature of the failure to throttle duplicates
        $sigData = [
            $subject,
            $context['api'] ?? '',
            $context['method'] ?? '',
            $context['url'] ?? '',
            $context['status'] ?? '',
        ];
        $signature = 'street_fail_' . md5(json_encode($sigData));

        // If we've sent this same alert within the suppression window, skip
        if (Cache::has($signature)) {
            return;
        }
        Cache::put($signature, 1, $suppressSeconds);

        $lines = [];
        foreach ($context as $k => $v) {
            $pretty = is_scalar($v) ? (string) $v : json_encode($v, JSON_PRETTY_PRINT);
            $lines[] = mb_strtoupper((string) $k) . ': ' . $pretty;
        }
        $body = implode("\n\n", $lines);

        try {
            Mail::raw($body ?: 'No additional context', function ($m) use ($to, $subject): void {
                $env = app()->environment();
                $m->to($to)->subject("❌ [{$env}] {$subject}");
            });
        } catch (Throwable $e) {
            Log::error('Street failure email could not be sent', ['error' => $e->getMessage()]);
        }
    }
}
