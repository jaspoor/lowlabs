<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class AddToken extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-token {userEmail} {tokenName} {tokenAbilities?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates and stores a new token for a given user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::firstWhere(['email' => $this->argument('userEmail')]);
        $tokenName = $this->argument('tokenName');
        $tokenAbilities = $this->argument('tokenAbilities');
        
        $token = $user->createToken($tokenName, $tokenAbilities); 

        $this->info('Token: ' . $token->plainTextToken);
    }
}
