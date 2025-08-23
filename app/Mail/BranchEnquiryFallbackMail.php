<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BranchEnquiryFallbackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $payload,
        public ?Property $property = null
    ) {}

    public function build()
    {
        return $this->subject('Website enquiry (fallback)')
            // replyTo is added dynamically in the Job
            ->view('emails.branch-fallback');
    }
}
