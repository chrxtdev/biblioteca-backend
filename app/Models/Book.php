<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'course',
        'description',
        'file_path',
        'cover_path',
        'is_verified',
        'user_id',
        'rejection_reason',
        'total_pages',
    ];

    // protected $casts = [
    //    'course' => \App\Enums\Course::class,
    // ];

    // Mantendo por compatibilidade se necessário, mas idealmente usar Enum
    public static function getCoursesOptions(): array
    {
        return \App\Enums\Course::options();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'book_user_favorites', 'book_id', 'user_id');
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function getUserProgress($userId)
    {
        return $this->readingProgress()->where('user_id', $userId)->first();
    }

    /**
     * Scope para livros verificados/aprovados.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope para busca por título ou autor.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(fn($q) => $q
            ->where('title', 'like', "%{$term}%")
            ->orWhere('author', 'like', "%{$term}%")
        );
    }

    /**
     * Scope para livros pendentes de aprovação.
     */
    public function scopePending($query)
    {
        return $query->where('is_verified', false)->whereNull('rejection_reason');
    }

    /**
     * Scope para livros rejeitados.
     */
    public function scopeRejected($query)
    {
        return $query->where('is_verified', false)->whereNotNull('rejection_reason');
    }

    protected static function booted(): void
    {
        static::deleting(function (Book $book) {
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }

            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
        });
    }
}
