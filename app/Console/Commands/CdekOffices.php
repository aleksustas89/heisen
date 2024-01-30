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

        $CdekCities = CdekCity::orderBy("updated_at", "ASC")->limit(50)->get();

        foreach ($CdekCities as $CdekCity) {

            $aIdsCdekOffices = [];
            foreach ($getOffices = $CdekController->getOffices($CdekCity->id) as $Office) {
                $aIdsCdekOffices[] = $Office->code;
            }

            foreach (CdekOffice::where("cdek_city_id", $CdekCity->id)->get() as $aOffice) {
                //если в полученном массиве нет офиса - удаляем
                if (!in_array($aOffice->code, $aIdsCdekOffices)) {
                    $aOffice->active = 0;
                    $aOffice->save();
                } 

                $key = array_search($aOffice->code, $aIdsCdekOffices);
                if (isset($aIdsCdekOffices[$key])) {
                    unset($aIdsCdekOffices[$key]);
                }
            }

            //добавляем те, которых нет
            if (count($aIdsCdekOffices) > 0) {
                foreach ($getOffices as $Office) {
                    if (in_array($Office->code, $aIdsCdekOffices)) {
                        $CdekOffice = new CdekOffice();
                        $CdekOffice->code = $Office->code;
                        $CdekOffice->name = $Office->name;
                        $CdekOffice->address_comment = $Office->address_comment ?? $Office->note ?? '';
                        $CdekOffice->uuid = $Office->uuid;
                        $CdekOffice->work_time = $Office->work_time;
                        $CdekOffice->cdek_region_id = $Office->location->region_code;
                        $CdekOffice->cdek_city_id = $Office->location->city_code;
                        $CdekOffice->longitude = $Office->location->longitude;
                        $CdekOffice->latitude = $Office->location->latitude;
                        $CdekOffice->weight_min = $Office->weight_min ?? 0;
                        $CdekOffice->weight_max = $Office->weight_max ?? 0;
        
                        $CdekOffice->save();
                    }
                }
            }

            $CdekCity->updated_at = date("Y-m-d H:i:s");
            $CdekCity->save();

        }

    }
}