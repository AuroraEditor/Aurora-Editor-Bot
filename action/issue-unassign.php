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

    $comment = $payload['comment']['body'] ?? '';

    if (preg_match($expressions['unassign'], strtolower($comment))) {
        // unless i've found a better way...
        foreach (explode("\n", strtolower($comment)) as $line) {

            // We are in a blockquote, skip this line.
            if (preg_match("/> /", $line)) {
                continue;
            }

            // We do match the expression.
            if (!preg_match($expressions['unassign'], strtolower($line))) {
                continue;
            }

            foreach ($payload['issue']['assignees'] ?? [] as $assignee) {
                if ($assignee["login"] == $user) {
                    // Sent to the discord bot log channel.
                    discord("Issue [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) is unassigned from [{$user}](<https://github.com/{$user}>).");

                    // Un-Assign the issue.
                    api(
                        $payload['issue']['url'],
                        json_encode(
                            array(
                                "assignees" => array()
                            )
                        )
                    );

                    api(
                        $payload['issue']['comments_url'],
                        json_encode(
                            array(
                                "body" => sprintf(
                                    "@%s You are unassigned from this issue. To reassign comment `@%s please assign me`.",
                                    $user,
                                    $settings['username']
                                )
                            )
                        ),
                        'POST'
                    );

                    // Break the assignees and line loops.
                    break 2;
                }
            }

            api(
                $payload['issue']['comments_url'],
                json_encode(
                    array(
                        "body" => sprintf(
                            "@%s You are not assigned to this issue. To assign comment `@%s please assign me`.",
                            $user,
                            $settings['username']
                        )
                    )
                ),
                'POST'
            );
        }
    }

    $AEdidRun[] = [true, "issue_assign", "PR $repo #$PRNumber assigned to $user."];
}
