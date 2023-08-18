<?php

use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function createAttachment($data = [])
{
    return Attachment::withoutEvents(
        fn () => Attachment::factory($data)->create()
    );
}
