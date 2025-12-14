<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFotoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lokasi_file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'judul_foto' => 'required|string|max:255',
            'deskripsi_foto' => 'required|string',
            'album_id' => 'nullable',
        ];
    }
}
