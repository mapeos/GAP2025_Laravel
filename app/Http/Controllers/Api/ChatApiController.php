<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ChatApiController extends Controller
{
    /**
     * Get list of users available for chat (professors and students)
     *
     * @return JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        try {
            $currentUserId = Auth::id();

            // Get professors
            $profesores = User::role('Profesor')
                ->where('id', '!=', $currentUserId)
                ->with('persona')
                ->select('id', 'name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->persona ? $user->persona->nombre_completo : $user->name,
                        'role' => 'Profesor'
                    ];
                });

            // Get students
            $alumnos = User::role('Alumno')
                ->where('id', '!=', $currentUserId)
                ->with('persona')
                ->select('id', 'name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->persona ? $user->persona->nombre_completo : $user->name,
                        'role' => 'Alumno'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'profesores' => $profesores,
                    'alumnos' => $alumnos
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users by role and name
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchUsers(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'role' => 'required|in:profesor,alumno',
                'search' => 'nullable|string|max:255'
            ]);

            $rol = ucfirst($request->input('role'));
            $search = $request->input('search');
            $currentUserId = Auth::id();

            $query = User::role($rol)
                ->where('id', '!=', $currentUserId)
                ->with('persona');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhereHas('persona', function($subQ) use ($search) {
                          $subQ->where('nombre', 'like', "%{$search}%")
                               ->orWhere('apellido1', 'like', "%{$search}%")
                               ->orWhere('apellido2', 'like', "%{$search}%");
                      });
                });
            }

            $usuarios = $query->select('id', 'name')
                ->limit(20)
                ->get()
                ->map(function ($user) use ($rol) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->persona ? $user->persona->nombre_completo : $user->name,
                        'role' => $rol
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $usuarios
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent chats for the authenticated user
     *
     * @return JsonResponse
     */
    public function getRecentChats(): JsonResponse
    {
        try {
            $currentUserId = Auth::id();
            $limit = request()->get('limit', 10);

            // Get the last message from each conversation
            $subQuery = ChatMessage::selectRaw('LEAST(sender_id, receiver_id) as u1, GREATEST(sender_id, receiver_id) as u2, MAX(id) as max_id')
                ->where(function($q) use ($currentUserId) {
                    $q->where('sender_id', $currentUserId)->orWhere('receiver_id', $currentUserId);
                })
                ->groupBy('u1', 'u2');

            $messageIds = $subQuery->pluck('max_id');
            $recentMessages = ChatMessage::whereIn('id', $messageIds)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Get user information for each chat
            $chatsWithUserInfo = [];
            foreach ($recentMessages as $message) {
                $otherUserId = $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
                $otherUser = User::with('persona')->find($otherUserId);

                if ($otherUser) {
                    $chatsWithUserInfo[] = [
                        'user_id' => $otherUserId,
                        'user_name' => $otherUser->name,
                        'user_full_name' => $otherUser->persona ? $otherUser->persona->nombre_completo : $otherUser->name,
                        'user_role' => $otherUser->getRoleNames()->first() ?? 'Unknown',
                        'last_message' => [
                            'id' => $message->id,
                            'content' => $message->content,
                            'sender_id' => $message->sender_id,
                            'receiver_id' => $message->receiver_id,
                            'created_at' => $message->created_at->toISOString(),
                            'is_sent_by_me' => $message->sender_id == $currentUserId
                        ]
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_chats' => $chatsWithUserInfo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving recent chats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages between two users (conversation)
     *
     * @param int $userId
     * @return JsonResponse
     */
    public function getConversation(int $userId): JsonResponse
    {
        try {
            $currentUserId = Auth::id();

            // Verify the other user exists
            $otherUser = User::with('persona')->find($userId);
            if (!$otherUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Get messages between the two users
            $limit = request()->get('limit', 50);
            $messages = ChatMessage::where(function($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $currentUserId)->where('receiver_id', $userId);
                })->orWhere(function($q) use ($currentUserId, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'asc') // Show oldest first for conversation
                ->limit($limit)
                ->get();

            // Format messages for API response
            $formattedMessages = $messages->map(function($message) use ($currentUserId) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'created_at' => $message->created_at->toISOString(),
                    'is_sent_by_me' => $message->sender_id == $currentUserId
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'full_name' => $otherUser->persona ? $otherUser->persona->nombre_completo : $otherUser->name,
                        'role' => $otherUser->getRoleNames()->first() ?? 'Unknown'
                    ],
                    'messages' => $formattedMessages
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a message to another user
     *
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function sendMessage(Request $request, int $userId): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $currentUserId = Auth::id();

            // Verify the receiver exists
            $receiver = User::find($userId);
            if (!$receiver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receiver not found'
                ], 404);
            }

            // Create and save the message
            $message = ChatMessage::create([
                'sender_id' => $currentUserId,
                'receiver_id' => $userId,
                'content' => $request->input('message')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => [
                    'message' => [
                        'id' => $message->id,
                        'content' => $message->content,
                        'sender_id' => $message->sender_id,
                        'receiver_id' => $message->receiver_id,
                        'created_at' => $message->created_at->toISOString(),
                        'is_sent_by_me' => true
                    ]
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chat overview - combines recent chats and available users
     * This is the main endpoint for the chat home screen
     *
     * @return JsonResponse
     */
    public function getChatOverview(): JsonResponse
    {
        try {
            $currentUserId = Auth::id();

            // Get recent chats
            $subQuery = ChatMessage::selectRaw('LEAST(sender_id, receiver_id) as u1, GREATEST(sender_id, receiver_id) as u2, MAX(id) as max_id')
                ->where(function($q) use ($currentUserId) {
                    $q->where('sender_id', $currentUserId)->orWhere('receiver_id', $currentUserId);
                })
                ->groupBy('u1', 'u2');

            $messageIds = $subQuery->pluck('max_id');
            $recentMessages = ChatMessage::whereIn('id', $messageIds)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $chatsWithUserInfo = [];
            foreach ($recentMessages as $message) {
                $otherUserId = $message->sender_id == $currentUserId ? $message->receiver_id : $message->sender_id;
                $otherUser = User::with('persona')->find($otherUserId);

                if ($otherUser) {
                    $chatsWithUserInfo[] = [
                        'user_id' => $otherUserId,
                        'user_name' => $otherUser->name,
                        'user_full_name' => $otherUser->persona ? $otherUser->persona->nombre_completo : $otherUser->name,
                        'user_role' => $otherUser->getRoleNames()->first() ?? 'Unknown',
                        'last_message' => [
                            'id' => $message->id,
                            'content' => $message->content,
                            'sender_id' => $message->sender_id,
                            'receiver_id' => $message->receiver_id,
                            'created_at' => $message->created_at->toISOString(),
                            'is_sent_by_me' => $message->sender_id == $currentUserId
                        ]
                    ];
                }
            }

            // Get available users (professors and students)
            $profesores = User::role('Profesor')
                ->where('id', '!=', $currentUserId)
                ->with('persona')
                ->select('id', 'name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->persona ? $user->persona->nombre_completo : $user->name,
                        'role' => 'Profesor'
                    ];
                });

            $alumnos = User::role('Alumno')
                ->where('id', '!=', $currentUserId)
                ->with('persona')
                ->select('id', 'name')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'full_name' => $user->persona ? $user->persona->nombre_completo : $user->name,
                        'role' => 'Alumno'
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'recent_chats' => $chatsWithUserInfo,
                    'available_users' => [
                        'profesores' => $profesores,
                        'alumnos' => $alumnos
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving chat overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
