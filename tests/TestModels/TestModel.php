<?php

namespace Codedor\MediaLibrary\Tests\TestModels;

use Codedor\MediaLibrary\Interfaces\HasFormats;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Codedor\MediaLibrary\Tests\TestFormats\TestHeroWebp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TestModel extends Model implements HasFormats
{
    public static function getFormats(Collection $formats): Collection
    {
        return $formats->add(TestHero::make('test_id'))
            ->add(TestHeroWebp::make('test_id'));
    }
}
