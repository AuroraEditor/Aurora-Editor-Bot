# for filename in payload/*.json; do
#     php index.php ${filename#"payload/"}
#     sleep 5
# done

# Run test (issue.json)
php index.php issue.json
sleep 2

# Run test (please_assign_me.json)
php index.php please_assign_me.json
sleep 2

# Run test (please_unassign_me.json)
php index.php please_unassign_me.json
sleep 2

# Run test (please_assign_me_quote.json) [Should do nothing]
php index.php please_assign_me_quote.json
sleep 2

# Run test (pr.json)
php index.php pr.json
sleep 2

# Run test (reject_pr.json)
php index.php reject_pr.json
sleep 2

# Run test (accept_pr.json)
php index.php accept_pr.json
sleep 2