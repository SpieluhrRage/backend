#!/bin/bash
read -r username
read -r password

output=$(php /var/www/html/src/admin/auth.php "$username" "$password")
if [[ "$output" == "Authentication successful" ]]; then
    exit 0
else
    exit 1
fi
