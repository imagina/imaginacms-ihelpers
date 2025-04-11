<?php

namespace Modules\Ihelpers\Rules;

use Illuminate\Contracts\Validation\Rule;

class DeleteFunctionRule implements Rule
{
  /**
   * Create a new rule instance.
   *
   * @return void
   */
  public $table;
  public $id;
  public $columnId;
  public $message;

  public function __construct($table, $id = null, $columnId = '', $message = '')
  {
    $this->table = $table;
    $this->id = $id;
    $this->columnId = $columnId;
    $this->message = !empty($message) ? $message : 'This resource cannot be deleted because it is associated with other records.';
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param mixed $value
   */
  public function passes($attribute, $value): bool
  {
    $query = \DB::table($this->table);

    if ($this->columnId) {
      $query->where($this->columnId, $this->id ?? $value);
    }

    return !$query->exists();
  }

  /**
   * Get the validation error message.
   */
  public function message(): string
  {
    return $this->message;
  }
}
