<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // 共通JSONレスポンス生成関数
        $json = function (int $code, string $message, ?array $details = null) {
            $payload = ['error' => ['code' => $code, 'message' => $message]];
            if (!is_null($details)) {
                $payload['error']['details'] = $details;
            }
            return response()->json($payload, $code);
        };

        // バリデーションエラー（422）
        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(422, '入力内容に誤りがあります。', $e->errors());
            }
        });

        // 404: モデルまたはルートが存在しない
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(404, 'リソースが見つかりません。');
            }
        });
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(404, 'リソースが見つかりません。');
            }
        });

        // 405: メソッド不許可
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(405, '許可されていないHTTPメソッドです。');
            }
        });

        // 401: 認証エラー
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(401, '認証が必要です。');
            }
        });

        // 403: 権限エラー
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return $json(403, 'この操作を実行する権限がありません。');
            }
        });

        // その他HTTP例外（400系など）
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $msg = $e->getMessage() ?: 'エラーが発生しました。';
                return $json($e->getStatusCode(), $msg);
            }
        });

        // 500: 想定外のサーバーエラー
        $exceptions->render(function (\Throwable $e, $request) use ($json) {
            if ($request->is('api/*') || $request->expectsJson()) {
                $msg = config('app.debug')
                    ? ($e->getMessage() ?: 'サーバーエラーが発生しました。')
                    : 'サーバーエラーが発生しました。';
                return $json(500, $msg);
            }
        });
    })
    ->create();

