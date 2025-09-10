# SQLite Database (micro-api-f3)

## Location

-   **Host (your PC)**: `./data/app.db`
-   **PHP container**: `/var/www/html/data/app.db`

## Interactive mode (inside the container)

``` bash
# enter the container
docker exec -it micro-api-f3-php-1 bash

# start sqlite3 on the database
sqlite3 /var/www/html/data/app.db
```

Once inside the `sqlite>` console:

``` sql
.headers on
.mode column
.tables
.schema users
SELECT * FROM users;
.exit
```

## Handy one-liners

``` bash
# List all tables
docker exec -it micro-api-f3-php-1 sqlite3 /var/www/html/data/app.db ".tables"

# Show schema of the users table
docker exec -it micro-api-f3-php-1 sqlite3 /var/www/html/data/app.db ".schema users"

# Display all rows from the users table
docker exec -it micro-api-f3-php-1 sqlite3 -header -column /var/www/html/data/app.db   "SELECT * FROM users;"
```

## Notes

-   The database is just a `.db` file → **SQLite does not run as a
    server**, PHP directly reads/writes into the file.\

-   You can also open `./data/app.db` directly on the host if `sqlite3`
    is installed locally:

    ``` bash
    sqlite3 data/app.db
    ```