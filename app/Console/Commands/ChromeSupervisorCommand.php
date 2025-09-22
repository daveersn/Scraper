<?php

namespace App\Console\Commands;

use HeadlessChromium\Browser;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ChromeSupervisorCommand extends Command
{
    protected $signature = 'chrome:supervise {--headless=1}';

    protected $description = 'Start and supervise a persistent headless Chrome instance';

    private ?Browser $browser = null;

    private ?string $socket = null;

    private BrowserFactory $browserFactory;

    private bool $running = true;

    public function handle(BrowserFactory $browserFactory): int
    {
        $this->browserFactory = $browserFactory;

        $socketFile = config('chrome.socket_file');
        $interval = (int) config('chrome.check_interval');

        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, fn () => $this->shutdown());
        pcntl_signal(SIGINT, fn () => $this->shutdown());

        while ($this->running) {
            if (! $this->isBrowserRunning()) {
                $this->startBrowser($socketFile);
            }

            sleep($interval);
        }

        $this->closeBrowser();

        return self::SUCCESS;
    }

    private function startBrowser(string $socketFile): void
    {
        $this->closeBrowser();

        $headless = $this->option('headless');
        $headless = ! ($headless === '0' || $headless === 'false');

        $this->browser = $this->browserFactory->createBrowser([
            'headless' => $headless,
            'ignoreCertificateErrors' => true,
            'enableImages' => true,
            'noSandbox' => true,
            'keepAlive' => true,
            'customFlags' => ['--disable-web-security'],
        ]);

        $this->socket = $this->browser->getSocketUri();

        File::put($socketFile, $this->socket);
    }

    private function isBrowserRunning(): bool
    {
        if (! $this->socket) {
            return false;
        }

        try {
            BrowserFactory::connectToBrowser($this->socket);
        } catch (BrowserConnectionFailed) {
            return false;
        }

        return true;
    }

    private function shutdown(): void
    {
        $this->running = false;
    }

    private function closeBrowser(): void
    {
        if ($this->browser === null) {
            return;
        }

        try {
            $this->browser->close();
        } catch (\Throwable $e) {
            report($e);
        }

        $this->browser = null;
    }
}
