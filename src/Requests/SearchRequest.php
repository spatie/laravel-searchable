<?php

namespace Spatie\Searchable\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            config('searchable.input_search', 'search') => config('searchable.rule_validation', ['required', 'min:5', 'string']),
        ];
    }
}
