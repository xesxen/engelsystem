<?php

namespace Engelsystem\Test\Unit\Models\Auth;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Engelsystem\Models\Auth\Role;

class RoleTest extends AuthModelTest
{
    use ArraySubsetAsserts;

    /** @var array */
    protected $data = [
        'name'        => 'Test Role',
        'description' => 'Lorem Ipsum',
    ];

    /**
     * @covers \Engelsystem\Models\Auth\Role::permissions
     */
    public function testPermissions()
    {
        $permission = $this->getPermission();
        $permission2 = $this->getPermission(['name' => 'Another Permission']);

        $role = new Role($this->data);
        $role->save();
        $role->permissions()->attach($permission);
        $role->permissions()->attach($permission2);

        $this->assertCount(2, $role->permissions);
        $this->assertEquals('foo.bar', $role->permissions()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\Auth\Role::teams
     */
    public function testTeams()
    {
        $team = $this->getTeam();

        $role = new Role($this->data);
        $role->save();
        $role->teams()->attach($team);

        $this->assertEquals('Test Team', $role->teams()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\Auth\Role::users
     */
    public function testUsers()
    {
        $team = $this->getTeam();
        $team2 = $this->getTeam([], ['name' => 'Dev']);
        $this->getUser($team);
        $this->getUser($team, ['name' => 'Foo', 'email' => 'foo@xample.com']);
        $this->getUser($team2, ['name' => 'Bar', 'email' => 'bar@xample.com']);

        $role = new Role($this->data);
        $role->save();
        $role->teams()->attach($team);
        $role->teams()->attach($team2);

        $this->assertCount(3, $role->users()->get());
        $this->assertEquals('Test User', $role->users()->get()->first()->name);
    }
}
