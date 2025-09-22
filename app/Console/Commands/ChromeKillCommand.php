<?php

namespace App\Console\Commands;

use HeadlessChromium\Browser;
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
        $process = Process::run('pgrep -f "chrome.*--headless"');

        if ($process->failed()) {
            throw new \RuntimeException("Could not fetch headless chrome processes: {$process->output()}");
        }

        $pids = explode("\n", trim($process->output()));

        foreach ($pids as $pid) {
            Process::run("kill -9 $pid");
        }
    }
}
