#!/bin/sh
set -e

echo "
Running with:

* Postgres 15
* PostGIS 3.3
"

docker run -it --rm --network doctrine-postgis-15-33 -e DB_HOST=db-15-33 -v "$(pwd)":/app doctrine-postgis-php "$@"
