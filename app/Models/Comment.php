<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable(['body', 'is_internal', 'author_name'])]
class Comment extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /** @param Builder<Comment> $query */
    public function scopeVisibleToCustomer(Builder $query): void
    {
        $query->where('is_internal', false);
    }

    public function fromCustomer(): bool
    {
        return $this->user_id === null;
    }

    public function authorName(): string
    {
        return $this->user?->name
            ?? $this->contact?->name
            ?? $this->author_name
            ?? __('comments.customer');
    }

    public function initials(): string
    {
        return collect(explode(' ', trim($this->authorName())))
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
