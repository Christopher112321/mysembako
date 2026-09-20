<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('Masukkan nama admin');
        $email = $this->option('email') ?: $this->ask('Masukkan email admin');
        $password = $this->option('password') ?: $this->secret('Masukkan password admin');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            $this->error('Validasi gagal:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        // Create admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => 1, // Admin role
        ]);

        $this->info('Admin berhasil dibuat!');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $user->id],
                ['Nama', $user->name],
                ['Email', $user->email],
                ['Role ID', $user->role_id],
            ]
        );

        return 0;
    }
}

