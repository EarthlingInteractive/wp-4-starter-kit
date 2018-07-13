#!/bin/bash

echo "running entrypoint script";
cd /var/www/public_html;

until nc -z $DBHOST $DBPORT; do
    echo "$(date) - waiting for db..."
    sleep 1
done

if ! $(wp core is-installed --allow-root); then
  wp db create --allow-root;
  wp core install --url=http://$SITE_URL --title=wp_4_docker --admin_user=root --admin_password=qwe123 --admin_email=root@$SITE_URL --allow-root;
fi

echo "Setting directory permissions";
chown -R www-data.www-data /var/www;

exec "$@"
