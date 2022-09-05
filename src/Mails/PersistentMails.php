<?php

namespace ProtoneMedia\LaravelDuskFakes\Mails;

use Illuminate\Support\Facades\Mail;

trait PersistentMails
{
    public function setUpPersistentMails()
    {
        Mail::swap(
            app(UncachedPersistentMailFake::class)
        );
    }

    public function tearDownPersistentMails()
    {
        Mail::cleanStorage();
    }
}
