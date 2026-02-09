# Installing Cloud Storage SDKs

This plugin requires cloud storage SDKs to function. Follow these steps to install them:

## Option 1: Using Composer (Recommended)

If you have Composer installed:

```bash
cd D:\wamp64\www\web\wp-content\plugins\offload-images-js-css
composer install
```

This will install both AWS SDK and Google Cloud Storage SDK.

## Option 2: Manual Installation

### Step 1: Install Composer

1. Download Composer from: https://getcomposer.org/download/
2. For Windows, download and run `Composer-Setup.exe`
3. Follow the installation wizard

### Step 2: Install SDKs

Open Command Prompt or PowerShell and run:

```bash
cd D:\wamp64\www\web\wp-content\plugins\offload-images-js-css
composer install
```

## Option 3: Download Pre-bundled Version

If you downloaded this plugin from GitHub releases, the SDKs should already be included in the `vendor/` directory.

## Verifying Installation

After installation, you should see a `vendor/` directory inside the plugin folder containing:
- `aws/` - AWS SDK for PHP (for Amazon S3 and DigitalOcean Spaces)
- `google/` - Google Cloud Storage SDK

## Troubleshooting

### "Composer is not recognized"

If you get this error, Composer is not installed or not in your system PATH. Install Composer first (see Option 2).

### "Permission denied"

Run your command prompt or PowerShell as Administrator.

### Still having issues?

Contact the developer:
- Email: hello@gunjanjaswal.me
- Website: www.gunjanjaswal.me

## What Gets Installed

When you run `composer install`, these packages are installed:

1. **aws/aws-sdk-php** (~50MB)
   - Required for Amazon S3
   - Required for DigitalOcean Spaces

2. **google/cloud-storage** (~20MB)
   - Required for Google Cloud Storage

Total size: ~70MB

## For Plugin Distribution

If you're distributing this plugin, you can:

1. Run `composer install --no-dev` to install only production dependencies
2. Include the entire plugin folder with the `vendor/` directory
3. Users can then activate the plugin without any additional setup
