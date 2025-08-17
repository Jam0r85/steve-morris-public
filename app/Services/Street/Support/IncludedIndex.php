<?php

declare(strict_types=1);

namespace App\Services\Street\Support;

final class IncludedIndex
{
    /** @var array<string, array<string, array>> */
    private array $index = [];

    public function __construct(array $included)
    {
        foreach ($included as $res) {
            $type = $res['type'] ?? null;
            $id = $res['id'] ?? null;
            if ( ! $type || ! $id) {
                continue;
            }
            $key = self::normType($type);
            $this->index[$key][$id] = (array) ($res['attributes'] ?? []);
        }
    }

    public static function normType(string $type): string
    {
        // tolerant key (e.g. "sales_listing" / "salesListing" -> "saleslisting")
        return preg_replace('/[^a-z]/', '', mb_strtolower($type));
    }

    public function get(string $type, string $id): ?array
    {
        return $this->index[self::normType($type)][$id] ?? null;
    }
}
