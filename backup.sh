#!/bin/bash

# This backup script is meant to be run from outside the container and within the root of the project folder.

source ./env;

DATE=`date +%Y%m%d`;

WEB_CONTAINER=$(docker-compose ps | grep 80 | awk '{print $1}');

# grab sql assets and store under /tmp
echo "creating /tmp/$DATE-$DBNAME.sql";
docker exec -e MYSQL_PWD=$DBPASS -it $WEB_CONTAINER bash -c 'mysqldump -u $DBUSER -h $DBHOST $DBNAME > /tmp/$DBNAME.sql';
docker exec -it $WEB_CONTAINER bash -c 'tar zcf /tmp/$DBNAME.sql.tar.gz /tmp/$DBNAME.sql';
docker cp $WEB_CONTAINER:/tmp/$DBNAME.sql.tar.gz /tmp/$DATE-$DBNAME.sql.tar.gz;

# grab upload assets as a gzip tarball
echo "creating /tmp/$DATE-$DBNAME-uploads.tar.gz";
docker exec -it $WEB_CONTAINER bash -c 'tar zcf /tmp/uploads.tar.gz /var/www/public_html/wp-content/uploads';
docker cp $WEB_CONTAINER:/tmp/uploads.tar.gz /tmp/$DATE-$DBNAME-uploads.tar.gz;
