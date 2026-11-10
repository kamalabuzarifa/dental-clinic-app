<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_dashboard_with_all_metrics(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHasAll([
            'stats',
            'todayAppointments',
            'pendingAlerts',
            'recentAppointments',
            'weeklyData',
            'topServices',
        ]);
        $response->assertSee('لوحة تحكم الإدارة');
        $response->assertSee('إجمالي المرضى');
        $response->assertSee('مواعيد اليوم');
    }

    public function test_admin_can_update_appointment_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $patient = User::factory()->create(['role' => 'patient']);
        $dentist = User::factory()->create(['role' => 'dentist']);
        $service = Service::create([
            'name' => 'فحص أسنان',
            'description' => 'فحص شامل',
            'price' => 150.00,
            'duration_minutes' => 30,
        ]);

        $appointment = Appointment::create([
            'patient_id' => $patient->id,
            'dentist_id' => $dentist->id,
            'service_id' => $service->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/appointments/{$appointment->id}/status", [
            'status' => 'confirmed',
        ]);

        $response->assertSessionHas('info');
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_admin_layout_has_responsive_sidebar_with_tooltips_and_collapse_classes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('sidebarCollapsed');
        $response->assertSee('activeTooltip');
        $response->assertSee('showTooltip($el, \'الرئيسية\')', false);
        $response->assertSee('showTooltip($el, \'إدارة الأطباء\')', false);
        $response->assertSee('showTooltip($el, \'المرضى\')', false);
        $response->assertSee('showTooltip($el, \'الحجوزات\')', false);
        $response->assertSee('showTooltip($el, \'الخدمات الطبية\')', false);
        $response->assertSee('showTooltip($el, \'التقارير والإحصائيات\')', false);
        $response->assertSee('showTooltip($el, \'إعدادات النظام\')', false);
        $response->assertSee('showTooltip($el, \'واجهة العيادة\')', false);
        $response->assertSee('showTooltip($el, \'تسجيل الخروج\')', false);
        $response->assertSee('custom-sidebar-scroll');
        // Ensure no clipped flyout leaking text
        $response->assertDontSee('class="hidden lg:block absolute right-full top-0 mr-3 w-56', false);
    }
}
