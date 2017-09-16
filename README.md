Sanbase
=======

Instalation
-----------

These are the installation instructions how to install the Sanbase
development environment. 


1. Install Vagrant and VirtualBox

2. Clone this repository and create a vagrant virtual machine

``` sh
$ mkdir ~/santiment
$ cd santiment
$ git clone git@github.com:santiment/sanbase.git
$ cd sanbase

$ vagrant up --provision

```

After the command finishes you will have a virtual machine hosting the
Sanbase. You can test it by visiting http://192.168.33.10/cashflow/static.html

3. Set up migrations

3.1. Create a file called `.env` in the root
folder of the repository and add the following line there:

``` sh
DEVBOX_IP=192.168.33.10
```

3.2. Install node.js and yarn
3.3. In the root folder of the repository run 

``` sh
$ yarn install
$ ./node_modules/.bin/db-migrate up
```

After this you can access the Sanbase at URL
http://192.168.33.10/cashflow/static.html Any edits of the html or php
files of the repository should be immediately visible. You can access
the database at 192.168.33.10:5432. The database's name is
"postgres", schema "sanbase", user "sanbase", password "sanbase".

The virtual machine configuration is contained in the file
`devbox.nix`. If you edit anything there you
have to run provisioning again by calling

``` sh
$ vagrant provision
```


Migrations
----------

Migrations to the database are done using db-migrate. Here are the instructions for using the tool:

### Instalation

1. Install node.js

2. Install yarn

3. In the root folder of the repository run

``` sh
$ yarn install
```

After this the `db-migrate` binary is installed in the folder
`./node_modules/.bin/` and `./node_modules/db-migrate/bin`

### Environments

We have set up two environments for db-migrate: "tunnel" and
"prod". Tunnel is used when we have created a ssh tunnel from the
database to localhost. Since the AWS Postgres instance is hidden from
the outisde world this environment can be useful. "prod" can be used
when we have direct access to the database host.

To set up the environments one can use environment variables (whose
names can be seen by looking at database.json) or one can write a .env
file in the root folder of the repository containing those variables.

For local development one can also add a "dev" environment in
`database.json` which can point to a local database instance.

### Creating a new migration

To create a new migration run

``` sh
$ ./node_modules/.bin/db-migrate --env tunnel create MIGRATION-NAME
```

(The --env parameter is due to a bug in db-migrate. Actually this
command does not talk to the database)

This command will create a new migration file in the `migrations`
folder. This file should be edited to set up the migration. There one
should provide commands for setting up and for tearing
down. Db-migrate has some special functions for creating or destroying
tables, indices and keys, but one can also just use raw sql using the
`db.runSql` method. For more information see
https://db-migrate.readthedocs.io/en/latest/API/SQL/

### Running a migration

To apply all migrations until the last one run

``` sh
$ ./node_modules/.bin/db-migrate --env ENV up
```


To go back one migration run

``` sh
$ ./node_modules/.bin/db-migrate --env ENV --count 1 down

```

To show the sql that will be executed without actually running it, use
the `--dry-run` option

For more informations see https://db-migrate.readthedocs.io/en/latest/Getting%20Started/usage/


Deployment
----------

The master branch is automatically deployed by a cron job to the
production server. The script runs once every 60 seconds
