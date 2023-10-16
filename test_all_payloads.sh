for filename in payload/*.json; do
    php index.php ${filename#"payload/"}
    sleep 5
done
