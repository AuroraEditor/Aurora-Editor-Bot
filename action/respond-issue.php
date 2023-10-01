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
    // api(
    //     $payload['issue']['url'],
    //     json_encode(
    //         array(
    //             "assignees" => array($user)
    //         )
    //     )
    // );

    // Send a message to the user who created the issue.
    api(
        $payload['issue']['url'] . "/comments",
        json_encode(
            array(
                "body" => "Thanks for submitting a issue!\r\nWe will review it as soon as possible."
            )
        ),
        "POST"
    );

    // discord("Issue [#$PRNumber]({$payload['issue']['html_url']}) assigned to $user and set to milestone [#$milestone](https://github.com/AuroraEditor/AuroraEditor/milestone/$milestone).");
    discord("Issue [#$PRNumber]({$payload['issue']['html_url']}) in {$repo} set to milestone [#$milestone](https://github.com/AuroraEditor/AuroraEditor/milestone/$milestone).");

    $AEdidRun = [true, "pr_assign_user", "PR #$PRNumber assigned to $user."];
}
