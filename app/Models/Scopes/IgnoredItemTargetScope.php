<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Query\JoinClause;

class IgnoredItemTargetScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! $this->hasItemTargetJoin($builder)) {
            return;
        }

        $builder->where('item_target.ignored', false);
    }

    private function hasItemTargetJoin(Builder $builder): bool
    {
        $joins = $builder->getQuery()->joins;

        if ($joins === null) {
            return false;
        }

        return collect($joins)->contains(function (JoinClause $join): bool {
            $table = $join->table;

            if (! is_string($table)) {
                return false;
            }

            return str_contains($table, 'item_target');
        });
    }
}
