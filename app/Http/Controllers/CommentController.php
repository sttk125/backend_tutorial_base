<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\ListCommentsRequest;

class CommentController extends Controller
{
    // GET /api/articles/{article}/comments
    public function index(ListCommentsRequest $request, Article $article)
    {
        $limit = (int) $request->query('limit', 10);

        $paginator = $article->comments()
            ->orderByDesc('created_at') // 投稿日時で新しい順
            ->paginate($limit);

        $items = collect($paginator->items())->map(function (Comment $c) {
            return [
                'comment_id' => $c->id,
                'user_id'    => $c->user_id,
                'article_id' => $c->article_id,
                'comment'    => $c->text, 
                'created_at' => $c->created_at?->toISOString(),
            ];
        });

        return response()->json([
            'page'     => $paginator->currentPage(),
            'limit'    => $paginator->perPage(),
            'total'    => $paginator->total(),
            'comments' => $items,
        ]);
    }

    // POST /api/articles/{article}/comments
    public function store(StoreCommentRequest $request, Article $article)
    {
        $data = $request->validated();

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id'    => auth()->id(),
            'text'       => $data['comment'], // 仕様: comment → 保存: text
        ]);

        return response()->json([
            'comment_id' => $comment->id,
            'user_id'    => $comment->user_id,
            'article_id' => $comment->article_id,
            'comment'    => $comment->text,
            'created_at' => $comment->created_at?->toISOString(),
        ], 201);
    }

    // PUT /api/comments/{comment}
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $data = $request->validated();

        $comment->update(['text' => $data['comment']]);

        return response()->json([
            'comment_id' => $comment->id,
            'user_id'    => $comment->user_id,
            'article_id' => $comment->article_id,
            'comment'    => $comment->text,
            'updated_at' => $comment->updated_at?->toISOString(),
        ]);
    }

    // DELETE /api/comments/{comment}
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'message' => 'コメントを削除しました',
        ], 200);
    }
}
