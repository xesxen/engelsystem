<?php

namespace Engelsystem\Migrations;

use Engelsystem\Database\Migration\Migration;
use Engelsystem\Models\Auth\Permission;
use Engelsystem\Models\Auth\Role;
use Engelsystem\Models\Team;
use Engelsystem\Models\User\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Schema\Blueprint;
use stdClass;

class CreateTeamsRolesAndPermissionsTables extends Migration
{
    use ChangesReferences;
    use Reference;

    /**
     * Run the migration
     */
    public function up()
    {
        $this->schema->create('teams', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->text('description')->nullable();

            $table->string('contact_name')->nullable()->default(null);
            $table->string('contact_email')->nullable()->default(null);
            $table->string('contact_dect', 40)->nullable()->default(null);

            $table->boolean('restricted')->default(false);
            $table->boolean('self_signup')->default(true);
            $table->boolean('requires_drivers_license')->default(false);
            $table->boolean('show_on_frontend')->default(true);
            $table->boolean('show_on_dashboard')->default(true);

            $table->timestamps();
        });

        $this->schema->create('roles', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        $this->schema->create('permissions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->unique();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        $this->schema->create('team_user', function (Blueprint $table) {
            $table->increments('id');

            $this->referencesUser($table);
            $this->references($table, 'teams', 'team_id');
            $table->boolean('confirmed')->default(true);

            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        $this->schema->create('supporter_team', function (Blueprint $table) {
            $table->increments('id');

            $this->referencesUser($table);
            $this->references($table, 'teams', 'team_id');

            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        $this->schema->create('role_team', function (Blueprint $table) {
            $table->increments('id');

            $this->references($table, 'teams', 'team_id');
            $this->references($table, 'roles', 'role_id');

            $table->timestamps();

            $table->unique(['role_id', 'team_id']);
        });

        $this->schema->create('permission_role', function (Blueprint $table) {
            $table->increments('id');

            $this->references($table, 'roles', 'role_id');
            $this->references($table, 'permissions', 'permission_id');

            $table->timestamps();

            $table->unique(['permission_id', 'role_id']);
        });

        if ($this->schema->hasTable('AngelTypes')) {
            $connection = $this->schema->getConnection();
            $connection
                ->table('Groups')
                ->update([
                    'UID' => $connection->raw('UID / 10')
                ]);

            /** @var Team[] $teams */
            $teams = [];
            /** @var stdClass[] $users */
            $angelTypes = $connection->table('AngelTypes')->get();
            foreach ($angelTypes as $data) {
                $team = new Team([
                    'name'                     => $data->name,
                    'description'              => $data->description ?: null,
                    'contact_name'             => $data->contact_name ?: null,
                    'contact_email'            => $data->contact_email ?: null,
                    'contact_dect'             => $data->contact_dect ?: null,
                    'restricted'               => (bool)$data->restricted,
                    'self_signup'              => !(bool)$data->no_self_signup,
                    'requires_drivers_license' => (bool)$data->requires_driver_license,
                    'show_on_dashboard'        => (bool)$data->show_on_dashboard,
                ]);
                $team->setAttribute('id', $data->id);

                $team->save();

                $teams[$team->id] = $team;
                $teams[$team->name] = $team;
            }

            /** @var Role[] $roles */
            $roles = [];
            /** @var stdClass[] $groups */
            $groups = $connection->table('Groups')->get();
            foreach ($groups as $data) {
                $role = new Role([
                    'name'        => $data->Name,
                    'description' => null,
                ]);
                $role->setAttribute('id', $data->UID);

                $role->save();

                $roles[$role->id] = $role;
            }

            /** @var Permission[] $permissions */
            $permissions = [];
            /** @var stdClass[] $privileges */
            $privileges = $connection->table('Privileges')->get();
            foreach ($privileges as $data) {
                $permission = new Permission([
                    'name'        => $data->name,
                    'description' => $data->desc ?: null,
                ]);
                $permission->setAttribute('id', $data->id);

                $permission->save();

                $permissions[$permission->id] = $permission;
            }

            $this->changeReferences(
                'AngelTypes',
                'id',
                'teams',
                'id'
            );

            $this->changeReferences(
                'Groups',
                'UID',
                'roles',
                'id'
            );

            $this->changeReferences(
                'Privileges',
                'id',
                'permissions',
                'id'
            );

            /** @var stdClass[] $groupPrivileges */
            $groupPrivileges = $connection->table('GroupPrivileges')->get();
            foreach ($groupPrivileges as $data) {
                $roles[$data->group_id]
                    ->permissions()
                    ->attach($permissions[$data->privilege_id]);
            }

            foreach ($roles as $role) {
                if ($role->name == 'Guest' || Team::whereName($role->name)->count()) {
                    continue;
                }

                $team = new Team([
                    'name'              => $role->name,
                    'description'       => $role->description,
                    'restricted'        => $role->name != 'Angel',
                    'show_on_frontend'  => !in_array($role->name, ['Developer']),
                    'show_on_dashboard' => !in_array($role->name, ['Developer', 'News Admin']),
                ]);

                $team->save();

                $teams[$team->id] = $team;
                $teams[$team->name] = $team;
            }

            /** @var stdClass $userAngelTypes */
            $userAngelTypes = $connection
                ->table('UserAngelTypes')
                ->orderBy('user_id')
                ->orderBy('angeltype_id')
                ->get();
            foreach ($userAngelTypes as $userAngelType) {
                $teams[$userAngelType->angeltype_id]->users()->attach(
                    $userAngelType->user_id,
                    ['confirmed' => (bool)$userAngelType->confirm_user_id]
                );

                if ($userAngelType->supporter) {
                    $teams[$userAngelType->angeltype_id]->supporters()->attach($userAngelType->user_id);
                }
            }

            /** @var stdClass[] $userGroups */
            $userGroups = $connection
                ->table('UserGroups')
                ->leftJoin('Groups', 'group_id', '=', 'Groups.UID')
                ->orderBy('UserGroups.uid')
                ->orderBy('Groups.Name')
                ->get(['Groups.Name AS group_name', 'UserGroups.uid AS user_id']);
            foreach ($userGroups as $userGroup) {
                $teams[$userGroup->group_name]->users()->attach($userGroup->user_id);
            }

            /** @var stdClass[] $teamRoles */
            $teamRoles = $connection
                ->table('roles')
                ->join('teams', 'roles.name', '=', 'teams.name')
                ->get(['roles.id AS role_id', 'teams.id AS team_id']);
            foreach ($teamRoles as $teamRole) {
                $teams[$teamRole->team_id]->roles()->attach($teamRole->role_id);
            }

            $this->schema->table('GroupPrivileges', function (Blueprint $table) {
                $table->unsignedInteger('id')->unsigned()->autoIncrement()->change();
            });
        }

        $this->schema->dropIfExists('GroupPrivileges');
        $this->schema->dropIfExists('UserGroups');
        $this->schema->dropIfExists('UserAngelTypes');
        $this->schema->dropIfExists('Privileges');
        $this->schema->dropIfExists('Groups');
        $this->schema->dropIfExists('AngelTypes');
    }

    /**
     * Reverse the migration
     */
    public function down()
    {
        $connection = $this->schema->getConnection();

        $this->schema->create('AngelTypes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 50);
            $table->boolean('restricted');
            $table->text('description');
            $table->boolean('requires_driver_license');
            $table->boolean('no_self_signup');
            $table->string('contact_name', 250)->nullable();
            $table->string('contact_dect', 40)->nullable();
            $table->string('contact_email', 250)->nullable();
            $table->boolean('show_on_dashboard');

            $table->unique('name');
        });

        $this->schema->create('Groups', function (Blueprint $table) {
            $table->string('Name', 35);

            $table->integer('UID')->primary();
        });

        $this->schema->create('Privileges', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 128)->unique();
            $table->string('desc', 1024);
        });

        $this->schema->create('UserAngelTypes', function (Blueprint $table) {
            $table->increments('id');

            $this->referencesUser($table);
            $this->references($table, 'teams', 'angeltype_id');
            $this->references($table, 'users', 'confirm_user_id')->nullable();
            $table->boolean('supporter')->nullable();

            $table->index(['user_id', 'angeltype_id', 'confirm_user_id']);
            $table->index(['angeltype_id']);
            $table->index(['confirm_user_id']);
            $table->index(['supporter']);
        });

        $this->schema->create('UserGroups', function (Blueprint $table) {
            $table->increments('id');

            $this->references($table, 'users', 'uid');
            $this->references($table, 'roles', 'group_id');

            $table->index(['uid', 'group_id']);
            $table->index(['group_id']);
        });

        $this->schema->create('GroupPrivileges', function (Blueprint $table) {
            $table->increments('id');

            $this->references($table, 'roles', 'group_id');
            $this->references($table, 'permissions', 'privilege_id');

            $table->index(['group_id', 'privilege_id']);
            $table->index(['privilege_id']);
        });

        $roleNames = Role::all('name')->pluck('name')->toArray();
        $teams = Team::query()
            ->whereNotIn('name', $roleNames)
            ->get();
        foreach ($teams as $team) {
            /** @var Team $team */
            $connection
                ->table('AngelTypes')
                ->insert([
                    'id'                      => $team->id,
                    'name'                    => $team->name,
                    'description'             => $team->description ?: '',
                    'restricted'              => $team->restricted,
                    'requires_driver_license' => $team->requires_drivers_license,
                    'no_self_signup'          => !$team->self_signup,
                    'contact_name'            => $team->contact_name,
                    'contact_dect'            => $team->contact_dect,
                    'contact_email'           => $team->contact_email,
                    'show_on_dashboard'       => $team->show_on_dashboard
                ]);
        }

        foreach (Role::all() as $role) {
            $connection
                ->table('Groups')
                ->insert([
                    'Name' => $role->name,
                    'UID'  => $role->id,
                ]);
        }

        foreach (Permission::all() as $permission) {
            $connection
                ->table('Privileges')
                ->insert([
                    'id'   => $permission->id,
                    'name' => $permission->name,
                    'desc' => $permission->description
                ]);
        }


        $teams = Team::with('users', 'supporters')
            ->whereNotIn('name', $roleNames)
            ->get();
        foreach ($teams as $team) {
            /** @var User[]|Collection $supporters */
            $supporters = $team->supporters;
            foreach ($team->users as $user) {
                /** @var User $user */
                $connection
                    ->table('UserAngelTypes')
                    ->insert([
                        'user_id'         => $user->id,
                        'angeltype_id'    => $team->id,
                        'confirm_user_id' => $user->pivot->confirmed ?: null,
                        'supporter'       => $supporters->where('id', '=', $user->id)->count(),
                    ]);
            }
        }

        $teams = Team::with('users')
            ->whereIn('name', $roleNames)
            ->get();
        foreach ($teams as $team) {
            foreach ($team->users as $user) {
                $role = Role::whereName($team->name)->first();
                /** @var User $user */
                $connection
                    ->table('UserGroups')
                    ->insert([
                        'uid'      => $user->id,
                        'group_id' => $role ? $role->id : $team->id,
                    ]);
            }
        }

        foreach (Permission::with('roles')->get() as $permission) {
            foreach ($permission->roles as $role) {
                $connection
                    ->table('GroupPrivileges')
                    ->insert([
                        'group_id'     => $role->id,
                        'privilege_id' => $permission->id,
                    ]);
            }
        }

        $this->schema->drop('permission_role');
        $this->schema->drop('role_team');
        $this->schema->drop('supporter_team');
        $this->schema->drop('team_user');

        $this->changeReferences(
            'teams',
            'id',
            'AngelTypes',
            'id',
            'unsignedInteger'
        );

        $this->changeReferences(
            'roles',
            'id',
            'Groups',
            'UID',
            'integer'
        );

        $this->changeReferences(
            'permissions',
            'id',
            'Privileges',
            'id'
        );

        $this->schema->drop('permissions');
        $this->schema->drop('roles');
        $this->schema->drop('teams');

        $connection
            ->table('Groups')
            ->update([
                'UID' => $connection->raw('UID * 10')
            ]);
    }
}
