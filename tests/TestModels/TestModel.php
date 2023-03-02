<?php

namespace Codedor\Attachments\Tests\TestModels;

use Codedor\Attachments\Interfaces\HasFormats;
use Codedor\Attachments\Tests\TestFormats\TestHero;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TestModel extends Model implements HasFormats
{
    public static function getFormats(Collection $formats): Collection
    {
        return $formats->add(TestHero::make('test_id'));
    }
}
