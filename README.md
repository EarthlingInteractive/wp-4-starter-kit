## Run

`WP v4.9.5`

This will initially build and then run your docker environment.  It will also use wp cli to create the initial database and administrator `root` user.

```
git clone git@github.com:EarthlingInteractive/wp_4_docker.git && cd wp_4_docker;

docker-compose up
```

## Access

Some users have reported issues using `localhost` when certain projects introduce redirects in the local data.  Therefore, it is suggested to use `xip.io` instead of `localhost`, replacing `wordpress` with the project name.

- [http://wordpress.127.0.0.1.xip.io/](http://wordpress.127.0.0.1.xip.io/)
- [http://wordpress.127.0.0.1.xip.io/wp-admin/](http://wordpress.127.0.0.1.xip.io/wp-admin/)

### admin

- Username: `root`
- Password: `qwe123`

---

## Environment variables

This project uses an `env_file` to centralize the definition of secrets and other variables used by the application.  To modify or add additional environment variables, edit the file `env`.

##### `env`

```
DBHOST=wordpress-mysql
DBNAME=wordpress
DBUSER=root
DBPASS=qwe123
DBPORT=3306
WP_TABLE_PREFIX=wp_
AUTH_KEY=1234
SECURE_AUTH_KEY=1234
LOGGED_IN_KEY=1234
NONCE_KEY=1234
AUTH_SALT=1234
SECURE_AUTH_SALT=1234
LOGGED_IN_SALT=1234
NONCE_SALT=1234
SITE_URL=wordpress.127.0.0.1.xip.io
```

## Volume mounts

This project uses volume mounts to mirror staging and production environments for such things as mysql data, upload assests and cache directories.  If additional mounts are required due to plugin requirements, update `docker-compose.yml` with appropriate `volu
mes` and add to the `wordpress-www` container.

In the event where you need to clear this data, follow these steps:

```
docker-compose down

docker volume rm wp4docker_wordpress-cache-data wp4docker_wordpress-mysql-data wp4docker_wordpress-scratch-data wp4docker_wordpress-upload-data

docker-compose up
```

### Debugging

Both access and error logging from apache is sent to stdout.  Logs will appear within the standard output of `docker-compose up`.  If additional debugging is required due to way a certain plugin works, you'll need to `docker exec -it wordpress-www /bin/bash` into the container and tail the appropriate logs.

---

## Backup and Restore

This starter kit comes with a general purpose `backup.sh` script that will collect gzip tarballs of the database and upload assets.  It must be run at the root of the project folder and will output compressed assets to `/tmp`.

```
./backup.sh

creating /tmp/20180427-wordpress.sql
creating /tmp/20180427-wordpress-uploads.tar.gz
```

## Installed plugins

- Advanced Custom Fields PRO
- Akismet Anti-spam
- Better Search Replace
- Gravity Forms
- iThemes Security
- Yoast SEO

## Creating Project off this starter kit

Fork the project and update the `docker-compose.yml` file to reflect the name of the new project.  Within the compose file, remove the `assets` volume mount and delete the directory from the project folder.  Before pushing this as a new project to github, make sure to remove the `.git` folder and re-initialize with `git init`.

