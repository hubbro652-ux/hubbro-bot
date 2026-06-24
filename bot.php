<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$token = "YOUR_BOT_TOKEN";

include("config.php");

$update = json_decode(file_get_contents("php://input"), true);

if (!$update) {
    exit;
}

if (isset($update["message"])) {

    $chat_id = $update["message"]["chat"]["id"];
    $text = $update["message"]["text"] ?? "";

    if (strpos($text, "/start") === 0) {

        $parts = explode(" ", $text);

        if (!isset($parts[1])) {

            sendMessage(
                $chat_id,
                "Welcome to HubPro Movies!"
            );

            exit;
        }

        $video_id = intval($parts[1]);

        $stmt = $conn->prepare(
            "SELECT telegram_message_id FROM videos WHERE id=? LIMIT 1"
        );

        $stmt->bind_param("i", $video_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 0) {

            sendMessage(
                $chat_id,
                "Video not found."
            );

            exit;
        }

        $video = $result->fetch_assoc();

        copyMessage(
            $chat_id,
            "-1004361799895",
            $video["telegram_message_id"]
        );
    }
}

function sendMessage($chat_id, $text)
{
    global $token;

    file_get_contents(
        "https://api.telegram.org/bot{$token}/sendMessage?" .
        http_build_query([
            "chat_id" => $chat_id,
            "text" => $text
        ])
    );
}

function copyMessage($chat_id, $from_chat_id, $message_id)
{
    global $token;

    $data = [
        "chat_id" => $chat_id,
        "from_chat_id" => $from_chat_id,
        "message_id" => $message_id
    ];

    $options = [
        "http" => [
            "header" => "Content-type: application/x-www-form-urlencoded",
            "method" => "POST",
            "content" => http_build_query($data)
        ]
    ];

    file_get_contents(
        "https://api.telegram.org/bot{$token}/copyMessage",
        false,
        stream_context_create($options)
    );
}

?>