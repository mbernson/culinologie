<?php

namespace App\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

/**
 * Visibility scope
 *
 * This is an Eloquent scope that can (globally) restrict access to a model.
 *
 * Assumptions:
 *
 * - The model has a `visibility` column of an integer type.
 * - The model has a `user_id` column that refers to a user model.
 */
final class VisibilityScope implements Scope
{
    // The item is visible to anyone
    public const VISIBILITY_PUBLIC = 0;
    // The item is only visible to its owner
    public const VISIBILITY_PRIVATE = 1;
    // The item is only visible to its owner and other logged in users
    public const VISIBILITY_LOGGED_IN = 2;

    private $tableName;

    public function apply(Builder $builder, Model $model)
    {
        $this->tableName = $model->getTable();

        if (Auth::check() && Auth::user()->is_approved) {
            $this->applyLoggedInScope($builder, Auth::user());
        } else {
            $this->applyPublicScope($builder);
        }
    }

    public function applyPublicScope(Builder $query)
    {
        return $query->where('visibility', '=', self::VISIBILITY_PUBLIC);
    }

    public function applyLoggedInScope(Builder $query, User $user)
    {
        $isPrivate = function ($query) use ($user) {
            $query->where('visibility', '=', self::VISIBILITY_PRIVATE)
                  ->where("{$this->tableName}.user_id", '=', $user->id);
        };

        $whereVisible = function ($query) use ($isPrivate) {
            $query->whereIn('visibility', [self::VISIBILITY_PUBLIC, self::VISIBILITY_LOGGED_IN])
                  ->orWhere($isPrivate);
        };

        return $query->where($whereVisible);
    }

    public function remove(Builder $builder, Model $model)
    {
        // TODO: Implement me
    }
}
