<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\CdekRegion;
use App\Models\CdekCity;
use App\Http\Controllers\CdekController;

class CdekRegionsAndCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cdek-update-regions-and-cities';

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

        if ($aRegions = $CdekController->getRegions()) {

            foreach ($aRegions as $aRegion) {
                if (is_null(CdekRegion::where("code", $aRegion->region_code)->first())) {
                    $CdekRegion = new CdekRegion();
                    $CdekRegion->name = $aRegion->region;
                    $CdekRegion->code = $aRegion->region_code;
                    $CdekRegion->save();
                }
            }
        }

        if ($aCities = $CdekController->getCities()) {
        
            foreach ($aCities as $aCity) {

                if (is_null(CdekCity::where("code", $aCity->code)->first())) {
                    $CdekCity = new CdekCity();
                    $CdekCity->name = $aCity->city;
                    $CdekCity->cdek_region_id = $aCity->region_code;
                    $CdekCity->code = $aCity->code;
                    $CdekCity->save();
                }
            }
        }
    }
}