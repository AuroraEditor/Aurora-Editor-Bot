<?php

$admins = array('0xWDG', 'nanashili');

if (
    $payload['action'] == 'created' &&
    isset($payload['issue']) &&
    isset($payload['comment']) &&
    preg_match("/\@aurora-editor-bot/", strtolower($payload['comment']['body'] ?? ''))
) {
    $PRNumber = $payload['issue']['number'];
    $user = $payload['issue']['user']['login'];
    $repo = $payload['repository']['full_name'];
    $isAdmin = in_array($user, $admins);

    if (preg_match("/please (accept|reject)/", strtolower($payload['comment']['body'] ?? ''), $matches)) {
        $acceptOrReject = $matches[1] ?? 'accept';

        discord(
            sprintf(
                "PR [{$repo}](https://github.com/{$repo}) [#$PRNumber]({$payload['issue']['html_url']}) %s [{$user}](https://github.com/{$user}).",
                $isAdmin ? "is {$acceptOrReject}ed by" : "is not {$acceptOrReject}ed for"
            )
        );

        if ($isAdmin) {
            api(
                $payload['issue']['pull_request']['url'] . '/reviews',
                json_encode(
                    array(
                        "body" => $acceptOrReject == 'accept' ? "Approved on behalf of @{$user}" : "Rejected on behalf of @{$user}",
                        "event" => $acceptOrReject == 'accept' ? "APPROVE" : "REQUEST_CHANGES"
                    )
                ),
                'POST'
            );
        } else {
            api(
                $payload['issue']['comments_url'],
                json_encode(
                    array(
                        "body" => "@{$user} You are not allowed to {$acceptOrReject} this PR."
                    )
                ),
                'POST'
            );
        }
    }

    $AEdidRun = [true, "issue_accept_pr", "PR $repo #$PRNumber assigned to $user."];
}
