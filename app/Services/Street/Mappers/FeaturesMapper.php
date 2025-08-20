<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;

class FeaturesMapper
{
    /**
     * Extracts feature names from the property relationships + included.
     * Returns a de-duplicated, ordered list of strings.
     *
     * @return array<int,string>
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $rel = $relationships['features']['data'] ?? null;
        if ( ! $rel) {
            return [];
        }

        // to-many: array; to-one: object
        $items = (is_array($rel) && array_is_list($rel)) ? $rel : [$rel];

        $names = [];
        foreach ($items as $r) {
            $id = $r['id'] ?? null;
            if ( ! $id) {
                continue;
            }

            $attrs = $inc->get('feature', $id);
            $name = is_array($attrs) ? ($attrs['name'] ?? null) : null;
            if (is_string($name) && mb_strlen(mb_trim($name))) {
                $names[] = mb_trim($name);
            }
        }

        // de-dupe while preserving order
        return array_values(array_unique($names));
    }
}
