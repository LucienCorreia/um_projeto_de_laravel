<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLivroRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'titulo' => 'required',
            'indices.*.titulo' => 'required',
            'indices.*.pagina' => 'required',
            'indices.*.subindices.*.titulo' => 'required',
            'indices.*.subindices.*.pagina' => 'required',
        ];
    }
}
