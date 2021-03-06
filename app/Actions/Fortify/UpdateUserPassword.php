<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     * 
     * @param \App\Models\User $user 
     * @param string[] $input 
     * @return void 
     * @throws ValidationException 
     * @throws InvalidArgumentException
     */
    public function update($user, array $input)
    {
        $validator = validator($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
                $validator->errors()
                    ->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');
        //'updatePassword'
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
