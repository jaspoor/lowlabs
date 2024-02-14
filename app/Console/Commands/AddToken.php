<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AddToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-token {user} {token-name} {token-abilities?*}';

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
        $user = User::find($this->argument('user'));
        $tokenName = $this->argument('token-name');
        $tokenAbilities = $this->argument('token-abilities');
        
        $token = $user->createToken($tokenName, $tokenAbilities); 

        $this->info('Token: ' . $token->plainTextToken);
    }
}
