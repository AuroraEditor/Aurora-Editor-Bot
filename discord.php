<?php

register_shutdown_function("discordCheckForFatal");
set_error_handler("discordErrorHandler");

/**
 * Check for fatal errors and send them to discord.
 *
 * @return void
 */
function discordCheckForFatal()
{
    $error = error_get_last();

    if (isset($error['type']) && $error['type'] == E_ERROR) {
        discord(
            "**PHP Fatal error**: <@&918204902373744710> " .
                "```\r\n{$error['message']}\r\n```\r\n" .
                "in `{$error['file']}` on line `{$error['line']}`"
        );
    }
}

/**
 * Log PHP Errors to discord
 * 
 * @param int    $errorNumber PHP Error number
 * @param string $errorString PHP Error string
 * @param string $errorFile   PHP file
 * @param int    $errorLine   line number
 * @param null   $context     context (if any)
 * 
 * @return void
 */
function discordErrorHandler(
    $errorNumber,
    $errorString,
    $errorFile,
    $errorLine,
    $context = null
) {
    if (preg_match("/DOMDocument/", $errorString)) {
        return;
    }

    /// @ sign temporary disabled error reporting
    /// < PHP 8.0.0
    if (error_reporting() == 0) {
        return;
    }

    $errorType = "Unknown";
    switch ($errorNumber) {
        case E_ERROR:
            $errorType = "Error";
            break;

        case E_WARNING:
            $errorType = "Warning";
            break;

        case E_PARSE:
            $errorType = "Parse error";
            break;

        case E_NOTICE:
            $errorType = "Notice";
            break;

        default:
            $errorType = "Unknown";
            break;
    }

    discord("**PHP {$errorType}**: <@&918438083861573692> ```\r\n{$errorString}\r\n```\r\nin `{$errorFile}` on line `{$errorLine}`");
}

/**
 * Send a message to discord.
 * 
 * @param string $message   Message to send to discord
 * @param string $toChannel Channel to send to
 * 
 * @return void
 */
function discord($message)
{
    // Default (Reporting channel)
    $webhookurl = "https://discord.com/api/webhooks/1155095495853752421/-DiXjDDOQEp1xqL1sSElkqnwQN6y4a3CBLwqiQbVdzeLoSCu7sjTtF-5cmGH68H-mYNS";

    if (PHP_OS === 'Darwin') {
        // Do not tag.
        $message = preg_replace("/@&/", "\@\&", $message);
        $message = "_Test Message:_ " . $message;
    }

    $json_data = json_encode(
        [
            // Username
            "username" => "Aurora Editor API",

            // Avatar URL.
            "avatar_url" => "https://avatars.githubusercontent.com/u/106490518?s=200&v=4",

            // Message
            "content" => $message,
        ],
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    );


    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    echo $response;
    curl_close($ch);
}
