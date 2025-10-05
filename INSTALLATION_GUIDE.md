# 🚀 دليل تثبيت وتشغيل نظام تبيان

## 📋 المتطلبات

### متطلبات الخادم
- PHP 8.1 أو أحدث
- Composer
- Node.js & NPM
- MySQL 8.0 أو أحدث
- Apache/Nginx

### ملحقات PHP المطلوبة
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- GD أو Imagick

## 🛠️ خطوات التثبيت

### 1. تحميل المشروع
```bash
# إذا كان لديك Git
git clone [repository-url] tibyan-system
cd tibyan-system

# أو فك ضغط الملف المرفق
unzip tibyan-system.zip
cd tibyan-system
```

### 2. تثبيت Dependencies
```bash
# تثبيت حزم PHP
composer install

# تثبيت حزم JavaScript
npm install
npm run build
```

### 3. إعداد البيئة
```bash
# نسخ ملف البيئة
cp .env.example .env

# توليد مفتاح التطبيق
php artisan key:generate
```

### 4. إعداد قاعدة البيانات

#### تحرير ملف .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tibyan_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### إنشاء قاعدة البيانات
```sql
CREATE DATABASE tibyan_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### تشغيل المايجريشن
```bash
php artisan migrate
```

### 5. إعداد التخزين
```bash
# إنشاء رابط التخزين العام
php artisan storage:link

# تعيين الصلاحيات
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 6. إنشاء المستخدم الرئيسي
```bash
php artisan tinker
```

```php
// في Tinker
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'المدير العام',
    'email' => 'admin@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
    'is_active' => true
]);
```

### 7. تشغيل الخادم
```bash
# للتطوير
php artisan serve

# الرابط: http://localhost:8000
```

## ⚙️ الإعدادات الإضافية

### إعداد البريد الإلكتروني
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### إعداد الملفات
```env
FILESYSTEM_DISK=public
```

### إعداد المنطقة الزمنية
```env
APP_TIMEZONE=Asia/Riyadh
```

## 🔧 إعداد الخادم للإنتاج

### Apache Configuration
```apache
<VirtualHost *:80>
    DocumentRoot /path/to/tibyan-system/public
    ServerName yourdomain.com
    
    <Directory /path/to/tibyan-system/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/tibyan-system/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### إعدادات الأمان
```bash
# تعيين المالك
chown -R www-data:www-data /path/to/tibyan-system

# الصلاحيات الآمنة
find /path/to/tibyan-system -type f -exec chmod 644 {} \;
find /path/to/tibyan-system -type d -exec chmod 755 {} \;
chmod -R 775 /path/to/tibyan-system/storage
chmod -R 775 /path/to/tibyan-system/bootstrap/cache
```

## 📊 إعداد قاعدة البيانات للإنتاج

### تحسين الأداء
```sql
-- في MySQL
SET GLOBAL innodb_buffer_pool_size = 1G;
SET GLOBAL query_cache_size = 256M;
SET GLOBAL max_connections = 200;
```

### النسخ الاحتياطي التلقائي
```bash
# إضافة إلى crontab
0 2 * * * /usr/bin/mysqldump -u username -p'password' tibyan_system > /backups/tibyan_$(date +\%Y\%m\%d).sql
```

## 🔄 مهام الجدولة (Cron Jobs)

```bash
# تحرير crontab
crontab -e

# إضافة هذا السطر
* * * * * cd /path/to/tibyan-system && php artisan schedule:run >> /dev/null 2>&1
```

## 🚨 استكشاف الأخطاء

### مشاكل شائعة وحلولها

#### خطأ "Class not found"
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

#### مشاكل الصلاحيات
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

#### مشاكل قاعدة البيانات
```bash
php artisan migrate:fresh
php artisan db:seed
```

#### مشاكل الملفات المرفوعة
```bash
php artisan storage:link
```

## 📝 تسجيل الدخول الأول

1. اذهب إلى: `http://yourdomain.com/login`
2. استخدم البيانات:
   - البريد الإلكتروني: `admin@example.com`
   - كلمة المرور: `password123`

**⚠️ مهم:** قم بتغيير كلمة المرور فور تسجيل الدخول الأول!

## 🔐 إعدادات الأمان

### تفعيل HTTPS
```bash
# باستخدام Let's Encrypt
sudo certbot --apache -d yourdomain.com
```

### إعداد Firewall
```bash
sudo ufw allow ssh
sudo ufw allow 'Apache Full'
sudo ufw enable
```

### تحديث .env للإنتاج
```env
APP_ENV=production
APP_DEBUG=false
```

## 📞 الدعم الفني

إذا واجهت أي مشاكل:

1. تحقق من ملفات السجل في `storage/logs/`
2. تأكد من تثبيت جميع المتطلبات
3. تحقق من الصلاحيات
4. راجع إعدادات قاعدة البيانات

---

**✅ بعد إكمال هذه الخطوات، سيكون نظام تبيان جاهزاً للاستخدام!**