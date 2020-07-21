# Yacht Service Network #
## Install Locally ##
### Install Composer ###
##### For Windows #####
* [Download](https://getcomposer.org/download/) installer and install it.
* Run under project root:
```
#!bash
composer install
```
### Install NPM ###
##### For Windows #####
* [Download](https://nodejs.org/en/download/) Node.js `10 LTS` installer and install it.
* Run under project root:
```
#!bash
npm install -g napa
npm run pre-install
npm install
```
### Install Yarn ###
##### For Windows #####
* [Download](https://yarnpkg.com/lang/en/docs/install/) installer and install it.
* Run under project root:
```
#!bash
npm run pre-install
yarn install
```
##### For Linux #####
* Follow [Yarn installation instructions](https://yarnpkg.com/en/docs/install#debian-stable)
### Install Gulp ###
* Run:
```
#!bash
npm install gulp-cli -g
```
### Prepare App Configuration ###
Rename **.env.example** to **.env** and then edit database, environment related details in that file.
##### For Windows #####
```
#!bash
copy /Y .env.example .env
```
##### For Linux #####
```
#!bash
cp -f .env.example .env
```
### Update Permissions ###
You'll need to make sure that the storage directory is writable by your webserver, since
caches and log files get written there. You should use the minimum permissions available for
writing, based on how you've got your webserver configured.
##### For Linux #####
```
#!bash
chmod -R 0775 storage/
chmod 0775 bootstrap/cache/
chmod +x artisan
```
### Compile Assets ###
```
#!bash
npm run dev
gulp
```
## Developers Tools ##
If you are working on admin panel frontend use command below to start webpack watcher.
```
#!bash
npm run watch-poll
```
If you are working on site frontend use gulp watcher.
```
#!bash
gulp watch
```
## Server Requirements ##
You need to make sure your server meets the following requirements:

* PHP >= 7.1.3
* Node.js = 10.*
* MySQL >= 5.7
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Ctype PHP Extension
* JSON PHP Extension
* BC Math PHP Extension