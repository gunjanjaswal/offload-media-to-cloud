# Offload Images JS CSS

![WordPress Plugin Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![WordPress Compatibility](https://img.shields.io/badge/wordpress-5.0%2B-brightgreen.svg)
![PHP Version](https://img.shields.io/badge/php-7.2%2B-purple.svg)
![License](https://img.shields.io/badge/license-GPLv2-red.svg)

> Seamlessly transfer and serve your WordPress media files from Amazon S3, DigitalOcean Spaces, or Google Cloud Storage with automatic sync and one-click bulk migration.

## 🚀 Features

### Multi-Cloud Provider Support
- **Amazon S3** - Industry-standard object storage
- **DigitalOcean Spaces** - S3-compatible with predictable pricing
- **Google Cloud Storage** - Google's powerful infrastructure

### Automatic Synchronization
- ✅ New uploads automatically copied to cloud storage
- ✅ All image thumbnails synced automatically
- ✅ URL rewriting for seamless cloud delivery
- ✅ Optional local file removal after upload

### Bulk Migration Tool
- 📦 One-click migration for existing media files
- 📊 Real-time progress tracking
- ⚡ Batch processing for optimal performance
- 🔍 Detailed error reporting

### Advanced Configuration
- 🌐 CDN integration (CloudFront, custom domains)
- 📁 Custom path prefix support
- 🔐 Secure credential storage
- ✔️ Built-in connection testing

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- Cloud storage account (Amazon S3, DigitalOcean Spaces, or Google Cloud Storage)
- Composer (for installing required SDKs)

## 🔧 Installation

### Via WordPress Admin

1. Download the plugin ZIP file
2. Navigate to **Plugins > Add New > Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Activate the plugin

### Manual Installation

1. Clone or download this repository
2. Upload the `offload-images-js-css` folder to `/wp-content/plugins/`
3. Activate the plugin through the WordPress admin panel

### Install Required SDKs

Navigate to your WordPress root directory and install the required SDK:

**For Amazon S3 / DigitalOcean Spaces:**
```bash
cd wp-content/plugins/offload-images-js-css
composer install
```

This will install both AWS SDK and Google Cloud Storage SDK automatically.

**Don't have Composer?**
1. Download from [getcomposer.org](https://getcomposer.org/)
2. Install it on your system
3. Run the command above

**For Plugin Developers:**
To create a distributable version with SDKs bundled:
```bash
composer install --no-dev --optimize-autoloader
```

Then zip the entire plugin folder. Users can install without running Composer.

See [SDK-INSTALLATION.md](SDK-INSTALLATION.md) for detailed instructions.

## ⚙️ Configuration

### 1. Access Settings
Navigate to **Offload Media > Settings** in your WordPress admin panel.

### 2. Choose Your Provider
Select from:
- Amazon S3
- DigitalOcean Spaces
- Google Cloud Storage

### 3. Enter Credentials

**For Amazon S3:**
- Access Key ID
- Secret Access Key
- Bucket Name
- Region (e.g., `us-east-1`)

**For DigitalOcean Spaces:**
- Access Key
- Secret Key
- Space Name (bucket)
- Region (e.g., `nyc3`)

**For Google Cloud Storage:**
- Service Account Key File Path
- Bucket Name
- Region (e.g., `us-central1`)

### 4. Optional Settings
- **CDN URL**: Enter your CloudFront or custom CDN domain
- **Path Prefix**: Organize files with a custom folder structure
- **Remove Local Files**: Automatically delete files from server after upload

### 5. Test & Save
Click **Test Connection** to verify your settings, then **Save Settings**.

## 📦 Bulk Offload Existing Media

1. Navigate to **Offload Media > Bulk Offload**
2. Review the count of media files to be offloaded
3. Click **Start Bulk Offload**
4. Monitor progress in real-time
5. Review any errors if they occur

## 🎯 Use Cases

- **High-Traffic Websites** - Reduce server load and bandwidth costs
- **Photography Sites** - Store and deliver large image libraries efficiently
- **E-commerce Stores** - Offload product images to improve performance
- **News & Magazines** - Handle extensive media archives with ease
- **Multi-Site Networks** - Centralize media storage across multiple sites

## 🔒 Security

- All credentials are stored securely in your WordPress database
- No data is sent to third parties (except your chosen cloud provider)
- Supports IAM roles and service accounts for enhanced security
- Optional encryption in transit and at rest (provider-dependent)

## 🛠️ Technical Details

### File Structure
```
offload-images-js-css/
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── providers/
│   │   ├── class-provider-base.php
│   │   ├── class-s3-provider.php
│   │   ├── class-spaces-provider.php
│   │   └── class-gcs-provider.php
│   ├── views/
│   │   ├── settings.php
│   │   └── bulk-offload.php
│   ├── class-offload-images-js-css.php
│   ├── class-settings.php
│   ├── class-uploader.php
│   └── class-bulk-offload.php
├── offload-images-js-css.php
├── readme.txt
└── README.md
```

### Hooks & Filters

**Actions:**
- `oijc_before_upload` - Fires before uploading to cloud storage
- `oijc_after_upload` - Fires after successful upload
- `oijc_before_delete` - Fires before deleting from cloud storage

**Filters:**
- `oijc_remote_path` - Modify the remote file path
- `oijc_file_url` - Modify the returned file URL
- `oijc_upload_args` - Modify upload arguments

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the GPLv2 or later - see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for details.

## 👨‍💻 Developer

**Gunjan Jaswal**

- 🌐 Website: [www.gunjanjaswal.me](https://www.gunjanjaswal.me)
- 📧 Email: [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me)
- ☕ Support: [Buy Me a Coffee](https://buymeacoffee.com/gunjanjaswal)

## 💖 Support

If you find this plugin helpful, please consider:

- ⭐ Starring this repository
- ☕ [Buying me a coffee](https://buymeacoffee.com/gunjanjaswal)
- 📢 Sharing with other WordPress users
- 💬 Leaving a review on WordPress.org

## 📚 Documentation

For detailed documentation, visit the [Wiki](https://github.com/gunjanjaswal/Offload-Images-JS-CSS/wiki) (coming soon).

## 🐛 Bug Reports

Found a bug? Please [open an issue](https://github.com/gunjanjaswal/Offload-Images-JS-CSS/issues) with detailed information.

## 🗺️ Roadmap

- [ ] Support for additional cloud providers (Azure, Backblaze B2)
- [ ] Advanced image optimization before upload
- [ ] Automatic backup and restore functionality
- [ ] Multi-site network support enhancements
- [ ] WP-CLI commands for bulk operations
- [ ] Integration with popular backup plugins

---

**Made with ❤️ by Gunjan Jaswal**
