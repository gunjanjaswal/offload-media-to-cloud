# Offload Media to Cloud

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.0%2B-brightgreen.svg)
![PHP Version](https://img.shields.io/badge/php-7.2%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPLv2-red.svg)

Automatically offload WordPress media to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. No Composer or external SDKs required — just install, activate, and configure.

[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ff813f?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/gunjanjaswal)

## Features

- **Amazon S3** — Industry-standard object storage
- **DigitalOcean Spaces** — S3-compatible with predictable pricing
- **Google Cloud Storage** — Google's infrastructure via HMAC keys
- **Zero Dependencies** — No Composer, no vendor folder, no SDKs
- **Auto Sync** — New uploads automatically copied to cloud storage
- **Bulk Migration** — One-click offload for existing media with progress tracking
- **CDN Support** — CloudFront, custom domains, any CDN
- **URL Rewriting** — Seamless cloud delivery without breaking links
- **Local File Removal** — Optionally delete local files after upload
- **Connection Testing** — Verify credentials before going live

## Requirements

- WordPress 5.0+
- PHP 7.2+
- Cloud storage account (S3, Spaces, or GCS)

No Composer or external libraries needed. Uses WordPress's built-in HTTP API with AWS Signature V4 request signing.

## Installation

1. Download the plugin ZIP or clone this repo
2. Upload to `/wp-content/plugins/offload-media-to-cloud/`
3. Activate through WordPress admin
4. Go to **Offload Media > Settings** and configure your provider

Or install directly from WordPress admin: **Plugins > Add New > Upload Plugin**

## Configuration

### Amazon S3
- Access Key ID, Secret Access Key, Bucket Name, Region (e.g. `us-east-1`)

### DigitalOcean Spaces
- Access Key, Secret Key, Space Name, Region (e.g. `nyc3`)

### Google Cloud Storage
- HMAC Access Key, HMAC Secret Key, Bucket Name
- Create HMAC keys in: GCS Console > Cloud Storage > Settings > Interoperability

### Optional Settings
- **CDN URL** — CloudFront or custom CDN domain
- **Path Prefix** — Organize files in cloud folders
- **Remove Local Files** — Auto-delete after upload

## Bulk Offload

Navigate to **Offload Media > Bulk Offload** to migrate all existing media files to cloud storage with real-time progress tracking.

## File Structure

```
offload-media-to-cloud/
├── assets/
│   ├── css/admin.css
│   └── js/admin.js
├── includes/
│   ├── providers/
│   │   ├── class-provider-base.php
│   │   ├── class-s3-provider.php
│   │   ├── class-spaces-provider.php
│   │   └── class-gcs-provider.php
│   ├── views/
│   │   ├── settings.php
│   │   └── bulk-offload.php
│   ├── class-offload-media-to-cloud.php
│   ├── class-s3-signing.php
│   ├── class-settings.php
│   ├── class-uploader.php
│   ├── class-dependency-checker.php
│   └── class-bulk-offload.php
├── offload-media-to-cloud.php
├── readme.txt
└── README.md
```

## License

GPLv2 or later — [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html)

## Developer

**Gunjan Jaswal** — [gunjanjaswal.me](https://www.gunjanjaswal.me) — [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me)

[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ff813f?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/gunjanjaswal)
