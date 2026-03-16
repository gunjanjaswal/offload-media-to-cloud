=== Offload Media to Cloud ===
Contributors: gunjanjaswal
Donate link: https://buymeacoffee.com/gunjanjaswal
Tags: s3, cloud storage, media offload, cdn, performance
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Offload your WordPress media to Amazon S3, DigitalOcean Spaces, or Google Cloud Storage. No Composer or external SDKs required.

== Description ==

**Offload Media to Cloud** automatically transfers your images, videos, documents, and all media files to leading cloud storage providers. Improve your website's performance, reduce hosting costs, and leverage CDN delivery — all with zero manual effort and no external dependencies.

= Key Features =

**Multi-Cloud Provider Support**

* **Amazon S3** — Industry-leading object storage with global infrastructure
* **DigitalOcean Spaces** — S3-compatible storage with predictable, affordable pricing
* **Google Cloud Storage** — Enterprise-grade storage powered by Google's network (via HMAC keys)

**Automatic Media Synchronization**

* New uploads automatically copied to cloud storage in real-time
* All image sizes and thumbnails synced automatically
* Videos, PDFs, documents — all file types supported
* Zero manual intervention required

**Powerful Bulk Migration Tool**

* One-click migration for existing media libraries
* Real-time progress tracking with detailed statistics
* Batch processing for optimal performance
* Comprehensive error reporting

**CDN & Performance Optimization**

* Seamless CloudFront and custom CDN integration
* Automatic URL rewriting for cloud delivery
* Global content delivery for faster load times
* Reduced server bandwidth and hosting costs

**Advanced Configuration**

* Custom path prefix for organized cloud storage
* Optional automatic local file removal
* Restore local files — download cloud media back to server
* Deactivation safety warning when local files are missing
* Secure credential storage
* Built-in connection testing before going live

**Zero Dependencies**

* No Composer required
* No external SDKs or libraries needed
* Works out of the box on any WordPress host
* Uses WordPress built-in HTTP API with secure request signing

= How It Works =

1. **Configure Your Provider** — Enter cloud storage credentials (access key, secret key, bucket, region)
2. **Test Connection** — Verify settings with built-in connection tester
3. **Automatic Sync** — All new uploads automatically copied to cloud storage
4. **Bulk Offload** — Migrate existing media files with one-click bulk tool
5. **Serve from Cloud** — Media URLs automatically rewritten to cloud/CDN URLs

= Supported Cloud Providers =

**Amazon S3**
The industry-standard object storage service with global reach, advanced features, and seamless CloudFront CDN integration.

**DigitalOcean Spaces**
S3-compatible storage with simple, predictable pricing and built-in CDN. Perfect for developers and growing businesses.

**Google Cloud Storage**
Powerful storage infrastructure from Google with multi-regional redundancy and edge caching capabilities. Uses HMAC keys for authentication.

= Perfect For =

* **High-Traffic Websites** — Reduce server load and bandwidth costs
* **Photography & Portfolio Sites** — Store and deliver large image libraries efficiently
* **E-commerce Stores** — Offload product images and improve checkout speed
* **News & Magazine Sites** — Handle extensive media archives with ease
* **Multi-Site Networks** — Centralize media storage across multiple sites

= SEO & Performance Benefits =

* **Faster Page Load Times** — Improve Core Web Vitals and SEO rankings
* **Global CDN Delivery** — Serve content from locations closest to your visitors
* **Mobile Optimization** — Faster image delivery for mobile users
* **Better Search Rankings** — Google rewards faster websites
* **Reduced Bounce Rate** — Keep visitors engaged with quick-loading pages

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Offload Media to Cloud"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Activate the plugin

No additional setup steps, libraries, or Composer required. The plugin works immediately after activation.

= Configuration =

1. Navigate to **Offload Media > Settings** in your WordPress admin
2. Select your storage provider (Amazon S3, DigitalOcean Spaces, or Google Cloud Storage)
3. Enter your credentials:
   * Access Key / Access Key ID
   * Secret Key / Secret Access Key
   * Bucket Name
   * Region
4. (Optional) Configure CDN URL and path prefix
5. Click "Test Connection" to verify your settings
6. Click "Save Settings"

= Google Cloud Storage Setup =

For GCS, you need HMAC keys instead of a service account JSON file:

