# Upgrading

## From v1 to v2

Since [spatie/image](https://spatie.be/docs/image/v3/upgrading) does not rely on Glide anymore, there are some breaking changes for the media formats:

```diff
<?php

namespace App\Formats;

use Codedor\MediaLibrary\Formats\Format;
- use Spatie\Image\Manipulations;
+ use Codedor\MediaLibrary\Formats\Manipulations;
+ use Spatie\Image\Enums\Fit;

class Aspect10x11 extends Format
{
    protected string $description = 'Used in the numbered media list block and in
        the media text block in architect when this format is chosen';

    public function definition(): Manipulations
    {
        return $this->manipulations
-            ->fit(Manipulations::FIT_CROP, 673, 740);
+            ->fit(Fit::Crop, 673, 740);
    }

    public function registerModelsForFormatter(): void
    {
        $this->registerFor(\Codedor\MediaLibrary\Models\Attachment::class);
    }
}
```

Apply this change to all your custom formats. And you should be good to go.
