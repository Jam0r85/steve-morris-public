<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;

/**
 * Orchestrator that delegates to focused sub-mappers by media type.
 */
final class MediaMapper
{
    /**
     * Build media rows for property_media.
     *
     * @return array<int,array{category:string,url:string,sort_order:int,width:?int,height:?int,is_image:?bool,media_type:?string,title:?string,url_thumbnail:?string,url_small:?string,url_medium:?string,url_large:?string,url_hero:?string,url_full:?string}>
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $out = [];
        $out = array_merge(
            $out,
            ImagesMediaMapper::map($relationships, $inc),
            FloorplansMediaMapper::map($relationships, $inc),
            EpcMediaMapper::map($relationships, $inc),
        );

        return $out;
    }

    /** @return string[] */
    public static function ids(array $rels, string $name): array
    {
        $data = $rels[$name]['data'] ?? null;
        if ( ! $data) {
            return [];
        }
        if (is_array($data) && array_is_list($data)) {
            return array_values(array_filter(array_map(fn($r) => $r['id'] ?? null, $data)));
        }

        return isset($data['id']) ? [$data['id']] : [];
    }
}

/**
 * Shared helpers for extracting urls/metadata from included attributes.
 */
final class MediaExtract
{
    /** @return array{canonical:?string, thumbnail:?string, small:?string, medium:?string, large:?string, hero:?string, full:?string} */
    public static function urls(array $attrs): array
    {
        $urls = is_array($attrs['urls'] ?? null) ? $attrs['urls'] : [];
        $full = $urls['full'] ?? ($attrs['pdf'] ?? ($attrs['url'] ?? null));

        return [
            'canonical' => $full ?? null,
            'thumbnail' => $urls['thumbnail'] ?? null,
            'small' => $urls['small'] ?? null,
            'medium' => $urls['medium'] ?? null,
            'large' => $urls['large'] ?? null,
            'hero' => $urls['hero'] ?? null,
            'full' => $urls['full'] ?? ($attrs['url'] ?? null),
        ];
    }

    /** @return array{is_image:?bool, media_type:?string, title:?string} */
    public static function meta(array $attrs): array
    {
        return [
            'is_image' => array_key_exists('is_image', $attrs) ? (bool) $attrs['is_image'] : null,
            'media_type' => $attrs['media_type'] ?? null,
            'title' => $attrs['title'] ?? null,
        ];
    }
}

/**
 * IMAGES (category = 'photo')
 */
final class ImagesMediaMapper
{
    /**
     * @return array<int,array{category:string,url:string,sort_order:int,width:?int,height:?int,is_image:?bool,media_type:?string,title:?string,url_thumbnail:?string,url_small:?string,url_medium:?string,url_large:?string,url_hero:?string,url_full:?string}>
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $out = [];
        $fallbackOrder = 0;

        foreach (MediaMapper::ids($relationships, 'images') as $mid) {
            $m = $inc->get('media', $mid);
            if ( ! $m) {
                continue;
            }

            $urls = MediaExtract::urls($m);
            if ( ! $urls['canonical']) {
                continue;
            }

            $meta = MediaExtract::meta($m);
            $sort = isset($m['order']) ? (int) $m['order'] : $fallbackOrder++;

            $out[] = [
                'category' => 'photo',
                'url' => $urls['canonical'],
                'sort_order' => $sort,
                'width' => null,
                'height' => null,

                'is_image' => $meta['is_image'],
                'media_type' => $meta['media_type'],
                'title' => $meta['title'],

                'url_thumbnail' => $urls['thumbnail'],
                'url_small' => $urls['small'],
                'url_medium' => $urls['medium'],
                'url_large' => $urls['large'],
                'url_hero' => $urls['hero'],
                'url_full' => $urls['full'],
            ];
        }

        return $out;
    }
}

/**
 * FLOORPLANS (category = 'floorplan')
 */
final class FloorplansMediaMapper
{
    /**
     * @return array<int,array{category:string,url:string,sort_order:int,width:?int,height:?int,is_image:?bool,media_type:?string,title:?string,url_thumbnail:?string,url_small:?string,url_medium:?string,url_large:?string,url_hero:?string,url_full:?string}>
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $out = [];

        foreach (MediaMapper::ids($relationships, 'floorplans') as $fid) {
            $f = $inc->get('floorplan', $fid);
            if ( ! $f) {
                continue;
            }

            $urls = MediaExtract::urls($f);
            if ( ! $urls['canonical']) {
                continue;
            }

            $meta = MediaExtract::meta($f);
            $sort = isset($f['order']) ? (int) $f['order'] : 999;

            $out[] = [
                'category' => 'floorplan',
                'url' => $urls['canonical'],
                'sort_order' => $sort,
                'width' => null,
                'height' => null,

                'is_image' => $meta['is_image'],
                'media_type' => $meta['media_type'],
                'title' => $meta['title'],

                'url_thumbnail' => $urls['thumbnail'],
                'url_small' => $urls['small'],
                'url_medium' => $urls['medium'],
                'url_large' => $urls['large'],
                'url_hero' => $urls['hero'],
                'url_full' => $urls['full'],
            ];
        }

        return $out;
    }
}

/**
 * EPC (category = 'epc')
 */
final class EpcMediaMapper
{
    /**
     * @return array<int,array{category:string,url:string,sort_order:int,width:?int,height:?int,is_image:?bool,media_type:?string,title:?string,url_thumbnail:?string,url_small:?string,url_medium:?string,url_large:?string,url_hero:?string,url_full:?string}>
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $out = [];

        $epcId = $relationships['epc']['data']['id'] ?? null;
        if ( ! $epcId) {
            return $out;
        }

        $e = $inc->get('epc', $epcId);
        if ( ! $e) {
            return $out;
        }

        $urls = MediaExtract::urls($e);
        if ( ! $urls['canonical']) {
            return $out;
        }

        $meta = MediaExtract::meta($e);

        $out[] = [
            'category' => 'epc',
            'url' => $urls['canonical'],
            'sort_order' => 999,
            'width' => null,
            'height' => null,

            'is_image' => $meta['is_image'],
            'media_type' => $meta['media_type'],
            'title' => $meta['title'],

            'url_thumbnail' => $urls['thumbnail'],
            'url_small' => $urls['small'],
            'url_medium' => $urls['medium'],
            'url_large' => $urls['large'],
            'url_hero' => $urls['hero'],
            'url_full' => $urls['full'],
        ];

        return $out;
    }
}
