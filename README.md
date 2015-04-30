# Culinology

This is a web-based cookbook application, built for a project at the 
[University of applied sciences, Leiden](http://www.hsleiden.nl/informatica).
The application lets people store and manage share and print their cooking recipes.

The project is built with Laravel 5 and MySQL.

## Setup

Brief setup steps:

1. Create a MySQL database, and import the files in the `sql/` directory. Migrations are not used in this project.
2. Copy `.env.example` to `.env` and configure the database connection there.
3. Point a virtual host to the `public/` directory and view the site.

## Development

**TODO**: Set up a Vagrant box for development.

* The `gulp` command will re-compile the LESS/SASS stylesheets.

## License

As agreed with the client, this project was released as open source on april 30th,
2015. It is licensed under the GPL v2 license. Refer to the `LICENSE.txt` file for this.