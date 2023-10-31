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
          
# Run test (pr.json)
php index.php pr.json
sleep 2

# Run test (pr_reject.json)
php index.php pr_reject.json
sleep 2

# Run test (pr_accept.json)
php index.php pr_accept.json
sleep 2


# Run test (pr_closed.json)
php index.php pr_closed.json
sleep 2

# Run test (pr_merged.json)
php index.php pr_merged.json
sleep 2