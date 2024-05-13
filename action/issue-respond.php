<?php

if (
    $payload['action'] == 'opened' &&
    isset($payload['issue'])
) {
    $PRNumber = $payload['issue']['number'];
    $user = $payload['issue']['user']['login'];
    $repo = $payload['repository']['full_name'] ?? 'unknown';
    $milestone = 1;

    // Assign the PR to the user who created it, and set the milestone to 1.
    api(
        $payload['issue']['url'],
        json_encode(
            array(
                "milestone" => $milestone
            )
        )
    );

    // Send a message to the user who created the issue.
    api(
        $payload['issue']['url'] . "/comments",
        json_encode(
            array(
                "body" => sprintf(
                    "Thanks for submitting a issue!\r\nto assign this issue to yourself please comment `@%s please assign me`",
                    $settings['username']
                )
            )
        ),
        "POST"
    );

    discord("Issue [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) set to milestone [#$milestone](<https://github.com/{$repo}/milestone/$milestone>).");

    $AEdidRun[] = [true, "pr_assign_milestone", "PR #{$PRNumber} assigned to milestone #{$milestone}."];
}
