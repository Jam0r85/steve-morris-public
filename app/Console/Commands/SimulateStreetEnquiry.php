<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Livewire\Properties\EnquiryForm;
use App\Models\Property;
use Illuminate\Console\Command;

class SimulateStreetEnquiry extends Command
{
    protected $signature = 'street:enquiry:simulate {provider_id} {--email=test@example.com}';

    protected $description = 'Simulate sending an enquiry to the Street API for a property (lookup by provider_id) with fallback + visitor confirmation emails';

    public function handle(): int
    {
        $providerId = $this->argument('provider_id');
        $email = $this->option('email');

        $property = Property::where('provider_id', $providerId)->first();

        if ( ! $property) {
            $this->error("❌ Property with provider_id {$providerId} not found.");

            return Command::FAILURE;
        }

        /** @var EnquiryForm $form */
        $form = app(EnquiryForm::class);
        $form->mount($property);

        // Dummy form data
        $form->first_name = 'Testy';
        $form->last_name = 'McTestface';
        $form->email = $email;
        $form->telephone_number = '07123456789';
        $form->message = 'This is a test enquiry submitted via artisan command.';
        $form->request_viewing = true;
        $form->request_valuation = false;

        $this->info("▶️ Submitting enquiry for property provider_id {$providerId} ({$property->address_single_line})…");

        $form->submit();

        if ($form->submitted) {
            $this->info('✅ Enquiry simulation completed. Check Street API / branch fallback email / visitor confirmation email.');
        } else {
            $this->warn('⚠️ Submission did not mark as submitted. Check logs for details.');
        }

        return Command::SUCCESS;
    }
}
