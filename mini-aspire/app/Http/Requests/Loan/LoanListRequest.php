<?php

namespace App\Http\Requests\Loan;

use Illuminate\Foundation\Http\FormRequest;

class LoanListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|numeric|exists:users,id',
            'status' => 'nullable|numeric',
            'page' => 'nullable|numeric',
            'limit' => 'nullable|numeric',
        ];
    }
}
