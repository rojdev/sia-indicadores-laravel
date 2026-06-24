<?php

namespace App\Listeners;

use App\Imports\AccionesImport;
use Maatwebsite\Excel\Events\ImportFinished;
use Maatwebsite\Excel\Concerns\WithEvents;


class AccionesImportListener
{
    public function handle(WithEvents $event)
    {
        (new AccionesImport)->updated();
    }
}
