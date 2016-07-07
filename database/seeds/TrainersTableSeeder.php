<?php

use Illuminate\Database\Seeder;

class TrainersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Trainer::create([
            
            'name' => 'Girish Singh',
            'email' => 'girish13@gmail.com',
            'password' => Hash::make('girish123'),
            'active' => 1,

        ]);    
    }
}
