<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Random\RandomException;

class UserSeeder extends Seeder
{
    private int $requiredUserCount;

    public function __construct()
    {
        $this->requiredUserCount = env('COUNT_OF_USERS', 150);
    }

    public function run()
    {
        $filePath = database_path('seeders/csv/users.csv');
        $postFilePath = database_path('seeders/csv/posts.csv');
        $friendFilePath = database_path('seeders/csv/friends.csv');

        if ($this->isCsvUserCountValid($filePath)) {
            $this->seedFromCsv($filePath);
        } else {
            $this->clearCsvFiles($filePath, $postFilePath, $friendFilePath);
            $this->seedFromFaker();
            $this->exportToCsv($filePath);
        }

        $this->resetUserIdSequence();
    }

    private function isCsvUserCountValid(string $filePath): bool
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        $handle = fopen($filePath, 'r');
        $lineCount = 0;

        while (fgetcsv($handle) !== false) {
            $lineCount++;
        }

        fclose($handle);

        return $lineCount - 1 === $this->requiredUserCount;
    }

    private function clearCsvFiles(string $userFilePath, string $postFilePath, string $friendFilePath): void
    {
        File::delete([$userFilePath, $postFilePath, $friendFilePath]);
    }

    private function seedFromCsv(string $filePath): void
    {
        ini_set('memory_limit', '2G');
        $start = microtime(true);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            dump("Файл не найден или недоступен для чтения: {$filePath}");
            return;
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

        $batchSize = 5000;
        $batchData = [];

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle)) !== false) {
                $rowData = array_combine($header, $data);
                foreach ($rowData as $key => $value) {
                    if ($value === '') {
                        $rowData[$key] = null;
                    }
                }

                $batchData[] = $rowData;

                if (count($batchData) === $batchSize) {
                    DB::table('users')->insert($batchData);
                    $batchData = [];
                    gc_collect_cycles();
                }
            }

            if (!empty($batchData)) {
                DB::table('users')->insert($batchData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dump('Ошибка при импорте из CSV: ' . $e->getMessage());
        }

        fclose($handle);

        $finish = microtime(true);
        dump('CSV Import Time: ' . ($finish - $start) . ' seconds');
    }

    private function seedFromFaker(): void
    {
        ini_set('memory_limit', '6G');
        $faker = Faker::create();
        $faker->unique(true);
        $genderOptions = ['male', 'female'];
        $hashedPassword = Hash::make('password');
        $batchSize = 5000;
        $users = [];
        $start = microtime(true);

        for ($i = 0; $i < $this->requiredUserCount; $i++) {
            $uniqueSuffix = microtime(true) . random_int(1, 1000);
            $email = $faker->safeEmail . $uniqueSuffix . '@example.com';
            $now = now();

            $users[] = [
                'name' => $faker->firstName,
                'surname' => $faker->lastName,
                'birth_date' => $faker->date,
                'gender' => $faker->randomElement($genderOptions),
                'hobbies' => $faker->sentence,
                'city' => $faker->city,
                'email' => $email,
                'email_verified_at' => $now,
                'password' => $hashedPassword,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($i % $batchSize === 0 && $i > 0) {
                DB::table('users')->insert($users);
                $users = [];
                $finish = microtime(true);
                dump($finish - $start, 'Batch: ' . ($i / $batchSize));
                $faker->unique(true);
            }
        }

        if (!empty($users)) {
            DB::table('users')->insert($users);
        }

        $finish = microtime(true);
        dump('Faker Generation Time: ' . ($finish - $start) . ' seconds');
    }

    private function exportToCsv(string $filePath): void
    {
        $handle = fopen($filePath, 'w');

        $users = DB::table('users')->get();

        fputcsv($handle, array_keys((array) $users->first()));

        foreach ($users as $user) {
            fputcsv($handle, (array) $user);
        }

        fclose($handle);

        dump('Exported users to CSV: ' . $filePath);
    }

    private function resetUserIdSequence(): void
    {
        $maxId = DB::table('users')->max('id');
        DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), ?, false)", [$maxId + 1]);
    }
}
