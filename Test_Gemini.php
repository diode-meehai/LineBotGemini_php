<?php
// require "vendor/autoload.php";
// use GeminiAPI\Client;
// use GeminiAPI\Resources\Parts\TextPart;

// $client = new Client('KEY-API');

// $text_reply = '';
// $StringText = "Gemini นายกประเทศไทยคือใคร/วันนี้วันอะไร";
// // Remove "apple " from the string
// $modifiedString = str_replace("apple ", "", $StringText);
// // Replace spaces with commas
// $modifiedString = str_replace(" ", ",", $modifiedString);

// $response = $client->geminiPro()->generateContent(
    
//     new TextPart($modifiedString),
// );

// $text_reply = $response->text();
// # print $response->text();
// print $text_reply
// // PHP: A server-side scripting language used to create dynamic web applications.
// // Easy to learn, widely used, and open-source.


//https://generativelanguage.googleapis.com/v1/models?key=AIzaSyD-isYr7zxQnAkODnOycq9ud4COsMr-lUs
$api_key = "KEY-API"; // ใส่ API Key ของคุณ
// $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=$api_key";
$url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=$api_key";


$question = "นายกประเทศไทยคือใคร/วันนี้วันอะไร";

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $question]
            ]
        ]
    ]
];

$headers = [
    "Content-Type: application/json"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$response_data = json_decode($response, true);

if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
    echo $response_data['candidates'][0]['content']['parts'][0]['text'];
} else {
    echo "❌ Error:\n";
    print_r($response_data);
}

?>