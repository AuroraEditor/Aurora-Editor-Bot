<?php
if (
    isset($_POST['event']) &&
    isset($_POST['PRNumber']) &&
    isset($_POST['message'])
) {
    global $AEweb;
    /*
Make this call

curl -L \
  -X POST \
  -H "Accept: application/vnd.github+json" \
  -H "Authorization: Bearer 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" \
  -H "X-GitHub-Api-Version: 2022-11-28" \
  https://api.github.com/repos/AuroraEditor/AuroraEditor/pulls/478/reviews \
  -d '{"body":"LGTM","event":"APPROVE"}'
*/

    discord("PR Reviewer called.\r\n" . print_r(array($_POST, $_SERVER['REMOTE_ADDR']), true));
    $curl = curl_init();

    curl_setopt(
        $curl,
        CURLOPT_URL,
        sprintf(
            "https://api.github.com/repos/AuroraEditor/AuroraEditor/pulls/%s/reviews",
            $_POST['PRNumber']
        )
    );

    curl_setopt(
        $curl,
        CURLOPT_RETURNTRANSFER,
        true
    );

    curl_setopt(
        $curl,
        CURLOPT_POST,
        true
    );

    curl_setopt(
        $curl,
        CURLOPT_POSTFIELDS,
        json_encode(
            array(
                "body" => $_POST['message'],
                "event" => $_POST['event']
            )
        )
    );

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
    echo $result;
    curl_close($curl);
    $AEweb = true;
}
