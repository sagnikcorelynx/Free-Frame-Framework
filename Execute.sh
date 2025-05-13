#!/bin/bash

echo "Deploying application..."
rsync -avz --delete --exclude='.*' ./Public/ /var/www/html/
echo "Application deployed successfully!"

echo "Setting up target..."
rm -f /var/www/html/index.html
ln -s /var/www/html/index.php /var/www/html/index
echo "Target set up successfully!"
