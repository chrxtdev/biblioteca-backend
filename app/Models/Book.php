<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
        'user_id'
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
}
