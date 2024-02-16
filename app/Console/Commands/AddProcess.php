<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Process;
use App\Models\ProcessStatus;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class AddProcess extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-process {clientName} {processName} {statusNames*}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually creates a new process';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Enter client name, if not present via command line option
        $clientName = $this->argument('clientName');
        $processName = $this->argument('processName');
        $statusNames = $this->argument('statusNames');

        // Find client
        $client = Client::firstWhere(['name' => $clientName]);
        if (!$client) {
            $this->error("Client {$clientName} not found");
        }

        try {
            // Create a new process
            $process = new Process;
            $process->client_id = $client->id;
            $process->name = $processName;
            $process->save();

            // Create process statuses
            $statusesData = array_map(function ($statusName) {
                return ['name' => $statusName];
            }, $statusNames);

            $process->processStatuses()->createMany($statusesData);            

            $process->refresh();
        }
        catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        // Success message
        $this->info('Client created successfully!');
        $this->info('New client id: ' . $client->id);
    }
}
