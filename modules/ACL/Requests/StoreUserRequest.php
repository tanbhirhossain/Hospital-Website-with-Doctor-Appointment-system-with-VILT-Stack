<?php

namespace Modules\ACL\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $permission = $this->isMethod('post') ? 'user.create' : 'user.edit';

        return $this->user()?->can($permission) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $passwordRules = ['string', Password::defaults()];

        if ($this->isMethod('post')) {
            array_unshift($passwordRules, 'required');
        } else {
            array_unshift($passwordRules, 'nullable');
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => $passwordRules,
            'roles' => ['array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')->where('guard_name', 'web')],
        ];
    }
}
