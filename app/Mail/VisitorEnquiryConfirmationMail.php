<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisitorEnquiryConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $firstName,
        public ?Property $property = null
    ) {}

    public function build()
    {
        $branchReply = config('services.street.branch_email') ?: config('services.street.failed_jobs_email');

        return $this->subject('We’ve received your enquiry')
            ->replyTo($branchReply)
            ->view('emails.visitor-confirmation');
    }
}
