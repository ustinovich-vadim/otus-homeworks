<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    private int $requiredUserCount;

    public function __construct()
    {
        $this->requiredUserCount = env('COUNT_OF_USERS', 150);
    }

    public function run()
    {
        $filePath = database_path('seeders/csv/posts.csv');
        $userFilePath = database_path('seeders/csv/users.csv');
        $friendFilePath = database_path('seeders/csv/friends.csv');

        if ($this->isUserCountValid() && file_exists($filePath)) {
            $this->seedFromCsv($filePath);
        } else {
            $this->clearCsvFiles($userFilePath, $filePath, $friendFilePath);
            $this->seedFromFaker();
            $this->exportToCsv($filePath);
        }

        $this->resetPostIdSequence();
    }

    private function isUserCountValid(): bool
    {
        $userCount = DB::table('users')->count();
        return $userCount === $this->requiredUserCount;
    }

    private function clearCsvFiles(string $userFilePath, string $postFilePath, string $friendFilePath): void
    {
        File::delete([$userFilePath, $postFilePath, $friendFilePath]);
    }

    private function seedFromCsv(string $filePath): void
    {
        ini_set('memory_limit', '6G');
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
                    DB::table('posts')->insert($batchData);
                    $batchData = [];
                    gc_collect_cycles();
                }
            }

            if (!empty($batchData)) {
                DB::table('posts')->insert($batchData);
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
        ini_set('memory_limit', '8G');
        $faker = Faker::create();
        $batchSize = 5000;
        $posts = [];
        $users = DB::table('users')->pluck('id');

        $start = microtime(true);
        $userIndex = 1;

        foreach ($users as $userId) {
            $postCount = rand(10, 100);

            for ($i = 0; $i < $postCount; $i++) {
                $posts[] = [
                    'user_id' => $userId,
                    'content' => $faker->paragraphs(rand(1, 5), true),
                    'created_at' => $faker->dateTimeThisYear,
                    'updated_at' => now(),
                ];

                if (count($posts) === $batchSize) {
                    DB::table('posts')->insert($posts);
                    $finish = microtime(true);

                    dump('User Index: ' . $userIndex, 'Time taken: ' . ($finish - $start) . ' seconds');
                    $posts = [];
                    gc_collect_cycles();
                }
            }

            $userIndex++;
        }

        if (!empty($posts)) {
            DB::table('posts')->insert($posts);
            $finish = microtime(true);
            dump('Final Batch', 'User Index: ' . $userIndex, 'Total Time taken: ' . ($finish - $start) . ' seconds');
        }
    }

    private function exportToCsv(string $filePath): void
    {
        $handle = fopen($filePath, 'w');
        $posts = DB::table('posts')->get();

        fputcsv($handle, array_keys((array)$posts->first()));

        foreach ($posts as $post) {
            fputcsv($handle, (array)$post);
        }

        fclose($handle);

        dump('Exported posts to CSV: ' . $filePath);
    }

    private function resetPostIdSequence(): void
    {
        $maxId = DB::table('posts')->max('id');
        DB::statement("SELECT setval(pg_get_serial_sequence('posts', 'id'), ?, false)", [$maxId + 1]);
    }
}
