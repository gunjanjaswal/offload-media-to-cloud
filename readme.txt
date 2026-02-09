=== Offload Images JS CSS - Cloud Media Storage for WordPress ===
Contributors: gunjanjaswal
Donate link: https://buymeacoffee.com/gunjanjaswal
Tags: s3, amazon s3, digitalocean spaces, google cloud storage, cdn, media offload, cloud storage, performance, optimization, images, videos, aws, gcs, cloudfront, media library, backup, storage, upload, bandwidth, speed
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically offload WordPress media to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. Boost performance, reduce server costs, and serve media via CDN with automatic sync and bulk migration.

== Description ==

**Offload Images JS CSS** is the ultimate WordPress media offloading solution that automatically transfers your images, videos, documents, and all media files to leading cloud storage providers. Dramatically improve your website's performance, reduce hosting costs, and leverage enterprise-grade CDN delivery - all with zero manual effort.

= 🚀 Supercharge Your WordPress Performance =

Stop letting heavy media files slow down your website. This plugin automatically offloads your entire WordPress media library to cloud storage, freeing up server resources and delivering content at lightning speed through global CDN networks.

= ⭐ Key Features =

**Multi-Cloud Provider Support**
* ☁️ **Amazon S3** - Industry-leading object storage with global infrastructure
* 🌊 **DigitalOcean Spaces** - S3-compatible storage with predictable, affordable pricing
* 📦 **Google Cloud Storage** - Enterprise-grade storage powered by Google's network

**Automatic Media Synchronization**
* ✅ New uploads automatically copied to cloud storage in real-time
* ✅ All image sizes and thumbnails synced automatically
* ✅ Videos, PDFs, documents - all file types supported
* ✅ Zero manual intervention required

**Powerful Bulk Migration Tool**
* 📦 One-click migration for existing media libraries
* 📊 Real-time progress tracking with detailed statistics
* ⚡ Batch processing for optimal performance
* 🔍 Comprehensive error reporting and retry logic

**CDN & Performance Optimization**
* 🚀 Seamless CloudFront and custom CDN integration
* ⚡ Automatic URL rewriting for cloud delivery
* 🌍 Global content delivery for faster load times
* 📉 Reduced server bandwidth and hosting costs

**Advanced Configuration**
* 🗂️ Custom path prefix for organized cloud storage
* 🗑️ Optional automatic local file removal
* 🔐 Secure credential storage and encryption
* ✔️ Built-in connection testing before going live

= 💡 Why Choose This Plugin? =

**Performance Optimization**
Offload media delivery to cloud infrastructure designed for speed and scalability. Reduce server load, improve page load times, and provide a better user experience for your visitors.

**Cost Efficiency**
Cloud storage is often more affordable than traditional hosting for large media libraries. Pay only for what you use, with no upfront costs or long-term commitments.

**Reliability & Uptime**
Leverage enterprise-grade infrastructure from AWS, DigitalOcean, or Google Cloud with 99.99% uptime SLAs and automatic redundancy.

**Scalability**
Handle traffic spikes effortlessly. Cloud storage scales automatically to meet demand, ensuring your media is always available.

= 🎯 Perfect For =

* **High-Traffic Websites** - Reduce server load and bandwidth costs
* **Photography & Portfolio Sites** - Store and deliver large image libraries efficiently
* **E-commerce Stores** - Offload product images and improve checkout speed
* **News & Magazine Sites** - Handle extensive media archives with ease
* **Membership Sites** - Deliver protected content via cloud storage
* **Video Platforms** - Stream videos from cloud infrastructure
* **Multi-Site Networks** - Centralize media storage across multiple sites
* **Agency & Developer Projects** - Professional solution for client websites

= 🔧 How It Works =

1. **Configure Your Provider** - Enter cloud storage credentials (access key, secret key, bucket, region)
2. **Test Connection** - Verify settings with built-in connection tester
3. **Automatic Sync** - All new uploads automatically copied to cloud storage
4. **Bulk Offload** - Migrate existing media files with one-click bulk tool
5. **Serve from Cloud** - Media URLs automatically rewritten to cloud/CDN URLs

= 🌟 Supported Cloud Providers =

**Amazon S3**
The industry-standard object storage service with global reach, advanced features, and seamless CloudFront CDN integration.

**DigitalOcean Spaces**
S3-compatible storage with simple, predictable pricing and built-in CDN. Perfect for developers and growing businesses.

**Google Cloud Storage**
Powerful storage infrastructure from Google with multi-regional redundancy and edge caching capabilities.

= 📈 SEO & Performance Benefits =

* ⚡ **Faster Page Load Times** - Improve Core Web Vitals and SEO rankings
* 🌍 **Global CDN Delivery** - Serve content from locations closest to your visitors
* 📱 **Mobile Optimization** - Faster image delivery for mobile users
* 🔍 **Better Search Rankings** - Google rewards faster websites
* 💰 **Reduced Bounce Rate** - Keep visitors engaged with quick-loading pages

= Developer Information =

