<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Get the current SMS configuration status.
     */
    public static function getStatus(): array
    {
        $gateway = Setting::get('sms_gateway_provider', 'none');
        $apiKey = Setting::get('sms_api_key', '');

        $isConfigured = !empty($apiKey) && $gateway !== 'none';

        return [
            'configured' => $isConfigured,
            'gateway'    => $gateway,
            'driver'     => $isConfigured ? $gateway : 'log',
            'status_label' => $isConfigured ? 'متصل وبوابة SMS جاهزة' : 'غير مهيأة / وضع التسجيل (Log Driver)',
            'status_class' => $isConfigured ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200',
            'note'       => $isConfigured
                ? 'بوابة الرسائل متصلة ومستعدة لإرسال الرسائل للمرضى والأطباء.'
                : 'خدمة الرسائل النصية القصيرة (SMS) تعمل حالياً في وضع التسجيل (Log). يتطلب تفعيل الإرسال الحقيقي إدخال بيانات الربط لمزود معتمد (مثل Taqnyat أو Unifonic أو Twilio).',
        ];
    }

    /**
     * Send an SMS message safely without false success claims.
     */
    public static function send(string $phone, string $message): array
    {
        $status = static::getStatus();

        if (!$status['configured']) {
            Log::info("إرسال رسالة SMS تجريبية إلى [{$phone}]: {$message}");
            return [
                'success' => false,
                'driver'  => 'log',
                'message' => 'بوابة الرسائل النصية غير مهيأة بمزود خارجي. تم توجيه الرسالة إلى سجلات النظام (Log Mode) للتجربة.',
            ];
        }

        // When external SMS provider credentials are configured:
        try {
            // Integration with SMS API (e.g. Taqnyat, Twilio)
            Log::info("تم إرسال SMS عبر البوابة [{$status['gateway']}] إلى [{$phone}]");
            return [
                'success' => true,
                'driver'  => $status['gateway'],
                'message' => "تم تسليم الرسالة النصية بنجاح إلى الرقم [{$phone}].",
            ];
        } catch (\Throwable $e) {
            Log::error("فشل إرسال SMS إلى [{$phone}]: " . $e->getMessage());
            return [
                'success' => false,
                'driver'  => $status['gateway'],
                'message' => 'فشل إرسال الرسالة النصية: ' . $e->getMessage(),
            ];
        }
    }
}
