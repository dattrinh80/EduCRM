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
                $isSuperAdmin = app('is_super_admin');
            } catch (\Exception $e) {
                $isSuperAdmin = false;
            }

            // Only filter by center if the user is NOT a super admin
            if (!$isSuperAdmin) {
                try {
                    $centerId = app('center_id');
                    if ($centerId) {
                        $builder->where(in_array('center_id', static::$guardableColumns ?? []) ? 'center_id' : $builder->getModel()->getTable() . '.center_id', $centerId);
                    } else {
                        // If centerId is null, and user is not super admin, they shouldn't see anything.
                        $builder->whereRaw('1 = 0');
                    }
                } catch (\Exception $e) {
                    // Context not available (e.g., CLI, jobs)
                }
            }
        });
    }
}
