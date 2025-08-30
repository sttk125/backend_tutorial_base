<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterEmailRequest;
use App\Http\Requests\VerifyTokenRequest;
use App\Http\Requests\RegisterCompleteRequest;
use App\Http\Requests\LoginRequest;
use App\Mail\VerifyTokenMail;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // (1) メール登録：トークン発行＆送信
    // POST /api/auth/register/request
    public function registerRequest(RegisterEmailRequest $request)
    {
        $email = strtolower($request->validated()['email']);

        // 6桁数字トークン（UIで扱いやすい）
        $token = (string) random_int(100000, 999999);

        // 既存破棄→新規発行（1メール1トークン）
        DB::table('email_verifications')->where('email', $email)->delete();
        DB::table('email_verifications')->insert([
            'email'      => $email,
            'token'      => $token,
            'expires_at' => CarbonImmutable::now()->addMinutes(30),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Mailpit( http://localhost:8025 )で受信確認できる
        Mail::to($email)->send(new VerifyTokenMail($token));

        return response()->json(['message' => '確認コードを送信しました'], 200);
    }

    // (2) トークン検証：verified_at を打つ
    // POST /api/auth/register/verify
    public function registerVerify(VerifyTokenRequest $request)
    {
        $data = $request->validated();
        $email = strtolower($data['email']);
        $token = $data['token']; // 数字6桁想定

        $row = DB::table('email_verifications')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$row) {
            return response()->json(['error' => ['code' => 422, 'message' => '不正な確認コードです。']], 422);
        }
        if (now()->greaterThan(CarbonImmutable::parse($row->expires_at))) {
            return response()->json(['error' => ['code' => 422, 'message' => '確認コードの有効期限が切れています。']], 422);
        }

        DB::table('email_verifications')->where('id', $row->id)->update(['verified_at' => now()]);
        return response()->json(['message' => 'メール確認が完了しました'], 200);
    }

    // (3) 会員登録完了：ユーザー作成＋APIトークン発行
    // POST /api/auth/register/complete
    public function registerComplete(RegisterCompleteRequest $request)
    {
        $data = $request->validated();
        $email = strtolower($data['email']);
        $token = $data['token'];

        $row = DB::table('email_verifications')
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$row || !$row->verified_at) {
            return response()->json(['error' => ['code' => 422, 'message' => 'メール確認が完了していません。']], 422);
        }
        if (now()->greaterThan(CarbonImmutable::parse($row->expires_at))) {
            return response()->json(['error' => ['code' => 422, 'message' => '確認コードの有効期限が切れています。']], 422);
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $data['name'], 'password' => Hash::make($data['password'])]
        );

        // Sanctumトークン発行
        $accessToken = $user->createToken('api')->plainTextToken;

        // 使い終わったトークンを削除（任意）
        DB::table('email_verifications')->where('email', $email)->delete();

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600, // 目安（Sanctumはデフォ無期限）
        ], 201);
    }

    // ログイン：メール＋パスワード → APIトークン発行
    // POST /api/auth/login
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', strtolower($data['email']))->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => ['code' => 401, 'message' => 'メールまたはパスワードが違います。']], 401);
        }

        $accessToken = $user->createToken('api')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
        ], 200);
    }

    // ログアウト：当該トークン失効
    // POST /api/auth/logout (auth:sanctum)
    public function logout()
    {
        $user = auth()->user();
        $user?->currentAccessToken()?->delete();
        return response()->json(['message' => 'ログアウトしました'], 200);
    }
}
