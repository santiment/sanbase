Sanbase
=======

Instalation
-----------

These are the installation instructions how to install the Sanbase
development environment. Right now we only support OSX and Linux host
machines:

1. Install Nix (if you are not using NixOS)

``` sh
curl https://nixos.org/nix/install | sh

```

2. Install NixOps

``` sh
nix-env -i nixops
```

3. Install VirtualBox

4. Clone this repository

``` sh
$ mkdir ~/santiment
$ cd santiment
$ git clone git@github.com:santiment/sanbase.git
$ cd sanbase

```

5. Create the development VM

``` sh
nixops create -d devbox ./devbox.nix ./devbox-vbox.nix
```

The virtual machine settings are in the file `devbox-vbox.nix`. You
can change them as you see fit.

6. Deploy the VM

``` sh
nixops deploy -d devbox
```

On my machine (Tzanko) this command exits with an error due to some
problem with Virtualbox. To fix the error startthe Virtualbox UI, then
start and stop the machine from there. After that run again `nixops
deploy -d devbox` and it should work.

7. Set up IP

In the output of the last command you will see an IP address which you
can use to access the machine. Create a file called `.env` in the root
folder of the repository and add the following line there:

``` sh
DEVBOX_IP=XXX.XXX.XXX.XXX
```
where XXX.XXX.XXX.XXX is the IP address

8. Install node.js and yarn
9. In the root folder of the repository run 

``` sh
$ yarn install
$ ./node_modules/.bin/db-migrate up
```

After this you can access the Sanbase at URL http://XXX.XXX.XXX.XXX/cashflow
(replace XXX.XXX.XXX.XXX with the VM's IP address). Any edits of the html or php
files of the repository should be immediately visible. You can access
the database at XXX.XXX.XXX.XXX:5432. The database's name is "postgres", schema
"sanbase", user "sanbase", password "sanbase".

The virtual machine configuration is contained in the files
`devbox.nix` and `devbox-vbox.nix`. If you edit anything there you
have to redeploy by running

``` sh
$ nixops deploy -d devbox
```

(This command can be run from anywhere.)


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
