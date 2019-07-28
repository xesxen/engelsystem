<?php

namespace Engelsystem\Test\Unit\Models\User;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use Engelsystem\Models\User\Contact;
use Engelsystem\Models\User\HasUserModel;
use Engelsystem\Models\User\PersonalData;
use Engelsystem\Models\User\Settings;
use Engelsystem\Models\User\State;
use Engelsystem\Models\User\User;
use Engelsystem\Test\Unit\Models\Auth\AuthModelTest;

class UserTest extends AuthModelTest
{
    use ArraySubsetAsserts;

    /** @var array */
    protected $data = [
        'name'     => 'lorem',
        'password' => '',
        'email'    => 'foo@bar.batz',
        'api_key'  => '',
    ];

    /**
     * @return array
     */
    public function hasOneRelationsProvider()
    {
        return [
            [
                Contact::class,
                'contact',
                [
                    'dect'   => '1234567',
                    'email'  => 'foo@bar.batz',
                    'mobile' => '1234/12341234',
                ],
            ],
            [
                PersonalData::class,
                'personalData',
                [
                    'first_name' => 'Foo',
                ],
            ],
            [
                Settings::class,
                'settings',
                [
                    'language' => 'de_DE',
                    'theme'    => 4,
                ],
            ],
            [
                State::class,
                'state',
                [
                    'force_active' => true,
                ],
            ],
        ];
    }

    /**
     * @covers       \Engelsystem\Models\User\User::contact
     * @covers       \Engelsystem\Models\User\User::personalData
     * @covers       \Engelsystem\Models\User\User::settings
     * @covers       \Engelsystem\Models\User\User::state
     *
     * @dataProvider hasOneRelationsProvider
     *
     * @param string $class
     * @param string $name
     * @param array  $data
     */
    public function testHasOneRelations($class, $name, $data)
    {
        $user = new User($this->data);
        $user->save();

        /** @var HasUserModel $contact */
        $contact = new $class($data);
        $contact->user()
            ->associate($user)
            ->save();

        $this->assertArraySubset($data, (array)$user->{$name}->attributesToArray());
    }

    /**
     * @covers \Engelsystem\Models\User\User::permissions
     */
    public function testPermissions()
    {
        $permission = $this->getPermission();
        $role = $this->getRole($permission);
        $team = $this->getTeam($role);

        $user = new User($this->data);
        $user->save();
        $user->teams()->attach($team);

        $this->assertEquals('foo.bar', $user->permissions()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\User\User::roles
     */
    public function testRoles()
    {
        $role = $this->getRole();
        $role2 = $this->getRole([], ['name' => 'Foo Role']);
        $team = $this->getTeam([$role, $role2]);

        $user = new User($this->data);
        $user->save();
        $user->teams()->attach($team);

        $this->assertCount(2, $user->roles);
        $this->assertEquals('Test Role', $user->roles()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\User\User::supports
     */
    public function testSupports()
    {
        $team = $this->getTeam();

        $user = new User($this->data);
        $user->save();
        $user->teams()->attach($team);
        $user->supports()->attach($team);

        $this->assertEquals('Test Team', $user->supports()->get()->first()->name);
    }

    /**
     * @covers \Engelsystem\Models\User\User::teams
     */
    public function testTeams()
    {
        $team = $this->getTeam();
        $team2 = $this->getTeam([], ['name' => 'Another Team']);

        $user = new User($this->data);
        $user->save();
        $user->teams()->attach($team);
        $user->teams()->attach($team2);

        $this->assertCount(2, $user->teams);
        $this->assertEquals('Test Team', $user->teams()->get()->first()->name);
    }
}
