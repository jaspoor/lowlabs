<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class AddClient extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-client {clientName}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually creates a new client';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientName = $this->argument('clientName');
        
        try {
            // Create a new client.
            $client = new Client();
            $client->name = $clientName;
            $client->save();
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
