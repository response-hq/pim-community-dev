#!/bin/bash

localexist=/srv/pim/.env
if [ -e "$localexist" ]; then

    echo "Overwriting environment variables in .env"
    
    # Replace Akeneo env vars in .env file w/ environment variables supplied by Docker
    sed -i "s|APP_DATABASE_HOST=.*|APP_DATABASE_HOST=$APP_DATABASE_HOST|g" $localexist
    sed -i "s|APP_DATABASE_PORT=.*|APP_DATABASE_PORT=$APP_DATABASE_PORT|g" $localexist
    sed -i "s|APP_DATABASE_NAME=.*|APP_DATABASE_NAME=$APP_DATABASE_NAME|g" $localexist
    sed -i "s|APP_DATABASE_USER=.*|APP_DATABASE_USER=$APP_DATABASE_USER|g" $localexist
    sed -i "s|APP_DATABASE_PASSWORD=.*|APP_DATABASE_PASSWORD=$APP_DATABASE_PASSWORD|g" $localexist
    sed -i "s|APP_INDEX_HOSTS=.*|APP_INDEX_HOSTS=$APP_INDEX_HOSTS|g" $localexist
    sed -i "s|APP_SECRET=.*|APP_SECRET=$APP_SECRET|g" $localexist

else
    echo "file:.env does not exist."
fi

echo "End of entrypoint script. Executing Docker command(s)..."
exec "$@"
