<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            
            'name' => 'Pulkit',
            'email' => 'gambhirpulkit@gmail.com',
            'password' => Hash::make('pulkit123'),
            'phone' => '8010127137',
            'phone_flag' => 1,

        ]);        
    }
}
