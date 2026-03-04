<?php

namespace Modules\Core\Persistence\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToCenter
{
    /**
     * Boot the BelongsToCenter trait for a model.
     *
     * @return void
     */
    protected static function bootBelongsToCenter()
    {
        static::addGlobalScope('center', function (Builder $builder) {
            try {
                $isGlobalScope = app('is_global_scope');
            } catch (\Exception $e) {
                $isGlobalScope = false;
            }

            // Only filter by center if the user does NOT have global scope
            if (!$isGlobalScope) {
                try {
                    $centerId = app('center_id');
                    if ($centerId) {
                        $builder->where(in_array('center_id', static::$guardableColumns ?? []) ? 'center_id' : $builder->getModel()->getTable() . '.center_id', $centerId);
                    } else {
                        // If centerId is null, and user is not global scope, they shouldn't see anything.
                        $builder->whereRaw('1 = 0');
                    }
                } catch (\Exception $e) {
                    // Context not available (e.g., CLI, jobs)
                }
            }
        });
    }
}
