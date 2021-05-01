<?php


namespace Stats4sd\KoboLink\Exports;

use Illuminate\Support\Facades\DB;
use App\Models\Team;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SqlViewExport implements \Maatwebsite\Excel\Concerns\FromCollection, WithHeadings
{
    public string $viewName;
    public $team;


    public function __construct(string $viewName, $team = null)
    {
        $this->viewName = $viewName;
        $this->team = $team;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        if ($this->team) {
            $collection = DB::table($this->viewName)->where('team_id', '=', $this->team->id)->get();
            return $collection->map(function ($item) {
                unset($item->team_id);
                return $item;
            });
        }
        return DB::table($this->viewName)->get();
    }

    public function headings(): array
    {
        $example = DB::table($this->viewName)->limit(1)->get();

        return collect($example->first())
            ->keys()
            ->filter(function ($heading) {
                return $heading !== "team_id";
            })->toArray();
    }
}
