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
                if (is_null(CdekRegion::where("id", $aRegion->region_code)->first())) {
                    $CdekRegion = new CdekRegion();
                    $CdekRegion->name = $aRegion->region;
                    $CdekRegion->id = $aRegion->region_code;
                    $CdekRegion->save();
                }
            }
        }

        $page = 0;

        do  {
            $aCities = $CdekController->getCities($page);

            $page++;

            foreach ($aCities as $aCity) {

                if (is_null(CdekCity::find($aCity->code))) {
                    $CdekCity = new CdekCity();
                    $CdekCity->id = $aCity->code;
                    $CdekCity->name = $aCity->city;
                    $CdekCity->cdek_region_id = $aCity->region_code;
                    $CdekCity->save();
                }
            }

        } while (count($aCities) > 0);
    }
}