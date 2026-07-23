<?php

namespace Modules\EMAILMARKETING\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarketingCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $htmlBody,
        public ?string $fromAddress = null,
        public ?string $fromName = null,
        public ?string $replyToAddress = null,
    ) {}

    public function build(): self
    {
        if ($this->fromAddress) {
            $this->from($this->fromAddress, $this->fromName ?: config('app.name'));
        }

        if ($this->replyToAddress) {
            $this->replyTo($this->replyToAddress);
        }

        return $this->subject($this->subjectLine)->html($this->htmlBody);
    }
}
