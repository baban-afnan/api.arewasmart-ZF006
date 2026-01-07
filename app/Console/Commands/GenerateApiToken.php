<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:generate-api-token {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API token for a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User not found with email: {$email}");
            return 1;
        }

        $token = Str::random(60);
        $user->forceFill([
            'api_token' => $token,
        ])->save();

        $this->info("API Token generated successfully for {$user->first_name} {$user->last_name}");
        $this->info("Token: {$token}");
        
        return 0;
    }
}
