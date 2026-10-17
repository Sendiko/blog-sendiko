# Sendiko — Personal Blog

A Laravel blog with public article pages and an authenticated writing dashboard. Articles support Markdown, cover photos, and inline image uploads.

## Run with Docker Compose and MySQL

Docker Compose runs the Laravel app with PHP 8.4-FPM, an Nginx frontend, and MySQL 8.4. PHP 8.4 is needed by the Symfony versions currently locked in `composer.lock`. The MySQL database and uploaded images are stored in named volumes.

Install Docker Engine and the Docker Compose plugin on your Linux server. Create the Compose environment file:

```bash
cp .env.docker.example .env
chmod 600 .env
```

Edit `.env` and set a unique admin email, admin password (at least 12 characters), MySQL application password, and MySQL root password. Use independently generated strong secrets for `DB_PASSWORD` and `MYSQL_ROOT_PASSWORD`. Keep the file private; Compose loads these values into the application and database containers. Get an initial Laravel application key without persisting an empty key:

```bash
docker compose build
docker compose run --rm --user www-data app php artisan key:generate --show
```

Copy the printed `base64:...` value into `APP_KEY` in `.env`. Do not share or commit the key. Start MySQL and the app, apply migrations, and create the configured writer account:

```bash
docker compose up -d --build
docker compose run --rm --user www-data app php artisan migrate --force
docker compose run --rm --user www-data app php artisan db:seed --force
```

The homepage hero and site favicon use `storage/app/public/hero_image.jpg` and `storage/app/public/favicon.jpg`. These files are included in the Docker image; the app container copies them into the persistent storage volume when it starts, so existing volumes receive the assets too. To replace either image, update the corresponding file and deploy with `docker compose up -d --build`. For a non-Docker deployment, make sure `php artisan storage:link` has been run.

The Nginx container listens on `127.0.0.1:8080` by default. If you use a host Nginx reverse proxy, set `APP_PORT=3014` in `.env` to match the example below. Confirm the site locally on the server:

```bash
docker compose ps
curl --fail http://127.0.0.1:8080/
```

For a public site, keep this port bound to loopback and put a TLS-enabled reverse proxy (such as host Nginx, Caddy, or a managed proxy) in front of it. Proxy to `127.0.0.1:8080`, configure HTTPS, and set `APP_URL` to the public HTTPS URL in `.env`. Set `APP_PORT` if another local port is needed. If you change it, use the new port in the local `curl` command. Do not expose the MySQL service publicly; it is only available on the private Compose network.

If the page loads without its CSS over HTTPS, inspect the rendered page source for the `build/assets/*.css` URLs. They must start with `https://` (or be root-relative URLs), not `http://`. Set `APP_URL` to the site's exact public HTTPS origin, such as `https://blog.sendiko.dev`. Laravel must also trust the HTTPS scheme forwarded by the TLS-terminating host Nginx. This project trusts the forwarded protocol header, and its internal Nginx passes that header to PHP-FPM. Rebuild and recreate both containers to deploy these changes:

```bash
docker compose up -d --build --force-recreate app web
docker compose exec --user www-data app php artisan optimize:clear
docker compose exec --user www-data app php artisan optimize
```

Check the page source again after the containers restart. Keep the host Nginx `proxy_set_header X-Forwarded-Proto $scheme;` directive. The Compose environment passes `APP_URL` to Laravel; a wrong `http://` value or an untrusted forwarded protocol can make Laravel generate HTTP asset links that browsers block as mixed content on an HTTPS page.

### Host Nginx reverse proxy with Certbot

For the domains `www.sendiko.my.id` and `www.sendiko.dev`, set the matching port and canonical URL in the Compose `.env` file:

```dotenv
APP_PORT=3014
APP_URL=https://www.sendiko.dev
```

Apply the port change and verify the local upstream before reloading host Nginx:

```bash
docker compose up -d
docker compose ps
curl --fail http://127.0.0.1:3014/
```

