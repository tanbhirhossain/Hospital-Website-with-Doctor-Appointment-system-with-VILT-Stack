<?php

namespace Modules\WEBSITE_EXTRA\Interfaces;

use Modules\WEBSITE_EXTRA\Models\ContactMessage;

interface ContactMessageNotifierInterface
{
    public function notifyAdmin(ContactMessage $message): void;

    public function notifyCustomer(ContactMessage $message): void;
}
