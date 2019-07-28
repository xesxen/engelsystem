<?php

namespace Engelsystem\Test\Unit\Models\Auth;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Engelsystem\Models\Auth\Permission;

class PermissionTest extends AuthModelTest
{
    use ArraySubsetAsserts;

    /** @var array */
    protected $data = [
        'name'        => 'foo.bar',
        'description' => 'Allows bar in foo',
    ];

    /**
     * @covers \Engelsystem\Models\Auth\Permission::roles
     */
    public function testPermissions()
    {
        $role = $this->getRole();
        $role2 = $this->getRole([], ['name' => 'Example Role']);

        $permission = new Permission($this->data);
        $permission->save();
        $permission->roles()->attach($role);
        $permission->roles()->attach($role2);

        $this->assertCount(2, $permission->roles);
        $this->assertEquals('Test Role', $permission->roles()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\Auth\Permission::users
     */
    public function testUsers()
    {
        $role = $this->getRole();
        $team = $this->getTeam($role);
        $this->getUser($team);

        $permission = new Permission($this->data);
        $permission->save();
        $permission->roles()->attach($role);

        $this->assertEquals('Test User', $permission->users()->get()->first()->name);
    }
}
