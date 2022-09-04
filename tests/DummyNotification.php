<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Notifications\Notification;

class DummyNotification extends Notification
{
    public function via()
    {
        return ['mail'];
    }

    public function toMail()
    {
    }
}