1. Go to Google Cloud Console > Cloud Storage > Settings
2. Click the "Interoperability" tab
3. Create an HMAC key for your service account
4. Use the Access Key and Secret as your credentials in the plugin

== Frequently Asked Questions ==

= Does this plugin require Composer or external libraries? =

No! The plugin works completely standalone. It uses WordPress's built-in HTTP API with AWS Signature V4 request signing. Just install, activate, and configure.

= Does this plugin upload files directly to cloud storage? =

Files are first uploaded to your WordPress server, then automatically copied to your cloud storage. The process is seamless and happens in the background.

= What happens to my existing media files? =

Existing media files are not automatically migrated. Use the **Bulk Offload** tool under Offload Media > Bulk Offload to migrate existing files with one click.

= Can I remove local files after uploading to cloud storage? =

Yes! Enable the "Remove Local Files" option in settings. Files will be automatically deleted from your server after successful upload to cloud storage.

= Will this work with my CDN (CloudFront, etc.)? =

Absolutely! Enter your CDN URL (CloudFront, KeyCDN, BunnyCDN, etc.) in the "CDN URL" field, and all media will be served through your CDN.

= What if I deactivate the plugin? =

Before deactivating, go to **Offload Media > Restore Local** to download all cloud-stored files back to your server. A warning notice on the Plugins page reminds you if local files are missing. After restoring, WordPress will serve media from your server as normal.

= Does this support video and document files? =

Yes! The plugin supports all file types that WordPress allows in the media library, including images, videos, documents, audio files, and archives.

= Is this compatible with page builders? =

Yes, the plugin works seamlessly with Elementor, Beaver Builder, Divi, WPBakery, Gutenberg, Oxygen, and more.

= Can I use this with WooCommerce? =

Yes! The plugin works perfectly with WooCommerce product images, galleries, and downloadable products.

= Does this work with WordPress Multisite? =

Yes, the plugin is multisite compatible. You can configure different cloud storage settings for each site in your network.

= How much does cloud storage cost? =

Cloud storage is very affordable:

* **Amazon S3**: ~$0.023/GB/month + data transfer
* **DigitalOcean Spaces**: $5/month for 250GB + 1TB transfer
* **Google Cloud Storage**: ~$0.020/GB/month + data transfer

Most small to medium websites pay less than $5-10/month.

= Can I migrate between cloud providers? =

Yes, you can change providers at any time. Update your settings and use the bulk offload tool to re-upload media to the new provider.

= Is my data secure? =

Yes! All credentials are stored securely in your WordPress database. Data is transmitted over HTTPS. Request signing ensures credentials are never sent in plain text.

= How do I get support? =

* Visit the WordPress.org support forum
* Contact the developer: hello@gunjanjaswal.me
* Report bugs on [GitHub](https://github.com/gunjanjaswal/Offload-Images-JS-CSS)

== Screenshots ==

1. Settings page — configure your cloud storage provider and credentials
2. Bulk offload tool — migrate existing media with real-time progress tracking
3. Restore local files — download cloud media back to server before deactivating
4. Connection test — verify your settings before going live
5. Plugin action links — quick access to settings and support

== Changelog ==

= 1.0.0 =
* Initial release
* Support for Amazon S3, DigitalOcean Spaces, and Google Cloud Storage
* No external SDK dependencies — uses built-in HTTP API with request signing
* Automatic upload synchronization for new media
* Bulk offload tool for existing media with batch processing
* CDN integration support (CloudFront, custom domains)
* Built-in connection testing
* Optional local file removal after cloud upload
* Restore local files tool — download cloud media back to server
* Deactivation safety warning when local files are missing
* Custom path prefix support
* Settings and Buy Me a Coffee links on Plugins page

== Upgrade Notice ==

= 1.0.0 =
Initial release. Install and activate — no additional setup required.

== Privacy Policy ==

This plugin does not collect or store any personal data. All cloud storage credentials are stored in your WordPress database and are only used to communicate with your chosen cloud storage provider. No data is sent to third parties except your selected cloud provider.

== Credits ==

Developed by [Gunjan Jaswal](https://www.gunjanjaswal.me)

If you find this plugin helpful, please consider:

* [Buy Me a Coffee](https://buymeacoffee.com/gunjanjaswal)
* Rating this plugin on WordPress.org
* Sharing with other WordPress users
