<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Banner;

class BannerObserver
{
    /**
     * Handle the Banner "creating" event.
     */
    public function creating(Banner $banner): void
    {
        $maxPosition = Banner::max('position') ?? 0;

        $banner->position = $maxPosition + 1;
    }
}
