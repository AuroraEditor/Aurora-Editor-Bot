<?php

if (
    $payload['action'] == 'created' &&
    isset($payload['issue']) &&
    isset($payload['comment']) &&
    (PHP_SAPI !== 'cli' ? $payload['comment']['user']['login'] != $settings['username'] : true)
) {
    $PRNumber = $payload['issue']['number'];
    $user = $payload['comment']['user']['login'];
    $repo = $payload['repository']['full_name'];

    if (
        preg_match("/please(.*)assign(.*)me/", strtolower($payload['comment']['body'] ?? ''))
    ) {
        discord(
            "Issue [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) is assigned to [{$user}](<https://github.com/{$user}>)."
        );

        api(
            $payload['issue']['url'],
            json_encode(
                array(
                    "assignees" => array($user)
                )
            )
        );
    }

    $AEdidRun[] = [true, "issue_assign", "PR $repo #$PRNumber assigned to $user."];
}
