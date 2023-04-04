<?php

namespace ProtoneMedia\LaravelDuskFakes\Mails;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Testing\Fakes\MailFake;

class PersistentMailFake extends MailFake
{
    private string $directory;

    private string $storage;

    public function __construct()
    {
        $this->directory = rtrim(config('dusk-fakes.mails.storage_root'), '/');

        $this->storage = $this->directory.'/serialized';

        (new Filesystem)->ensureDirectoryExists($this->directory);

        $this->loadMails();
    }

    public function cleanStorage()
    {
        (new Filesystem)->cleanDirectory($this->directory);
    }

    public function loadMails(): self
    {
        $unserialized = file_exists($this->storage)
            ? rescue(fn () => unserialize(file_get_contents($this->storage)), [], false)
            : [];

        $this->mailables = $unserialized['mailables'] ?? [];
        $this->queuedMailables = $unserialized['queuedMailables'] ?? [];

        return $this;
    }

    public function send($view, array $data = [], $callback = null)
    {
        parent::send($view, $data, $callback);

        $this->storeMails();
    }

    public function queue($view, $queue = null)
    {
        parent::queue($view, $queue);

        $this->storeMails();
    }

    private function storeMails()
    {
        (new Filesystem)->ensureDirectoryExists($this->directory);

        file_put_contents($this->storage, serialize([
            'mailables' => $this->mailables,
            'queuedMailables' => $this->queuedMailables,
        ]));
    }
}
