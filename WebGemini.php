<?php

require "vendor/autoload.php";
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$client = new Client('----Gemini API keys----');
$response = $client->geminiPro()->generateContent(
    new TextPart('นายกประเทศไทยคือใคร'),
);

print $response->text();
// PHP: A server-side scripting language used to create dynamic web applications.
// Easy to learn, widely used, and open-source.

?>