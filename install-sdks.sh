#!/bin/bash

echo "========================================"
echo "Offload Images JS CSS - SDK Installer"
echo "========================================"
echo ""
echo "This will install the required cloud storage SDKs."
echo ""

# Check if composer is installed
if ! command -v composer &> /dev/null; then
    echo "ERROR: Composer is not installed!"
    echo ""
    echo "Please install Composer first:"
    echo "Visit: https://getcomposer.org/download/"
    echo ""
    exit 1
fi

echo "Composer found! Installing SDKs..."
echo ""

# Install dependencies
composer install --no-dev --optimize-autoloader

if [ $? -eq 0 ]; then
    echo ""
    echo "========================================"
    echo "SUCCESS! SDKs installed successfully."
    echo "========================================"
    echo ""
    echo "The following SDKs are now available:"
    echo "- AWS SDK for PHP (Amazon S3, DigitalOcean Spaces)"
    echo "- Google Cloud Storage SDK"
    echo ""
    echo "You can now activate the plugin in WordPress!"
    echo ""
else
    echo ""
    echo "========================================"
    echo "ERROR: Installation failed!"
    echo "========================================"
    echo ""
    echo "Please check the error messages above."
    echo ""
    echo "Need help? Contact: hello@gunjanjaswal.me"
    echo ""
fi
