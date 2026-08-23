<?php

namespace App\Http\Requests;

use App\Models\Link;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MoveLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $link = $this->route('link');

        return $link instanceof Link && $this->user()?->can('move', $link) === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'direction' => ['required', 'string', Rule::in(['up', 'down'])],
        ];
    }
}
