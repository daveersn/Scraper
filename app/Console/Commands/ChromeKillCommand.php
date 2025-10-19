<?php

namespace App\Console\Commands;

use App\Browser\Browser;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ChromeKillCommand extends Command
{
    protected $signature = 'chrome:kill';

    protected $description = 'Kill headless chrome processes';

    public function handle(): void
    {
        try {
            app(Browser::class)->close();
        } catch (BrowserConnectionFailed) {
        }

        $this->killOrphanProcesses();
    }

    private function killOrphanProcesses(): void
    {
        try {
            $process = Process::run('pgrep chrome | grep -v artisan');

            $pids = explode("\n", trim($process->output()));

            foreach ($pids as $pid) {
                Process::run("kill -9 $pid");
            }
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
