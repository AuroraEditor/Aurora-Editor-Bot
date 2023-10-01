<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$AEweb = false;
$AEdidRun = [false, "init", ""];

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
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);

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

    if (PHP_OS == "Darwin") {
        discord("URL: " . $url);
        discord("JSON: " . $json);
        discord("Result: " . substr($result, 0, 1000) . "...");
    }

    curl_close($curl);
}

if (PHP_OS == "Darwin") {
    echo "Overwrite payload with test data.\r\n";
    $_POST['payload'] = file_get_contents("payload/accept_pr.json");
}

if (isset($_POST['payload'])) {
    file_put_contents(
        $fileName = "gh-action/" . time() . ".txt",
        $_POST['payload']
    );
    $logURL = $settings['serverURL'] . $fileName;
    // discord("Log: {$logURL}");

    // delete old logs
    foreach ($file = glob("gh-action/*.txt") as $filename) {
        if (time() - filemtime($filename) > ((60 * 60) * 24)) {
            unlink($filename);
        }
    }

    if (!isset($_POST['payload'])) {
        discord("No payload found.\r\nLog: {$logURL}");
        exit();
    }

    $payload = json_decode($_POST['payload'], true);

    if (!isset($payload['action'])) {
        // discord("No action found.\r\nLog: {$logURL}");
        exit();
    }

    foreach ($file = glob("action/*.php") as $filename) {
        include $filename;
    }
}

header("location: /");
