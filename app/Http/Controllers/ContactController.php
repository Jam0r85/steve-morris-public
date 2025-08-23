<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $orgPhone = '+44 121 355 0880';
        $orgEmail = 'contact@steve-morris.co.uk';

        $departments = [
            ['title' => 'Lettings', 'email' => 'lettings@steve-morris.co.uk', 'phone' => $orgPhone],
            ['title' => 'Sales', 'email' => 'sales@steve-morris.co.uk', 'phone' => $orgPhone],
            ['title' => 'Block Management', 'email' => 'block@steve-morris.co.uk', 'phone' => $orgPhone],
            ['title' => 'General Enquiries', 'email' => $orgEmail, 'phone' => $orgPhone],
        ];

        $branches = Branch::query()
            ->select([
                'uuid', 'name', 'public_name', 'email_address', 'telephone', 'website',
                'address_single_line', 'address_anon_single_line',
                'address_building_number', 'address_building_name',
                'address_street', 'address_line_1', 'address_line_2',
                'address_line_3', 'address_line_4', 'address_town',
                'address_country', 'address_postcode', 'address_udprn',
            ])
            ->publicOrder()
            ->get();

        $jsonLdScript = app(\App\Support\StructuredData::class)->contactPage(
            orgName: 'Steve Morris & Son LLP',
            orgUrl: url('/'),
            orgPhone: $orgPhone,
            orgEmail: $orgEmail,
            logoUrl: asset('images/logo-dark.webp'),
            branches: $branches,
            pageUrl: route('contact'),
            includeContactPage: true // set to false if you don't want the ContactPage node
        );

        return view('contact', [
            'canonical' => route('contact'),
            'orgPhone' => $orgPhone,
            'orgEmail' => $orgEmail,
            'departments' => $departments,
            'branches' => $branches,
            'jsonLdScript' => $jsonLdScript,
        ]);
    }
}
