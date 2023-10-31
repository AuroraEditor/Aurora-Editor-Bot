<?php

if (
    $payload['action'] == 'closed' &&
    isset($payload['pull_request'])
) {
    $PRNumber = $payload['pull_request']['number'];
    $user = $payload['pull_request']['user']['login'];
    $repo = $payload['repository']['full_name'];
    $merged = $payload['pull_request']['merged'] ?? false;

    if ($merged) {
        discord("PR $repo #$PRNumber is accepted! Closing message to $user.");

        api(
            $payload['pull_request']['comments_url'],
            json_encode(
                array(
                    "body" => "Hey @{$user},\r\n\r\nThanks for your contribution.\r\nYour PR has been merged!\r\nYour name will appear on https://auroraeditor.com/#contributors in a few moments, if you have contributed to the editor.\r\n\r\nThanks again for your contribution!\r\n\r\nKind Regards,\r\nAurora Editor Team."
                )
            ),
            'POST'
        );
    } else {
        api(
            $payload['pull_request']['comments_url'],
            json_encode(
                array(
                    "body" => "Hey @{$user},\r\n\r\nThanks for your contribution.\r\nYour PR has not been merged!\r\nPlease read the above comments why your pull request was not accepted.\r\n\r\nThanks again for your contribution!\r\n\r\nKind Regards,\r\nAurora Editor Team."
                )
            ),
            'POST'
        );
    }


    $AEdidRun[] = [true, "pr_closed_message", "PR $repo #$PRNumber Closing message to $user."];
}
