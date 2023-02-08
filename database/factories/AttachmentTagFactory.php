<?php

namespace Codedor\Attachments\Database\Factories;

use Codedor\Attachments\Models\AttachmentTag;
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
