<?php


namespace Modules\Ihelpers\Traits;


trait UserStamps
{

    /**
     *
     * @param bool
     */
    protected $userstamping = true;


    public static function boot()
    {

        parent::boot();

        /**
         * add created_by and pudated_by field to model fillable
         *
         */
        static::retrieved(function($model) {
            $model->fillable = array_merge($model->fillable, [$model->getCreatedByColumn(), $model->getUpdatedBycolumn()]);
        });

        //event before create model
        static::creating(function($model){
            if (!$model->isUserstamping() || is_null($model->getCreatedByColumn())) {
                return;
            }

            if (is_null($model->{$model->getCreatedByColumn()})) {
                $model->{$model->getCreatedByColumn()} = \Auth::id();
            }

            if (is_null($model->{$model->getUpdatedByColumn()}) && ! is_null($model->getUpdatedByColumn())) {
                $model->{$model->getUpdatedByColumn()} = \Auth::id();
            }
        });

        //event before update model
        static::updating(function($model){
            \Log::info($model->getUpdatedByColumn());
            if (!$model->isUserstamping() || is_null($model->getUpdatedByColumn()) || is_null(\Auth::id())) {
                return;
            }

            if (is_null($model->{$model->getCreatedByColumn()})) {
                $model->{$model->getCreatedByColumn()} = \Auth::id();
            }

            $model->{$model->getUpdatedByColumn()} = \Auth::id();
        });
    }

    /**
     * Get the name of the "created by" column.
     *
     * @return string
     */
    public function getCreatedByColumn()
    {
        return defined('static::CREATED_BY') ? static::CREATED_BY : 'created_by';
    }

    /**
     * Get the name of the "updated by" column.
     *
     * @return string
     */
    public function getUpdatedByColumn()
    {
        return defined('static::UPDATED_BY') ? static::UPDATED_BY : 'updated_by';
    }

    /**
     * Get the user that created the model.
     */
    public function creator()
    {
        return $this->belongsTo($this->getUserClass(), $this->getCreatedByColumn());
    }

    /**
     * Get the user that edited the model.
     */
    public function editor()
    {
        return $this->belongsTo($this->getUserClass(), $this->getUpdatedByColumn());
    }


    public function getUserClass(){
        $driver = config('asgard.user.config.driver');
        return "Modules\\User\\Entities\\{$driver}\\User";
    }

    /**
     * Check if we're maintaing Userstamps on the model.
     *
     * @return bool
     */
    public function isUserstamping()
    {
        return $this->userstamping;
    }

}
