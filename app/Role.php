<?php

namespace App;

/**
 * Class Role
 * @package App
 */
class Role extends \Cartalyst\Sentinel\Roles\EloquentRole
{
    /**
     * @var null|array
     */
    protected $standardPermissions = null;

    /**
     * @return mixed
     */
    public function scopeSearchable($query) {
        return $query->whereIn($this->getTable() . '.slug', User::SEARCHABLE_ROLES);
    }

    /**
     * @param array $permissions
     * @return array
     */
    public function applyPermissions($permissions)
    {
        if (is_null($this->standardPermissions)) {
            $standardPermissions =  new StandardPermissions();
            $this->standardPermissions = $standardPermissions->getSecondaryPermissions();
        }

        foreach($this->standardPermissions as $item) {
            $status = boolval($permissions[$item] ?? false);
            if ($status) {
                $this->updatePermission($item, boolval($permissions[$item] ?? false), true);
            } else {
                $this->removePermission($item);
            }
        }

        return $this->getPermissions();
    }

    /**
     * @param array $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->applyPermissions($this->permissions);
        return parent::save($options);
    }
}