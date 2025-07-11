
<?php
// ------------ Gemini API ------------------ //
//require_once "GeminiHelper.php";

// $text_reply = callGemini($updated_string_Gemini);
// ------------ Gemini API ------------------ //
// 📌 GeminiHelper.php
function callGemini($question, $model = "gemini-2.5-flash") {
    $api_key = "KEY-API";
    $url = "https://generativelanguage.googleapis.com/v1/models/$model:generateContent?key=$api_key";

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
        return $response_data['candidates'][0]['content']['parts'][0]['text'];
    } elseif (isset($response_data['error'])) {
        return "❌ Error: " . $response_data['error']['message'];
    } else {
        return "❌ ไม่พบคำตอบจาก Gemini API";
    }
}
?>
