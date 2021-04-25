<?php


namespace Stats4sd\KoboLink\Exports;

use \Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Stats4sd\KoboLink\Models\TeamXlsform;

class FormSubmissionsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    private TeamXlsform $form;

    public function forForm(TeamXlsform $form)
    {
        $this->form = $form;

        return $this;
    }

    public function headings(): array
    {
        $submission = collect(json_decode($this->form->submissions->first()->pluck('content')[0]))->toArray();

        return array_keys($submission);
    }

    public function collection(): Collection
    {
        $submissions = $this->form->submissions;
        $data = [];

        foreach ($submissions as $submission) {
            $data[] = json_decode($submission->content);
        }

        return collect($data);
    }
}
