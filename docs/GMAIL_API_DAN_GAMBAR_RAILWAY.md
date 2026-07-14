# Konfigurasi Gmail API dan Gambar Cloudinary di Railway

## Variables Gmail API

Tambahkan pada **Railway → Service Laravel → Variables**:

```env
MAIL_MAILER=gmail-api
GMAIL_CLIENT_ID=isi_client_id_google
GMAIL_CLIENT_SECRET=isi_client_secret_google
GMAIL_REFRESH_TOKEN=isi_refresh_token_google
GMAIL_API_TIMEOUT=20
MAIL_FROM_ADDRESS=jasakampusmarketplace@gmail.com
MAIL_FROM_NAME=JasaKampus
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tafinal-production.up.railway.app
```

`MAIL_FROM_ADDRESS` harus sama dengan akun Gmail yang memberikan izin `gmail.send` di OAuth Playground.

Hapus variable SMTP lama agar tidak digunakan tanpa sengaja:

```text
MAIL_URL
MAIL_SCHEME
MAIL_HOST
MAIL_PORT
MAIL_USERNAME
MAIL_PASSWORD
MAIL_ENCRYPTION
```

## Variables Cloudinary

Tambahkan:

```env
CLOUDINARY_CLOUD_NAME=isi_cloud_name
CLOUDINARY_API_KEY=isi_api_key
CLOUDINARY_API_SECRET=isi_api_secret
```

Semua upload thumbnail jasa, foto profil, dan foto review disimpan sebagai URL HTTPS Cloudinary. File lokal lama yang sudah hilang dari Railway tidak dapat dipulihkan otomatis. Untuk jasa dengan gambar lama yang rusak, masuk sebagai freelancer lalu buka **Jasa Saya → Ganti thumbnail → Simpan Gambar**.

## Setelah mengubah Variables

Redeploy aplikasi. Startup project sudah menjalankan pembersihan cache. Untuk pemeriksaan manual melalui Railway Shell:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:list --name=freelancer.jasa.thumbnail.update
```

## Tes Gmail API

Jalankan:

```bash
php artisan tinker
```

Kemudian:

```php
Mail::raw('Tes Gmail API JasaKampus', function ($message) {
    $message->to('alamat-penerima@gmail.com')
        ->subject('Tes Gmail API');
});
```

Jangan memasukkan Client Secret atau Refresh Token ke GitHub, screenshot, atau chat.
