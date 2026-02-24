<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Book;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'current_page',
        'total_pages',
        'progress_percentage',
        'last_read_at',
        'is_completed',
        'view_mode',
        'pdf_scale',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'is_completed' => 'boolean',
        'pdf_scale' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function updateProgress(int $currentPage, int $totalPages)
    {
        $this->current_page = $currentPage;
        $this->total_pages = $totalPages;
        $this->progress_percentage = $totalPages > 0 ? (int) round(($currentPage / $totalPages) * 100) : 0;
        $this->is_completed = $currentPage >= $totalPages;
        $this->last_read_at = now();
        $this->save();
    }

    /**
     * Get or create reading progress for a user and book.
     */
    public static function getForUser(User $user, int $bookId): self
    {
        return static::firstOrCreate(
            [
                'user_id' => $user->id,
                'book_id' => $bookId
            ],
            [
                'current_page' => 0,
                'total_pages' => 0,
                'progress_percentage' => 0,
                'is_completed' => false
            ]
        );
    }
}
