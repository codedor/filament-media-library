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

it('preserves manual crops when regenerating formats', function () {
    $format = new TestHero('test');

    /** @var Attachment $attachment */
    $attachment = createAttachment([
        'type' => 'image',
        'extension' => 'jpg',
        'disk' => 'public',
    ]);

    // Create a manual crop for this format
    $formatRecord = $attachment->formats()->create([
        'format' => $format->key(),
        'data' => ['crop' => 'manual_crop_data'],
    ]);

    // Make sure the relationship is working
    expect($attachment->formats()->count())->toBe(1);
    expect($attachment->formats()->where('format', $format->key())->first())->not()->toBeNull();
    expect($attachment->formats()->where('format', $format->key())->first()->data)->not()->toBeNull();

    // Use the real LocalConversion class to test our fix
    $conversion = new \Codedor\MediaLibrary\Conversions\LocalConversion;

    // The conversion should return true but not actually process because of manual crop
    $result = $conversion->convert($attachment, $format, force: true);

    expect($result)->toBe(true);

    // The file should not exist because conversion was skipped
    expect($attachment->getStorage()->exists("$attachment->directory/{$format->filename($attachment)}"))->toBe(false);
});
