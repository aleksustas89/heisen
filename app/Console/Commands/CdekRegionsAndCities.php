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
            DB::table('cdek_regions')->truncate();

            foreach ($aRegions as $aRegion) {

                $CdekRegion = new CdekRegion();
                $CdekRegion->name = $aRegion->region;
                $CdekRegion->id = $aRegion->region_code;
                $CdekRegion->save();
            }
        }

        if ($aCities = $CdekController->getCities()) {
            
            DB::table('cdek_cities')->truncate();

            foreach ($aCities as $aCity) {

                $CdekCity = new CdekCity();
                $CdekCity->name = $aCity->city;
                $CdekCity->id = $aCity->code;
                $CdekCity->cdek_region_id = $aCity->region_code;
                $CdekCity->save();
            }
        }
    }
}