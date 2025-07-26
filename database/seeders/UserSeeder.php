<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Userモデルを使うために追加
use Illuminate\Support\Facades\Hash; // パスワードをハッシュ化するために追加

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のユーザーデータをクリア (テスト実行ごとにリセットするため)
        // 注意: ユーザーデータも全て消えます。本番環境で実行する際は注意が必要です。
        User::truncate();

        // 特定のユーザーを作成

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // パスワードは必ずハッシュ化する
            'email_verified_at' => now(), // メール認証済みとしておく
        ]);

        User::create([
            'name' => 'Test User 1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password'), // パスワードは必ずハッシュ化する
            'email_verified_at' => now(), // メール認証済みとしておく
        ]);

        User::create([
            'name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Factoryを使ってさらにランダムなユーザーを数人追加することも可能
        User::factory()->count(2)->create(); // 残り2人はランダムなユーザー
    }
}