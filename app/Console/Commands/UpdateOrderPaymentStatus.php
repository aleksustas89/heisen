<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShopOrder;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail\SendOrderPaymentStatus;
use App\Http\Controllers\CartController;
use App\Models\Shop;

class UpdateOrderPaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-order-payment-status';

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
        $Shop = Shop::get();

        foreach (ShopOrder::where("created_at", ">", date("Y-m-d H:i:s", strtotime("-5 minutes", strtotime(date("Y-m-d H:i:s")))))->where("paid", 0)->get() as $ShopOrder) {
            if (!is_null($ShopOrder->UkassaOrder)) {
                $CartController = new CartController();
                $payment = $CartController->getPaymentInfo($ShopOrder->UkassaOrder->ukassa_result_uuid);
                if ($payment->_status == 'succeeded') {
                    $ShopOrder->paid = 1;
                    $ShopOrder->save();

                    Mail::to($Shop->email)->send(new SendOrderPaymentStatus($ShopOrder));
                }
            }
        }
    }
}