<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ShopOrder;
use App\Models\Comment;
use App\Models\Mail\SendCommentReminderAfterOrder;
use Illuminate\Support\Facades\Mail;

class SendCommentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-comment-reminder';

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

        foreach ($days = [14] as $day) {

            $Orders = ShopOrder::where("created_at", "<=", date("Y-m-d", strtotime("-". ($day - 1) ." days")) . ' 00:00:00')
                ->where("created_at", ">=", date("Y-m-d", strtotime("-". ($day + 1) ." days")) . ' 00:00:00')  
                ->where("deleted", 0)  
                ->get();

            foreach ($Orders as $ShopOrder) {

                Mail::to($ShopOrder->email)->send(new SendCommentReminderAfterOrder($ShopOrder));
            }
        }


    }
}