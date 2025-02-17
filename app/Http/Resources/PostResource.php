<?php

namespace App\Http\Resources;

use App\Traits\HandlesSoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    use HandlesSoftDeletes;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $comments = $this->comments;

        $user = $this->user;
        $isDeactivated = $user ? $this->isDeactivated($user) : false;

        return [
            'id' => $this->id,
            'body' => $this->body,
            'preview' => $this->preview,
            'preview_url' => $this->preview_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user' => $isDeactivated ? [
                'id' => $user ? $user->id : null,
                'name' => $user ? $user->name : null,
                'status' => 'Deactivated',
            ] : new UserResource($user),
            'group' => new GroupResource($this->group),
            'attachments' => PostAttachmentResource::collection($this->attachments),
            // 'attachments' => PostAttachmentResource::collection($this->attachments ?? []),
            'num_of_reactions' => $this->reactions_count,
            'num_of_comments' => count($comments),
            'current_user_has_reaction' => $this->reactions->count() > 0,
            'comments' => self::convertCommentsIntoTree($comments),
            'num_of_shares' => $this->shares_count,
            // 'shared_post' => $this->sharedPost ? new self($this->sharedPost) : null,
            'shared_post' => $this->sharedPost ? [
                'id' => $this->sharedPost->id,
                'body' => $this->sharedPost->body,
                'preview' => $this->sharedPost->preview,
                'preview_url' => $this->sharedPost->preview_url,
                'created_at' => $this->sharedPost->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->sharedPost->updated_at->format('Y-m-d H:i:s'),
                'view' => route('post.view', ['post' => $this->sharedPost->id]),
                'user' => new UserResource($this->sharedPost->user),
                'group' => new GroupResource($this->sharedPost->group),
                'attachments' => PostAttachmentResource::collection($this->sharedPost->attachments ?? [])
            ] : null,
        ];
    }

    /**
     *
     *
     * @param \App\Models\Comment[] $comments
     * @param                       $parentId
     * @return array
     * @author Ahllaine Christian De Ocera <acdeocera.bb88@gmail.com>
     */
    private static function convertCommentsIntoTree($comments, $parentId = null): array
    {
        $commentTree = [];

        foreach ($comments as $comment) {
            if ($comment->parent_id === $parentId) {
                // Find all comment which has parentId as $comment->id
                $children = self::convertCommentsIntoTree($comments, $comment->id);
                $comment->childComments = $children;
                $comment->numOfComments = collect($children)->sum('numOfComments') + count($children);

                $commentTree[] = new CommentResource($comment);
            }
        }

        return $commentTree;
    }
}
