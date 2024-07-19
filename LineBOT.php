<?php

// -------- Line BOT API----------- //
include '_connect_line.php';
$requestMethod = $_SERVER["REQUEST_METHOD"];
// -------- Line BOT API----------- //


// ------------ Gmini API ------------------ //
// require_once __DIR__ . '/vendor/autoload.php';
require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;
$ClientGemini = new Client('----Gemini API keys----');
// ------------ Gmini API ------------------ //


// ------- Send LINE Notify------- //
// https://memo8.com/line-notify-php/
function replyMessage_LINENotify($CheckToken, $messageText)
{
    // global $Token_LineNotify;
    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer '.$CheckToken
    ];
    //$fields = 'message= '.$messageText.'$userId';
    $fields = 'message= '.$messageText;


    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://notify-api.line.me/api/notify');
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec( $ch );
    curl_close( $ch );
    var_dump($result);
    $result = json_decode($result,TRUE);
    return $result;
}
// ------- Send LINE Notify------- //


// ------- รับข้อมูลจาก LINE ------- //
function replyMessage($replyToken, $messageText)
{
    global $channel_access_token;
    $response = [
        'replyToken' => $replyToken,
        'messages' => [
            ['type' => 'text', 'text' => $messageText]
        ]
    ];

    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $channel_access_token
    ]);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}
// ------- รับข้อมูลจาก LINE ------- //



// -------- Line BOT -------- //
$ask_RoomPrice_type = ["ราคาห้อง","RoomPrice", "Room Price", "room Price", "room price"];
$ask_Gemini_type = ["Gemini","gemini"];
$ask_Accoun_type = ["Accoun", "Accounting", "Accounting department", "บัญชี"];

$request = file_get_contents('php://input');
$data = json_decode($request, true);
$text = $data['events'][0]['message']['text'];
$replyToken = $data['events'][0]['replyToken'];

// ------ check_and_remove_words
function check_and_remove_words($ask_Gemini_type, $string_to_check) {
    $found_words = array_filter($ask_Gemini_type, function($word) use ($string_to_check) {
        return stripos($string_to_check, $word) !== false;
    });

    if (!empty($found_words)) {
        // Remove the found words from the string
        $string_to_check = str_ireplace($found_words, '', $string_to_check);
        return [$string_to_check, true];
    }
    return [$string_to_check, false];
}
// ------ check_and_remove_words

if ($text)
{
    if (in_array($text, $ask_RoomPrice_type))
    {   
        $text_reply = '';
        // foreach ($Query_PriceRoom as $type)
        // {   
        //     $text_reply .= '>: ' . $type['common_name'] . "\n";
        //     $text_reply .= 'รหัส: ' . $type['item_code'] . "\n";
        //     $text_reply .= 'ราคา: ' . $type['unit_price'] . "\n\n";
        // }
        replyMessage($replyToken, $text_reply);
    
    }
    
    else
    {   
        list($updated_string_Gemini, $words_found_Gemini) = check_and_remove_words($ask_Gemini_type, $text);
        list($updated_string_Accoun, $words_found_Accoun) = check_and_remove_words($ask_Accoun_type, $text);
        
        if ($words_found_Gemini) 
        {
            // // ------------ Gmini API ------------------ //
            $response = $ClientGemini->geminiPro()->generateContent(
                new TextPart($updated_string_Gemini),
            );

            $text_reply = $response->text();
            replyMessage($replyToken,$text_reply);
        }

        else if ($words_found_Accoun) 
        {
            // ------------ LINE Notify to Accoun  ------------------ //
            $content = file_get_contents('php://input');
            $arrayJson = json_decode($content, true);
            $id = $arrayJson['events'][0]['source']['userId'];

            $text_reply = "กำลังติดต่อ Accounting department";
            replyMessage($replyToken,$text_reply);

            $TextNofifly = "\n\n". "userId: " .$id. "\n\n". "แจ้ง: ". "\n".$updated_string_Accoun;
            replyMessage_LINENotify($Token_Individual_LineNotify, $TextNofifly);
        }

        else
        {  
            $content = file_get_contents('php://input');
            $arrayJson = json_decode($content, true);
            $id = $arrayJson['events'][0]['source']['userId'];

            $text_reply = "กรุณารอการติดต่อกลับจาก Admin";
            replyMessage($replyToken,$text_reply);

            $TextNofifly = "\n\n". "userId: " .$id. "\n\n". "แจ้ง: ". "\n".$text;
            replyMessage_LINENotify($Token_LineNotify, $TextNofifly);
    
        }
 
        
    }

}
else
{
    $text_reply = "No Type Data";
    replyMessage($replyToken, $text_reply);
}
// -------- Line BOT -------- //
http_response_code(200);
echo 'OK';

?>