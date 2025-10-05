# دليل النشر والتثبيت - نظام تبيان 🚀

## 📋 متطلبات الخادم

### الحد الأدنى للمتطلبات:
- PHP 8.1+ مع الإضافات التالية:
  - BCMath PHP Extension
  - Ctype PHP Extension
  - Fileinfo PHP extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension
  - GD PHP Extension
  - ZIP PHP Extension

- MySQL 8.0+ أو MariaDB 10.3+
- Nginx أو Apache
- Composer 2.x
- Node.js 16+ (للبناء فقط)

### المتطلبات الموصى بها:
- 2GB RAM كحد أدنى، 4GB+ موصى به
- 10GB مساحة تخزين كحد أدنى
- SSL Certificate
- Backup نظام

## 🔧 التثبيت على الخادم

### 1. رفع الملفات
```bash
# رفع المشروع إلى الخادم
scp -r tibyan-system-complete/ user@server:/var/www/
```

### 2. إعداد الصلاحيات
```bash
cd /var/www/tibyan-system-complete
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

### 3. تثبيت التبعيات
```bash
composer install --optimize-autoloader --no-dev
```

### 4. إعداد البيئة
```bash
cp .env.example .env
php artisan key:generate
```

### 5. تحديث ملف .env
```bash
nano .env
```
```env
APP_NAME="نظام تبيان"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tibyan_production
DB_USERNAME=tibyan_user
DB_PASSWORD=secure_password

# إعدادات الايمل
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### 6. إعداد قاعدة البيانات
```bash
# إنشاء قاعدة البيانات
mysql -u root -p
CREATE DATABASE tibyan_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tibyan_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON tibyan_production.* TO 'tibyan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# تنفيذ الهجرات
php artisan migrate --force
php artisan db:seed --force
```

### 7. إعداد التخزين
```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🌐 إعداد Nginx

### ملف الإعداد: `/etc/nginx/sites-available/tibyan`
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/tibyan-system-complete/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # إعدادات الملفات الكبيرة
    client_max_body_size 100M;
}
```

### تفعيل الموقع
```bash
sudo ln -s /etc/nginx/sites-available/tibyan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 🔒 إعداد SSL

### استخدام Let's Encrypt
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

## 📊 مراقبة النظام

### إعداد مراقبة MySQL
```sql
-- إنشاء مستخدم للمراقبة
CREATE USER 'monitor'@'localhost' IDENTIFIED BY 'monitor_password';
GRANT PROCESS, REPLICATION CLIENT ON *.* TO 'monitor'@'localhost';
```

### إعداد Cron Jobs
```bash
sudo crontab -e
```
```cron
# تنظيف اللوجز اليومي
0 2 * * * cd /var/www/tibyan-system-complete && php artisan schedule:run >> /dev/null 2>&1

# نسخ احتياطي يومي
0 3 * * * /path/to/backup-script.sh

# تحديث cache كل ساعة
0 * * * * cd /var/www/tibyan-system-complete && php artisan config:cache >> /dev/null 2>&1
```

## 💾 النسخ الاحتياطي

### سكريبت النسخ الاحتياطي: `backup-script.sh`
```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/tibyan"
PROJECT_DIR="/var/www/tibyan-system-complete"

# إنشاء مجلد النسخ
mkdir -p $BACKUP_DIR

# نسخ قاعدة البيانات
mysqldump -u tibyan_user -psecure_password tibyan_production > $BACKUP_DIR/database_$DATE.sql

# نسخ الملفات
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $PROJECT_DIR storage/app/public

# نسخ الإعدادات
cp $PROJECT_DIR/.env $BACKUP_DIR/env_$DATE.backup

# حذف النسخ القديمة (أكثر من 30 يوم)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
find $BACKUP_DIR -name "*.backup" -mtime +30 -delete

echo "تم إنجاز النسخ الاحتياطي في $DATE"
```

## 🚨 استكشاف الأخطاء

### مشاكل شائعة وحلولها:

#### 1. خطأ 500 Internal Server Error
```bash
# فحص اللوجز
tail -f /var/log/nginx/error.log
tail -f /var/www/tibyan-system-complete/storage/logs/laravel.log

# فحص الصلاحيات
sudo chown -R www-data:www-data /var/www/tibyan-system-complete/storage
sudo chmod -R 775 /var/www/tibyan-system-complete/storage
```

#### 2. مشكلة قاعدة البيانات
```bash
# فحص الاتصال
php artisan tinker
DB::connection()->getPdo();

# إعادة تشغيل MySQL
sudo systemctl restart mysql
```

#### 3. مشكلة الذاكرة
```bash
# زيادة memory_limit في PHP
sudo nano /etc/php/8.1/fpm/php.ini
# memory_limit = 512M

sudo systemctl restart php8.1-fpm
```

## 📈 تحسين الأداء

### 1. إعداد Redis للكاش
```bash
sudo apt install redis-server
```

```env
# في ملف .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 2. إعداد Queue Worker
```bash
# إنشاء خدمة systemd
sudo nano /etc/systemd/system/tibyan-worker.service
```

```ini
[Unit]
Description=Tibyan Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/tibyan-system-complete
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable tibyan-worker
sudo systemctl start tibyan-worker
```

## 🔄 التحديثات

### تحديث النظام
```bash
cd /var/www/tibyan-system-complete

# نسخ احتياطي
./backup-script.sh

# تحديث الكود
git pull origin main

# تحديث التبعيات
composer install --optimize-autoloader --no-dev

# تنفيذ الهجرات الجديدة
php artisan migrate --force

# مسح الكاش
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# إعادة بناء الكاش
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📞 الدعم الفني

في حالة مواجهة مشاكل في النشر:

1. فحص ملفات اللوجز أولاً
2. التأكد من متطلبات الخادم
3. مراجعة إعدادات قاعدة البيانات
4. التواصل مع فريق الدعم الفني

---
**مع تمنياتنا بنشر ناجح وآمن! 🎉**