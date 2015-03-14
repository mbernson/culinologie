<?php namespace App\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Recipe;
use Auth;

final class VisibilityScope implements ScopeInterface
{

    public function apply(Builder $builder, Model $model)
    {
        if (Auth::check()) {
            $this->applyLoggedInScope($builder, Auth::user());
        } else {
            $this->applyPublicScope($builder);
        }
    }

    public function applyPublicScope(Builder $query)
    {
        return $query->where('visibility', '=', Recipe::VISIBILITY_PUBLIC);
    }

    public function applyLoggedInScope(Builder $query, User $user)
    {
        $isPrivate = function ($query) use ($user) {
            $query->where('visibility', '=', Recipe::VISIBILITY_PRIVATE)
                  ->where('user_id', '=', $user->id);
        };

        $whereVisible = function ($query) use ($isPrivate) {
            $query->whereIn('visibility', [Recipe::VISIBILITY_PUBLIC, Recipe::VISIBILITY_LOGGED_IN])
                  ->orWhere($isPrivate);
        };

        return $query->where($whereVisible);
    }

    public function remove(Builder $builder, Model $model)
    {
        // TODO: Implement me
    }
}
