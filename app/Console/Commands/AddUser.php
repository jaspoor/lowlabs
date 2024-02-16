<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Hash;

class AddUser extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-user {clientName} {fullName} {userEmail}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientName = $this->argument('clientName');
        $fullName = $this->argument('fullName');
        $userEmail = $this->argument('userEmail');

        // Always enter password from userinput for more security.
        $password = $this->secret('Please enter a new password.');

        // Find client
        $client = Client::firstWhere(['name' => $clientName]);
        if (!$client) {
            $this->error("Client {$clientName} not found");
        }

        try {
            // Create a new user.
            $user = new User;
            $user->client_id = $client->id;
            $user->name = $fullName;
            $user->email = $userEmail;
            $user->password = Hash::make($password);
            $user->save();
        }
        catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        // Success message
        $this->info('User created successfully!');
        $this->info('New user id: ' . $user->id);
    }
}
