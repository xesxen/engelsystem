<?php

namespace Engelsystem\Test\Unit\Models\User;

use Engelsystem\Models\User\HasUserModel;
use Engelsystem\Test\Unit\Models\ModelTest;
use Engelsystem\Test\Unit\Models\User\Stub\HasUserModelImplementation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasUserModelTest extends ModelTest
{
    /**
     * @covers \Engelsystem\Models\User\HasUserModel::user
     */
    public function testHasOneRelations()
    {
        /** @var HasUserModel $contact */
        $model = new HasUserModelImplementation();

        $this->assertInstanceOf(BelongsTo::class, $model->user());
    }
}
