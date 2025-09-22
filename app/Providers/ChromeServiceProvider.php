<?php

namespace App\Providers;

use App\Scraping\Browser\Browser;
use HeadlessChromium\AutoDiscover;
use HeadlessChromium\BrowserFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class ChromeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const array CHROME_BINARIES = ['google-chrome', 'chrome', 'chromium-browser', 'chromium'];

    public function register(): void
    {
        $this->app->bind(BrowserFactory::class, function (): BrowserFactory {
            $binary = $this->getChromeBinary() ?? (new AutoDiscover)->guessChromeBinaryPath();

            if ($binary === null) {
                throw new RuntimeException('Chrome binary could not be determined.');
            }

            Log::debug("Instancing BrowserFactory with Chrome binary: $binary");

            $factory = new BrowserFactory($binary);

            $factory->setOptions([
                'ignoreCertificateErrors' => true,
                'windowSize' => [1920, 1000],
                'debugLogger' => Log::channel(),
            ]);

            return $factory;
        });

        $this->app->bind(Browser::class, function () {
            $socketPath = config('chrome.socket_file');

            if (! File::exists($socketPath)) {
                throw new \RuntimeException("Chrome socket file not found at {$socketPath}");
            }

            $socket = trim(File::get($socketPath));
            if ($socket === '') {
                throw new \RuntimeException("Chrome socket file {$socketPath} is empty");
            }

            // Core ChromePHP Browser
            $browser = BrowserFactory::connectToBrowser($socket, [
                'debugLogger' => Log::channel(),
            ]);

            // Our extended Browser with the core connection
            return new Browser($browser->getConnection());
        });
    }

    public function boot(): void {}

    public function provides(): array
    {
        return [
            BrowserFactory::class,
            Browser::class,
        ];
    }

    private function getChromeBinary(): ?string
    {
        return collect(self::CHROME_BINARIES)
            ->map(function ($command) {
                $process = Process::run("command -v $command");

                return $process->successful() && strlen($process->output()) > 0
                    ? str($process->output())
                        ->explode("\n")
                        ->first()
                    : null;
            })
            ->filter()
            ->first();
    }
}
