# Exponential Platform DXP v5.0.x (Open Source)

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-blue)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.3%2B-black)](https://symfony.com/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-green)](LICENSE)

**Exponential Platform DXP** is a pure single-kernel content management and digital experience platform built on **Symfony 7.3+** and **PHP 8.3+**. This is the modern, forward-only release line — no LegacyBridge, no classic legacy kernel, no third-party layout builders.

This repository is the Composer **project skeleton** for bootstrapping a new Exponential Platform DXP v5.0.x site.

---

## Table of Contents

1. [Requirements](#requirements)
2. [Quick Start](#quick-start)
3. [What's Included](#whats-included)
4. [Branching & Versioning](#branching--versioning)
5. [Documentation](#documentation)
6. [Contributing](#contributing)
7. [Copyright & Licence](#copyright--licence)

---

## Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| PHP | 8.3 | 8.3 or 8.5 |
| Composer | 2.x | latest 2.x |
| Symfony | 7.3 | 7.3+ |
| MySQL | 8.0 | 8.0+ (utf8mb4) |
| MariaDB | 10.6 | 10.11+ |
| PostgreSQL | 14 | 16+ |
| SQLite | 3.35 | 3.39+ (dev/testing) |
| Redis | 6.0 | 7.x (optional) |
| Varnish | 6.0 | 7.1+ (optional) |
| Apache | 2.4 | 2.4 (event + PHP-FPM) |
| Nginx | 1.18 | 1.24+ |

> **No Node.js or Yarn required.** Frontend assets are served via Symfony AssetMapper (`importmap`). No webpack build step is needed at the skeleton level — any custom asset pipelines are project-specific.

---

## Quick Start

### Composer create-project (recommended)

```bash
composer create-project se7enxweb/exponential-platform-dxp-skeleton my-project
cd my-project
```

Then:
1. Copy `.env` to `.env.local` and configure your database connection.
2. Run `php bin/console ibexa:install ibexa-oss` to provision the database.
3. Start the dev server: `symfony server:start`

See [INSTALL.md](INSTALL.md) for the complete step-by-step guide.

### GitHub git clone (developers)

```bash
git clone git@github.com:se7enxweb/exponential-platform-dxp-skeleton.git
cd exponential-platform-dxp-skeleton
git checkout master
composer install
```

---

## What's Included

- **Exponential Platform DXP core** via the `se7enxweb/exponential-platform-dxp` metapackage
- **Symfony 7.3+** full-stack framework (Symfony Flex managed)
- **Symfony AssetMapper** — zero-build-step frontend assets via `importmap`
- **REST API v2** — full content repository REST API
- **GraphQL API** — overblog/graphql-bundle schema generation
- **JWT Authentication** — lexik/jwt-authentication-bundle
- **Doctrine ORM** — with migrations support
- **Liip Imagine** — on-demand image processing and variation generation
- **FOSHttpCache** — HTTP reverse-proxy cache invalidation (Varnish-ready)
- **Solr** support (optional; default search engine: legacy/DB)

Not included (by design):
- LegacyBridge / Exponential (classic) legacy kernel
- Netgen Layouts
- Netgen Site bundle
- Webpack Encore / Yarn / Node.js build pipeline (skeleton level)

---

## Branching & Versioning

| Branch | Stability | Symfony | PHP | Note |
|---|---|---|---|---|
| `master` | dev | 7.3+ | 8.3+ | active development (`5.0.x-dev` alias) |

Pull requests should target: **`master`**

---

## Documentation

- [INSTALL.md](INSTALL.md) — Full installation and operations guide
- [Exponential Platform](https://platform.exponential.earth) — Project home page
- [GitHub Issues](https://github.com/se7enxweb/exponential-platform-dxp-skeleton/issues)
- [GitHub Discussions](https://github.com/se7enxweb/exponential-platform-dxp-skeleton/discussions)

---

## Contributing

1. Fork this repository
2. Create a feature branch off `master`
3. Open a pull request — target branch: `master`

---

## Copyright & Licence

Copyright &copy; 1998 – 2026 7x (se7enx.com). All rights reserved unless otherwise noted.

Exponential Platform DXP is Open Source software released under the **GNU GPL v2 or any later version**. See [LICENSE](LICENSE) for details.
