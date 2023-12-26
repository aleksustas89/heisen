<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CdekOffice;
use App\Models\CdekCity;
use App\Http\Controllers\CdekController;

class CdekOffices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cdek-update-offices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $CdekController = new CdekController();

        DB::table('cdek_offices')->truncate();

        foreach (CdekCity::get() as $CdekCity) {
            if ($aOffices = $CdekController->getOffices($CdekCity->code)) {

                foreach ($aOffices as $aOffice) {
                    $CdekOffice = new CdekOffice();
                    $CdekOffice->code = $aOffice->code;
                    $CdekOffice->name = $aOffice->name;
                    $CdekOffice->address_comment = $aOffice->address_comment ?? $aOffice->note ?? '';
                    $CdekOffice->uuid = $aOffice->uuid;
                    $CdekOffice->work_time = $aOffice->work_time;
                    $CdekOffice->cdek_region_id = $aOffice->location->region_code;
                    $CdekOffice->cdek_city_id = $aOffice->location->city_code;
                    $CdekOffice->longitude = $aOffice->location->longitude;
                    $CdekOffice->latitude = $aOffice->location->latitude;
                    $CdekOffice->weight_min = $aOffice->weight_min ?? 0;
                    $CdekOffice->weight_max = $aOffice->weight_max ?? 0;
    
                    $CdekOffice->save();
                }
    
            }
        }




    }
}