Use this as `/etc/nginx/sites-available/sendiko`, replacing the existing proxy configuration. The TLS certificate must cover **both** hostnames because TLS negotiation happens before Nginx can redirect either hostname:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name www.sendiko.my.id www.sendiko.dev;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    server_name www.sendiko.my.id www.sendiko.dev;

    ssl_certificate /etc/letsencrypt/live/www.sendiko.dev/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/www.sendiko.dev/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    client_max_body_size 8m;

    location / {
        proxy_pass http://127.0.0.1:3014;
        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_set_header X-Forwarded-Host $host;
    }
}
```

If the current Certbot certificate does not include both names, inspect it with `sudo certbot certificates` and issue/expand a certificate for both `www.sendiko.dev` and `www.sendiko.my.id` before enabling the HTTPS server block. Keep the certificate paths consistent with the actual certificate name Certbot reports.

On Fedora, Nginx normally loads `/etc/nginx/conf.d/*.conf`; it does not automatically load `sites-available`. If you keep the file at `/etc/nginx/sites-available/sendiko`, make sure your main Nginx configuration includes an enabled file from `sites-enabled`, or put the server block in `/etc/nginx/conf.d/sendiko.conf` instead. Check which files Nginx loads with `sudo nginx -T`, then validate and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

The Compose port is intentionally bound to `127.0.0.1`, so host Nginx can reach it locally while the app port and MySQL are not exposed directly to the network.

#### Cover photo upload limits

Cover and inline images are limited by Laravel to 5 MB. The PHP-FPM image allows individual uploads up to 6 MB and whole multipart requests up to 8 MB. Keep `client_max_body_size 8m;` in both the Docker Nginx configuration and the host Nginx reverse-proxy `server` block. If you change these limits, keep the proxy, PHP, and Laravel limits consistent. After changing the PHP settings in `docker/php/uploads.ini`, rebuild the app image:

```bash
docker compose up -d --build app
```

Useful operations:

```bash
docker compose logs -f app web db
docker compose exec --user www-data app php artisan optimize
docker compose down
```

`docker compose down` preserves the database and uploads. Do not use `docker compose down -v` unless intentionally deleting both persistent volumes. To deploy code changes, pull the new source and run `docker compose up -d --build`; then run `docker compose run --rm --user www-data app php artisan migrate --force`. Back up the `mysql_data` and `app_storage` volumes before upgrades.

### Troubleshooting: HTTP 500 with an empty `APP_KEY`

Laravel needs a persistent application key to encrypt sessions and cookies. If `APP_KEY` was left empty, generate a key and set it in the host `.env` file:

```bash
docker compose run --rm --no-deps app php artisan key:generate --show
```

Copy the printed `base64:...` value into `APP_KEY` in `.env`, then recreate the application container so it receives the new value:

```bash
docker compose up -d --force-recreate app
```

Keep the same key for future deployments. Changing it later invalidates existing encrypted sessions and cookies. If the site still returns 500, check `docker compose logs --tail=100 app` and, with `APP_DEBUG=false`, inspect the Laravel log inside the running container:

```bash
docker compose exec --user www-data app tail -n 100 storage/logs/laravel.log
```

## Requirements

- Linux server with a domain name pointed to it
- PHP 8.3 or later and Composer 2
- PHP extensions required by Laravel, including `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, and `XML`
- Node.js 20.19+ (22.18+ recommended) and npm for the frontend asset build
- Nginx (or another PHP-FPM-compatible web server)
- SQLite with the PHP SQLite extension, or a MySQL/MariaDB database
- HTTPS certificate for the domain

## 1. Install system packages

On Ubuntu/Debian, install PHP-FPM, the extensions required by this app, Composer, Node.js/npm, and Nginx. Package versions depend on your distribution; make sure the installed PHP version is at least 8.3.

Example package names (adjust PHP version and Node.js source for your distribution):

```bash
sudo apt update
sudo apt install nginx composer nodejs npm \
  php8.3-cli php8.3-fpm php8.3-bcmath php8.3-curl php8.3-mbstring \
  php8.3-xml php8.3-sqlite3 php8.3-mysql php8.3-zip
```

Enable only the database driver you plan to use (`php8.3-sqlite3` for SQLite or `php8.3-mysql` for MySQL/MariaDB).

### Fedora Server

The rest of this guide applies to Fedora too. Use this section for Fedora package installation, PHP-FPM, file permissions, SELinux, firewall, and Nginx configuration instead of the Ubuntu/Debian-specific commands below.

Install the required packages:

```bash
sudo dnf install nginx composer git nodejs npm \
  php-cli php-fpm php-common php-bcmath php-mbstring php-xml \
  php-pdo php-sqlite3 php-mysqlnd php-gd php-opcache policycoreutils-python-utils
```

Enable the database driver you plan to use (`php-sqlite3` for SQLite or `php-mysqlnd` for MySQL/MariaDB). Check that Fedora's PHP version satisfies this project's PHP 8.3-or-later requirement:

```bash
php -v
composer --version
node --version
npm --version
```

If PHP is older than 8.3, or Node.js does not meet the required version, use a Fedora release or supported package repository that provides compatible versions before continuing.

Fedora's default PHP-FPM pool can run as `apache`, while Nginx runs as `nginx`. For a straightforward single-app deployment, edit `/etc/php-fpm.d/www.conf` and set the pool user and group to `nginx`:

```ini
user = nginx
group = nginx
listen.owner = nginx
listen.group = nginx
listen.mode = 0660
```

If the pool config sets `listen.acl_users` or `listen.acl_groups`, comment those settings out when using `listen.owner` and `listen.group`. Confirm the socket path with `grep -E '^[; ]*listen[.]' /etc/php-fpm.d/www.conf`; the default is `/run/php-fpm/www.sock`, which is used in the Fedora Nginx example below.

Start and enable PHP-FPM, Nginx, and the firewall. Open only the public HTTP and HTTPS services:

```bash
sudo systemctl enable --now php-fpm nginx firewalld
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

## 2. Deploy the application

Clone the repository into a directory outside the public web root. This guide uses `/var/www/sendiko`; replace it with your chosen deployment path and Git repository URL.

```bash
sudo mkdir -p /var/www/sendiko
# Ubuntu/Debian:
sudo chown "$USER":www-data /var/www/sendiko
# Fedora: use this instead of the www-data command above:
# sudo chown "$USER":nginx /var/www/sendiko
git clone <YOUR_REPOSITORY_URL> /var/www/sendiko
cd /var/www/sendiko
```

Install production PHP dependencies and build the frontend assets:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
```

Use `npm ci` when the committed `package-lock.json` is available. If there is no lockfile in your deployment source, use `npm install` instead.

## 3. Configure the environment

Create the production environment file and generate a unique application key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set production values. At minimum, set:

```dotenv
APP_NAME="Sendiko"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@your-domain.example
ADMIN_PASSWORD=<A-unique-password-of-at-least-12-characters>

LOG_LEVEL=warning
```

Replace all example values. Keep `.env` private and do not commit it or share it. Use a password manager or your server's secret manager to generate and enter the admin password; do not put credentials in shell history, deployment scripts, or source control.

### Database: SQLite

SQLite is the default configuration. Create its database file if it does not exist and make sure the PHP-FPM user can write to the database directory and file:

```bash
touch database/database.sqlite
```

Set these values in `.env`:

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/sendiko/database/database.sqlite
```

SQLite works for a small, single-server personal blog. Back up the database file regularly.

### Database: MySQL or MariaDB (alternative)

Create an empty database and a dedicated database user with access to that database. Put the connection details in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sendiko
DB_USERNAME=sendiko_app
DB_PASSWORD=<A-unique-database-password>
```

Use a strong database password and grant the application user only the permissions it needs on the blog database.

## 4. Set storage permissions and run migrations

Laravel must be able to write to `storage` and `bootstrap/cache`. The public disk also stores uploaded article images. On Ubuntu/Debian, set ownership to `www-data`:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
```

On Fedora, first make sure the PHP-FPM pool is configured to run as `nginx` as described above. From the project directory, make the code readable by the web server and allow Laravel's writable directories to be updated by both your deployment account and PHP-FPM:

```bash
sudo chown -R "$USER":nginx /var/www/sendiko
sudo chown -R "$USER":nginx /var/www/sendiko/storage /var/www/sendiko/bootstrap/cache
sudo chmod -R g+rX /var/www/sendiko
sudo chmod -R g+rwX /var/www/sendiko/storage /var/www/sendiko/bootstrap/cache
sudo find /var/www/sendiko/storage /var/www/sendiko/bootstrap/cache -type d -exec chmod g+s {} \;
```

Add your deployment account to the `nginx` group if needed, then start a new login session so the group membership takes effect:

```bash
sudo usermod -aG nginx "$USER"
```

If using SQLite, also make the database writable by the PHP-FPM group and ensure its parent directory is writable so SQLite can create journal files:

```bash
sudo chown "$USER":nginx /var/www/sendiko/database /var/www/sendiko/database/database.sqlite
sudo chmod 2770 /var/www/sendiko/database
sudo chmod 660 /var/www/sendiko/database/database.sqlite
```

Fedora enables SELinux by default. Keep it enforcing and add persistent labels so Nginx/PHP-FPM can read the application and write only to Laravel's required locations:

```bash
sudo semanage fcontext -a -t httpd_sys_content_t '/var/www/sendiko(/.*)?'
sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/sendiko/storage(/.*)?'
sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/sendiko/bootstrap/cache(/.*)?'
sudo semanage fcontext -a -t httpd_sys_rw_content_t '/var/www/sendiko/database(/.*)?'
sudo restorecon -Rv /var/www/sendiko
```

The database rule is needed for SQLite; it can be omitted when using MySQL/MariaDB. If you change the deployment path, update both the filesystem commands and the SELinux rules. Do not disable SELinux to work around permission errors.

For Ubuntu/Debian with SQLite, give `www-data` write access to both the database file and its parent directory as well. For example:

```bash
sudo chown www-data:www-data database database/database.sqlite
sudo chmod 775 database
sudo chmod 664 database/database.sqlite
```

Run the database migrations, create the configured admin user, and link public image storage:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

The admin seeder reads `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD` from `.env`. It fails if any are missing or invalid. It is safe to run again for the same admin email; it updates that account's name and password. The blog currently uses a single-user author workflow and does not implement separate role-based permissions.

## 5. Configure Nginx and PHP-FPM

Point the site document root at Laravel's `public` directory only. Never configure the web root as the project directory.

Example Nginx server block for Ubuntu/Debian (`/etc/nginx/sites-available/sendiko`):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.example www.your-domain.example;
    root /var/www/sendiko/public;

    index index.php;
    charset utf-8;
    client_max_body_size 8m;

    add_header X-Content-Type-Options nosniff always;
    add_header X-Frame-Options SAMEORIGIN always;
    add_header Referrer-Policy strict-origin-when-cross-origin always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

On Fedora, save the server block as `/etc/nginx/conf.d/sendiko.conf`. Use the Fedora PHP-FPM socket and verify the site configuration:

```bash
sudoedit /etc/nginx/conf.d/sendiko.conf
```

Use the server block above, changing the PHP-FPM location block to:

```nginx
location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php-fpm/www.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    fastcgi_param DOCUMENT_ROOT $realpath_root;
}
```

Then check and reload Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

For Ubuntu/Debian, enable and check the site:

```bash
sudo ln -s /etc/nginx/sites-available/sendiko /etc/nginx/sites-enabled/sendiko
sudo nginx -t
sudo systemctl reload nginx
```

Configure TLS/HTTPS for the domain using your hosting provider or a trusted ACME client such as Certbot. Set `APP_URL` to the HTTPS URL after TLS is active.

## 6. Finish production setup

After configuring `.env` and the web server, cache Laravel's configuration and routes:

```bash
php artisan optimize
```

Visit `https://your-domain.example`, then sign in at `/login` with the configured admin email and password. Use **Articles** in the dashboard to write and publish posts. Uploaded cover and inline photos are served from `/storage`.

## Updating the site

Back up the database and uploaded images before deploying updates. From the application directory:

```bash
git pull
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan down
php artisan migrate --force
php artisan optimize
php artisan up
```

If a new release changes file ownership or introduces new writable paths, make sure they remain writable by the PHP-FPM user. Restart/reload PHP-FPM when required by your deployment environment.

## Backups and troubleshooting

- Back up the database and `storage/app/public` (uploaded photos) together, and test restoring them.
- For permission or 500 errors, inspect `storage/logs/laravel.log` and your PHP-FPM/Nginx logs.
- For broken CSS or JavaScript, check that `npm run build` completed and that `public/build` was deployed.
- For missing uploaded images, check the `public/storage` link, `storage/app/public` permissions, and `APP_URL`.
- Keep `APP_DEBUG=false` on a public server.
- Never run `php artisan migrate:fresh` on a production database; it drops existing tables and data.
