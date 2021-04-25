<?php


namespace Stats4sd\KoboLink\Exports;

use Illuminate\Support\Facades\DB;

class SqlViewExport implements \Maatwebsite\Excel\Concerns\FromCollection
{
    public string $viewName;

    public function __construct(string $viewName)
    {
        $this->viewName = $viewName;
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        return DB::table($this->viewName)->get();
    }
}
