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

    $expression = '/(@' . $settings['username'] . ')?( )?(please)?( )?assign( )?me/';
    $comment = $payload['comment']['body'] ?? '';

    if (preg_match($expression, strtolower($comment))) {
        // unless i've found a better way...
        foreach (explode("\n", strtolower($comment)) as $line) {

            // We are in a blockquote, skip this line.
            if (preg_match("/> /", $line)) {
                continue;
            }

            // We do match the expression.
            if (!preg_match($expression, strtolower($line))) {
                continue;
            }

            // Sent to the discord bot log channel.
            discord("Issue [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) is assigned to [{$user}](<https://github.com/{$user}>).");

            // Assign the issue.
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
