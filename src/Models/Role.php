<?php

namespace Macgriog\Acl\Models;

use Macgriog\Acl\Traits\RolePermissions;
use Illuminate\Database\Eloquent\Model;

/**
 * Basic example Role model.
 */
class Role extends Model
{
    use RolePermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'label', 'permissions'
    ];
}
