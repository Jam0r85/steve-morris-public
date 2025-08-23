<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CircuitBreaksStreet
{
    protected function streetBreakerKey(string $channel = 'open:enquiries'): string
    {
        return "street:cb:{$channel}";
    }

    /** True if circuit is OPEN (skip external calls) */
    protected function streetBreakerOpen(string $channel = 'open:enquiries'): bool
    {
        $key = $this->streetBreakerKey($channel) . ':until';

        return time() < (int) Cache::get($key, 0);
    }

    /** Record a failure; open the breaker after $threshold within $window, for $cooldown seconds */
    protected function streetBreakerRecordFailure(
        string $channel = 'open:enquiries',
        int $threshold = 3,
        int $window = 60,
        int $cooldown = 300
    ): void {
        $base = $this->streetBreakerKey($channel);
        $countKey = $base . ':count';
        $untilKey = $base . ':until';

        $count = (int) Cache::increment($countKey);
        Cache::put($countKey, $count, now()->addSeconds($window));

        if ($count >= $threshold) {
            Cache::put($untilKey, time() + $cooldown, now()->addSeconds($cooldown));
        }
    }

    /** Record a success; close the breaker */
    protected function streetBreakerRecordSuccess(string $channel = 'open:enquiries'): void
    {
        $base = $this->streetBreakerKey($channel);
        Cache::forget($base . ':count');
        Cache::forget($base . ':until');
    }
}
