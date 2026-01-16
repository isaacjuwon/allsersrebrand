<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewArtisansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = base_path('database/seeders/new_data.sql');

        if (!file_exists($sqlPath)) {
            $this->command->error("SQL file not found at: $sqlPath");
            return;
        }

        // Seed Users
        $this->seedUsers($sqlPath);

        // Seed Posts
        $this->seedPosts($sqlPath);
    }

    private function seedUsers($sqlPath): void
    {
        $handle = fopen($sqlPath, "r");
        if ($handle) {
            $inUsersTable = false;
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                // Detect start of users insert block
                if (str_starts_with($line, 'INSERT INTO `users`')) {
                    $inUsersTable = true;
                }

                // If we are in users table but encountered a new INSERT for another table
                if ($inUsersTable && str_starts_with($line, 'INSERT INTO `') && !str_starts_with($line, 'INSERT INTO `users`')) {
                    $inUsersTable = false;
                }

                // If line matches (int, ...) pattern
                if ($inUsersTable && str_starts_with($line, '(')) {
                    // Remove leading '(' and trailing '),' or ');'
                    $trimLine = rtrim($line, ',;');
                    $trimLine = ltrim($trimLine, '(');
                    $trimLine = rtrim($trimLine, ')');

                    // Parse CSV respecting single quotes
                    $row = str_getcsv($trimLine, ',', "'", "\\");

                    if (count($row) < 16) {
                        continue; // invalid row or parsing error
                    }

                    // Indices:
                    // 0: user_id, 1: username, 2: email, 3: password, 4: lat, 5: long, 6: lastname, 7: firstname, 8: pic, 9: gender, 10: work, 11: bio, 12: exp, 13: status, 14: phone, 15: address

                    $work = isset($row[10]) ? $row[10] : 'Guest';

                    // Role inference
                    $role = 'artisan';
                    if (strpos(strtolower($work), 'guest') !== false) {
                        $role = 'guest';
                    }

                    // Name composition
                    $firstName = $row[7] ?? '';
                    $lastName = $row[6] ?? '';
                    $name = trim("$firstName $lastName");
                    if (empty($name) || $name === 'NULL NULL') {
                        $name = $row[1] ?? 'User'; // Fallback to username
                    }
                    if ($firstName === 'NULL')
                        $name = trim("$lastName");
                    if ($lastName === 'NULL')
                        $name = trim("$firstName");
                    if ($name === 'NULL' || $name === '')
                        $name = str_replace('@', '', $row[1]);

                    // Profile Pic
                    $profilePic = $row[8] ?? 'no_pic.jpg';
                    if ($profilePic === 'NULL' || is_null($profilePic) || $profilePic === 'no_pic.jpg') {
                        $profilePic = null;
                    } else {
                        if (!str_starts_with($profilePic, 'profilePics/')) {
                            $profilePic = 'profilePics/' . $profilePic;
                        }
                    }

                    $userData = [
                        'id' => (int) $row[0],
                        'username' => str_replace('@', '', $row[1]),
                        'name' => $name,
                        'email' => $row[2],
                        'password' => $row[3],
                        'latitude' => ($row[4] === 'NULL') ? null : (float) $row[4],
                        'longitude' => ($row[5] === 'NULL') ? null : (float) $row[5],
                        'profile_picture' => $profilePic,
                        'gender' => $row[9] ?? 'Male',
                        'work' => $work === 'NULL' ? 'Guest' : $work,
                        'bio' => ($row[11] === 'NULL') ? null : $row[11],
                        'experience_year' => ($row[12] === 'NULL') ? null : $row[12],
                        'work_status' => ($row[13] === 'NULL') ? null : $row[13],
                        'phone_number' => ($row[14] === 'NULL') ? null : $row[14],
                        'address' => ($row[15] === 'NULL') ? null : $row[15],
                        'role' => $role,
                        'email_verified_at' => now(),
                    ];

                    $existingUser = \App\Models\User::where('username', $userData['username'])->first();
                    if ($existingUser && $existingUser->id !== $userData['id']) {
                        if ($existingUser->email !== $userData['email']) {
                            $userData['username'] = $userData['username'] . '_' . uniqid();
                        }
                    }

                    \App\Models\User::updateOrCreate(['id' => $userData['id']], $userData);
                }
            }
            fclose($handle);
            $this->command->info('✅ Users processed successfully!');
        }
    }

    private function seedPosts($sqlPath): void
    {
        $handle = fopen($sqlPath, "r");
        if ($handle) {
            $inPostsTable = false;
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                if (str_starts_with($line, 'INSERT INTO `posts`')) {
                    $inPostsTable = true;
                }

                if ($inPostsTable && str_starts_with($line, 'INSERT INTO `') && !str_starts_with($line, 'INSERT INTO `posts`')) {
                    $inPostsTable = false;
                }

                if ($inPostsTable && str_starts_with($line, '(')) {
                    $trimLine = rtrim($line, ',;');
                    $trimLine = ltrim($trimLine, '(');
                    $trimLine = rtrim($trimLine, ')');

                    $row = str_getcsv($trimLine, ',', "'", "\\");

                    if (count($row) < 7) {
                        continue;
                    }

                    // Indices: 0: post_id, 1: content, 2: image, 3: video, 4: user_id, 5: date, 6: time

                    $content = $row[1] === 'NULL' ? '' : $row[1];

                    $images = trim($row[2]);
                    if ($images !== 'NULL' && !empty($images)) {
                        $imageList = explode(',', $images);
                        $formattedImages = [];
                        foreach ($imageList as $img) {
                            $img = trim($img);
                            if (empty($img))
                                continue;
                            if (!str_starts_with($img, 'posts/images/')) {
                                $formattedImages[] = 'posts/images/' . $img;
                            } else {
                                $formattedImages[] = $img;
                            }
                        }
                        $images = !empty($formattedImages) ? implode(',', $formattedImages) : null;
                    } else {
                        $images = null;
                    }

                    $video = trim($row[3]);
                    if ($video !== 'NULL' && !empty($video)) {
                        if (!str_starts_with($video, 'posts/videos/')) {
                            $video = 'posts/videos/' . $video;
                        }
                    } else {
                        $video = null;
                    }

                    $date = $row[5] === 'NULL' ? date('Y-m-d') : $row[5];
                    $time = $row[6] === 'NULL' ? '00:00:00' : $row[6];
                    $createdAt = "$date $time";

                    $postData = [
                        'id' => (int) $row[0],
                        'user_id' => (int) $row[4],
                        'content' => $content,
                        'images' => $images,
                        'video' => $video,
                    ];

                    if (!\App\Models\User::where('id', $postData['user_id'])->exists()) {
                        // warning? or just skip
                        continue;
                    }

                    $post = \App\Models\Post::updateOrCreate(['id' => $postData['id']], $postData);
                    $post->created_at = $createdAt;
                    $post->updated_at = $createdAt;
                    $post->save(['timestamps' => false]);
                }
            }
            fclose($handle);
            $this->command->info('✅ Posts processed successfully!');
        }
    }
}
