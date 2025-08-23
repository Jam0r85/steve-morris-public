<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function sales()
    {
        return view('properties', [
            'channel' => 'sales',
        ]);
    }

    public function lettings()
    {
        return view('properties', [
            'channel' => 'lettings',
        ]);
    }

    public function show(
        Request $request,
        string $channel,
        string $slug,
        Property $property
    ) {
        // --- 1) Guard channel mismatch ---
        if ($property->listing_category !== $channel) {
            return redirect()->route('properties.show', [
                'channel' => $property->listing_category,
                'slug' => $property->slug ?: $slug,
                'property' => $property->slug_id,
            ], 301);
        }

        // --- 2) Handle old slugs ---
        if ($property->slugRedirects()->where('old_slug', $slug)->exists()) {
            return redirect()->route('properties.show', [
                'channel' => $channel,
                'slug' => $property->slug ?: $slug,
                'property' => $property->slug_id,
            ], 301);
        }

        // --- 3) Canonical slug redirect ---
        if ( ! empty($property->slug) && $property->slug !== $slug) {
            return redirect()->route('properties.show', [
                'channel' => $channel,
                'slug' => $property->slug,
                'property' => $property->slug_id,
            ], 301);
        }

        // --- 4) Eager-load media ---
        $property->load([
            'media' => fn ($m) => $m->orderBy('category')->orderBy('sort_order'),
        ]);

        $title = $property->seoTitle();
        $description = $property->short_description
            ?: ($property->short_description_lettings
                ?: str($property->full_description)->limit(160));

        // --- 6) Structured Data ---
        $jsonLdScript = app(\App\Support\StructuredData::class)->propertyDetailPage(
            orgName: config('app.name', 'Steve Morris & Son LLP'),
            orgUrl: url('/'),
            pageUrl: url()->current(),
            property: $property,
            channel: $channel,
        );

        // --- 7) No similar yet, pass empty collection ---
        $similar = collect();

        return view('property', [
            'channel' => $channel,
            'property' => $property,
            'similar' => $similar,
            'title' => $title,
            'description' => $description,
            'jsonLdScript' => $jsonLdScript,
        ]);
    }
}
