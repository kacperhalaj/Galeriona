<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeAdmin extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Create a new admin user';

    public function handle()
    {
        $this->info('Tworzenie nowego administratora...');
        
        $firstName = $this->ask('Podaj imię');
        $lastName = $this->ask('Podaj nazwisko'); 
        $username = $this->ask('Podaj nazwę użytkownika');
        $email = $this->ask('Podaj email');
        $password = $this->secret('Podaj hasło (min. 8 znaków)');
        $passwordConfirmation = $this->secret('Potwierdź hasło');
        
        // Walidacja
        if ($password !== $passwordConfirmation) {
            $this->error('Hasła nie są identyczne!');
            return 1;
        }
        
        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ], [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255', 
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
        
        if ($validator->fails()) {
            $this->error('Błędy walidacji:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }
        
        // Tworzenie administratora
        try {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->info('Administrator został utworzony pomyślnie!');
            $this->info('Imię: ' . $user->first_name);
            $this->info('Nazwisko: ' . $user->last_name);
            $this->info('Username: ' . $user->username);
            $this->info('Email: ' . $user->email);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Błąd podczas tworzenia administratora: ' . $e->getMessage());
            return 1;
        }
    }
}