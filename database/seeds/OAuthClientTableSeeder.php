<?php

use Illuminate\Database\Seeder;

class OAuthClientTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\OAuthClient::create([
            'id' => 'g3b259fde3ed9ff3843819b',
            'secret' => '3d7f5f8f793d59c25502l0ae8c4a95b',
            'name' => 'android'

        ]);

        \App\OAuthClient::create([
            'id' => 'g3b259fde3ed9ff3843833b',
            'secret' => '3d7h5f8f793d59c25502c0ae8c4a95b',
            'name' => 'ios'

        ]);

        \App\OAuthClient::create([
            'id' => 'g3b259fre3ed9ff3843839b',
            'secret' => '3d7f5f8f795d59c25502c0ae8c4a95b',
            'name' => 'web'

        ]);        

        \App\OAuthClient::create([
            'id' => 'g3b259gre3ed9ff3143839b',
            'secret' => '3d7f5f8f795d51c25502c0ae8c4a95b',
            'name' => 'android-trainer'

        ]);                

    }
}
