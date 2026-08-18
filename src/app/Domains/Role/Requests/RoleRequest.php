<?php

namespace App\Domains\Role\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role') ? $this->route('role')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($roleId),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ];
    }
}
