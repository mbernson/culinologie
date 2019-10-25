<?php
namespace App\Requests;

/**
 * @author Wessel Stam <wessel@blendis.nl>
 */
class SaveRecipeRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'categories' => 'nullable',
            'categories.*' => 'exists:categories,id'
        ];

        return $rules;
    }
}