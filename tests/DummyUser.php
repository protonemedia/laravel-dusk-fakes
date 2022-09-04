<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;

class DummyUser extends User
{
    use Notifiable;
}
