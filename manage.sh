#!/bin/bash

# Configuration
COMPOSE="docker-compose"
CONTAINER="restaurant_app"

# Help function
usage() {
    echo "Usage: $0 {start|stop|down|restart|build|comp|comp-update|logs}"
    exit 1
}

# Ensure we are in the correct directory (the one containing this script)
cd "$(dirname "$0")"

case "$1" in
    start)
        echo "Starting Gourmet Express environment..."
        $COMPOSE up -d
        ;;
    stop)
        echo "Stopping environment..."
        $COMPOSE stop
        ;;
    down)
        echo "Removing containers..."
        $COMPOSE down
        ;;
    restart)
        echo "Restarting environment..."
        $COMPOSE restart
        ;;
    build)
        echo "Building containers..."
        $COMPOSE up -d --build
        ;;
    comp)
        echo "Running composer install..."
        docker exec restaurant_app composer install
        ;;
    comp-update)
        echo "Updating composer dependencies..."
        docker exec restaurant_app composer update
        ;;
    logs)
        $COMPOSE logs -f
        ;;
    *)
        echo "Usage: ./scripts/manage.sh {start|stop|down|restart|build|comp|comp-update|logs}"
        exit 1
esac

echo "Done."