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
    $isAdmin = in_array($user, $settings['admins']);

    if (
        preg_match("/\@" . $settings['username'] . "/", strtolower($payload['comment']['body'] ?? '')) &&
        preg_match("/please (accept|approve|reject)/", strtolower($payload['comment']['body'] ?? ''), $matches)
    ) {
        print("Should do something...");
        $acceptOrReject = in_array(
            $matches[1] ?? 'accept',
            ['accept', 'approve']
        ) ? 'accept' : 'reject';

        discord(
            sprintf(
                "PR [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['issue']['html_url']}>) %s [{$user}](<https://github.com/{$user}>).",
                $isAdmin ? "is {$acceptOrReject}ed by" : "is not {$acceptOrReject}ed for"
            )
        );

        if ($isAdmin) {
            print("Accepting...");
            discord(api(
                $payload['issue']['pull_request']['url'] . '/reviews',
                json_encode(
                    array(
                        "body" => $acceptOrReject == 'accept' ? "Approved on behalf of @{$user}" : "Rejected on behalf of @{$user}",
                        "event" => $acceptOrReject == 'accept' ? "APPROVE" : "REQUEST_CHANGES"
                    )
                ),
                'POST'
            ));
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

        // Delete comment after use.
        // api($payload['comment']['url'], null, 'DELETE');
    }

    $AEdidRun[] = [true, "review_pr", "PR $repo #$PRNumber assigned to $user."];
}
