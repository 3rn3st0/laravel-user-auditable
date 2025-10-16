<?php

namespace ErnestoCh\UserAuditable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait UserAuditable
{
    /**
     * Boot the UserAuditable trait
     */
    public static function bootUserAuditable(): void
    {
        static::creating(function (Model $model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'created_by')) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleting(function (Model $model) {
            if (Auth::check() && Schema::hasColumn($model->getTable(), 'deleted_by')) {
                if (method_exists($model, 'bootSoftDeletes')) {
                    $model->deleted_by = Auth::id();
                    $model->save();
                }
            }
        });

        static::restoring(function (Model $model) {
            if (Schema::hasColumn($model->getTable(), 'deleted_by')) {
                $model->deleted_by = null;
            }
        });
    }

    /**
     * Get the user who created the record
     */
    public function creator()
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'created_by');
    }

    /**
     * Get the user who updated the record
     */
    public function updater()
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'updated_by');
    }

    /**
     * Get the user who deleted the record
     */
    public function deleter()
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'deleted_by');
    }

    /**
     * Scope a query to only include records created by a specific user
     */
    public function scopeCreatedBy(Builder $query, $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope a query to only include records updated by a specific user
     */
    public function scopeUpdatedBy(Builder $query, $userId): Builder
    {
        return $query->where('updated_by', $userId);
    }

    /**
     * Scope a query to only include records deleted by a specific user
     */
    public function scopeDeletedBy(Builder $query, $userId): Builder
    {
        return $query->where('deleted_by', $userId);
    }
}
