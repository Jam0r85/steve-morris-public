<?php

declare(strict_types=1);

namespace App\Livewire\Properties;

use App\Jobs\Street\SubmitEnquiryToStreet;
use App\Models\Property;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Rule;
use Livewire\Component;

class EnquiryForm extends Component
{
    public Property $property;

    // Form fields
    #[Rule('required|string|min:2')]
    public string $first_name = '';

    #[Rule('required|string|min:2')]
    public string $last_name = '';

    #[Rule('required|email')]
    public string $email = '';

    #[Rule('nullable|string|min:6')]
    public string $telephone_number = '';

    #[Rule('required|string|min:5')]
    public string $message = '';

    public bool $request_viewing = false;

    public bool $request_valuation = false;

    // Honeypot
    public string $website = '';

    // Derived/context
    public ?string $property_uuid = null;

    public ?string $branch_uuid = null;

    public string $custom_source = 'Website';

    public bool $submitted = false;

    public ?string $errorMessage = null;

    public function mount(Property $property): void
    {
        $this->property = $property;

        $this->property_uuid = $this->looksLikeUuid((string) $property->provider_id)
            ? (string) $property->provider_id
            : null;

        // Prefer property branch_id (Street UUID), fallback to config
        $this->branch_uuid = $this->looksLikeUuid((string) ($property->branch_id ?? ''))
            ? (string) $property->branch_id
            : (config('services.street.branch_uuid') ?: null);

        $this->custom_source = (string) (config('services.street.custom_source') ?: 'Website');
    }

    public function submit(): void
    {
        $this->errorMessage = null;
        $this->submitted = false;

        // Honeypot
        if ('' !== mb_trim($this->website)) {
            $this->submitted = true;
            $this->resetForm();
            $this->resetValidation();

            return;
        }

        // Rate limits
        $emailHash = sha1((string) $this->email);
        $ip = request()->ip() ?: '0.0.0.0';
        $prop = (string) ($this->property->provider_id ?? 'na');

        // Per-property: 2/min
        $perPropertyKey = "enquiry:{$emailHash}:{$ip}:{$prop}";
        if ( ! RateLimiter::attempt($perPropertyKey, 2, fn () => null, 60)) {
            $this->errorMessage = 'Please wait a moment before submitting again for this property.';

            return;
        }

        // Global: 5/min
        $globalKey = "enquiry-global:{$emailHash}:{$ip}";
        if ( ! RateLimiter::attempt($globalKey, 5, fn () => null, 60)) {
            $this->errorMessage = 'You’re sending messages a bit quickly. Please wait a moment and try again.';

            return;
        }

        // Daily: 30/day
        $dailyKey = "enquiry-daily:{$emailHash}:{$ip}";
        if ( ! RateLimiter::attempt($dailyKey, 30, fn () => null, 86400)) {
            $this->errorMessage = 'Daily enquiry limit reached. Please try again tomorrow.';

            return;
        }

        $this->validate();

        // Build attributes for the job
        $attrs = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email_address' => $this->email,
            'telephone_number' => $this->telephone_number ?: null,
            'message' => $this->message,
            'custom_source' => $this->custom_source,
            'property_uuid' => $this->property_uuid,
            'branch_uuid' => $this->branch_uuid,
            'request_viewing' => $this->request_viewing,
            'request_valuation' => $this->request_valuation,
        ];

        // Queue everything heavy
        SubmitEnquiryToStreet::dispatch($attrs, $this->property->id);

        // Instant UX
        $this->submitted = true;
        $this->resetForm();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.properties.enquiry-form');
    }

    // Helpers
    protected function resetForm(): void
    {
        $this->reset([
            'first_name',
            'last_name',
            'email',
            'telephone_number',
            'message',
            'request_viewing',
            'request_valuation',
            'website',
        ]);
    }

    protected function looksLikeUuid(string $v): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}\-?[0-9a-fA-F]{4}\-?[1-5][0-9a-fA-F]{3}\-?[89abAB][0-9a-fA-F]{3}\-?[0-9a-fA-F]{12}$/',
            $v
        );
    }
}
