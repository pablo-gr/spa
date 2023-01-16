#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE "$POSTGRES_NAME";
    GRANT ALL PRIVILEGES ON DATABASE "$POSTGRES_NAME" TO "$POSTGRES_USER";
    CREATE DATABASE "$POSTGRES_NAME_TEST";
    GRANT ALL PRIVILEGES ON DATABASE "$POSTGRES_NAME_TEST" TO "$POSTGRES_USER";
EOSQL
