<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FriendSeeder extends Seeder
{
    public function run()
    {
        $filePath = database_path('seeders/csv/friends.csv');

        if (file_exists($filePath)) {
            $this->seedFromCsv($filePath);
        } else {
            $this->seedFromFaker();
            $this->exportToCsv($filePath);
        }

        $this->resetFriendIdSequence();
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
                    DB::table('friends')->insert($batchData);
                    $batchData = [];
                    gc_collect_cycles();
                }
            }

            if (!empty($batchData)) {
                DB::table('friends')->insert($batchData);
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

        $userIds = DB::table('users')->pluck('id')->toArray();
        $friendships = [];
        $batchSize = 5000;

        $start = microtime(true);

        foreach ($userIds as $userId) {
            $randomFriends = $faker->randomElements($userIds, rand(10, 100));

            foreach ($randomFriends as $friendId) {
                if ($userId !== $friendId) {
                    $friendships[] = [
                        'user_id' => $userId,
                        'friend_id' => $friendId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (count($friendships) >= $batchSize) {
                DB::table('friends')->insert($friendships);
                $friendships = [];
                gc_collect_cycles();
            }
        }

        if (!empty($friendships)) {
            DB::table('friends')->insert($friendships);
        }

        $finish = microtime(true);
        dump('Faker Generation Time: ' . ($finish - $start) . ' seconds');
    }

    private function exportToCsv(string $filePath): void
    {
        $handle = fopen($filePath, 'w');
        $friendships = DB::table('friends')->get();

        fputcsv($handle, array_keys((array)$friendships->first()));

        foreach ($friendships as $friendship) {
            fputcsv($handle, (array)$friendship);
        }

        fclose($handle);

        dump('Exported friendships to CSV: ' . $filePath);
    }

    private function resetFriendIdSequence(): void
    {
        $maxId = DB::table('friends')->max('id');
        DB::statement("SELECT setval(pg_get_serial_sequence('friends', 'id'), ?, false)", [$maxId + 1]);
    }
}
