<?php

namespace Codedor\MediaLibrary\Tests\TestFormats;

use Codedor\MediaLibrary\Formats\Format;
use Codedor\MediaLibrary\Formats\Manipulations;
use Codedor\MediaLibrary\Tests\TestModels\TestModel;
use Spatie\Image\Enums\Fit;

class TestHeroWebp extends Format
{
    protected string $description = 'Test format';

    public function definition(): Manipulations
    {
        return $this->manipulations
            ->fit(Fit::Crop, 100, 100)
            ->format('webp')
            ->sepia();
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(TestModel::class, [
            'test_id',
        ]);
    }
}
