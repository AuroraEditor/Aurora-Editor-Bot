<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$AEweb = false;
$AEdidRun = array([true, "init", ""]);

include 'config.php';

if (!isset($settings)) {
    exit("I cannot continue without settings, please see config.sample.php for an example.");
}

include 'discord.php';
function api($url, $json, $extra = "PATCH")
{
    global $settings;
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $extra);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);

    // if (is_array($json)) {
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    // }

    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
            "User-Agent: Aurora Editor Bot",
            "Accept: application/vnd.github+json",
            "Authorization: Bearer {$settings['github_token']}",
            "X-GitHub-Api-Version: 2022-11-28"
        )
    );

    $result = curl_exec($curl);

    if (curl_errno($curl)) {
        discord('Error:' . curl_error($curl));
    }

    curl_close($curl);

    return $result;
}

if (PHP_SAPI === 'cli') {
    if (!isset($argv[1])) {
        echo "Please use php {$argv[0]} payload.json";
        exit(2);
    } else {
        $file = "payload/" . $argv[1];
        if (file_exists($file) && !preg_match("/\.\./", $file)) {
            echo "Overwrite payload with test data from payload/{$argv[1]}.\r\n";
            $_POST['payload'] = file_get_contents($file);
        } else {
            echo file_exists($file) ? "File contains invalid characters" : "File {$file} not found";
            exit(3);
        }
    }
}

if (isset($_POST['payload'])) {
    // Save payload log for traceability
    file_put_contents(
        $fileName = "gh-action/" . time() . ".json",
        $_POST['payload']
    );

    // The log URL.
    $logURL = $settings['serverURL'] . $fileName;
    // discord("Log: {$logURL}");

    // delete old logs
    foreach ($file = glob("gh-action/*.json") as $filename) {

        // Disable error reporting
        error_reporting(0);

        // Check if file exists
        if (file_exists($filename)) {
            // If older than 24hours, then delete
            if (time() - filemtime($filename) > ((60 * 60) * 24)) {
                // Delete
                unlink($filename);
            }
        }

        // Enable error reporting
        error_reporting(E_ALL);
    }

    if (!isset($_POST['payload'])) {
        discord("No payload found.\r\nLog: {$logURL}");
        exit();
    }

    // Create $payload
    $payload = json_decode($_POST['payload'], true);

    if (!isset($payload['action'])) {
        // discord("No action found.\r\nLog: {$logURL}");
        exit();
    }

    foreach ($file = glob("action/*.php") as $filename) {
        include $filename;
    }
}

if (!headers_sent()) {
    header("location: /");
}
