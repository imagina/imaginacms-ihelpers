<?php

namespace Modules\Ihelpers\Rules;

use http\Params;
use Illuminate\Contracts\Validation\Rule;
use Modules\Iblog\Repositories\PostRepository;

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

    $relatedPostIds = $query->pluck('post_id');

    if ($relatedPostIds->isNotEmpty()) {
      $postRepository = app('Modules\Iblog\Repositories\PostRepository');

      $params = (object)[
        'filter' => ['id' => $relatedPostIds->toArray()]
      ];

      $posts = $postRepository->getItemsBy($params);

      return $posts->isEmpty();
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
