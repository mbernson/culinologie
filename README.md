# Culinologie

This is a web-based cookbook application, originally built for a project at the 
[University of applied sciences, Leiden](http://www.hsleiden.nl/informatica).
The application lets people store, manage, share and print cooking recipes.

The project is built with PHP 8, Laravel 9 and MySQL 8.

## Development

A development environment is provided using Laravel Sail (which uses Docker).

1. Install Docker and Composer
2. Run `composer install`
3. Run `./vendor/bin/sail up`

## Deployment

Brief setup steps:

1. Create a MySQL database, and import the files in the `sql/` directory. Migrations are not used in this project.
2. Copy `.env.example` to `.env` and configure the database connection there.
3. Point a virtual host to the `public/` directory and view the site.

## License

As agreed with the client, this project was released as open source on april 30th,
2015. It is licensed under the GPL v2 license. Refer to the `LICENSE.txt` file for this.
