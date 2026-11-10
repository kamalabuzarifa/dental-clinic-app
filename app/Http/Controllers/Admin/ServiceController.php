<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $services = Service::withCount('appointments')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.services.index', compact('services', 'search'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255', 'unique:services,name'],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'description'      => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'             => 'يرجى إدخال اسم الخدمة الطبية.',
            'name.unique'               => 'اسم الخدمة موجود بالفعل في السجلات.',
            'price.required'            => 'يرجى تحديد السعر الرسمي للخدمة.',
            'price.numeric'             => 'يجب أن يكون السعر قيمة عددية صحيحة.',
            'duration_minutes.required' => 'يرجى تحديد المدة التقديرية للجلسة بالدقائق.',
            'duration_minutes.min'      => 'أقل مدة للجلسة هي 5 دقائق.',
        ]);

        $newService = Service::create($validated);
        \App\Models\AuditLog::record('create_service', "إضافة خدمة طبية جديدة [{$newService->name}] بسعر [{$newService->price}]", $newService);

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'تمت إضافة الخدمة الطبية بنجاح إلى قائمة الخدمات.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255', 'unique:services,name,' . $service->id],
            'price'            => ['required', 'numeric', 'min:0'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'description'      => ['nullable', 'string', 'max:1000'],
        ], [
            'name.required'             => 'يرجى إدخال اسم الخدمة الطبية.',
            'name.unique'               => 'اسم الخدمة موجود بالفعل لسجل آخر.',
            'price.required'            => 'يرجى تحديد السعر الرسمي للخدمة.',
            'price.numeric'             => 'يجب أن يكون السعر قيمة عددية صحيحة.',
            'duration_minutes.required' => 'يرجى تحديد المدة التقديرية للجلسة بالدقائق.',
        ]);

        $oldValues = $service->only(['name', 'price', 'duration_minutes', 'description']);
        $service->update($validated);
        \App\Models\AuditLog::record('update_service', "تحديث بيانات الخدمة الطبية [{$service->name}]", $service, $oldValues, $validated);

        return redirect()
            ->route('admin.services.index')
            ->with('info', 'تم تحديث بيانات الخدمة الطبية بنجاح.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        if ($service->appointments()->exists()) {
            return redirect()
                ->route('admin.services.index')
                ->with('error', 'لا يمكن حذف هذه الخدمة نظراً لوجود مواعيد وحجوزات مسجلة مرتبطة بها.');
        }

        \App\Models\AuditLog::record('delete_service', "حذف الخدمة الطبية [{$service->name}]", $service);
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('error', 'تم حذف الخدمة بنجاح من النظام.');
    }
}
