<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // 既存ユーザー（DatabaseSeederで作る想定）がいなければ作る
        if (User::count() === 0) {
            User::factory()->count(3)->create();
        }

        // 記事10件
        Article::factory()
            ->count(10)
            ->create()
            ->each(function (Article $article) {
                // 各記事に2〜5件のコメント
                Comment::factory()
                    ->count(fake()->numberBetween(2, 5))
                    ->state(function () use ($article) {
                        return [
                            'article_id' => $article->id,
                            // 既存ユーザーからランダムに紐づけ
                            'user_id'    => User::inRandomOrder()->value('id'),
                        ];
                    })
                    ->create();
            });
    }
}
