<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

final class PropertyController extends Controller
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

    public function show(Request $request, string $channel, string $slug, string $slug_id)
    {
        // Look up by short id (fast, stable)
        $prop = Property::query()
            ->where('slug_id', $slug_id)
            ->with(['media' => fn($m) => $m->orderBy('category')->orderBy('sort_order')])
            ->firstOrFail();

        // Guard channel mismatch (someone visiting /sales for a lettings property)
        if ($prop->listing_category !== $channel) {
            return redirect()->route('properties.show', [
                'channel' => $prop->listing_category,
                'slug' => $prop->slug,
                'slug_id' => $prop->slug_id,
            ], 301);
        }

        // 301 if slug changed (you already store old slugs for history—optional extra check)
        if ($prop->slug && $prop->slug !== $slug) {
            return redirect()->route('properties.show', [
                'channel' => $channel,
                'slug' => $prop->slug,
                'slug_id' => $prop->slug_id,
            ], 301);
        }

        // Archived UX (keep 200, show banner + similar props)
        $similar = Property::query()
            ->active(true)
            ->where('listing_category', $channel)
            ->inTown($prop->address_town)
            ->bedsAtLeast(max(1, (int) $prop->bedrooms - 1))
            ->bedsAtMost((int) $prop->bedrooms + 1)
            ->whereKeyNot($prop->getKey())
            ->withPrimaryMedia()
            ->limit(6)
            ->get();

        return view('properties.show', compact('prop', 'similar'));
    }
}
