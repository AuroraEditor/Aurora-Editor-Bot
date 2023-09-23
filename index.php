<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$AEweb = false;
$AEdidRun = [false, "init", ""];

include 'discord.php';
include 'aeweb.php';
function api($url, $json, $extra = "PATCH")
{
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
            "Authorization: Bearer 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",
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
    $_POST['payload'] = file_get_contents("payload_pr.json");
}

if (isset($_POST['payload'])) {
    file_put_contents(
        $fileName = "gh-action/" . time() . ".txt",
        $_POST['payload']
    );
    $logURL = "https://wesleydegroot.nl/projects/AEBot/" . $fileName;
    // discord("Log: {$logURL}");

    if (!isset($_POST['payload'])) {
        discord("No payload found.\r\nLog: {$logURL}");
        exit();
    }

    $payload = json_decode($_POST['payload'], true);

    if (!isset($payload['action'])) {
        discord("No action found.\r\nLog: {$logURL}");
        exit();
    }

    foreach ($file = glob("action-*.php") as $filename) {
        include $filename;
    }

    // delete old logs
    foreach ($file = glob("gh-action/*.txt") as $filename) {
        if (time() - filemtime($filename) > ((60 * 60) * 24)) {
            unlink($filename);
        }
    }
}



?><title>AEBot</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
<!-- <link rel="stylesheet" href="https://unpkg.com/mvp.css"> -->

<h1>PR Reviewer</h1>

<form method="post">
    <label for="event">Event</label>
    <select name="event">
        <option value="APPROVE">APPROVE</option>
        <option value="REQUEST_CHANGES">REQUEST_CHANGES</option>
        <option value="COMMENT">COMMENT</option>
    </select><br />

    <label for="PRNumber">PR Number</label>
    <input type="text" name="PRNumber" placeholder="PR Number">
    <br />

    <label for="message">Message</label>
    <input type="text" name="message" placeholder="message" value="LGTM">
    <br />

    <label for="submit">Submit</label>
    <input type="submit" value="submit">
</form>