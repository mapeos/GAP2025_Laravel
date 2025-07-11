<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatHidden;

class ChatHiddenController extends Controller
{
    public function hide($otherUserId)
    {
        ChatHidden::firstOrCreate([
            'user_id' => Auth::id(),
            'other_user_id' => $otherUserId,
        ]);
        return response()->json(['success' => true]);
    }
    public function unhide($otherUserId)
    {
        ChatHidden::where('user_id', Auth::id())
            ->where('other_user_id', $otherUserId)
            ->delete();
        return response()->json(['success' => true]);
    }
}
