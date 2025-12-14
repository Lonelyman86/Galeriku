<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $album = $this->route('album');
        return Auth::check() && $album && $album->user_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $albumId = $this->route('album')->id;
        
        return [
            'nama_album' => [
                'required',
                Rule::unique('albums')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                })->ignore($albumId),
            ],
            'deskripsi' => 'required',
        ];
    }
}
