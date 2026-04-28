<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Route group already enforces auth + role:admin, so authorization is
     * granted at this layer.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $ignoreId = $this->route('id');

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => [
                'required',
                'string',
                'max:200',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('products', 'slug')->ignore($ignoreId),
            ],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:course,software,mixed'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'preview_youtube_id' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:active,draft'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'slug.regex' => 'Slug hanya boleh berisi huruf kecil, angka, dan tanda hubung.',
            'thumbnail.image' => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Thumbnail harus jpeg, png, atau webp.',
            'thumbnail.max' => 'Thumbnail maksimal 2 MB.',
            'commission_rate.max' => 'Komisi maksimal 100%.',
        ];
    }
}
