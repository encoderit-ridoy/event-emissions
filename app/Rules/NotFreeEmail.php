<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotFreeEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $domain = explode('@', $value)[1];
        $freeEmailProviders = ['gmail.com', 'yahoo.com', 'hotmail.com'];
        return !in_array($domain, $freeEmailProviders);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'フリーメールアドレスでのご登録はできません。';
    }
}
