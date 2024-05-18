<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\UsedQRCode;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    /**
     * @OA\GET (
     *     path="/api/user/qr",
     *     tags={"QR코드"},
     *     summary="QR코드 생성",
     *     description="QR코드 생성해서 이미지로 반환",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="422", description="Validation Error"),
     * )
     */
    public function generator(Request $request)
    {
        $user_id = auth('users')->id();
        $userData = User::select('id')->where('id', $user_id)->first();

        // 사용자 정보를 문자열로 변환하여 QR 코드에 사용합니다.
        //QR생성  웹에서 볼려면'svg'
        //return QrCode::format('svg')->size(300)->encoding('UTF-8')->generate($userData);
        return QrCode::format('svg')->size(300)->encoding('UTF-8')->generate((string) $userData->id);
    }

    /**
     * @OA\Post (
     * path="/api/qr/check",
     * tags={"QR코드"},
     * summary="QR코드체크",
     * description="QR코드로 사용자를 확인합니다.",
     *     @OA\RequestBody(
     *         description="QR코드로 받은 사용자 id",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema (
     *                 @OA\Property (property="id", type="string", description="user_id", example=3),
     *             )
     *         )
     *     ),
     *  @OA\Response(response="200", description="Success"),
     *  @OA\Response(response="500", description="Fail"),
     * )
     */
    public function check(Request $request)
    {
        $checkId = User::where('id', $request->id)->first();
        if ($checkId) {
            return response()->json(['message' => '인증된 사용자 입니다'], 200);
        } else {
            return response()->json(['message' => '인증되지 않은 사용자 입니다.'], 200);
        }
    }
}
