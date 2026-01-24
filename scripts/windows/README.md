# تثبيت وتشغيل NSSM لـ Laravel Queue Worker

## ما هو NSSM؟
NSSM (Non-Sucking Service Manager) هو أداة تحول أي برنامج إلى Windows Service يعمل في الخلفية تلقائياً.

---

## الخطوات بالتفصيل:

### الخطوة 1: تحميل NSSM

1. اذهب إلى: https://nssm.cc/download
2. حمّل النسخة الأخيرة (nssm-2.24.zip أو أحدث)
3. فك الضغط عن الملف
4. انسخ `nssm.exe` من مجلد `win64` (أو `win32` لو نظامك 32-bit)
5. الصق `nssm.exe` في هذا المجلد: `scripts/windows/`

### الخطوة 2: تعديل المسارات

افتح ملف `install-queue-service.bat` وعدّل المسارات:

```batch
REM غيّر هذه المسارات حسب جهازك:
set "PHP_PATH=C:\xampp\php\php.exe"
```

**مسارات PHP الشائعة:**
- XAMPP: `C:\xampp\php\php.exe`
- WAMP: `C:\wamp64\bin\php\php8.x\php.exe`
- Laragon: `C:\laragon\bin\php\php-8.x\php.exe`
- Standalone: `C:\php\php.exe`

### الخطوة 3: تشغيل سكريبت التثبيت

1. **كليك يمين** على `install-queue-service.bat`
2. اختار **"Run as administrator"**
3. انتظر حتى ينتهي التثبيت

### الخطوة 4: التحقق من التشغيل

افتح Command Prompt كـ Administrator واكتب:
```cmd
sc query NCA3-Queue
```

أو استخدم:
```cmd
nssm status NCA3-Queue
```

---

## إدارة الخدمة:

### باستخدام سكريبت الإدارة:
شغّل `manage-queue-service.bat` كـ Administrator

### أو باستخدام الأوامر مباشرة:

```cmd
# حالة الخدمة
nssm status NCA3-Queue

# تشغيل
nssm start NCA3-Queue

# إيقاف
nssm stop NCA3-Queue

# إعادة تشغيل
nssm restart NCA3-Queue

# فتح واجهة الإعدادات
nssm edit NCA3-Queue

# حذف الخدمة
nssm remove NCA3-Queue confirm
```

---

## ملفات السجلات (Logs):

```
storage/logs/queue-worker.log  - سجل العمليات
storage/logs/queue-error.log   - سجل الأخطاء
```

---

## استكشاف الأخطاء:

### الخدمة لا تعمل:
1. تأكد من مسار PHP صحيح
2. تأكد من تشغيل كـ Administrator
3. افتح `queue-error.log` وشوف الأخطاء

### الخدمة تتوقف باستمرار:
1. تأكد من `QUEUE_CONNECTION=database` في ملف `.env`
2. تأكد من وجود جدول `jobs` في قاعدة البيانات:
   ```cmd
   php artisan queue:table
   php artisan migrate
   ```

### الـ Jobs مش بتتنفذ:
1. تأكد الخدمة شغالة: `nssm status NCA3-Queue`
2. تأكد في jobs في الجدول:
   ```sql
   SELECT COUNT(*) FROM jobs;
   ```

---

## إعدادات متقدمة:

### تغيير عدد المحاولات:
```cmd
nssm set NCA3-Queue AppParameters "artisan queue:work --tries=5"
nssm restart NCA3-Queue
```

### تغيير الذاكرة:
```cmd
nssm set NCA3-Queue AppParameters "artisan queue:work --memory=512"
nssm restart NCA3-Queue
```

### تشغيل أكثر من worker:
كرر التثبيت بأسماء مختلفة:
```cmd
nssm install NCA3-Queue-2 "C:\xampp\php\php.exe"
nssm set NCA3-Queue-2 AppParameters "artisan queue:work"
nssm set NCA3-Queue-2 AppDirectory "C:\path\to\project"
nssm start NCA3-Queue-2
```

---

## الملفات:

| الملف | الوصف |
|-------|-------|
| `install-queue-service.bat` | سكريبت تثبيت الخدمة |
| `manage-queue-service.bat` | سكريبت إدارة الخدمة (قائمة تفاعلية) |
| `nssm.exe` | برنامج NSSM (يجب تحميله) |
