<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteSuperAdmin extends Command
{
    protected $signature = 'cvs:promote-super-admin {email : User email address}';

    protected $description = 'Grant Super Admin access to a user by email';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            $this->error("No user found for email: {$email}");

            return self::FAILURE;
        }

        $user->is_super_admin = true;
        $user->save();

        $this->info("Super Admin enabled for {$user->name} ({$user->email}).");

        return self::SUCCESS;
    }
}
