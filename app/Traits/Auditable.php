<?php

namespace App\Traits;

use App\Services\AuditService;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditService::log('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $old = array_intersect_key($model->getOriginal(), $model->getDirty());
            $new = $model->getDirty();
            AuditService::log('updated', $model, $old, $new);
        });

        static::deleted(function ($model) {
            AuditService::log('deleted', $model, $model->getOriginal(), null);
        });
    }
}
