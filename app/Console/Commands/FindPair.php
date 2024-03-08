<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Console\Command;

class FindPair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:find-pair {array : The array of numbers} {target : The target number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find pair';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $array = array_unique(json_decode($this->argument('array'), true));
        $targetNumber = $this->argument('target');

        $pair = (new Helper())->findPair($array, $targetNumber);

        if ($pair)
            $this->info("Pair found: $pair[0], $pair[1]");
        else
            $this->info("No pair found with sum greater than $targetNumber.");
    }
}
