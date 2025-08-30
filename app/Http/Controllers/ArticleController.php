<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $limit = (int)($request->query('limit', 10));

        $p = Article::query()
            ->withCount('comments')
            ->orderByDesc('created_at')
            ->paginate($limit);

        return response()->json([
            'limit'    => $p->perPage(),
            'total'    => $p->total(),
            'articles' => $p->items(),
        ]);
    }

    public function show(Article $article)
    {
        $article->load(['comments' => fn($q) => $q->orderByDesc('created_at')]);
        return $article;
    }
}
