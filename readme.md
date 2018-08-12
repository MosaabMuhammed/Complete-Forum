# Council [![Build Status](https://travis-ci.org/MosaabMuhammed/Complete-Forum.svg?branch=master)](https://travis-ci.org/MosaabMuhammed/Complete-Forum)

This is an open source forum that contains the most of well-known functionalities of any form.

## Installation
### Step 1.
> To run this project, you must have PHP 7+ installed as prequisite.

Begin by cloning this repo to your machine, and installing all Composer dependencies.

```bash
git clone https://github.com/MosaabMuhammed/Complete-Forum.git
cd Complete-Forum && composer install
php artisan key:generate
mv .env.example .env
```

### Step 2.

Next, create a new database and reference its name and username/password withing the project's `.env` file. In the example below. we've named the database , "council."

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=council
DB_USERNAME=root
DB_PASSWORD=root
```

### Step 3.
Until an administration portal is available, manually insert any number of "channels" (think of these as forum categories) into the "channels" table in your database.
Once finished, clear your server cache, and you're all set to go!
```bash
php artisan cache:clear
```

### Step 4.
Use your forum! by writing in your terminal (in the current directory "Complete-Forum")
```bash
php artisan serve
```
