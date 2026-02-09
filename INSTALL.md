# Offload Images JS CSS

A modern WordPress plugin for offloading media files to cloud storage.

## Quick Start

### 1. Install Dependencies

```bash
cd wp-content/plugins/offload-images-js-css
composer install
```

This installs both AWS SDK (for S3/Spaces) and Google Cloud SDK.

### 2. Activate Plugin

Activate the plugin in WordPress admin.

### 3. Configure

Go to **Offload Media > Settings** and configure your cloud provider.

### 4. Bulk Offload (Optional)

Use **Offload Media > Bulk Offload** to migrate existing media.

## For Distribution

To bundle SDKs with the plugin:

```bash
composer install --no-dev --optimize-autoloader
```

Then zip the entire folder for distribution.

## Developer

**Gunjan Jaswal**
- Email: hello@gunjanjaswal.me
- Website: www.gunjanjaswal.me
- Support: https://buymeacoffee.com/gunjanjaswal

## License

GPLv2 or later
