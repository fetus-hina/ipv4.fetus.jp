# ipv4.fetus.jp

[日本語](README.md)

---

Source code for [ipv4.fetus.jp](https://ipv4.fetus.jp/).

This project downloads allocation data from the Regional Internet Registries (RIRs) and publishes
up-to-date information on the web.

The downloaded data describes each block as a start address and the number of addresses that follow
it; this project converts them into CIDR notation.<br>
It also publishes blocks merged with their neighbors, along with text data you can use for access
control in Apache, Nginx and others.

For most purposes the data published on the site should be enough, but if you would rather manage
it yourself, you can build and run your own server from this source.


## About automatic data acquisition

If you fetch the data automatically, please read the notes on access intervals and so on:<br>
https://ipv4.fetus.jp/about#automation


## Data published via Git

https://github.com/fetus-hina/ipv4.fetus.jp-exports


## Requirements

- Linux (may also work on any system that provides a Unix-like command line)
- PHP (64bit) ≧ 8.4
  - PHP-FPM
- Node.js (LTS or latest)
- PostgreSQL
- Any web server you like (Apache, Nginx, etc.)


## Install (Server-Side)

1. Set up PHP, Node.js and PostgreSQL

2. Create a role (user) and a database in PostgreSQL.<br>
   See [config/components/db/db.php](https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/config/components/db/db.php) for the default settings.<br>
   You can of course use settings other than the defaults.<br>
   If you do, adjust the configuration files after `git clone` and before `make` in the steps below.

3. Build the app
   ```bash
   $ git clone https://github.com/fetus-hina/ipv4.fetus.jp.git
   $ cd ipv4.fetus.jp
   $ touch .production
   $ make
   $ ./yii migrate/up --interactive=0
   ```

4. Run the initial data update (takes about 30 minutes).
   ```bash
   $ ./yii update
   ```

5. Set up a web server


## Update the app

```bash
$ git fetch --prune origin
$ git merge --ff-only origin/master
$ make
$ ./yii migrate/up --interactive=0
```


## Update the database

Use cron or a systemd timer to run the following command about once a day.

```bash
$ /path/to/yii update --interactive=0
```

## License

Copyright (C) AIZAWA Hina  
MIT License
