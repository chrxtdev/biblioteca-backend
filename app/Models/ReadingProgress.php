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
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'is_completed' => 'boolean',
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
        $this->progress_percentage = $totalPages > 0 ? round(($currentPage / $totalPages) * 100) : 0;
        $this->is_completed = $currentPage >= $totalPages;
        $this->last_read_at = now();
        $this->save();
    }
}
