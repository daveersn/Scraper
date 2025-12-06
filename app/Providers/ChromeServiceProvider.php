<?php

namespace App\Providers;

use App\Http\Integrations\Browser\Browser;
use HeadlessChromium\AutoDiscover;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
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
                'debugLogger' => Log::channel(),
                'userAgent' => config('scraping.chrome.user_agent'),
                'windowSize' => [
                    config('scraping.chrome.viewport.width'),
                    config('scraping.chrome.viewport.height'),
                ],
                'keepAlive' => true,
                'ignoreCertificateErrors' => true,
                'customFlags' => ['--disable-web-security'],
            ]);

            return $factory;
        });

        $this->app->bind(Browser::class, function (Application $app) {
            $socketPath = config('chrome.socket_file');

            try {
                $socket = trim(File::get($socketPath));
            } catch (FileNotFoundException) {
                $socket = null;
            }

            if (blank($socket)) {
                return $this->createBrowser(
                    factory: $app->get(BrowserFactory::class),
                    socketPath: $socketPath
                );
            }

            try {
                // Core ChromePHP Browser
                $browser = BrowserFactory::connectToBrowser($socket);
            } catch (BrowserConnectionFailed) {
                return $this->createBrowser(
                    factory: $app->get(BrowserFactory::class),
                    socketPath: $socketPath
                );
            }

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

    private function createBrowser(BrowserFactory $factory, string $socketPath): Browser
    {
        $browser = $factory->createBrowser([
            'headless' => config('chrome.headless'),
        ]);
        File::put($socketPath, $browser->getSocketUri());

        return new Browser($browser->getConnection());
    }
}
