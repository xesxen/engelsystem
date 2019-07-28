<?php

namespace Engelsystem\Test\Unit\Models\Auth;

use Engelsystem\Models\Auth\Permission;
use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\BaseModel;
use Engelsystem\Models\Team;
use Engelsystem\Models\User\User;
use Engelsystem\Test\Unit\Models\ModelTest;

abstract class AuthModelTest extends ModelTest
{
    /** @var array */
    protected $user = [
        'name'     => 'Test User',
        'password' => 'nope',
        'email'    => 'foo@bar.batz',
        'api_key'  => 'Test123',
    ];

    /** @var array */
    protected $team = [
        'name' => 'Test Team',
    ];

    /** @var array */
    protected $role = [
        'name' => 'Test Role',
    ];

    /** @var array */
    protected $permission = [
        'name' => 'foo.bar',
    ];

    /**
     * @param array $data
     * @return Permission
     */
    protected function getPermission($data = [])
    {
        /** @var Permission $model */
        $model = $this->getModel(Permission::class, array_merge($this->permission, $data));

        return $model;
    }

    /**
     * @param Permission|Permission[] $permissions
     * @param array                   $data
     * @return Role
     */
    protected function getRole($permissions = [], $data = [])
    {
        /** @var Role $model */
        $model = $this->getModel(Role::class, array_merge($this->role, $data), ['permissions' => $permissions]);

        return $model;
    }

    /**
     * @param Role|Role[] $roles
     * @param array       $data
     * @return Team
     */
    protected function getTeam($roles = [], $data = [])
    {
        /** @var Team $model */
        $model = $this->getModel(Team::class, array_merge($this->team, $data), ['roles' => $roles]);

        return $model;
    }

    /**
     * @param Team|Team[] $teams
     * @param array       $data
     * @return User
     */
    protected function getUser($teams = [], $data = [])
    {
        /** @var User $model */
        $model = $this->getModel(User::class, array_merge($this->user, $data), ['teams' => $teams]);

        return $model;
    }

    /**
     * @param string $class BaseModel implementation name
     * @param array  $data
     * @param array  $attachments
     * @return BaseModel
     */
    protected function getModel($class, $data = [], $attachments = [])
    {
        /** @var BaseModel $instance */
        $instance = new $class($data);
        $instance->save();

        foreach ($attachments as $type => $attachment) {
            $objects = is_object($attachment) ? [$attachment] : $attachment;
            foreach ($objects as $object) {
                $instance->$type()->attach($object);
            }
        }

        return $instance;
    }
}
