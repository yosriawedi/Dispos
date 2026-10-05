#!/bin/sh
set -e

if [ -n "$DATABASE_URL" ]; then
	echo "Running database migrations..."
	php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

	echo "Warming up cache..."
	php bin/console cache:warmup --env=prod
fi

exec "$@"
