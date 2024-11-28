# ipv4.fetus.jp

[日本語](README.md)

---

Source code for [ipv4.fetus.jp](https://ipv4.fetus.jp/).

Download allocation data from the Regional Internet Registry (RIR) and provide up-to-date
information on the web.

The downloaded data is provided in the form of "start address" and "addresses from it", but this
project converts them to CIDR format.<br>
In addition, it provides a consolidated list of adjacent blocks and address list that can be used
for access control in Apache, Nginx, etc.

I think that it is sufficient to use the data provided on the site as usual, but if you really want
to manage it yourself, you can build and operate the server from this source.



## About automatic data acquisition

Check the following page for instructions on access intervals, etc.<br>
https://ipv4.fetus.jp/about#automation


## Data publication by Git

https://github.com/fetus-hina/ipv4.fetus.jp-exports


## Requirements

- Linux (It might work if a Unix-like command line interface is provided)
- PHP (64bit) ≧ 8.4
  - PHP-FPM
- Node.js (LTS or latest)
- PostgreSQL
- A web server as you like (Apache, Nginx, etc.)


## Install (Server-Side)

1. Set up PHP, Node.js and PostgreSQL

2. Create a `role(user)` and a `database` on PostgreSQL.<br>
   See [config/components/db/db.php](https://github.com/fetus-hina/ipv4.fetus.jp/blob/master/config/components/db/db.php) for default settings.<br>
   Of course you can set them up differently than default configurations.<br>
   If you change the settings, adjust the configuration file after `clone` and before `make` in the next step.

3. Build the app
   ```bash
   $ git clone https://github.com/fetus-hina/ipv4.fetus.jp.git
   $ cd ipv4.fetus.jp
   $ touch .production
   $ make
   $ ./yii migrate/up --interactive=0
   ```

4. Update the database (takes about 30 minutes).
   ```bash
   ./yii update
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

Use "cron" or "systemd timer" to run the following command about once a day.

```bash
/path/to/yii update --interactive=0
```

## License

Copyright (C) AIZAWA Hina  
MIT License
