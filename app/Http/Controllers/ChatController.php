<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $followings = $user->followings()->get();

        return Inertia::render('Chat/Home', [
            'followings' => UserResource::collection($followings),
        ]);
    }

    /**
     * Retrieve or create a conversation with the given user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversation(User $user)
    {
        // Find or create conversation
        $conversation = Conversation::where(function ($query) use ($user) {
            $query->where('user_id1', auth()->id())
                ->where('user_id2', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('user_id1', $user->id)
                ->where('user_id2', auth()->id());
        })->with('messages')->first(); // Eager load messages

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id1' => auth()->id(),
                'user_id2' => $user->id,
            ]);
        }

        $messages = $conversation->messages;

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message to a conversation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'conversation_id' => 'required',
            'message' => 'required',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'conversation_id' => $request->conversation_id,
            'message' => $request->message,
        ]);

        return response()->json($message);
    }
}