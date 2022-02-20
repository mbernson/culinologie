<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @author Wessel Stam <wessel@blendis.nl>
 */
class SaveRecipeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'categories' => 'nullable',
            'categories.*' => 'exists:categories,id',
        ];
    }
}
