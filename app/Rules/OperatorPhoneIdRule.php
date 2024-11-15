<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Operator;

class OperatorPhoneIdRule implements Rule
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
        return Operator::WhereHas('user', function ($query) use ($value) {
            $query->where('phone', $value);
        })->orWhere('identification', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('unavailable');
    }
}
