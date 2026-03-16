<div align="center">

# ☁️ Offload Media to Cloud

### Seamlessly offload WordPress media to cloud storage — zero dependencies, zero hassle.

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B?style=for-the-badge&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2-E74C3C?style=for-the-badge)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.1.0-2ECC71?style=for-the-badge)](https://github.com/gunjanjaswal/offload-media-to-cloud/releases)

<br>

[![Amazon S3](https://img.shields.io/badge/Amazon%20S3-FF9900?style=flat-square&logo=amazons3&logoColor=white)](#-amazon-s3)
[![DigitalOcean](https://img.shields.io/badge/DigitalOcean%20Spaces-0080FF?style=flat-square&logo=digitalocean&logoColor=white)](#-digitalocean-spaces)
[![Google Cloud](https://img.shields.io/badge/Google%20Cloud%20Storage-4285F4?style=flat-square&logo=googlecloud&logoColor=white)](#-google-cloud-storage)

<br>

[![Buy Me a Coffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ff813f?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/gunjanjaswal)

---

</div>

## ✨ Features

<table>
<tr>
<td width="50%">

### 🚀 Core
- **Auto Sync** — New uploads instantly copied to cloud
- **Bulk Migration** — One-click offload with progress tracking
- **Smart Re-link** — Detects existing cloud files, skips re-upload
- **Full URL Rewriting** — Post content, srcset, thumbnails — all covered
- **Auto-Retry** — Resumes on connection drops (up to 5 retries)

</td>
<td width="50%">

### ☁️ Providers
- **Amazon S3** — Industry-standard object storage
- **DigitalOcean Spaces** — S3-compatible, predictable pricing
- **Google Cloud Storage** — Google infrastructure via HMAC keys

</td>
</tr>
<tr>
<td>

### 🛡️ Safety & Recovery
- **Restore Local Files** — Download cloud files back to server
- **Deactivation Warning** — Alert on Plugins page if files are cloud-only
- **Connection Testing** — Verify credentials before going live
- **Local File Removal** — Optional auto-delete after upload

</td>
<td>

### ⚡ Performance
- **CDN Support** — CloudFront, DO CDN, custom domains
- **Zero Dependencies** — No Composer, no vendor folder, no SDKs
- **Lightweight** — Uses WordPress built-in HTTP API
- **AWS Sig V4** — Secure request signing, no keys in transit

</td>
</tr>
</table>

---

## 🔧 Repair & Diagnostic Tools

> **Built-in tools to diagnose and fix common cloud storage issues — no manual database edits needed.**

<table>
<tr>
<td width="33%" align="center">

### 🔐 Fix Permissions
Scans all offloaded files via HEAD requests. Files returning **403 AccessDenied** or other errors are listed with a one-click fix that sets `public-read` ACL or re-uploads with correct permissions.

**Offload Media > Fix Permissions**

</td>
<td width="33%" align="center">

### 🖼️ Fix Thumbnails
Finds offloaded images where **thumbnail sizes are missing** from the cloud. Common after bulk offload interruptions or when WordPress generates new image sizes. Uploads missing thumbnails and stores their cloud URLs.

**Offload Media > Fix Thumbnails**

</td>
<td width="33%" align="center">

### 🔗 Fix URLs
Detects when your **CDN URL, bucket, or region** settings changed but stored media URLs still point to the old location. Bulk-updates all URLs to match current settings — **no re-uploading needed**.

**Offload Media > Fix URLs**

</td>
</tr>
</table>

Each tool follows a **Scan → Review → Fix** workflow with real-time progress tracking, batch processing, and detailed error reporting.

---

## 📋 Requirements

| Requirement | Minimum |
|------------|---------|
| WordPress | 5.0+ |
| PHP | 7.2+ |
| Cloud Account | S3, Spaces, or GCS |

> **No Composer or external libraries needed.** Uses WordPress's built-in HTTP API with AWS Signature V4 request signing.

---

## 🔧 Installation

```bash
# Clone the repo
git clone https://github.com/gunjanjaswal/offload-media-to-cloud.git

# Copy to your WordPress plugins directory
cp -r offload-media-to-cloud /path/to/wp-content/plugins/
```

**Or via WordPress admin:**
1. **Plugins > Add New > Upload Plugin**
2. Upload the ZIP file
3. Activate
4. Go to **Offload Media > Settings**

---

## ⚙️ Configuration

### <img src="https://img.shields.io/badge/-Amazon%20S3-FF9900?style=flat-square&logo=amazons3&logoColor=white" alt="S3">

| Field | Example |
|-------|---------|
| Access Key ID | `AKIAIOSFODNN7EXAMPLE` |
| Secret Access Key | `wJalrXUtnFEMI/K7MDENG/...` |
| Bucket Name | `my-media-bucket` |
| Region | `us-east-1` |

### <img src="https://img.shields.io/badge/-DigitalOcean%20Spaces-0080FF?style=flat-square&logo=digitalocean&logoColor=white" alt="DO">

| Field | Example |
|-------|---------|
| Access Key | `DO00XXXXXXXXXXXXXXXXXX` |
| Secret Key | `your-secret-key` |
| Space Name | `my-space` |
| Region | `nyc3`, `sfo3`, `sgp1` |

### <img src="https://img.shields.io/badge/-Google%20Cloud%20Storage-4285F4?style=flat-square&logo=googlecloud&logoColor=white" alt="GCS">

| Field | Example |
|-------|---------|
| HMAC Access Key | `GOOGXXXXXXXXXXXXXXXXX` |
| HMAC Secret Key | `your-hmac-secret` |
| Bucket Name | `my-gcs-bucket` |

> Create HMAC keys in: **GCS Console > Cloud Storage > Settings > Interoperability**

### Optional Settings

| Setting | Description |
|---------|-------------|
| 🌐 **CDN URL** | CloudFront, DO CDN, or custom domain for faster delivery |
| 📁 **Path Prefix** | Organize files in cloud folders (e.g. `wp-uploads`) |
| 🗑️ **Remove Local Files** | Auto-delete from server after successful cloud upload |

---

## 🔄 How It Works

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│   WordPress   │     │    Plugin     │     │    Cloud     │
│   Upload      │────▶│   Auto Sync   │────▶│   Storage    │
│               │     │               │     │  (S3/DO/GCS) │
└──────────────┘     └──────────────┘     └──────────────┘
                            │
                            ▼
                     ┌──────────────┐
                     │  URL Rewrite  │
                     │  ─────────── │
                     │  Post Content │
                     │  Srcset URLs  │
                     │  Thumbnails   │
                     │  CDN Delivery │
                     └──────────────┘
```

1. **Upload** — Media uploaded to WordPress as usual
2. **Sync** — Plugin automatically copies to cloud storage
3. **Rewrite** — All URLs rewritten to cloud/CDN endpoints
4. **Serve** — Images delivered from cloud, not your server
5. *(Optional)* **Cleanup** — Local files removed to save disk space

---

## 📦 Admin Menu Pages

| Page | Description |
|------|-------------|
| **Settings** | Configure provider, credentials, CDN, path prefix, local file removal |
| **Bulk Offload** | Migrate entire media library to cloud with progress tracking & auto-retry |
| **Restore Local** | Download cloud files back to server before deactivating |
| **Fix Permissions** | Scan for 403/404 errors and set public-read ACL |
| **Fix Thumbnails** | Find & upload missing thumbnail sizes to cloud |
| **Fix URLs** | Update stale URLs after CDN/bucket/region config changes |

---

## 🏗️ Architecture

```
offload-media-to-cloud/
│
├── 📄 offload-media-to-cloud.php    ← Plugin entry point
│
├── 📁 includes/
│   ├── 🔧 class-offload-media-to-cloud.php  ← Core orchestrator
│   ├── ⚙️ class-settings.php                ← Settings & AJAX handlers
│   ├── ☁️ class-uploader.php                ← Auto-sync new uploads
│   ├── 📦 class-bulk-offload.php            ← Bulk migration
│   ├── ⬇️ class-bulk-restore.php            ← Bulk restore
│   ├── 🔐 class-fix-permissions.php         ← Scan & fix ACL
│   ├── 🖼️ class-fix-thumbnails.php          ← Fix missing thumbs
│   ├── 🔗 class-fix-urls.php                ← Fix URL mismatches
│   ├── 🔏 class-s3-signing.php              ← AWS Sig V4 signing
│   ├── ✅ class-dependency-checker.php       ← PHP requirements
│   │
│   ├── 📁 providers/
│   │   ├── 🏗️ class-provider-base.php       ← Abstract base
│   │   ├── 🟠 class-s3-provider.php         ← Amazon S3
│   │   ├── 🔵 class-spaces-provider.php     ← DigitalOcean Spaces
│   │   └── 🔴 class-gcs-provider.php        ← Google Cloud Storage
│   │
│   └── 📁 views/
│       ├── 🖥️ settings.php                  ← Settings page UI
│       ├── 📦 bulk-offload.php              ← Bulk offload UI
│       ├── ⬇️ bulk-restore.php              ← Bulk restore UI
│       ├── 🔐 fix-permissions.php           ← Fix permissions UI
│       ├── 🖼️ fix-thumbnails.php            ← Fix thumbnails UI
│       └── 🔗 fix-urls.php                  ← Fix URLs UI
│
├── 📁 assets/
│   ├── 🎨 css/admin.css                     ← Modern admin styles
│   └── ⚡ js/admin.js                       ← Admin functionality
│
├── 📄 readme.txt                             ← WordPress.org readme
└── 📄 README.md                              ← You are here
```

---

## 🔒 Security

- All credentials stored securely in WordPress database
- Data transmitted over **HTTPS** only
- **AWS Signature V4** request signing — credentials never sent in plain text
- AJAX endpoints protected with WordPress nonce verification
- Capability checks (`manage_options`) on all admin actions
- Input sanitization on all user inputs

---

## 🤝 Compatibility

| | Supported |
|---|-----------|
| **Page Builders** | Elementor, Beaver Builder, Divi, WPBakery, Gutenberg, Oxygen |
| **E-commerce** | WooCommerce product images, galleries, downloadable products |
| **Multisite** | Full multisite support with per-site configuration |
| **CDNs** | CloudFront, BunnyCDN, KeyCDN, DigitalOcean CDN, custom domains |

---

## 💰 Cloud Storage Pricing

| Provider | Storage | Transfer |
|----------|---------|----------|
| **Amazon S3** | ~$0.023/GB/month | ~$0.09/GB |
| **DigitalOcean Spaces** | $5/month for 250GB | 1TB included |
| **Google Cloud Storage** | ~$0.020/GB/month | ~$0.12/GB |

> Most small to medium WordPress sites pay **less than $5–10/month**.

---

## 📄 License

This project is licensed under the **GPLv2 or later** — see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for details.

---

<div align="center">

### 👨‍💻 Developer

**Gunjan Jaswal**

[![Website](https://img.shields.io/badge/Website-gunjanjaswal.me-667eea?style=for-the-badge&logo=google-chrome&logoColor=white)](https://www.gunjanjaswal.me)
[![Email](https://img.shields.io/badge/Email-hello%40gunjanjaswal.me-EA4335?style=for-the-badge&logo=gmail&logoColor=white)](mailto:hello@gunjanjaswal.me)
[![GitHub](https://img.shields.io/badge/GitHub-gunjanjaswal-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/gunjanjaswal)

---

**If this plugin saves you time, consider supporting its development:**

[![Buy Me a Coffee](https://img.shields.io/badge/☕%20Buy%20Me%20a%20Coffee-ff813f?style=for-the-badge&logo=buy-me-a-coffee&logoColor=white)](https://buymeacoffee.com/gunjanjaswal)

⭐ **Star this repo** if you find it useful!

</div>
