#!/bin/bash

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running.start Docker first."
    exit 1
fi

# Start MySQL container
docker compose up -d mysql

# Wait for MySQL to be ready
sleep 10

# Check if container is running
if docker compose ps mysql | grep -q "Up"; then
else
    echo "Check logs with: docker compose logs mysql"
fi
