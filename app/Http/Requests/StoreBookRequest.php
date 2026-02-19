<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Já autenticado via middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'course' => 'required|string|in:' . implode(',', \App\Enums\Course::values()),
            'file_path' => 'required|file|mimes:pdf|max:10240',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'author.required' => 'O autor é obrigatório.',
            'author.max' => 'O nome do autor não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 5000 caracteres.',
            'course.required' => 'Selecione um curso/categoria.',
            'course.in' => 'O curso selecionado não é válido.',
            'file_path.required' => 'O arquivo PDF é obrigatório.',
            'file_path.mimes' => 'O arquivo deve ser um PDF válido.',
            'file_path.max' => 'O arquivo PDF não pode ter mais de 10MB.',
            'cover_path.image' => 'A capa deve ser uma imagem.',
            'cover_path.mimes' => 'A capa deve ser JPG, PNG ou WebP.',
            'cover_path.max' => 'A capa não pode ter mais de 5MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'author' => 'autor',
            'description' => 'descrição',
            'course' => 'curso',
            'file_path' => 'arquivo PDF',
            'cover_path' => 'capa',
        ];
    }
}
