<?php

namespace CludyMe\MetaData\Concerns;

use Illuminate\Container\Container;
use Illuminate\Support\MessageBag;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

trait HasValidation
{
    /**
     * The message bag instance containing validation error messages
     *
     * @var MessageBag
     */
    protected $validationErrors;

    /**
     * Validation rules
     *
     * @return array
     */
    abstract static public function rules();

    /**
     * The array of validation custom error messages.
     *
     * @return array
     */
    protected function validationCustomMessages()
    {
        return [];
    }

    /**
     * The array of validation custom attribute names.
     *
     * @return array
     */
    protected function validationCustomAttributes()
    {
        return [];
    }

    /**
     * Listen for save event
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            return $model->validate();
        });
    }

    /**
     * Validates current attributes against rules
     *
     * @return bool
     */
    public function validate()
    {
        $validatorFactory = new Factory(new Translator(new ArrayLoader(), config('app.locale')), new Container());
        $validator = $validatorFactory->make($this->getValidationAttributes(), static::rules(), $this->validationCustomMessages(), $this->validationCustomAttributes());

        if ($validator->passes()) {
            return true;
        }

        $this->validationErrors = $validator->messages();

        return false;
    }

    /**
     * Retrieve error message bag
     *
     * @return MessageBag
     */
    public function errors()
    {
        return $this->hasErrors() ? $this->validationErrors : new MessageBag;
    }

    /**
     * Check if a model has been saved
     *
     * @return bool
     */
    public function isSaved()
    {
        return $this->hasErrors();
    }

    /**
     * Check if there are errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->validationErrors instanceof MessageBag;
    }

    /**
     * Returns the model data used for validation.
     *
     * @return array
     */
    protected function getValidationAttributes()
    {
        return $this->getAttributes();
    }
}
