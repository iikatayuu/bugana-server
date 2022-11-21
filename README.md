
# BUGANA Backend Server
This repository contains the backend API and Web Admin GUI codes.


### Prerequisites
 * MySQL 8.0+
 * PHP 5.0+
 * [Composer 2.4+](https://getcomposer.org)


### Installation
Clone this repository:

```shell
git clone https://github.com/eidoriantan/bugana-server.git
```

Install packages with [Composer](https://getcomposer.org):

```shell
composer install
```

Copy `config.sample.json` to `config.json` then edit the file to configure your database connection.


#### Setting up Admin and Head Admin credentials
You should register an account (each for admin and head admin) using the [mobile application](https://github.com/eidoriantan/bugana). After the registration, update the account's type (`headadmin` for Head Admin and `admin` for Admin) and verified status from your database.

You can also run this SQL to set up the admin accounts after registration.

```sql
UPDATE `users` SET `type`='headadmin', `verified`=1 WHERE `id`=1;
UPDATE `users` SET `type`='admin', `verified`=1 WHERE `id`=2;
```
