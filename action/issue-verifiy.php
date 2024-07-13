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
    $comment = $payload['comment']['body'] ?? '';

    if (
        preg_match("/\@" . $settings['username'] . "/", strtolower($payload['comment']['body'] ?? '')) &&
        preg_match("/verify/", strtolower($payload['comment']['body'] ?? ''), $matches)
    ) {
        if ($isAdmin) {
            foreach (explode("\n", strtolower($comment)) as $line) {

                // We are in a blockquote, skip this line.
                if (preg_match("/> /", $line)) {
                    continue;
                }

                if (preg_match("/verify/", $line)) {
                    api(
                        $payload['issue']['url'] . '/labels',
                        json_encode(
                            array(
                                "labels" => array("verified", "bug")
                            )
                        ),
                        'PUT'
                    );
                }
            }
        }
    }
}
