#!/bin/bash
set -e

if [ ! -d ".serve" ]; then
    echo "Getting infrastrucutre..."
    git clone git@gitlab.com:niwee-productions/infrastructures/php.git .serve
fi

if [ ! -d ".serve/launcher" ]; then
    git clone git@gitlab.com:niwee-productions/tools/launcher.git .serve/launcher
fi

if [ ! -f "docker-compose.yml" ]; then
    cp ./.serve/docker-compose.yml docker-compose.yml
fi

if [ ! -f ".gitignore" ]; then
    echo "Creating gitignore..."

    curl -o .gitignore https://www.toptal.com/developers/gitignore/api/macos,composer,node,yarn,phpunit,phpstorm,intellij,visualstudiocode,wordpress
fi

if ! grep -qw ".serve" .gitignore; then
    echo -e "\n.serve" >>.gitignore
fi

if ! docker network inspect dev &>/dev/null; then
    echo "No network named 'dev' found. Creating..."
    docker network create dev
fi

if [[ ! -f ".env" && -f "./.serve/.env.template" ]]; then
    echo "Creating .env file..."
    if ! cp .serve/.env.template .env; then
        echo "Cannot create .env file. Please create it manually."
        exit 0
    fi
fi

if [[ ! -f ".db.env" && -f "./.serve/.db.env.template" ]]; then
    echo "Creating .db.env file..."
    if ! cp .serve/.db.env.template .db.env; then
        echo "Cannot create .db.env file. Please create it manually."
        exit 0
    fi
fi

if [[ ! -f ".db.local.env" && -f "./.serve/.db.env.template" ]]; then
    echo "Creating .db.local.env file..."
    if ! cp .serve/.db.env.template .db.local.env; then
        echo "Cannot create .db.local.env file. Please create it manually."
        exit 0
    fi
fi

if [ -f "extra.sh" ]; then
    echo "Running extra.sh..."
    ./extra.sh
fi

# Set filepaths
export SQL_FILE_PATH="sql"

# If launcher is not found, clone it
if [[ -d ".serve" ]]; then
    export DIR=".serve/"
fi

# Set container names
export SERVER=nginx
export APP=app
export DB=mariadb
export NODE=gulp
export TYPE=php
export USER_ID=$(id -u)

# Set templates
unset LAUNCHER_TEMPLATES[@]
LAUNCHER_TEMPLATES=("git@gitlab.com:niwee-productions/bases/php/onepage.git" "git@gitlab.com:niwee-productions/bases/php/classic.git")

# Execute the launcher with all params
LAUNCHER_TEMPLATES="${LAUNCHER_TEMPLATES[@]}" ${DIR}launcher/launcher.sh "$@"
