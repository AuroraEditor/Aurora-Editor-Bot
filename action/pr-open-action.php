<?php

if (
    $payload['action'] == 'opened' &&
    isset($payload['pull_request'])
) {
    $PRNumber = $payload['pull_request']['number'];
    $user = $payload['pull_request']['user']['login'];
    $repo = $payload['repository']['full_name'] ?? 'unknown';
    $milestone = 1;

    // Assign the PR to the user who created it, and set the milestone to 1.
    api(
        $payload['pull_request']['issue_url'],
        json_encode(
            array(
                "milestone" => $milestone
            )
        )
    );
    api(
        $payload['pull_request']['issue_url'],
        json_encode(
            array(
                "assignees" => array($user)
            )
        )
    );

    // Send a message to the user who created the PR.
    api(
        $payload['pull_request']['comments_url'],
        json_encode(
            array(
                "body" => "Thanks for submitting a pull request!\r\nWe will review it as soon as possible."
            )
        ),
        'POST'
    );

    discord("PR [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['pull_request']['html_url']}>) is assigned to [$user](<https://github.com/$user>) and set to milestone [#$milestone](<https://github.com/{$repo}/milestone/$milestone>).");

    // Enable auto merge (using GraphQL)
    api(
        "https://api.github.com/graphql",
        json_encode(
            array(
                "query" => "mutation MyMutation {
                        enablePullRequestAutoMerge(input: { pullRequestId: \"{$payload['pull_request']['node_id']}\", mergeMethod: SQUASH}) {
                            clientMutationId
                        }
                    }"
            )
        ),
        "POST"
    );

    if ($user == "dependabot[bot]") {
        discord("PR [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['pull_request']['html_url']}>) by {$user} is automatically accepted.");

        api(
            $payload['pull_request']['url'] . '/reviews',
            json_encode(
                array(
                    "body" => "LGTM",
                    "event" => "APPROVE"
                )
            ),
            'POST'
        );
    }

    $AEdidRun[] = [true, "open_pr_action", "PR #$PRNumber assigned to $user."];
}
