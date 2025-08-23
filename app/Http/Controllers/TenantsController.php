<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class TenantsController extends Controller
{
    public function __invoke(): View
    {
        $pageTitle = 'Information for Tenants';
        $pageDescription = 'Viewings, applications, referencing, permitted payments, Right to Rent checks, and maintenance support for tenants in Sutton Coldfield.';

        // Same FAQs you had in Blade, moved here
        $tenantFaqs = [
            [
                'name' => 'What is Right to Rent?',
                'acceptedAnswer' => [
                    'text' => 'Right to Rent is a UK legal requirement in England, introduced under the Immigration Act 2014 and expanded by later legislation, obliging landlords to check that prospective tenants over 18 have the legal right to reside in the UK before renting to them. Checks may involve verifying documents or using the Home Office’s online service (e.g., via a share code). For full details, see the Government’s official guidance on how to prove your right to rent - https://www.gov.uk/prove-right-to-rent',
                ],
            ],
            [
                'name' => 'How is my deposit protected, and when will I get the prescribed information?',
                'acceptedAnswer' => [
                    'text' => 'Your deposit is protected with MyDeposits (Insured Scheme). Within 30 days of receiving your deposit we will send you the prescribed information, including scheme details and how to raise a dispute.',
                ],
            ],
            [
                'name' => 'What bills am I responsible for paying, and which ones are included in the rent?',
                'acceptedAnswer' => [
                    'text' => 'Unless your tenancy agreement states otherwise, you are responsible for council tax, gas, electricity, water, and broadband/TV services. Any bills included in the rent will be clearly listed in your agreement.',
                ],
            ],
            [
                'name' => 'How often will inspections be carried out, and how much notice will I be given?',
                'acceptedAnswer' => [
                    'text' => 'The first inspection is approximately three months after move-in, then normally once every 12 months. You will always receive at least 24 hours’ written notice and we will arrange a convenient time with you.',
                ],
            ],
            [
                'name' => 'What should I do if I need a repair, and how quickly will it be dealt with?',
                'acceptedAnswer' => [
                    'text' => 'Report repairs either by email or via Street (our online system). Emergency issues—such as no heating or major leaks—are prioritised and attended to as quickly as possible. Non-urgent repairs are handled within a reasonable timeframe in line with landlord obligations.',
                ],
            ],
            [
                'name' => 'Am I allowed to redecorate or make changes to the property?',
                'acceptedAnswer' => [
                    'text' => 'Please request permission before making any alterations or redecorating. Minor decorative changes may be agreed in writing; structural or permanent alterations are not permitted without the landlord’s written consent.',
                ],
            ],
            [
                'name' => 'Can I keep a pet in the property, and are there any conditions?',
                'acceptedAnswer' => [
                    'text' => 'Pets are only permitted with the landlord’s prior written consent. If approved, you may be asked to sign a pet addendum and you will be responsible for any additional cleaning or damage at the end of the tenancy.',
                ],
            ],
            [
                'name' => 'What happens at the end of the tenancy if I want to stay on?',
                'acceptedAnswer' => [
                    'text' => 'If both parties wish to continue, the tenancy can roll into a periodic (month-to-month) agreement or be renewed for a further fixed term. We will contact you towards the end of your tenancy to confirm arrangements.',
                ],
            ],
            [
                'name' => 'What notice period do I need to give if I want to move out?',
                'acceptedAnswer' => [
                    'text' => 'You must give at least one month’s written notice, and your notice must align with your rent due date. For example, if rent is due on the 10th, your notice should end on the 9th of the relevant month.',
                ],
            ],
            [
                'name' => 'How often can the landlord increase the rent, and by how much?',
                'acceptedAnswer' => [
                    'text' => 'During a fixed term, rent can only be increased if your agreement allows for it or by mutual consent. On a periodic tenancy, a landlord can propose one increase per year, which must be fair and in line with local market rents.',
                ],
            ],
            [
                'name' => 'What are my rights if the landlord wants to sell the property or move back in?',
                'acceptedAnswer' => [
                    'text' => 'Your tenancy continues to be valid if the landlord sells; any new owner must honour it until it ends. To regain possession, the landlord must follow the correct legal process (e.g., Section 21 or Section 8) and provide the required notice.',
                ],
            ],
        ];

        $jsonLdScript = app(\App\Support\StructuredData::class)->tenantsPage(
            orgName: config('app.name', 'Steve Morris & Son LLP'),
            orgUrl: url('/'),
            pageUrl: route('tenants'),
            pageTitle: $pageTitle,
            pageDescription: $pageDescription,
            faqs: $tenantFaqs,
            includeBreadcrumbs: false, // flip to true if you render breadcrumbs in UI
        );

        return view('tenants', [
            'title' => $pageTitle,
            'description' => $pageDescription,
            'tenantFaqs' => $tenantFaqs,
            'jsonLdScript' => $jsonLdScript,
        ]);
    }
}
