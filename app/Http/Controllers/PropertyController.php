<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\BuildPropertyPage;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function sales()
    {
        return view('properties.index', [
            'channel' => 'sales',
        ]);
    }

    public function lettings()
    {
        return view('properties.index', [
            'channel' => 'lettings',
        ]);
    }

    public function show(Request $request, string $channel, string $slug, Property $property, BuildPropertyPage $build)
    {
        // Eager-load media in the order you want
        $property->load(['media' => fn ($m) => $m->orderBy('category')->orderBy('sort_order')]);

        // Guard channel mismatch (e.g., visiting /sales for a lettings property)
        if ($property->listing_category !== $channel) {
            return redirect()->route('properties.show', [
                'channel' => $channel,
                'slug' => $property->slug,
                'property' => $property->slug_id,
            ], 301);
        }

        // Canonical slug redirect if the slug changed
        if ( ! empty($property->slug) && $property->slug !== $slug) {
            return redirect()->route('properties.show', [
                'channel' => $channel,
                'slug' => $property->slug,
                'property' => $property->slug_id,
            ], 301);
        }

        // Build page data (meta, gallery images, json-ld, etc.)
        $page = $build($property, $channel);
        $similar = $property->similar($channel, 6);

        return view('properties.show', array_merge([
            'channel' => $channel,
            'property' => $property,
            'similar' => $similar,
        ], $page));
    }
}
