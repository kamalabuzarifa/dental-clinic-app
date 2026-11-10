<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users for each role
        User::firstOrCreate(
            ['email' => 'admin@clinic.com'],
            [
                'name' => 'مدير النظام',
                'phone' => '0501234567',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'dentist@clinic.com'],
            [
                'name' => 'د. أحمد الشامي (أخصائي تقويم وجراحة)',
                'phone' => '0502345678',
                'role' => 'dentist',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'dentist2@clinic.com'],
            [
                'name' => 'د. سارة المنصور (أخصائية طب أسنان الأطفال)',
                'phone' => '0503456789',
                'role' => 'dentist',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'receptionist@clinic.com'],
            [
                'name' => 'موظف الاستقبال',
                'phone' => '0504567890',
                'role' => 'receptionist',
                'password' => Hash::make('password123'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'patient@clinic.com'],
            [
                'name' => 'المريض الافتراضي (محمد خالد)',
                'phone' => '0505678901',
                'role' => 'patient',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Create Default Dental Services
        $services = [
            [
                'name' => 'كشف واستشارة طبية عامة',
                'description' => 'فحص شامل لصحة الفم والأسنان مع وضع الخطة العلاجية المناسبة.',
                'price' => 150.00,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'تنظيف وتلميع الأسنان وإزالة الجير',
                'description' => 'إزالة التراكمات الجيرية وتلميع الأسنان بأحدث أجهزة الموجات فوق الصوتية.',
                'price' => 250.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'حشو الأسنان التجميلي (Composite)',
                'description' => 'ترميم الأسنان المتسوسة بحشوات ضوئية تجميلية مطابقة للون السن الطبيعي.',
                'price' => 350.00,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'علاج وحشو العصب والجذور',
                'description' => 'إزالة العصب الملتهب وتنظيف القنوات الجذرية بدقة بأحدث الأجهزة الإلكترونية.',
                'price' => 700.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'تبييض الأسنان بالليزر',
                'description' => 'جلسة تبييض متطورة وسريعة للحصول على ابتسامة ناصعة البياض في جلسة واحدة.',
                'price' => 800.00,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'خلع الأسنان البسيط / الجراحي',
                'description' => 'خلع الأسنان المتضررة أو ضروس العقل تحت تخدير موضعي بدون ألم.',
                'price' => 300.00,
                'duration_minutes' => 45,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}
