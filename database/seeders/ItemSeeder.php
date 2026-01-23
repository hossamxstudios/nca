<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $items = [
                ['name' => 'رخصة', 'description' => 'رخص البناء والتشغيل', 'order' => 1],
                ['name' => 'إيصالات', 'description' => 'إيصالات السداد والاستلام', 'order' => 38],
                ['name' => 'تأمينات اجتماعية', 'description' => 'مستندات التأمينات الاجتماعية', 'order' => 2],
                ['name' => 'بطايق شخصية', 'description' => 'البطاقات الشخصية', 'order' => 39],
                ['name' => 'بيان صلاحية الموقع', 'description' => 'بيانات صلاحية المواقع', 'order' => 3],
                ['name' => 'كارنية نقابة المهندسين', 'description' => 'كارنيهات نقابة المهندسين', 'order' => 40],
                ['name' => 'إخطار تخصيص قطعة قطعه', 'description' => 'إخطارات تخصيص القطع', 'order' => 4],
                ['name' => 'بطاقة ضريبة', 'description' => 'البطاقات الضريبية', 'order' => 41],
                ['name' => 'محضر استلام موقع', 'description' => 'محاضر استلام المواقع', 'order' => 5],
                ['name' => 'إقرار ضريبي', 'description' => 'الإقرارات الضريبية', 'order' => 42],
                ['name' => 'الحماية المدنية', 'description' => 'مستندات الحماية المدنية', 'order' => 6],
                ['name' => 'حساب الأعمال المطلوب ترخيصها', 'description' => 'حسابات الأعمال للترخيص', 'order' => 43],
                ['name' => 'محضر معاينة', 'description' => 'محاضر المعاينة', 'order' => 7],
                ['name' => 'بيان صلاحية الأعمال المطلوب ترخيصها', 'description' => 'بيانات صلاحية الأعمال', 'order' => 44],
                ['name' => 'نتيجة معاينة', 'description' => 'نتائج المعاينات', 'order' => 8],
                ['name' => 'إقرار وتعهد وتفويض', 'description' => 'إقرارات وتعهدات وتفويضات', 'order' => 45],
                ['name' => 'طلب الحصول على خدمة', 'description' => 'طلبات الحصول على الخدمات', 'order' => 9],
                ['name' => 'خطابات', 'description' => 'الخطابات الرسمية', 'order' => 46],
                ['name' => 'طلب تراخيص', 'description' => 'طلبات التراخيص', 'order' => 10],
                ['name' => 'ورق بخط اليد', 'description' => 'أوراق مكتوبة بخط اليد', 'order' => 47],
                ['name' => 'بيانات الممثل القانوني', 'description' => 'بيانات الممثلين القانونيين', 'order' => 11],
                ['name' => 'قرار وزاري', 'description' => 'القرارات الوزارية', 'order' => 48],
                ['name' => 'بيان الأعمال المطلوب الترخيص لها', 'description' => 'بيانات الأعمال للترخيص', 'order' => 12],
                ['name' => 'عقود', 'description' => 'العقود القانونية', 'order' => 49],
                ['name' => 'شهادة صلاحية الأعمال للترخيص', 'description' => 'شهادات صلاحية الأعمال', 'order' => 13],
                ['name' => 'طلب تعديل', 'description' => 'طلبات التعديل', 'order' => 50],
                ['name' => 'النموذج المرافق بتقرير التربة', 'description' => 'نماذج تقارير التربة', 'order' => 14],
                ['name' => 'طلب استخراج بيان صلاحية', 'description' => 'طلبات استخراج بيانات الصلاحية', 'order' => 51],
                ['name' => 'النموذج المرفق بالنوته الحسابية', 'description' => 'نماذج النوتة الحسابية', 'order' => 15],
                ['name' => 'موقف مالي وعقاري', 'description' => 'المواقف المالية والعقارية', 'order' => 52],
                ['name' => 'نموذج إنشاء مبنى', 'description' => 'نماذج إنشاء المباني', 'order' => 16],
                ['name' => 'شهادة تحصيل', 'description' => 'شهادات التحصيل', 'order' => 53],
                ['name' => 'نموذج يوضح شكل لافتة الاعمال', 'description' => 'نماذج أشكال الفتات', 'order' => 17],
                ['name' => 'قيمة تكاليف الأعمال', 'description' => 'قيم وتكاليف الأعمال', 'order' => 18],
                ['name' => 'إقرار وتعهد', 'description' => 'إقرارات وتعهدات', 'order' => 19],
                ['name' => 'بيانات المهندسين المشرفين', 'description' => 'بيانات المهندسين المشرفين', 'order' => 20],
                ['name' => 'تقرير هندسي استشاري – سند جوانب الحفر', 'description' => 'تقارير هندسية استشارية', 'order' => 21],
                ['name' => 'توكيل (عام / خاص)', 'description' => 'التوكيلات العامة والخاصة', 'order' => 22],
                ['name' => 'وثيقة التأمين', 'description' => 'وثائق التأمين', 'order' => 23],
                ['name' => 'شهادة مهندس استشاري', 'description' => 'شهادات المهندسين الاستشاريين', 'order' => 24],
                ['name' => 'النقابة العامة للمهندسين', 'description' => 'مستندات النقابة العامة للمهندسين', 'order' => 25],
                ['name' => 'سجل قيد الرسومات', 'description' => 'سجلات قيد الرسومات', 'order' => 26],
                ['name' => 'جدول حصر المهندسين', 'description' => 'جداول حصر المهندسين', 'order' => 27],
                ['name' => 'النوته الحسابية', 'description' => 'النوتة الحسابية', 'order' => 28],
                ['name' => 'تقرير فني', 'description' => 'التقارير الفنية', 'order' => 29],
                ['name' => 'سجل تجاري', 'description' => 'السجلات التجارية', 'order' => 30],
                ['name' => 'وثيقة تأمين من المسؤولية', 'description' => 'وثائق التأمين من المسؤولية', 'order' => 31],
                ['name' => 'وثيقة عشرية', 'description' => 'الوثائق العشرية', 'order' => 32],
                ['name' => 'رسوم ترخيص ومرافق', 'description' => 'رسوم التراخيص والمرافق', 'order' => 33],
                ['name' => 'إيصال استلام نقدية', 'description' => 'إيصالات استلام النقدية', 'order' => 34],
                ['name' => 'إيصال استلام طلب خدمة', 'description' => 'إيصالات استلام طلبات الخدمة', 'order' => 35],
                ['name' => 'رسم موقف تنفيذي', 'description' => 'رسومات المواقف التنفيذية', 'order' => 36],
                ['name' => 'رسم استخراج بيان صلاحية', 'description' => 'رسومات استخراج بيانات الصلاحية', 'order' => 37],
                ['name' => 'حساب تكاليف', 'description' => 'حسابات تكاليف الأعمال', 'order' => 54],
                ['name' => 'رسومات', 'description' => 'الرسومات الهندسية', 'order' => 55],
                ['name' => 'مخططات', 'description' => 'المخططات الهندسية', 'order' => 56],
            ];

            foreach ($items as $item) {
                Item::firstOrCreate(
                    ['name' => $item['name']],
                    $item
                );
            }

            DB::commit();

            $this->command->info('Items seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ItemSeeder failed: '.$e->getMessage());
            $this->command->error('Failed to seed items: '.$e->getMessage());
        }
    }
}
