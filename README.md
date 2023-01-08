
# BUGANA Backend Server
This repository contains the backend API and Web Admin GUI codes.


### Prerequisites
 * MySQL 8.0+
 * [PHP 5.0+](https://www.php.net/)
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
Access `/setup.php` to set up head admin and admin accounts.