* **Developer:** Gunjan Jaswal
* **Email:** hello@gunjanjaswal.me
* **Website:** [www.gunjanjaswal.me](https://www.gunjanjaswal.me)
* **Support:** [Buy Me a Coffee](https://buymeacoffee.com/gunjanjaswal)

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Offload Images JS CSS"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Activate the plugin

= Configuration =

1. Navigate to **Offload Media > Settings** in your WordPress admin
2. Select your storage provider (Amazon S3, DigitalOcean Spaces, or Google Cloud Storage)
3. Enter your credentials:
   - Access Key / Access Key ID
   - Secret Key / Secret Access Key
   - Bucket Name
   - Region
4. (Optional) Configure CDN URL and path prefix
5. Click "Test Connection" to verify your settings
6. Click "Save Settings"

= Installing Required Libraries =

For the plugin to work, you need to install the appropriate SDK:

**For Amazon S3 and DigitalOcean Spaces:**
Install AWS SDK for PHP via Composer in your WordPress root or plugin directory:
`composer require aws/aws-sdk-php`

**For Google Cloud Storage:**
Install Google Cloud Storage PHP library via Composer:
`composer require google/cloud-storage`

== Frequently Asked Questions ==

= Does this plugin upload files directly to cloud storage? =

No, files are first uploaded to your WordPress server, then automatically copied to your cloud storage. This is a WordPress limitation, not a plugin limitation. However, the process is seamless and happens in the background.

= What happens to my existing media files? =

Existing media files are not automatically migrated. Use the **Bulk Offload** tool under Offload Media > Bulk Offload to migrate existing files with one click. The tool shows real-time progress and handles thousands of files efficiently.

= Can I remove local files after uploading to cloud storage? =

Yes! Enable the "Remove Local Files" option in settings. Files will be automatically deleted from your server after successful upload to cloud storage, freeing up valuable server space.

= Will this work with my CDN (CloudFront, etc.)? =

Absolutely! Enter your CDN URL (CloudFront, KeyCDN, BunnyCDN, etc.) in the "CDN URL" field, and all media will be served through your CDN for maximum performance.

= What if I deactivate the plugin? =

If you've removed local files, your media will continue to be served from cloud storage. If you want to restore local files, you'll need to download them from your cloud storage manually or use a backup.

= Does this support video and document files? =

Yes! The plugin supports all file types that WordPress allows in the media library, including:
* Images (JPG, PNG, GIF, WebP, SVG)
* Videos (MP4, MOV, AVI, WebM)
* Documents (PDF, DOC, DOCX, XLS, XLSX)
* Audio files (MP3, WAV, OGG)
* Archives (ZIP, RAR)

= Is this compatible with page builders? =

Yes, the plugin works seamlessly with popular page builders including:
* Elementor
* Beaver Builder
* Divi
* WPBakery
* Gutenberg
* Oxygen
* And more!

= Will this improve my website's SEO? =

Yes! Faster page load times (a key ranking factor) are achieved by:
* Offloading media delivery to fast cloud infrastructure
* Leveraging global CDN networks
* Reducing server response times
* Improving Core Web Vitals scores

= How much does cloud storage cost? =

Cloud storage is very affordable:
* **Amazon S3**: ~$0.023/GB/month + data transfer
* **DigitalOcean Spaces**: $5/month for 250GB + 1TB transfer
* **Google Cloud Storage**: ~$0.020/GB/month + data transfer

Most small to medium websites pay less than $5-10/month.

= Can I use this with WooCommerce? =

Yes! The plugin works perfectly with WooCommerce product images, galleries, and downloadable products. Offload your product media to improve store performance.

= Does this work with WordPress Multisite? =

Yes, the plugin is multisite compatible. You can configure different cloud storage settings for each site in your network.

= What happens if cloud storage goes down? =

Major cloud providers (AWS, DigitalOcean, Google) have 99.99% uptime SLAs. In the rare event of downtime, you can temporarily disable the plugin to serve from local backups.

= Can I migrate between cloud providers? =

Yes, you can change providers at any time. Simply update your settings and use the bulk offload tool to re-upload media to the new provider.

= How do I get support? =

For support:
* Check the plugin documentation
* Visit the WordPress.org support forum
* Contact the developer: hello@gunjanjaswal.me
* Report bugs on GitHub

= Is my data secure? =

Yes! All credentials are stored securely in your WordPress database using WordPress's built-in security features. Data is transmitted over HTTPS, and you can enable encryption at rest in your cloud provider settings.

== Screenshots ==

1. Settings page with provider configuration
2. Bulk offload tool with progress tracking
3. Connection test results
4. Media library showing offloaded files

== Changelog ==

= 1.0.0 =
* Initial release
* Support for Amazon S3, DigitalOcean Spaces, and Google Cloud Storage
* Automatic upload synchronization
* Bulk offload tool for existing media
* CDN integration support
* Connection testing
* Optional local file removal
* Custom path prefix support

== Upgrade Notice ==

= 1.0.0 =
Initial release of Offload Images JS CSS plugin.

== Support the Developer ==

If you find this plugin helpful, please consider supporting its development:

* [Buy Me a Coffee](https://buymeacoffee.com/gunjanjaswal)
* Rate this plugin on WordPress.org
* Share with other WordPress users

== Privacy Policy ==

This plugin does not collect or store any personal data. All cloud storage credentials are stored securely in your WordPress database and are only used to communicate with your chosen cloud storage provider.

== Credits ==

Developed with ❤️ by Gunjan Jaswal
