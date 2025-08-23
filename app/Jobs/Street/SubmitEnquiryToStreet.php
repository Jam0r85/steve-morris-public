<?php

declare(strict_types=1);

namespace App\Jobs\Street;

use App\Mail\BranchEnquiryFallbackMail;
use App\Mail\VisitorEnquiryConfirmationMail;
use App\Models\Property;
use App\Traits\CircuitBreaksStreet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SubmitEnquiryToStreet implements ShouldQueue
{
    use CircuitBreaksStreet, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public array $attributes,        // first_name, last_name, email_address, telephone_number, message, custom_source, property_uuid, branch_uuid, request_viewing, request_valuation
        public int $propertyId
    ) {}

    public function handle(): void
    {
        /** @var Property|null $property */
        $property = Property::find($this->propertyId);

        $payload = [
            'data' => [
                'type' => 'enquiry',
                'attributes' => $this->attributes + [
                    'property_to_sell' => false,
                    'property_to_let' => false,
                ],
            ],
        ];

        $apiOk = false;

        try {
            if ( ! $this->streetBreakerOpen('open:enquiries')) {
                $response = Http::withToken((string) config('services.street.open.token'))
                    ->baseUrl(mb_rtrim((string) config('services.street.open.url'), '/'))
                    ->accept('application/vnd.api+json')
                    ->contentType('application/vnd.api+json')
                    ->timeout(15)
                    ->retry(2, 250, throw: false)
                    ->post('/enquiries', $payload);

                $apiOk = $response->successful();

                if ($apiOk) {
                    $this->streetBreakerRecordSuccess('open:enquiries');
                } else {
                    $this->streetBreakerRecordFailure('open:enquiries');
                    Log::warning('Street enquiry failed', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } else {
                Log::warning('Street circuit breaker OPEN — skipping Open API call, using email fallback.');
            }
        } catch (Throwable $e) {
            $this->streetBreakerRecordFailure('open:enquiries');
            Log::error('Street enquiry exception', ['message' => $e->getMessage()]);
        }

        // Fallback email to branch if API failed or breaker open
        if ( ! $apiOk) {
            try {
                $to = config('services.street.branch_email') ?: config('services.street.failed_jobs_email');
                $mailable = new BranchEnquiryFallbackMail($payload, $property);

                // Set Reply-To to visitor for quick replies
                $replyName = mb_trim(($this->attributes['first_name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? ''));
                $mailable->replyTo(new Address($this->attributes['email_address'], $replyName));

                Mail::to($to)->send($mailable);
            } catch (Throwable $e) {
                Log::error('Branch fallback email failed', ['message' => $e->getMessage()]);
            }
        }

        // Always send visitor confirmation (queued)
        try {
            Mail::to($this->attributes['email_address'])
                ->queue(new VisitorEnquiryConfirmationMail(
                    firstName: (string) ($this->attributes['first_name'] ?? ''),
                    property: $property
                ));
        } catch (Throwable $e) {
            Log::error('Visitor confirmation email queue failed', ['message' => $e->getMessage()]);
        }
    }
}
