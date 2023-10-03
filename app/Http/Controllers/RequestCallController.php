<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Telegram;
use App\Models\TelegramUserChat;

class RequestCallController extends Controller
{
    public function index(Request $request)
    {

        if (!is_null($Telegram = Telegram::find(1))) {

            $text = "Новая заявка обратного звонка!" . "%0A";
            $text .= "Телефон: " . $request->phone . "%0A";
            $text .= "Имя: " . $request->name . "%0A";

            $TelegramUsers = TelegramUserChat::select("chat_id")
                ->join("telegram_users", "telegram_users.id", "=", "telegram_user_chats.telegram_user_id")
                ->join("telegram_chats", "telegram_chats.id", "=", "telegram_users.telegram_chat_id")
                ->where("telegram_chats.name", 'REQUEST-CALL')
                ->get();
            foreach ($TelegramUsers as $TelegramUser) {
        
                $ch = curl_init();
 
                curl_setopt($ch, CURLOPT_USERAGENT, filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot'. $Telegram->token .'/sendMessage?chat_id='. $TelegramUser->chat_id .'&text='. $text);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);      
             
                curl_exec($ch);
             
                curl_close($ch);

            }
        }

        return response()->json('<div class="uk-alert-success uk-alert" uk-alert=""><p>Спасибо! Наши менеджеры свяжутся с Вами в ближайшее время!</p></div>');
    }
}
