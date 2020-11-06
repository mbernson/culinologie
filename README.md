# Culinology

This is a web-based cookbook application, built for a project at the 
[University of applied sciences, Leiden](http://www.hsleiden.nl/informatica).
The application lets people store and manage share and print their cooking recipes.

The project is built with Laravel 6 and MySQL.

## Setup

Brief setup steps:

1. Create a MySQL database, and import the files in the `sql/` directory. Migrations are not used in this project.
2. Copy `.env.example` to `.env` and configure the database connection there.
3. Point a virtual host to the `public/` directory and view the site.

## Development

On your development machine, you will need:

* Docker + Docker compose (pget Docker desktop on a Mac](https://docs.docker.com/docker-for-mac/install/))
* Node.js
* A mysql client if you want a database shell

To run a development setup:

```
# Configuration
cp .env.example .env
php artisan key:generate

# Frontend
npm install
npm run dev

# Start containers
docker-compose up
```

You can then visit the site on [http://localhost:8000].

To connect to the database:

```
mysql -h 127.0.0.1 -u root -p'supersecretpassword' culinologie
```

* Use `npm run watch` to compile SASS/JS while developing
* Run `npm run prod` before deploying

## License

As agreed with the client, this project was released as open source on april 30th,
2015. It is licensed under the GPL v2 license. Refer to the `LICENSE.txt` file for this.
