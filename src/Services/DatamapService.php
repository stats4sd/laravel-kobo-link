<?php

namespace Stats4sd\KoboLink\Services;

use Log;
use Stats4sd\KoboLink\Models\Submission;

class DatamapService
{
    /**
     * This example method will run whenever a submission from a form linked to a Data Map with id 'testMap'.
     * You should replace this entire Service Class with your own, and include a method for every Data Map you create.
     *
     * If you create any new database entries from the submission, you should update the $submission->entries array to reference the new entries.
     * For example, if the submission is a household survey; you may create 1 new App\Models\Household entry and 2 new App\Models\HouseholdMember entries.
     * So you should update the $submission->entries property:
     *      $submission->entries = [
     *          Household::class => [$household->id],
     *          HouseholdMember::class => [$memberOne->id, $memberTwo->id]
     *      ];
     *
     * If the submission processing results in errors, e.g. validation errors, you should add the errors to the $submission->errors array.
     *
     */
    public function testMap(Submission $submission): void
    {
        Log::info('submission processed with id = ' . $submission->id);
    }
}
