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

    if (preg_match('/(@' . $settings['username'] . ')?( )?(please)?( )?assign( )?me/', strtolower($payload['comment']['body'] ?? ''))) {
        // unless i've found a better way...
        foreach (explode("\n", strtolower($payload['comment']['body'] ?? ''))
            as $line) {
            // We are in a blockquote, skip this line.
            if (preg_match("/> /", $line)) {
                continue;
            }

            // Assign the issue.
            discord("Issue [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) is assigned to [{$user}](<https://github.com/{$user}>).");

            api(
                $payload['issue']['url'],
                json_encode(
                    array(
                        "assignees" => array($user)
                    )
                )
            );
        }
    }

    $AEdidRun[] = [true, "issue_assign", "PR $repo #$PRNumber assigned to $user."];
}
