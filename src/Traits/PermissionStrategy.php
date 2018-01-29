<?php
/**
 * @date 22.11.2016
 * @file    PermissionStrategy.php
 * @author Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Macgriog\Acl\Traits;

trait PermissionStrategy
{

    /**
     * @var array Allowed permissions values.
     *
     * Possible options:
     *    0 => Remove.
     *    1 => Add.
     *
     * @return array
     */
    public function getAllowedPermissionsValues() : array
    {
        return [0, 1];
    }

    /**
     * Setter for permissions.
     *
     * @param array $permissions
     */
    public function setPermissionsAttribute(array $permissions)
    {
        foreach ($permissions as $permission => &$value) {
            if (!in_array($value = (int) $value, $this->getAllowedPermissionsValues())) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid value "%s" for permission "%s" given.',
                    $value,
                    $permission
                ));
            }

            if ($value === 0) {
                unset($permissions[$permission]);
            }
        }
        $this->attributes['permissions'] = !empty($permissions) ? json_encode($permissions) : '';
    }

    /**
     * Returns the json decoded permissions.
     *
     * @return array
     */
    public function getPermissionsAttribute() : array
    {
        return !empty($this->attributes['permissions'])
            ? json_decode($this->attributes['permissions'], true)
            : [];
    }

    /**
     * @return array
     */
    public function getMergedPermissions() : array
    {
        return empty($this->permissions) ? [] : $this->permissions;
    }


     /**
     * See if a model has access to the passed permission(s).
     * Permissions may be merged from all groups/roles the model belongs to
     * and then are checked against the passed permission(s).
     *
     * If multiple permissions are passed, the model must
     * have access to all permissions passed through, unless the
     * "all" flag is set to false.
     *
     * @param  string|array  $permissions
     * @param  bool  $all
     * @return bool
     */
    public function hasPermission($permissions, $all = true) : bool
    {
        $mergedPermissions = $this->getMergedPermissions();

        foreach ($this->asArray($permissions) as $permission) {
            $matched = $this->matchesPermission($permission, $mergedPermissions);

            if ($all === true && $matched === false) {
                return false;
            } elseif ($all === false && $matched === true) {
                return true;
            }
        }

        return false === $all ? false : true;
    }

    protected function matchesPermission($permission, $allPermissions)
    {
        $match = false;
        foreach (array_keys($allPermissions) as $currentPermission) {
            if ($permission === $currentPermission && $allPermissions[$permission] === 1) {
                $match = true;
                break;
            }
        }
        return $match;
    }

    protected function asArray($arrayOrString) : array
    {
        return is_array($arrayOrString) ? $arrayOrString : [$arrayOrString];
    }
}
