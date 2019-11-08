<?php

namespace Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    protected $devUsers = [
        'saklakyo@gmail.com' => 'Marius',
        'richard.jacobsen@lurn.com' => 'Richard Jacobsen',
        'jeremy.larson@lurn.com' => 'Jeremy Larson',
    ];

    public function run()
    {
        foreach ($this->devUsers as $email => $name) {
            factory(User::class)->create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
                'status' => 99,
            ]);
        }

        factory(User::class, 25)->create();
    }
}
