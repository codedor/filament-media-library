<?php

use Codedor\MediaLibrary\Conversions\Conversion;
use Codedor\MediaLibrary\Jobs\GenerateAttachmentFormat;
use Codedor\MediaLibrary\Models\Attachment;
use Codedor\MediaLibrary\Tests\TestFormats\TestHero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

it('dispatches conversions for all formats', function () {
    $format = new TestHero('test');

    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    $this->mock(Conversion::class, function (MockInterface $mock) use (
        $format, $attachment
    ) {
        $mock->shouldReceive('convert')
            ->once()
            ->withArgs([$attachment, $format, false]);
    });

    $job = new GenerateAttachmentFormat($attachment, $format);
    $job->handle();
});
