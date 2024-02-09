<?php

namespace Codedor\MediaLibrary\Tests\TestFormats;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;
use Spatie\Image\Manipulations;

class TestNoHeight extends Format
{
    protected string $description = 'Test format';

    public function definition(): Manipulations
    {
        return $this->manipulations
            ->fit(Manipulations::FIT_CROP, 100)
            ->sepia();
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(TestModel::class, [
            'test_id',
        ]);
    }
}
