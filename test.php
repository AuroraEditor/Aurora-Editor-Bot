<?php

$settings['username'] = 'aurora-care-bear';

$phrases = [
    "@aurora-care-bear please assign me",
    "@aurora-care-bear assign me",
    "assign me",
    "please assign me",
    "> @aurora-care-bear please assign me",
    "> @aurora-care-bear assign me",
    "> assign me",
    "> please assign me",
];

foreach ($phrases as $phrase) {
    echo $phrase . "\n";

    $payload['comment']['body'] = $phrase;

    print_r([
        $phrase,
        preg_match(
            '/^(>)( )?(@' . $settings['username'] . ')?( )?(please)?( )?assign me$/',
            strtolower($payload['comment']['body'] ?? '')
        )
    ]);
}

// ^
