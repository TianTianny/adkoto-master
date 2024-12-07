<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "name" => $this->name,
            "surname" => $this->surname,
            "email" => $this->email,
            "phone" => $this->phone,
            "birthday" => $this->birthday,
            "gender" => $this->gender,
            "email_verified_at" => $this->email_verified_at,
            "is_admin" => $this->is_admin,
            "is_filament_admin" => $this->is_filament_admin,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "username" => $this->username,
            'pinned_post_id' => $this->pinned_post_id,
            // "cover_url" => $this->cover_path ? Storage::url($this->cover_path) : null,
            "cover_url" => $this->cover_path ? Storage::url($this->cover_path) : '/img/default_cover.jpg',
            "avatar_url" => $this->avatar_path ? Storage::url($this->avatar_path) : '/img/default_avatar.png',
            'last_message' => $this->when(isset($this->last_message), $this->last_message),
            'last_message_sender_name' => $this->when(isset($this->last_message_sender_name), $this->last_message_sender_name),
            'last_message_sender_id' => $this->when(isset($this->last_message_sender_id), $this->last_message_sender_id),
            'last_message_read_at' => $this->when(isset($this->last_message_read_at), $this->last_message_read_at),
            'unread_count' => $this->when(isset($this->unread_count), $this->unread_count),
        ];
    }
}
