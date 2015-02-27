<?php namespace App\Traits;

use App\Scopes\VisibilityScope;

trait HasVisibilities {
    public static function bootHasVisibilities() {
        static::addGlobalScope(new VisibilityScope);
    }
}
