#!/bin/bash
set -e

echo "Updating config from dev to prod..."
sed -i 's/development/production/g' config/app.yml

echo "Updating debug from true to false..."
sed -i 's/debug: true/debug: false/g' config/app.yml

echo "Updating url from localhost to production..."
sed -i 's/localhost:8080/trident.byniwee.cloud/g' config/app.yml
