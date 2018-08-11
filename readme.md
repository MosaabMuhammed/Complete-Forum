# Council

This is an open source forum that contains the most of well-known functionalities of every form.

## Installation
### Step 1.
> To run this project, you must have PHP 7+ installed as prequisite.

Begin by cloning this repo to your machine, and installing all Composer dependencies.

```bash
git clone https://github.com/MosaabMuhammed/Complete-Forum.git
cd Complete-Forum && composer install
php artisan key:generate
```

### Step2.

Next, create a new database and reference its name and username/password withing the project's `.env` file. In the example below. we've named the database , "council."

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=council
DB_USERNAME=root
DB_PASSWORD=root
```
