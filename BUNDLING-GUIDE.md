# How to Bundle SDKs with Plugin

To create a distributable version of this plugin with all SDKs included:

## Step 1: Install Composer

Download from: https://getcomposer.org/download/

## Step 2: Install Dependencies

Open terminal in the plugin directory and run:

```bash
composer install --no-dev --optimize-autoloader
```

This will:
- Install AWS SDK for PHP
- Install Google Cloud Storage SDK
- Create optimized autoloader
- Skip development dependencies

## Step 3: Verify Installation

Check that these directories exist:
- `vendor/aws/`
- `vendor/google/`
- `vendor/autoload.php`

## Step 4: Create Distribution

Your plugin is now ready! The entire folder (including `vendor/`) can be:
- Zipped and distributed
- Uploaded to WordPress.org
- Committed to GitHub (vendor is no longer ignored)

## File Size

The complete plugin with SDKs will be approximately:
- Plugin code: ~100 KB
- AWS SDK: ~50 MB
- Google Cloud SDK: ~20 MB
- **Total: ~70 MB**

## For End Users

Users who download the bundled version can simply:
1. Upload the plugin ZIP to WordPress
2. Activate the plugin
3. Configure settings - no additional installation needed!

The plugin automatically detects and loads the bundled SDKs.
