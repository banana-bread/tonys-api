<?php

namespace App\Models\Interfaces;

interface ReceivesBookingNotifications
{
    public function wasSentBookingConfirmation(): string;
}
