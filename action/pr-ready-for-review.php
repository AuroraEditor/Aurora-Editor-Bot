<?php

if (
    $payload['action'] == 'ready_for_review' &&
    isset($payload['pull_request'])
) {
    $PRNumber = $payload['pull_request']['number'];
    $user = $payload['pull_request']['user']['login'];
    $repo = $payload['repository']['full_name'] ?? 'unknown';
    $milestone = 1;

    discord("PR [{$repo}](<https://github.com/{$repo}>) [#$PRNumber](<{$payload['pull_request']['html_url']}>) by [$user](<https://github.com/$user>) is ready for review!");

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

    $AEdidRun[] = [true, "pr_ready_for_review", "PR #$PRNumber from $user, Automerge enabled"];
}
