<?php

namespace App\Http\Controllers;

use App\Models\MeetingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MeetingRoomController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/meeting-room",
     *     tags={"회의실"},
     *     summary="목록",
     *     description="회의실 목록",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="500", description="Server Error"),
     * )
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['meeting_rooms' => MeetingRoom::all()]);
    }

    /**
     * @OA\Post (
     *     path="/api/meeting-room",
     *     tags={"회의실"},
     *     summary="추가",
     *     description="회의실 추가",
     *     @OA\RequestBody(
     *         description="설명",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema (
     *                 @OA\Property (property="room_number", type="string", description="추가할 회의실의 번호", example="203"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="422", description="Validation Exception"),
     *     @OA\Response(response="500", description="Server Error"),
     * )
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'room_number' => 'required|string',
            ]);
        } catch (ValidationException $exception) {
            $errorStatus = $exception->status;
            $errorMessage = $exception->getMessage();
            return response()->json(['error'=>$errorMessage], $errorStatus);
        }

        $meetingRoom = MeetingRoom::create([
            'room_number' => $validated['room_number'],
        ]);

        if(!$meetingRoom) return response()->json(['error' => '회의실 추가에 실패하였습니다.'], 500);

        return response()->json(['success' => '회의실이 추가되었습니다.', 'meeting_room' => $meetingRoom], 201);
    }

    /**
     * @OA\Get (
     *     path="/api/meeting-room/{id}",
     *     tags={"회의실"},
     *     summary="회의실 예약 목록",
     *     description="특정 회의실의 예약 목록",
     *     @OA\Parameter(
     *          name="id",
     *          description="회의실의 룸 번호",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="500", description="Server Error"),
     * )
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make([$id], [
            'id' => 'required|exists:meeting_rooms,room_number|string'
        ]);

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            $errorStatus = $exception->status;
            $errorMessage = $exception->getMessage();
            return response()->json(['error'=>$errorMessage], $errorStatus);
        }

        return response()->json(['meeting_room' => MeetingRoom::with('meetingRoomReservations')->findOrFail($id)]);
    }

    /**
     * @OA\Delete (
     *     path="/api/meeting-room/{id}",
     *     tags={"회의실"},
     *     summary="회의실 삭제",
     *     description="특정 회의실 삭제",
     *     @OA\Parameter(
     *          name="id",
     *          description="회의실의 룸 번호",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer"),
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="422", description="Validation Exception"),
     *     @OA\Response(response="500", description="Server Error"),
     * )
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make([$id], [
            'id' => 'required|exists:meeting_rooms,room_number|string'
        ]);

        try {
            $validator->validate();
        } catch (ValidationException $exception) {
            $errorStatus = $exception->status;
            $errorMessage = $exception->getMessage();
            return response()->json(['error'=>$errorMessage], $errorStatus);
        }

        $meetingRoom = MeetingRoom::findOrFail($id);

        if(!$meetingRoom->delete()) return response()->json(['error' => '회의실 삭제에 실패하였습니다.'], 500);

        return response()->json(['success' => '회의실이 삭제되었습니다.']);
    }
}