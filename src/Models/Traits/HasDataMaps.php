<?php

namespace Stats4sd\KoboLink\Models\Traits;

use Stats4sd\KoboLink\Models\Submission;

trait HasDataMaps
{
    public function exampleMap(Submission $submission): void
    {
        \Log::info('submission processed with id: ' . $submission->id);

        $submission->processed = true;
        $submission->save();

    }

}
