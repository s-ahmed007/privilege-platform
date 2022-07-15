<?php

namespace App\Rules;

use App\CardPromoCodes;
use DB;
use Illuminate\Contracts\Validation\Rule;

class unique_if_changed implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $table, $column1, $column2, $message)
    {
        $this->row_id = $id;
        $this->table = $table;
        $this->column1 = $column1;
        $this->column2 = $column2;
        $this->message = $message;
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
        $row_value = DB::table($this->table)->where($this->column2, $this->row_id)->first();
        $column1 = $this->column1;

        if ($value == $row_value->$column1) {//not changed
            return true;
        } else {//changed
            $exists = DB::table($this->table)->where($column1, $value)->count();
            if ($exists > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
