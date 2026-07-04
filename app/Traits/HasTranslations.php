<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Translate any field.
     *
     * Example:
     * $model->translate('name')
     * $model->translate('title')
     */
    public function translate(string $field): ?string
    {
        $locale = App::currentLocale();
        $column = "{$field}_{$locale}";
        if (isset($this->{$column})) {
            return $this->{$column};
        }
        $fallback = "{$field}_" . config('app.fallback_locale');
        return $this->{$fallback};
    }
}
