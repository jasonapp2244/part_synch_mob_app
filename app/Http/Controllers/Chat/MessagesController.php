<?php

namespace App\Http\Controllers\Chat;

use Chatify\Http\Controllers\Api\MessagesController as ChatifyMessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Chatify\Facades\ChatifyMessenger as Chatify;

class MessagesController extends ChatifyMessagesController
{
    /**
     * Override: Search users by first_name/last_name instead of 'name'.
     */
    public function search(Request $request)
    {
        $input = trim(filter_var($request['input']));

        $records = User::where('id', '!=', Auth::user()->id)
            ->where(function ($q) use ($input) {
                $q->where('first_name', 'LIKE', "%{$input}%")
                  ->orWhere('last_name', 'LIKE', "%{$input}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$input}%"]);
            })
            ->paginate($request->per_page ?? $this->perPage);

        $items = [];
        foreach ($records->items() as $record) {
            $userData = Chatify::getUserWithAvatar($record);
            $items[] = $userData;
        }

        return Response::json([
            'records' => $items,
            'total' => $records->total(),
            'last_page' => $records->lastPage(),
        ], 200);
    }

    /**
     * Override: Validate user_id before starring to prevent null constraint violation.
     */
    public function favorite(Request $request)
    {
        $userId = $request['user_id'];

        if (!$userId) {
            return Response::json([
                'status' => 0,
                'message' => 'user_id is required',
            ], 422);
        }

        // Verify user exists
        if (!User::where('id', $userId)->exists()) {
            return Response::json([
                'status' => 0,
                'message' => 'User not found',
            ], 404);
        }

        $favoriteStatus = Chatify::inFavorite($userId) ? 0 : 1;
        Chatify::makeInFavorite($userId, $favoriteStatus);

        return Response::json([
            'status' => $favoriteStatus,
        ], 200);
    }
}
