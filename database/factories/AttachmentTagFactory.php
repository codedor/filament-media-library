<?php

namespace Wotz\MediaLibrary\Database\Factories;

use Wotz\MediaLibrary\Models\AttachmentTag;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentTagFactory extends Factory
{
    protected $model = AttachmentTag::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word,
        ];
    }
}
