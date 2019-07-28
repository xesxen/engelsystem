<?php

namespace Engelsystem\Test\Unit\Models;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Engelsystem\Models\Team;
use Engelsystem\Test\Unit\Models\Auth\AuthModelTest;

class TeamTest extends AuthModelTest
{
    use ArraySubsetAsserts;

    /** @var array */
    protected $data = [
        'name'                     => 'Example team',
        'description'              => 'Lorem Ipsum',
        'contact_name'             => 'Con Tact',
        'contact_dect'             => '1337',
        'contact_email'            => 'foo@bar.batz',
        'restricted'               => false,
        'self_signup'              => true,
        'requires_drivers_license' => false,
        'show_on_frontend'         => true,
        'show_on_dashboard'        => true,
    ];

    /**
     * @covers \Engelsystem\Models\Team::roles
     */
    public function testRoles()
    {
        $role = $this->getRole();
        $role2 = $this->getRole([], ['name' => 'Another Role']);

        $team = new Team($this->data);
        $team->save();
        $team->roles()->attach($role);
        $team->roles()->attach($role2);

        $this->assertCount(2, $team->roles);
        $this->assertEquals('Test Role', $team->roles()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\Team::supporters
     */
    public function testSupporters()
    {
        $user = $this->getUser();

        $team = new Team($this->data);
        $team->save();
        $team->supporters()->attach($user);

        $this->assertEquals('Test User', $team->supporters()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\Team::users
     */
    public function testUsers()
    {
        $user = $this->getUser();

        $team = new Team($this->data);
        $team->save();
        $team->users()->attach($user);

        $this->assertEquals('Test User', $team->users()->get()->first()->name);
    }
}
