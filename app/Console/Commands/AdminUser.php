<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Command
{
    protected $signature = 'legion:admin {username} {--reset-password}';

    protected $description = 'Create an owner or explicitly reset an existing owner password (interactive).';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();
        if ($user && ! $this->option('reset-password')) {
            $this->error('User exists. Use --reset-password only for an intentional reset.');

            return 1;
        }
        if (! $this->input->isInteractive()) {
            $this->error('An interactive terminal is required to enter a password securely.');

            return 1;
        }
        $password = $this->secret('Новый пароль (не менее 12 символов)');
        if (strlen($password) < 12) {
            $this->error('Password is too short.');

            return 1;
        }
        $user ??= new User(['username' => $this->argument('username'), 'first_name' => '', 'last_name' => '', 'email' => '']);
        $user->forceFill(['password' => Hash::make($password), 'is_active' => true, 'is_staff' => true, 'is_superuser' => true])->save();
        $this->info('Owner saved. Password was not written to any file.');

        return 0;
    }
}
