<?php

namespace App\Http\Controllers;

use App\Http\Enums\ReactionEnum;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use App\Notifications\CommentDeleted;
use App\Notifications\CommentPosted;
use App\Notifications\CommentReplied;
use App\Notifications\PostDeleted;
use App\Notifications\PostReacted;
use App\Notifications\PostShared;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function view(Request $request, Post $post)
    {
        if ($post->group_id && !$post->group->hasApprovedUser(Auth::id())) {
            return inertia('Error', [
                'title' => 'Permission Denied',
                'body' => "You don't have permission to view that post"
            ])->toResponse($request)->setStatusCode(403);
        }

        $post->loadCount([
            'reactions',
            'shares',
        ]);
        $post->load([
            'comments' => function ($query) {
                $query->withCount('reactions'); // SELECT * FROM comments WHERE post_id IN (1, 2, 3...)
                // SELECT COUNT(*) from reactions
            },
        ]);

        return inertia('Post/View', [
            'post' => new PostResource($post)
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        // Post::create($data);
        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $post = Post::create($data);

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }

            DB::commit();

            $followers = $user->followers;
            // Notification::send($followers, new PostCreated($post, $user, null));
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }

        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    public function share(Request $request, $postId)
    {
        $request->validate([
            'body' => 'nullable|string',
        ]);

        $originalPost = Post::findOrFail($postId);
        $userId = Auth::id();

        $body = trim($request->body) !== '' ? $request->body : '';

        $sharedPost = Post::create([
            'user_id' => $userId,
            // 'body' => $request->body,
            'body' => $body,
            'shared_post_id' => $originalPost->id,
        ]);

        // Log post details for debugging
        Log::info('Post shared:', [
            'Shared Post ID' => $sharedPost->id,
            'Original Post ID' => $originalPost->id,
            'Sharer ID' => $userId,
            'Original Post Owner ID' => $originalPost->user->id
        ]);

        // Check if the notification should be sent
        if ($originalPost->user->id !== $userId) {
            Log::info('Sending notification to user:', [
                'Receiver ID' => $originalPost->user->id,
                'Sharer ID' => $userId
            ]);

            // $originalPost->user->notify(new PostShared($originalPost, Auth::user()));

            $originalPost->user->notify(new PostShared(Auth::user(), $originalPost));

            Log::info('Notification sent successfully!');
        } else {
            Log::info('Notification not sent: User shared their own post.');
        }

        return response()->json(['message' => 'Post shared successfully!']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        // $post->update($request->validated());
        $user = $request->user();

        DB::beginTransaction();
        $allFilePaths = [];
        try {
            $data = $request->validated();
            $post->update($data);

            $deleted_ids = $data['deleted_file_ids'] ?? []; // 1, 2, 3, 4

            $attachments = PostAttachment::query()
                ->where('post_id', $post->id)
                ->whereIn('id', $deleted_ids)
                ->get();

            foreach ($attachments as $attachment) {
                $attachment->delete();
            }

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            foreach ($files as $file) {
                $path = $file->store('attachments/' . $post->id, 'public');
                $allFilePaths[] = $path;
                PostAttachment::create([
                    'post_id' => $post->id,
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'created_by' => $user->id
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Post $post)
    // {
    //     $id = Auth::id();

    //     if ($post->isOwner($id) || $post->group && $post->group->isAdmin($id)) {
    //         $post->delete();

    //         if (!$post->isOwner($id)) {
    //             $post->user->notify(new PostDeleted($post->group));
    //         }

    //         return back();
    //     }

    //     return response("You don't have permission to delete this post", 403);
    // }
    public function destroy(Post $post)
    {
        $id = Auth::id();

        if ($post->isOwner($id) || $post->group && $post->group->isAdmin($id)) {
            if ($post->attachments()->exists()) {
                foreach ($post->attachments as $attachment) {
                    Storage::delete($attachment->path);
                    $attachment->delete();
                }
            }

            $post->delete();

            if (!$post->isOwner($id)) {
                $post->user->notify(new PostDeleted($post->group));
            }

            return back();
        }

        return response("You don't have permission to delete this post", 403);
    }


    public function downloadAttachment(PostAttachment $attachment)
    {
        // TODO check if user has permission to download that attachment

        return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    public function postReaction(Request $request, Post $post)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userId = Auth::id();
        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->first();

        // if ($reaction) {
        //     $hasReaction = false;
        //     $reaction->delete();
        if ($reaction) {
            // if ($reaction->type === $data['reaction']) {
            if ($data['reaction'] === 'like' || $reaction->type === $data['reaction']) {
                $reaction->delete();
                $hasReaction = false;
            } else {
                $reaction->update(['type' => $data['reaction']]);
                $hasReaction = true;
            }
        } else {
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);
            $hasReaction = true;

            if ($post->user->id !== $userId) {
                // Notify post owner about the new reaction
                $post->user->notify(new PostReacted(Auth::user(), $post, $data['reaction']));
            }
        }

        $reactions = Reaction::where('object_id', $post->id)->where('object_type', Post::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }

    public function createComment(Request $request, Post $post)
    {
        $userId = Auth::id();
        $data = $request->validate([
            'comment' => ['required'],
            'parent_id' => ['nullable', 'exists:comments,id']
        ]);

        $comment = Comment::create([
            'post_id' => $post->id,
            'comment' => nl2br($data['comment']),
            'user_id' => $userId,
            'parent_id' => $data['parent_id'] ?: null
        ]);

        // Notify the post owner for top-level comments
        if ($data['parent_id'] === null) {
            if ($post->user->id !== $userId) {
                $post->user->notify(new CommentPosted(Auth::user(), $post, $data['comment']));
            }
        } else {
            // Notify the original comment owner for replies
            $parentComment = Comment::find($data['parent_id']);

            if ($parentComment && $parentComment->user_id !== $userId) {
                $parentComment->user->notify(new CommentReplied(Auth::user(), $post, $data['comment'], $parentComment));
            }
        }

        return response(new CommentResource($comment), 201);
    }

    public function deleteComment(Comment $comment)
    {
        $post = $comment->post;
        $id = Auth::id();
        if ($comment->isOwner($id) || $post->isOwner($id)) {

            $allComments = Comment::getAllChildrenComments($comment);
            $deletedCommentCount = count($allComments);

            $comment->delete();

            if (!$comment->isOwner($id)) {
                $comment->user->notify(new CommentDeleted($comment, $post));
            }

            // return response('', 204);
            return response(['deleted' => $deletedCommentCount], 200);
        }

        return response("You don't have permission to delete this comment.", 403);
    }

    public function updateComment(UpdateCommentRequest $request, Comment $comment)
    {
        $data = $request->validated();

        $comment->update([
            'comment' => nl2br($data['comment'])
        ]);

        return new CommentResource($comment);
    }

    public function commentReaction(Request $request, Comment $comment)
    {
        $data = $request->validate([
            'reaction' => [Rule::enum(ReactionEnum::class)]
        ]);

        $userId = Auth::id();
        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $comment->id)
            ->where('object_type', Comment::class)
            ->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $comment->id,
                'object_type' => Comment::class,
                'user_id' => $userId,
                'type' => $data['reaction']
            ]);
        }

        $reactions = Reaction::where('object_id', $comment->id)->where('object_type', Comment::class)->count();

        return response([
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction
        ]);
    }

    public function fetchUrlPreview(Request $request)
    {
        $data = $request->validate([
            'url' => 'url'
        ]);
        $url = $data['url'];

        $html = file_get_contents($url);

        $dom = new \DOMDocument();

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);

        // Load HTML content into the DOMDocument
        $dom->loadHTML($html);

        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(false);

        $ogTags = [];
        $metaTags = $dom->getElementsByTagName('meta');
        foreach ($metaTags as $tag) {
            $property = $tag->getAttribute('property');
            if (str_starts_with($property, 'og:')) {
                $ogTags[$property] = $tag->getAttribute('content');
            }
        }

        return $ogTags;
    }

    public function pinUnpin(Request $request, Post $post)
    {
        $forGroup = $request->get('forGroup', false);
        $group = $post->group;

        if ($forGroup && !$group) {
            return response("Invalid Request", 400);
        }

        if ($forGroup && !$group->isAdmin(Auth::id())) {
            return response("You don't have permission to perform this action", 403);
        }

        $pinned = false;
        if ($forGroup && $group->isAdmin(Auth::id())) {
            if ($group->pinned_post_id === $post->id) {
                $group->pinned_post_id = null;
            } else {
                $pinned = true;
                $group->pinned_post_id = $post->id;
            }
            $group->save();
        }

        if (!$forGroup) {
            $user = $request->user();
            if ($user->pinned_post_id === $post->id) {
                $user->pinned_post_id = null;
            } else {
                $pinned = true;
                $user->pinned_post_id = $post->id;
            }
            $user->save();
        }

        return back()->with('success', 'Post was successfully ' . ($pinned ? 'pinned' : 'unpinned'));

        //        return response("You don't have permission to perform this action", 403);
    }

    // public function getReactions(Post $post)
    // {
    //     $reactions = Reaction::where('object_id', $post->id)
    //         ->where('object_type', Post::class)
    //         ->with('user')
    //         ->get(['user_id', 'type']);

    //     return response()->json($reactions);
    // }
    public function getReactions(Post $post)
    {
        $reactions = Reaction::where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->with('user')
            ->get(['user_id', 'type']);

        $reactionsWithUser = $reactions->map(function ($reaction) {
            return [
                'user' => new UserResource($reaction->user),
                'type' => $reaction->type,
            ];
        });

        return response()->json($reactionsWithUser);
    }
}
