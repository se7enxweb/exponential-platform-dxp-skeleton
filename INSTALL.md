# Exponential Platform DXP v5.0.x (Stable; Platform v5; Open Source)
## Installation & Operations Guide

> **Platform v5 DXP** is the standard single-kernel release of Exponential Platform. It runs the **Exponential Platform v5 OSS** new-stack kernel on **Symfony 7.3+** with **PHP 8.3+**.
>
> Frontend assets are managed by **Symfony AssetMapper** (`importmap`) — no Node.js, Yarn, or webpack required.
>
> This guide uses numbered **Git Save Points** throughout. Commit at each one so you can return to any working checkpoint without redoing completed work.

---

> **Console Command Prefix Convention**
>
> All `bin/console` commands in this distribution support three name prefixes. Only
> `exponential:` is the canonical name going forward; the others are deprecated aliases that
> remain fully functional for backward compatibility:
>
> | **Preferred — use this** | Deprecated (functional) | Deprecated (functional) |
> |---|---|---|
> | `exponential:*` | `ibexa:*` | `ezplatform:*` / `ezpublish:*` |

---

## Table of Contents

1. [Requirements](#1-requirements)
2. [Architecture Overview](#2-architecture-overview)
3. [First-Time Installation](#3-first-time-installation)
   - [3a. Composer create-project (recommended)](#3a-composer-create-project-recommended)
   - [3b. GitHub git clone (developers)](#3b-github-git-clone-developers)
4. [Environment Configuration (.env.local)](#4-environment-configuration-envlocal)
5. [Database Setup](#5-database-setup)
   - [5a. MySQL / MariaDB](#5-database-setup)
   - [5b. PostgreSQL](#5-database-setup)
   - [5c. SQLite (zero-config)](#5c-sqlite-zero-config-database)
6. [Web Server Setup](#6-web-server-setup)
   - [6a. Apache 2.4](#6a-apache-24)
   - [6b. Nginx](#6b-nginx)
   - [6c. Symfony CLI (development only)](#6c-symfony-cli-development-only)
7. [File & Directory Permissions](#7-file--directory-permissions)
8. [Frontend Assets (Symfony AssetMapper)](#8-frontend-assets-symfony-assetmapper)
9. [JWT Authentication (REST API)](#9-jwt-authentication-rest-api)
10. [GraphQL Schema](#10-graphql-schema)
11. [Search Index](#11-search-index)
12. [Image Variations](#12-image-variations)
13. [Cache Management](#13-cache-management)
14. [Day-to-Day Operations: Start / Stop / Restart](#14-day-to-day-operations-start--stop--restart)
15. [Updating the Codebase](#15-updating-the-codebase)
16. [Cron Jobs](#16-cron-jobs)
17. [Solr Search Engine (optional)](#17-solr-search-engine-optional)
18. [Varnish HTTP Cache (optional)](#18-varnish-http-cache-optional)
19. [Troubleshooting](#19-troubleshooting)
20. [Database Conversion](#20-database-conversion)
    - [20a. Any → SQLite](#20a-any--sqlite-go-to-sqlite)
    - [20b. SQLite → MySQL / MariaDB](#20b-sqlite--mysql--mariadb)
    - [20c. SQLite → PostgreSQL](#20c-sqlite--postgresql)
    - [20d. MySQL / MariaDB → PostgreSQL](#20d-mysql--mariadb--postgresql)
    - [20e. SQLite → Oracle](#20e-sqlite--oracle)
    - [20f. Any → Oracle](#20f-any--oracle)
    - [20g. Post-conversion checklist](#20g-post-conversion-checklist)
21. [Complete CLI Reference](#21-complete-cli-reference)

---

## 1. Requirements

### PHP

- **PHP 8.3+** (PHP 8.3 or 8.5 recommended)
- Required extensions: `gd` or `imagick`, `curl`, `json`, `pdo_mysql` or `pdo_pgsql` or `pdo_sqlite`, `xsl`, `xml`, `intl`, `mbstring`, `opcache`, `ctype`, `iconv`
- For SQLite: `pdo_sqlite` + `sqlite3` PHP extensions
- `memory_limit` ≥ 256M
- `date.timezone` must be set in `php.ini`
- `max_execution_time` ≥ 120

### Web Server

- **Apache 2.4** with `mod_rewrite`, `mod_deflate`, `mod_headers`, `mod_expires` enabled _or_
- **Nginx 1.18+** with PHP-FPM

### Composer

- **Composer 2.x** — run `composer self-update` to ensure the latest 2.x release

```bash
# Universal installer (all UNIX / macOS / BSD)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --2          # install Composer v2
php -r "unlink('composer-setup.php');"
sudo mv composer.phar /usr/local/bin/composer
```

| OS | Package manager install |
|---|---|
| Debian / Ubuntu / Mint | `apt install composer` (may be older — prefer the installer above) |
| RHEL / AlmaLinux / Rocky | `dnf install composer` (EPEL required: `dnf install epel-release`) |
| Fedora | `dnf install composer` |
| openSUSE / SUSE | `zypper install php-composer2` |
| Arch / Manjaro | `pacman -S composer` |
| Slackware | SlackBuild at slackbuilds.org |
| FreeBSD | `pkg install php83-composer` (adjust PHP version) |
| macOS (Homebrew) | `brew install composer` |
| macOS (MacPorts) | `port install php-composer` |
| Generic | [getcomposer.org/download](https://getcomposer.org/download/) |

### Database

- **[MySQL 8.0+](https://dev.mysql.com/downloads/mysql/)** with `utf8mb4` / `utf8mb4_unicode_520_ci` _or_
- **[MariaDB 10.3+](https://mariadb.org/download/)** (10.6+ recommended) _or_
- **[PostgreSQL 14+](https://www.postgresql.org/download/)** _or_
- **[SQLite 3.35+](https://www.sqlite.org/download.html)** — no server required; dev/testing only. Requires the `pdo_sqlite` and `sqlite3` PHP extensions.

Installing MySQL / MariaDB by OS:

| OS | MySQL | MariaDB |
|---|---|---|
| Debian / Ubuntu / Mint | `apt install mysql-server` | `apt install mariadb-server` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install mysql-server` | `dnf install mariadb-server` |
| Fedora | `dnf install community-mysql-server` | `dnf install mariadb-server` |
| openSUSE / SUSE SLES | `zypper install mysql-community-server` | `zypper install mariadb` |
| Arch / Manjaro | `pacman -S mysql` or `pacman -S mariadb` | same |
| Slackware | SlackBuilds.org/mariadb | — |
| FreeBSD | `pkg install mysql80-server` | `pkg install mariadb1011-server` |
| OpenBSD | `pkg_add mariadb-server` | same |
| macOS (Homebrew) | `brew install mysql` | `brew install mariadb` |
| macOS (MacPorts) | `port install mysql8` | `port install mariadb` |
| Generic binary | [dev.mysql.com/downloads](https://dev.mysql.com/downloads/mysql/) | [mariadb.org/download](https://mariadb.org/download/) |

Installing PostgreSQL by OS:

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install postgresql` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install postgresql-server && postgresql-setup --initdb` |
| Fedora | `dnf install postgresql-server` |
| openSUSE / SUSE SLES | `zypper install postgresql-server` |
| Arch / Manjaro | `pacman -S postgresql` |
| Slackware | SlackBuilds.org/postgresql |
| FreeBSD | `pkg install postgresql16-server` |
| OpenBSD | `pkg_add postgresql-server` |
| macOS (Homebrew) | `brew install postgresql@16` |
| macOS (MacPorts) | `port install postgresql16-server` |
| Generic | [postgresql.org/download](https://www.postgresql.org/download/) |

### Optional

- **[Redis 6+](https://redis.io/download/)** · recommended for production caching and sessions
- **[Solr 7.7 or 8.11.1+](https://solr.apache.org/downloads.html)** · for advanced full-text search (default engine is `legacy`)
- **[Varnish 6.0 or 7.1+](https://varnish-cache.org/releases/)** · for HTTP reverse-proxy caching
- **[ImageMagick](https://imagemagick.org/script/download.php)** · for advanced image processing (`IMAGEMAGICK_PATH` env var, default `/usr/bin`)

Installing optional services by OS:

| OS | Redis | Solr | Varnish | ImageMagick |
|---|---|---|---|---|
| Debian / Ubuntu / Mint | `apt install redis` | solr.apache.org tarball | `apt install varnish` | `apt install imagemagick` |
| RHEL / AlmaLinux / Rocky | `dnf install redis` (EPEL) | tarball | `dnf install varnish` (EPEL) | `dnf install ImageMagick` |
| Fedora | `dnf install redis` | tarball | `dnf install varnish` | `dnf install ImageMagick` |
| openSUSE / SUSE | `zypper install redis` | tarball | `zypper install varnish` | `zypper install ImageMagick` |
| Arch / Manjaro | `pacman -S redis` | AUR: solr | AUR: varnish | `pacman -S imagemagick` |
| Slackware | SlackBuilds | tarball | source | SlackBuilds |
| FreeBSD | `pkg install redis` | `pkg install solr` | `pkg install varnish` | `pkg install ImageMagick7` |
| OpenBSD | `pkg_add redis` | tarball | `pkg_add varnish` | `pkg_add ImageMagick` |
| macOS (Homebrew) | `brew install redis` | `brew install solr` | `brew install varnish` | `brew install imagemagick` |
| macOS (MacPorts) | `port install redis` | tarball | source | `port install ImageMagick` |
| Generic download | redis.io | solr.apache.org | varnish-cache.org | imagemagick.org |

### Full Requirements Summary

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.3 | 8.3 or 8.5 |
| Composer | 2.x | latest 2.x |
| MySQL | 8.0 | 8.0+ (utf8mb4) |
| MariaDB | 10.3 | 10.6+ |
| PostgreSQL | 14 | 16+ |
| SQLite | 3.35 | 3.39+ (dev/testing) |
| Redis | 6.0 | 7.x (optional) |
| Solr | 7.7 | 8.11.x (optional) |
| Varnish | 6.0 | 7.1+ (optional) |
| Apache | 2.4 | 2.4 (event + PHP-FPM) |
| Nginx | 1.18 | 1.24+ |

> **No Node.js or Yarn required.** Frontend assets are managed by Symfony AssetMapper (`importmap`). All JavaScript and CSS dependencies are fetched via `importmap:install` — no build step needed.

---

## 2. Architecture Overview

```
Browser Request
      │
      ▼
   Web Server (Apache / Nginx)
      │
      ▼
  public/index.php (Symfony Entry Point)
      │
      └─── Symfony Kernel (Platform v5 OSS — Symfony 7.3+)
                ├── Platform v5 Admin UI (/adminui/)
                ├── REST API v2 (/api/ezp/v2/)
                ├── GraphQL API (/graphql)
                └── Symfony/Twig site controllers
```

### Key Directories

```
project-root/
├── src/                   Your application code
├── templates/             Twig templates
├── config/                Symfony configuration
├── assets/                Source assets (CSS, JS) — served via AssetMapper
├── importmap.php          AssetMapper importmap (replaces package.json)
├── vendor/                PHP packages (composer-managed; not committed)
├── public/                Web root
│   ├── assets/            AssetMapper-published assets (fingerprinted)
│   └── bundles/           Symfony public assets (symlinked by assets:install)
└── var/                   Runtime cache, logs, sessions
```

---

## 3. First-Time Installation

### 3a. Composer create-project (recommended)

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
cd my-project
```

Composer will:
1. Download all PHP packages
2. Run Symfony Flex recipes (including the `se7enxweb/exponential-platform-dxp` recipe)
3. Execute `post-install-cmd` scripts:
   - `assets:install` — publishes bundle `public/` assets to `public/bundles/`
   - `cache:clear` — warms up the initial cache

> 💾 **Git Save Point 1 — Project created**
> ```bash
> git init && git add -A
> git commit -m "chore(init): composer create-project exponential-platform-dxp-skeleton"
> ```

Continue from [Section 4](#4-environment-configuration-envlocal).

---

### 3b. GitHub git clone (developers)

```bash
git clone git@github.com:se7enxweb/exponential-platform-dxp-skeleton.git
cd exponential-platform-dxp-skeleton
git checkout master
```

#### Step 1 — Install PHP dependencies

```bash
composer install --keep-vcs
```

> 💾 **Git Save Point 1 — Vendors installed**
> ```bash
> git add composer.lock && git commit -m "chore(install): lock vendor dependencies"
> ```

#### Step 2 — Configure environment

See [Section 4](#4-environment-configuration-envlocal).

#### Step 3 — Create the database

See [Section 5](#5-database-setup).

#### Step 4 — Set permissions

See [Section 7](#7-file--directory-permissions).

#### Step 5 — Install frontend asset packages

```bash
php bin/console importmap:install
php bin/console assets:install --symlink --relative public
```

#### Step 6 — Generate JWT keypair

```bash
php bin/console lexik:jwt:generate-keypair
```

#### Step 7 — Generate GraphQL schema

```bash
php bin/console ibexa:graphql:generate-schema
```

#### Step 8 — Clear all caches

```bash
php bin/console cache:clear
```

#### Step 9 — Reindex search

```bash
php bin/console exponential:reindex
```

> 💾 **Git Save Point 2 — Installation complete**
> ```bash
> git add -A
> git commit -m "chore(install): platform v5 DXP install complete"
> ```

#### Step 10 — Start the dev server

```bash
symfony server:start
```

Access points after install:

| URL | What you get |
|---|---|
| `https://127.0.0.1:8000/` | Public site (Twig) |
| `https://127.0.0.1:8000/adminui/` | Platform v5 Admin UI (React) |
| `https://127.0.0.1:8000/api/ezp/v2/` | REST API v2 |
| `https://127.0.0.1:8000/graphql` | GraphQL endpoint |

---

## 4. Environment Configuration (.env.local)

**Never commit `.env.local`.** It overrides `.env` with host-specific secrets.

```bash
cp .env .env.local
$EDITOR .env.local
```

### Minimum required variables

```dotenv
# Application
APP_ENV=prod             # or dev
APP_SECRET=<random-32-char-hex-string>

# Database — MySQL / MariaDB
DATABASE_DRIVER=pdo_mysql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_NAME=your_db_name
DATABASE_USER=your_db_user
DATABASE_PASSWORD=your_db_password
DATABASE_CHARSET=utf8mb4
DATABASE_COLLATION=utf8mb4_unicode_520_ci
DATABASE_VERSION=mariadb-10.6.0

# JWT (REST API authentication)
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=<random-64-char-hex-string>
```

### PostgreSQL (alternative to MySQL)

```dotenv
DATABASE_DRIVER=pdo_pgsql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=5432
DATABASE_NAME=your_db_name
DATABASE_USER=your_db_user
DATABASE_PASSWORD=your_db_password
DATABASE_CHARSET=utf8
DATABASE_VERSION=16
```

### SQLite (zero-config alternative — dev / testing)

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

### Search engine

```dotenv
SEARCH_ENGINE=legacy       # default
# SEARCH_ENGINE=solr
```

### HTTP cache

```dotenv
HTTPCACHE_PURGE_TYPE=local
HTTPCACHE_DEFAULT_TTL=86400
HTTPCACHE_PURGE_SERVER=http://localhost:80
```

### Application cache backend

```dotenv
CACHE_POOL=cache.tagaware.filesystem
# CACHE_POOL=cache.redis
# CACHE_DSN=redis://localhost:6379
```

### Mail

```dotenv
MAILER_DSN=null://null
```

### Other

```dotenv
IMAGEMAGICK_PATH=/usr/bin
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
SESSION_HANDLER_ID=session.handler.native_file
SESSION_SAVE_PATH=%kernel.project_dir%/var/sessions/%kernel.environment%
```

---

## 5. Database Setup

### Create the database

```sql
-- MySQL / MariaDB
CREATE DATABASE exponential
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_520_ci;

GRANT ALL PRIVILEGES ON exponential.* TO 'your_db_user'@'localhost'
  IDENTIFIED BY 'your_db_password';
FLUSH PRIVILEGES;
```

```bash
# PostgreSQL
psql -U postgres -c "CREATE DATABASE exponential ENCODING 'UTF8';"
```

### Import schema and demo data

```bash
php bin/console ibexa:install ibexa-oss
# Deprecated aliases (still work):
# php bin/console ibexa:install exponential-oss
# php bin/console ezplatform:install exponential-oss
```

The demo data creates an administrator user:
- **Username:** `admin`
- **Password:** `publish`

**Change the admin password immediately** after installation.

> 💾 **Git Save Point — Database provisioned**
> ```bash
> git commit --allow-empty -m "chore(install): database created and demo data imported"
> ```

### Run Doctrine migrations (on updates)

```bash
php bin/console doctrine:migration:migrate --allow-no-migration
```

---

### 5c. SQLite (zero-config database)

#### Step 1 — Verify PHP extensions

```bash
php -m | grep -i sqlite
# Expected: SQLite3 and pdo_sqlite
```

#### Step 2 — Configure `.env.local`

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
MESSENGER_TRANSPORT_DSN=sync://
```

#### Step 3 — Run the install command

```bash
php bin/console exponential:install exponential-oss
```

#### Step 4 — Fix file permissions

```bash
chmod 664 var/data_dev.db
chown $USER:www-data var/data_dev.db
```

#### Step 5 — Clear caches

```bash
php bin/console cache:clear
```

> 💾 **Git Save Point — SQLite install complete**
> ```bash
> git commit --allow-empty -m "chore(install): sqlite database provisioned for dev"
> ```

---

## 6. Web Server Setup

### 6a. Apache 2.4

```bash
a2enmod rewrite deflate headers expires
```

Example virtual host:

```apache
<VirtualHost *:80>
    ServerName exponential.local
    DocumentRoot /var/www/exponential/public
    DirectoryIndex index.php

    <Directory /var/www/exponential/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/exponential_error.log
    CustomLog ${APACHE_LOG_DIR}/exponential_access.log combined
</VirtualHost>
```

### 6b. Nginx

```nginx
server {
    listen 80;
    server_name exponential.local;
    root /var/www/exponential/public;
    index index.php;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_param APP_ENV prod;
        fastcgi_param APP_DEBUG 0;
        internal;
    }

    location ~ \.php$ { return 404; }

    error_log /var/log/nginx/exponential_error.log;
    access_log /var/log/nginx/exponential_access.log;
}
```

### 6c. Symfony CLI (development only)

Download: [symfony.com/download](https://symfony.com/download)

```bash
curl -sS https://get.symfony.com/cli/installer | bash
mv ~/.symfony5/bin/symfony /usr/local/bin/symfony
```

| OS | Install |
|---|---|
| Debian / Ubuntu / Mint | `curl -sS https://get.symfony.com/cli/installer \| bash` |
| RHEL / AlmaLinux / Rocky / Fedora | `curl -sS https://get.symfony.com/cli/installer \| bash` |
| Arch / Manjaro | AUR: `yay -S symfony-cli` — or the universal installer |
| macOS (Homebrew) | `brew install symfony-cli/tap/symfony-cli` |
| Generic binary | [github.com/symfony-cli/symfony-cli/releases](https://github.com/symfony-cli/symfony-cli/releases) |

```bash
symfony server:start               # HTTPS dev server on https://127.0.0.1:8000
symfony server:start -d            # run in background
symfony server:stop                # stop background server
symfony server:log                 # tail server log
```

---

## 7. File & Directory Permissions

Replace `www-data` with your actual web server user.

```bash
# Symfony runtime directories
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/

# Platform v5 public var directory
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX public/var/
```

If `setfacl` is unavailable, install the `acl` package first:

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint / Pop!_OS | `apt install acl` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install acl` |
| Fedora | `dnf install acl` |
| openSUSE / SUSE SLES | `zypper install acl` |
| Arch / Manjaro | `pacman -S acl` |
| FreeBSD | built in — mount filesystem with `-o acls` |
| macOS | ACLs are enabled by default; use `chmod +a` syntax instead |

If ACLs are not available on your filesystem (NFS, some BSD mounts, macOS APFS):

```bash
chown -R www-data:www-data var/ public/var/
chmod -R 775 var/ public/var/
```

---

## 8. Frontend Assets (Symfony AssetMapper)

Platform v5 uses **Symfony AssetMapper** (`importmap`) instead of Webpack Encore / Yarn. No Node.js or build tool is required.

### Install asset packages (importmap)

On fresh installs or after adding new importmap entries:

```bash
php bin/console importmap:install
```

This downloads JavaScript packages defined in `importmap.php` into `assets/vendor/` (analogous to `node_modules/` but PHP-side).

### Publish bundle public assets

```bash
php bin/console assets:install --symlink --relative public
```

### Asset compilation (production)

In development (`APP_ENV=dev`), AssetMapper serves assets directly from `assets/` with no build step. For production, Symfony automatically fingerprints and writes assets to `public/assets/`:

```bash
php bin/console asset-map:compile
```

This copies and fingerprints all mapped assets into `public/assets/` for deployment. Run it once before deploying to production.

### Updating importmap packages

```bash
php bin/console importmap:update
```

### What requires an asset republish

| Changed | Action needed |
|---|---|
| New bundle added that ships `Resources/public/` | `assets:install` |
| `importmap.php` entries added or changed | `importmap:install` |
| Deploying to production | `asset-map:compile` |

---

## 9. JWT Authentication (REST API)

```bash
php bin/console lexik:jwt:generate-keypair
# Writes:
#   config/jwt/private.pem
#   config/jwt/public.pem
```

On key rotation:

```bash
php bin/console lexik:jwt:generate-keypair --overwrite
php bin/console cache:clear
```

---

## 10. GraphQL Schema

Run after any content type or field type changes:

```bash
php bin/console ibexa:graphql:generate-schema
```

---

## 11. Search Index

### Full reindex

```bash
php bin/console exponential:reindex
```

### Incremental reindex

```bash
php bin/console exponential:reindex --iteration-count=100
```

### Reindex a specific content type

```bash
php bin/console exponential:reindex --content-type=article
```

---

## 12. Image Variations

### Clear generated variation cache

```bash
php bin/console liip:imagine:cache:remove
php bin/console cache:clear
```

### Example variation configuration

```yaml
# config/packages/ibexa.yaml
ibexa:
    system:
        site_group:
            image_variations:
                small:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [160, 120] }
                medium:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [480, 360] }
                large:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [960, 720] }
```

---

## 13. Cache Management

### Clear Symfony application cache

```bash
php bin/console cache:clear
php bin/console cache:clear --env=prod
```

### Warm up cache (production)

```bash
php bin/console cache:warmup --env=prod
```

### Clear a specific cache pool

```bash
php bin/console cache:pool:clear cache.redis
php bin/console cache:pool:clear cache.tagaware.filesystem
```

### Purge HTTP cache

```bash
php bin/console fos:httpcache:invalidate:path / --all
php bin/console fos:httpcache:invalidate:tag <tag>
```

### Nuclear option (development)

```bash
rm -rf var/cache/dev var/cache/prod
php bin/console cache:warmup --env=prod
```

---

## 14. Day-to-Day Operations: Start / Stop / Restart

### Apache

```bash
systemctl start apache2
systemctl stop apache2
systemctl restart apache2
systemctl reload apache2
```

### Nginx

```bash
systemctl start nginx
systemctl stop nginx
systemctl reload nginx
```

### PHP-FPM

```bash
systemctl restart php8.3-fpm
systemctl reload php8.3-fpm
```

### Redis (if used)

```bash
systemctl start redis
systemctl restart redis
```

### Symfony CLI dev server

```bash
symfony server:start -d
symfony server:stop
symfony server:log
symfony server:status
```

### After deploying code changes (production checklist)

```bash
# 1. Pull code
git pull --rebase

# 2. Install/update vendors
composer install --no-dev -o

# 3. Run Doctrine migrations
php bin/console doctrine:migration:migrate --allow-no-migration --env=prod

# 4. Publish bundle public assets
php bin/console assets:install --symlink --relative public --env=prod

# 5. Install/update importmap packages
php bin/console importmap:install --env=prod

# 6. Compile assets for production (fingerprinting)
php bin/console asset-map:compile --env=prod

# 7. Clear & warm up caches
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 8. Reindex search (if content model changed)
# php bin/console exponential:reindex --env=prod
```

---

## 15. Updating the Codebase

### Pull latest code and rebuild

```bash
git pull --rebase
composer install
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
```

### Update Composer packages

```bash
composer update
composer update se7enxweb/exponential-platform-dxp

# After update, always run:
php bin/console doctrine:migration:migrate --allow-no-migration
php bin/console cache:clear
php bin/console exponential:reindex
```

### Update importmap packages

```bash
php bin/console importmap:update
php bin/console importmap:install
```

---

## 16. Cron Jobs

Add to crontab (`crontab -e -u www-data`):

```cron
# Platform v5 cron runner (every 5 minutes)
*/5 * * * * /usr/bin/php /var/www/exponential/bin/console ezplatform:cron:run --env=prod >> /var/log/exponential-cron.log 2>&1
```

---

## 17. Solr Search Engine (optional)

### Switch from legacy to Solr

1. Set `SEARCH_ENGINE=solr` and `SOLR_DSN`/`SOLR_CORE` in `.env.local`
2. Clear cache: `php bin/console cache:clear`
3. Provision the Solr core:
   ```bash
   php bin/console ezplatform:solr:create-core --cores=default
   ```
4. Reindex all content:
   ```bash
   php bin/console exponential:reindex
   ```

### Switch back to legacy search

```dotenv
SEARCH_ENGINE=legacy
```

```bash
php bin/console cache:clear
```

---

## 18. Varnish HTTP Cache (optional)

1. Set env vars in `.env.local`:
   ```dotenv
   HTTPCACHE_PURGE_TYPE=varnish
   HTTPCACHE_PURGE_SERVER=http://127.0.0.1:6081
   HTTPCACHE_VARNISH_INVALIDATE_TOKEN=<your-secret>
   TRUSTED_PROXIES=127.0.0.1
   ```
2. Set `APP_HTTP_CACHE=0` in your web server vhost.
3. Clear cache after any VCL change:
   ```bash
   php bin/console cache:clear
   php bin/console fos:httpcache:invalidate:path / --all
   ```

---

## 19. Troubleshooting

### White screen / 500 error

```bash
tail -f var/log/dev.log
tail -f var/log/prod.log
APP_ENV=dev php bin/console cache:clear
```

### "Class not found" after composer update

```bash
composer dump-autoload -o
php bin/console cache:clear
```

### Assets not loading (404 on /bundles/ or /assets/)

```bash
php bin/console assets:install --symlink --relative public
php bin/console importmap:install
php bin/console asset-map:compile   # production only
```

### Cache not clearing / stale content

```bash
php bin/console cache:clear
rm -rf var/cache/dev var/cache/prod
php bin/console cache:warmup --env=prod
```

### Image variations missing

```bash
php bin/console liip:imagine:cache:remove
php bin/console cache:clear
```

### Search results outdated

```bash
php bin/console exponential:reindex
```

### Permission denied on var/ or public/var/

```bash
setfacl -R  -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
setfacl -dR -m u:www-data:rwX -m g:www-data:rwX var/ public/var/
```

### JWT authentication errors (REST API)

```bash
php bin/console lexik:jwt:generate-keypair --overwrite
php bin/console cache:clear
```

---

## 20. Database Conversion

This section covers converting an existing, running Exponential Platform DXP application from one database engine to another using free and open-source tools only.

All tools listed below are either:
- distributed under OSI-approved open-source licences (MIT, GPL, BSD, Apache 2.0), or
- free CLI utilities included with the database server packages.

> **Before you start — backup everything.**
> ```bash
> # MySQL / MariaDB
> mysqldump -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" "$DATABASE_NAME" > backup_$(date +%Y%m%d).sql
> # PostgreSQL
> pg_dump -U "$DATABASE_USER" "$DATABASE_NAME" > backup_$(date +%Y%m%d).sql
> # SQLite
> cp var/data_dev.db var/data_dev.db.bak
> # Also backup var/ and your .env.local
> cp .env.local .env.local.bak
> ```

### Tool inventory

All tools are free and open-source. Download links and cross-platform install commands are provided for every tool.

#### `mysqldump` / `mysql` CLI

Bundled with every MySQL and MariaDB server package.

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install default-mysql-client` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install mysql` |
| Fedora | `dnf install community-mysql` |
| openSUSE / SUSE | `zypper install mysql-client` |
| Arch / Manjaro | `pacman -S mysql-clients` |
| FreeBSD | `pkg install mysql80-client` |
| OpenBSD | `pkg_add mariadb-client` |
| macOS (Homebrew) | `brew install mysql-client` |

#### `pg_dump` / `psql`

Bundled with PostgreSQL server packages.

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install postgresql-client` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install postgresql` |
| Fedora | `dnf install postgresql` |
| openSUSE / SUSE | `zypper install postgresql-client` |
| Arch / Manjaro | `pacman -S postgresql-libs` |
| FreeBSD | `pkg install postgresql16-client` |
| OpenBSD | `pkg_add postgresql-client` |
| macOS (Homebrew) | `brew install libpq` |

#### `sqlite3` CLI

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install sqlite3` |
| RHEL / CentOS / AlmaLinux / Rocky | `dnf install sqlite` |
| Fedora | `dnf install sqlite` |
| openSUSE / SUSE | `zypper install sqlite3` |
| Arch / Manjaro | `pacman -S sqlite` |
| FreeBSD | `pkg install sqlite3` |
| macOS | pre-installed on all versions |

#### pgloader

[pgloader.io](https://pgloader.io/) · [github.com/dimitri/pgloader](https://github.com/dimitri/pgloader) · Licence: PostgreSQL (BSD-like)

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install pgloader` |
| Fedora | `dnf install pgloader` |
| Arch / Manjaro | AUR: `yay -S pgloader` |
| FreeBSD | `pkg install pgloader` |
| macOS (Homebrew) | `brew install pgloader` |
| Docker (any OS) | `docker run --rm -it dimitri/pgloader:latest pgloader <args>` |

#### mysql2sqlite

[github.com/dumblob/mysql2sqlite](https://github.com/dumblob/mysql2sqlite) · Licence: MIT

```bash
curl -LO https://raw.githubusercontent.com/dumblob/mysql2sqlite/master/mysql2sqlite
chmod +x mysql2sqlite
```

#### sqlite3-to-mysql

[github.com/techouse/sqlite3-to-mysql](https://github.com/techouse/sqlite3-to-mysql) · Licence: MIT

```bash
pip3 install sqlite3-to-mysql
```

#### Apache Hop (ETL engine)

[hop.apache.org](https://hop.apache.org/) · [github.com/apache/hop](https://github.com/apache/hop) · Licence: Apache 2.0

Fully open-source ETL engine; supports SQLite, MySQL, PostgreSQL, Oracle, and dozens of other databases over JDBC. Required for any → Oracle and Oracle → any workflows.

| OS | Install |
|---|---|
| Linux / macOS / Windows | Download zip from [hop.apache.org/download](https://hop.apache.org/download/) and unzip |
| Docker (any OS) | `docker run -it apache/hop:latest` |

```bash
# Requires Java 11+; check with:
java -version

# Download and unzip (adjust version as needed)
curl -LO https://downloads.apache.org/hop/2.11.0/apache-hop-client-2.11.0.zip
unzip apache-hop-client-2.11.0.zip -d /opt/hop

# Launch GUI
/opt/hop/hop-gui.sh
```

#### ora2pg (Oracle → PostgreSQL)

[github.com/darold/ora2pg](https://github.com/darold/ora2pg) · Licence: GPL v3

Industry-standard open-source tool for migrating data **from** Oracle to PostgreSQL.

| OS | Install command |
|---|---|
| Debian / Ubuntu / Mint | `apt install ora2pg` |
| RHEL / CentOS / AlmaLinux / Rocky | `cpan Ora2Pg` (or build from source) |
| Fedora | `dnf install perl-Ora2Pg` |
| Arch / Manjaro | AUR: `yay -S ora2pg` |
| Any OS | `cpan -i Ora2Pg` |

---

### 20a. Any → SQLite (go to SQLite)

#### From MySQL / MariaDB → SQLite

```bash
# 1. Get the script
curl -LO https://raw.githubusercontent.com/dumblob/mysql2sqlite/master/mysql2sqlite
chmod +x mysql2sqlite

# 2. Dump and pipe into SQLite
mysqldump --no-tablespaces --skip-extended-insert --compact \
  -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" \
  -h "$DATABASE_HOST" "$DATABASE_NAME" \
  | ./mysql2sqlite - | sqlite3 var/data_dev.db
```

#### From PostgreSQL → SQLite

```bash
touch var/data_dev.db

cat > /tmp/pg_to_sqlite.load <<'EOF'
LOAD DATABASE
  FROM postgresql://db_user:db_pass@127.0.0.1/db_name
  INTO sqlite:///PROJECTDIR/var/data_dev.db

WITH include no drop, create tables, create indexes, reset sequences;
EOF

sed -i "s|PROJECTDIR|$(pwd)|g" /tmp/pg_to_sqlite.load
pgloader /tmp/pg_to_sqlite.load
```

#### After migrating to SQLite — update .env.local

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_dev.db"
MESSENGER_TRANSPORT_DSN=sync://
```

Fix permissions and clear caches (see Section 5c steps 4–5).

---

### 20b. SQLite → MySQL / MariaDB

Create the target database first (see Section 5 — Database Setup).

```bash
pip3 install sqlite3-to-mysql

sqlite3mysql \
  --sqlite-file var/data_dev.db \
  --mysql-database "$DATABASE_NAME" \
  --mysql-user "$DATABASE_USER" \
  --mysql-password "$DATABASE_PASSWORD" \
  --mysql-host "$DATABASE_HOST" \
  --mysql-port 3306 \
  --chunk 1000
```

Update `.env.local`:

```dotenv
DATABASE_DRIVER=pdo_mysql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=3306
DATABASE_NAME=your_db_name
DATABASE_USER=your_db_user
DATABASE_PASSWORD=your_db_password
DATABASE_CHARSET=utf8mb4
DATABASE_COLLATION=utf8mb4_unicode_520_ci
DATABASE_VERSION=mariadb-10.6.0
# Remove DATABASE_URL=sqlite:// and MESSENGER_TRANSPORT_DSN=sync://
```

---

### 20c. SQLite → PostgreSQL

```bash
psql -U postgres -c "CREATE DATABASE exponential ENCODING 'UTF8';"

cat > /tmp/sqlite_to_pg.load <<EOF
LOAD DATABASE
  FROM sqlite:///$(pwd)/var/data_dev.db
  INTO postgresql://pg_user:pg_pass@127.0.0.1/exponential

WITH include no drop, create tables, create indexes, reset sequences;
EOF

pgloader /tmp/sqlite_to_pg.load
```

Update `.env.local`:

```dotenv
DATABASE_DRIVER=pdo_pgsql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=5432
DATABASE_NAME=exponential
DATABASE_USER=pg_user
DATABASE_PASSWORD=pg_pass
DATABASE_CHARSET=utf8
DATABASE_VERSION=16
# Remove DATABASE_URL=sqlite:// and MESSENGER_TRANSPORT_DSN=sync://
```

---

### 20d. MySQL / MariaDB → PostgreSQL

```bash
psql -U postgres -c "CREATE DATABASE exponential ENCODING 'UTF8';"

cat > /tmp/mysql_to_pg.load <<'EOF'
LOAD DATABASE
  FROM      mysql://db_user:db_pass@127.0.0.1/source_db
  INTO      postgresql://pg_user:pg_pass@127.0.0.1/exponential

WITH include no drop, create tables, create indexes, reset sequences, foreign keys

SET work_mem TO '128MB'

CAST
  column type matching ~/enum/ to text,
  type tinyint to boolean using tinyint-to-boolean,
  type longtext to text, type mediumtext to text,
  type int with unsigned to bigint;
EOF

pgloader /tmp/mysql_to_pg.load
```

Update `.env.local`:

```dotenv
DATABASE_DRIVER=pdo_pgsql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=5432
DATABASE_NAME=exponential
DATABASE_USER=pg_user
DATABASE_PASSWORD=pg_pass
DATABASE_CHARSET=utf8
DATABASE_VERSION=16
```

---

### 20e. SQLite → Oracle

Oracle does not provide a fully open-source server. The freely available options are:

- **Oracle XE 21c** — free-to-use, proprietary; download from [oracle.com/database/technologies/xe-downloads.html](https://www.oracle.com/database/technologies/xe-downloads.html)
- **Apache Hop** (Apache 2.0) — fully open-source ETL engine; see tool inventory above
- **DBeaver Community** — [dbeaver.io](https://dbeaver.io/) · Apache 2.0 — GUI database tool with built-in data-transfer wizard

> **Note:** Doctrine's Oracle DBAL driver requires the `oci8` or `pdo_oci` PHP extension.

#### Method A: CSV export + SQL\*Loader

```bash
# 1. List tables in the SQLite database
sqlite3 var/data_dev.db ".tables"

# 2. Export each table to CSV (repeat per table)
sqlite3 -header -csv var/data_dev.db \
  "SELECT * FROM your_table;" > /tmp/your_table.csv

# 3. Create a SQL*Loader control file per table (skipping the header row)
cat > /tmp/load_your_table.ctl <<'EOF'
LOAD DATA
INFILE '/tmp/your_table.csv'
APPEND INTO TABLE your_table
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
TRAILING NULLCOLS
( col1, col2, col3 )
EOF

# 4. Load via sqlldr (part of Oracle InstantClient Basic package)
sqlldr userid=db_user/db_pass@oracle_host:1521/ORCL \
  control=/tmp/load_your_table.ctl \
  log=/tmp/load_your_table.log \
  skip=1
```

#### Method B: Apache Hop ETL pipeline

```bash
# Install Apache Hop (see tool inventory above)
/opt/hop/hop-gui.sh

# In the Hop GUI:
#   File → New pipeline
#   Add: Table Input (SQLite JDBC: org.sqlite.JDBC, URL: jdbc:sqlite:var/data_dev.db)
#   Add: Table Output (Oracle JDBC: oracle.jdbc.OracleDriver, URL: jdbc:oracle:thin:@oracle_host:1521:ORCL)
#   Connect → Run
```

Update `.env.local` after migration:

```dotenv
DATABASE_DRIVER=oci8
DATABASE_HOST=oracle_host
DATABASE_PORT=1521
DATABASE_NAME=ORCL
DATABASE_USER=db_user
DATABASE_PASSWORD=db_pass
DATABASE_CHARSET=AL32UTF8
# Remove DATABASE_URL=sqlite:// and MESSENGER_TRANSPORT_DSN=sync://
```

---

### 20f. Any → Oracle

#### MySQL / MariaDB → Oracle

```bash
# Option A: Apache Hop pipeline (recommended — handles type mapping automatically)
#   Source: MySQL Table Input (JDBC: com.mysql.cj.jdbc.Driver)
#   Target: Oracle Table Output (JDBC: oracle.jdbc.OracleDriver)
/opt/hop/hop-gui.sh

# Option B: CSV export + SQL*Loader
# Export each table to CSV from MySQL:
mysql -u "$DATABASE_USER" -p"$DATABASE_PASSWORD" -h "$DATABASE_HOST" \
  --batch --silent --raw "$DATABASE_NAME" \
  -e "SELECT * FROM your_table" | sed 's/\t/,/g' > /tmp/your_table.csv

# Then load via sqlldr as shown in 20e Method A above.
```

Update `.env.local`:

```dotenv
DATABASE_DRIVER=oci8
DATABASE_HOST=oracle_host
DATABASE_PORT=1521
DATABASE_NAME=ORCL
DATABASE_USER=db_user
DATABASE_PASSWORD=db_pass
DATABASE_CHARSET=AL32UTF8
```

#### PostgreSQL → Oracle

```bash
# Option A: Apache Hop pipeline
#   Source: PostgreSQL Table Input (JDBC: org.postgresql.Driver)
#   Target: Oracle Table Output (JDBC: oracle.jdbc.OracleDriver)
/opt/hop/hop-gui.sh

# Option B: pg_dump schema + CSV data + sqlldr

# 1. Dump the schema
pg_dump -U pg_user --schema-only "$DATABASE_NAME" > /tmp/pg_schema.sql
# Adapt PostgreSQL DDL to Oracle syntax:
#   SERIAL / BIGSERIAL  → NUMBER + SEQUENCE
#   TEXT                → CLOB
#   BOOLEAN             → NUMBER(1)
#   BYTEA               → BLOB
# Apply to Oracle:
sqlplus db_user/db_pass@oracle_host:1521/ORCL @/tmp/oracle_schema.sql

# 2. Export each table as CSV
psql -U pg_user -d "$DATABASE_NAME" \
  -c "\COPY your_table TO '/tmp/your_table.csv' WITH CSV HEADER"

# 3. Load via sqlldr (see control file format in 20e Method A above)
sqlldr userid=db_user/db_pass@oracle_host:1521/ORCL \
  control=/tmp/load_your_table.ctl \
  log=/tmp/load_your_table.log \
  skip=1
```

Update `.env.local`:

```dotenv
DATABASE_DRIVER=oci8
DATABASE_HOST=oracle_host
DATABASE_PORT=1521
DATABASE_NAME=ORCL
DATABASE_USER=db_user
DATABASE_PASSWORD=db_pass
DATABASE_CHARSET=AL32UTF8
```

#### Oracle → PostgreSQL / MySQL (using ora2pg)

For teams migrating **away from** Oracle to PostgreSQL (and then optionally to MySQL):

```bash
apt install ora2pg   # Debian/Ubuntu; also via 'cpan Ora2Pg' on any OS

cat > /tmp/ora2pg.conf <<'EOF'
ORACLE_DSN  dbi:Oracle:host=oracle_host;sid=ORCL
ORACLE_USER system
ORACLE_PWD  password
SCHEMA      YOUR_SCHEMA
TYPE        TABLE, INSERT, SEQUENCE, INDEX, CONSTRAINT
OUT_FILE    /tmp/ora_export.sql
QUOTE_STRING_WITH_DOLLAR 0
EOF

ora2pg -c /tmp/ora2pg.conf
psql -U pg_user exponential < /tmp/ora_export.sql
```

Update `.env.local` (PostgreSQL target):

```dotenv
DATABASE_DRIVER=pdo_pgsql
DATABASE_HOST=127.0.0.1
DATABASE_PORT=5432
DATABASE_NAME=exponential
DATABASE_USER=pg_user
DATABASE_PASSWORD=pg_pass
DATABASE_CHARSET=utf8
DATABASE_VERSION=16
```

> **Note:** `ora2pg` is GPL v3. Oracle XE (Express Edition) is free to use but not open-source. Full Oracle enterprise migration is outside the scope of this guide.

---

### 20g. Post-conversion checklist

```bash
# 1. Update .env.local with new database vars
$EDITOR .env.local

# 2. Clear the Symfony container and cache
php bin/console cache:clear

# 3. Validate Doctrine entity mappings
php bin/console doctrine:schema:validate

# 4. Run pending Doctrine migrations
php bin/console doctrine:migration:migrate --allow-no-migration

# 5. Regenerate the GraphQL schema
php bin/console ibexa:graphql:generate-schema

# 6. Reindex search
php bin/console exponential:reindex

# 7. Smoke-test the site
curl -I http://localhost/
curl -I http://localhost/adminui/

# 8. Fix SQLite permissions if SQLite is the target
chmod 664 var/data_dev.db
chown "$USER":www-data var/data_dev.db
```

---

## 21. Complete CLI Reference

### 21.1 Symfony Core

```bash
php bin/console list                                          # list all commands
php bin/console help <command>                                # help for any command
php bin/console cache:clear                                   # clear current APP_ENV cache
php bin/console cache:clear --env=prod                        # clear production cache
php bin/console cache:warmup --env=prod                       # warm up production cache
php bin/console cache:pool:clear cache.redis                  # clear a named cache pool
php bin/console assets:install --symlink --relative public    # publish bundle assets
php bin/console importmap:install                             # download importmap packages
php bin/console importmap:update                              # update importmap packages
php bin/console asset-map:compile                             # compile + fingerprint for production
php bin/console debug:router                                  # list all routes
php bin/console debug:container                               # list all service IDs
php bin/console debug:config <bundle>                         # dump resolved bundle config
php bin/console debug:event-dispatcher                        # list all registered listeners
php bin/console messenger:consume                             # consume messages from queue
php bin/console lexik:jwt:generate-keypair                    # generate RSA keypair
php bin/console lexik:jwt:generate-keypair --overwrite        # rotate keypair
```

### 21.2 Doctrine / Migrations

```bash
php bin/console doctrine:migration:migrate --allow-no-migration   # run pending migrations
php bin/console doctrine:migration:migrate --dry-run              # preview SQL only
php bin/console doctrine:migration:status                         # show pending/applied status
php bin/console doctrine:migration:diff                           # generate migration from entity diff
php bin/console doctrine:schema:validate                          # validate entity ↔ DB schema
php bin/console doctrine:database:create                          # create the database
php bin/console doctrine:database:drop --force                    # drop the database (DESTRUCTIVE)
```

### 21.3 Platform v5 New Stack

```bash
php bin/console exponential:install exponential-oss           # schema + demo data (canonical)
php bin/console ibexa:install ibexas-oss                      # deprecated alias (functional)
php bin/console exponential:reindex                           # full reindex
php bin/console exponential:reindex --iteration-count=100     # incremental
php bin/console exponential:reindex --content-type=article    # one content type
php bin/console ezplatform:solr:create-core --cores=default   # provision Solr core
php bin/console ezplatform:content:cleanup-drafts             # remove stale drafts
php bin/console ezplatform:content:cleanup-versions --keep=3  # keep last N per content
php bin/console ezplatform:cron:run                           # run Platform v5 cron scheduler
php bin/console ibexa:graphql:generate-schema                 # regenerate from content model
php bin/console fos:httpcache:invalidate:path / --all         # purge all HTTP cache paths
php bin/console fos:httpcache:invalidate:tag <tag>            # purge by cache tag
php bin/console liip:imagine:cache:remove                     # remove all cached variations
php bin/console liip:imagine:cache:remove --filter=small      # remove one variation alias
php bin/console debug:config ibexa                            # dump full resolved platform config
```

### 21.4 Composer Maintenance

```bash
composer install                       # install from composer.lock
composer install --no-dev -o           # production + optimised autoloader
composer update                        # update all within constraints
composer update se7enxweb/exponential-platform-dxp  # update one package
composer dump-autoload -o              # optimised (production) autoloader
composer show                          # list all installed packages
composer outdated                      # list outdated packages
composer audit                         # check for security advisories
composer validate                      # validate composer.json / composer.lock
```

### 21.5 Symfony CLI

```bash
symfony server:start                   # start HTTPS dev server (https://127.0.0.1:8000)
symfony server:start -d                # start in background daemon mode
symfony server:stop                    # stop background server
symfony server:log                     # tail server access/error log
symfony server:status                  # show server status + URL
symfony check:requirements             # verify PHP + extension requirements
symfony check:security                 # audit composer.lock for known CVEs
```

---

*For web server configuration templates see `doc/apache2/` and `doc/nginx/` (if present).*
*For Docker-based development see `doc/docker/` and `compose.override.yaml` (if present).*

---

*Copyright &copy; 1998 – 2026 7x (se7enx.com). All rights reserved unless otherwise noted.*
