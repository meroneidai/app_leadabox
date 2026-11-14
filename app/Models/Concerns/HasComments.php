<?php

namespace App\Models\Concerns;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComments
{
    /** @return MorphMany<Comment, $this> */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest('id');
    }

    /** @return MorphMany<Comment, $this> */
    public function publicComments(): MorphMany
    {
        return $this->comments()->where('is_internal', false);
    }

    public function addCustomerComment(string $body, ?int $contactId = null, ?string $authorName = null): Comment
    {
        $comment = new Comment([
            'body' => $body,
            'is_internal' => false,
            'author_name' => $authorName,
        ]);

        $comment->contact_id = $contactId;

        return $this->comments()->save($comment);
    }

    public function addStaffComment(string $body, int $userId, bool $internal = false): Comment
    {
        $comment = new Comment([
            'body' => $body,
            'is_internal' => $internal,
        ]);

        $comment->user_id = $userId;

        return $this->comments()->save($comment);
    }
}
