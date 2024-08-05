<?php

namespace Codedor\MediaLibrary\Tests\TestFormats;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Formats\Manipulations;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;
use Spatie\Image\Enums\Fit;

class TestHero extends Format
{
    protected string $description = 'Test format';

    public function definition(): Manipulations
    {
        return $this->manipulations
            ->fit(Fit::Crop, 100, 100)
            ->sepia();
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(TestModel::class, [
            'test_id',
        ]);
    }
}
