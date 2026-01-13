<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Book extends Model
{
    // ...

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
    ];

    const COURSES = [
        'Engenharia Civil' => 'Engenharia Civil',
        'Direito' => 'Direito',
        'Enfermagem' => 'Enfermagem',
        'Administração' => 'Administração',
        'Psicologia' => 'Psicologia',
        'Serviço Social' => 'Serviço Social',
        'Fisioterapia' => 'Fisioterapia',
        'Geral' => 'Geral / Outros',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

