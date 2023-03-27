<?php

use Codedor\Attachments\Conversions\Conversion;
use Codedor\Attachments\Jobs\GenerateAttachmentFormat;
use Codedor\Attachments\Models\Attachment;
use Codedor\Attachments\Tests\TestFormats\TestHero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

it('dispatches conversions for all formats', function () {


    $format = new TestHero('test');

    /** @var Attachment $attachment */
    $attachment = Attachment::factory([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ])->create();

    $this->mock(Conversion::class, function (MockInterface $mock) use (
        $format, $attachment
    ) {
        $mock->shouldReceive('convert')
            ->once()
            ->withArgs([$attachment, $format]);
    });

    $job = new GenerateAttachmentFormat($attachment, $format);
    $job->handle();
});
