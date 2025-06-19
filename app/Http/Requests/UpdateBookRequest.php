<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $bookId = $this->route('book')->id;

        return [
            'title' => 'sometimes|string',
            'isbn' => 'sometimes|string|unique:books,isbn,' . $bookId,
            'year' => 'sometimes|integer',
            'author_id' => 'sometimes|exists:authors,id',
            'category_id' => 'sometimes|exists:categories,id',
            'cover_image' => 'nullable|image|max:2048',
            'synopsis' => 'nullable|string',
            'pages' => 'nullable|integer',
            'publisher' => 'nullable|string',
            'stock' => 'nullable|integer|min:0',
        ];
    }
}
