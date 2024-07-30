<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '6G');
        $faker = Faker::create();
        $faker->unique(true);
        $genderOptions = ['male', 'female'];
        $hashedPassword = Hash::make('password');
        $batchSize = 5000;
        $users = [];
        $start = microtime(true);
        for ($i = 0; $i < 1000000; $i++) {
            $uniqueSuffix = microtime(true) . random_int(1, 1000);
            $email = $faker->safeEmail . $uniqueSuffix . '@example.com';

            $users[] = [
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'birth_date' => $faker->date,
                'gender' => $faker->randomElement($genderOptions),
                'hobbies' => $faker->sentence,
                'city' => $faker->city,
                'email' => $email,
                'password' => $hashedPassword,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($i % $batchSize === 0 && $i > 0) {
                DB::table('users')->insert($users);
                $users = [];
                $finish = microtime(true);
                dump( $finish - $start);
                $faker->unique(true);
            }
        }

        if (!empty($users)) {
            DB::table('users')->insert($users);
        }
    }
}
