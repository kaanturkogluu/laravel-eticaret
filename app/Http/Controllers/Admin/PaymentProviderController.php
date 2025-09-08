<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProvider;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentProviderController extends Controller
{
    /**
     * Payment provider listesi
     */
    public function index()
    {
        $providers = PaymentProvider::orderBy('sort_order')->get();
        
        return view('admin.payment-providers.index', compact('providers'));
    }

    /**
     * Payment provider oluşturma formu
     */
    public function create()
    {
        $supportedProviders = PaymentServiceFactory::getSupportedProviders();
        $currencies = ['TRY', 'USD', 'EUR', 'GBP'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'wallet', 'cash_on_delivery'];
        
        return view('admin.payment-providers.create', compact('supportedProviders', 'currencies', 'paymentMethods'));
    }

    /**
     * Payment provider kaydet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_providers,code',
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'supported_currencies' => 'required|array|min:1',
            'supported_currencies.*' => 'string|in:TRY,USD,EUR,GBP',
            'supported_payment_methods' => 'required|array|min:1',
            'supported_payment_methods.*' => 'string|in:credit_card,bank_transfer,wallet,cash_on_delivery',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_fixed' => 'nullable|numeric|min:0',
            'test_mode' => 'boolean',
            'config' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Provider code'un desteklendiğini kontrol et
        if (!PaymentServiceFactory::isProviderSupported($request->code)) {
            return back()->withErrors(['code' => 'Bu ödeme sağlayıcısı desteklenmiyor.'])->withInput();
        }

        $provider = PaymentProvider::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'logo_url' => $request->logo_url,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'supported_currencies' => $request->supported_currencies,
            'supported_payment_methods' => $request->supported_payment_methods,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'commission_rate' => $request->commission_rate ?? 0,
            'commission_fixed' => $request->commission_fixed ?? 0,
            'test_mode' => $request->boolean('test_mode'),
            'config' => $request->config ?? []
        ]);

        return redirect()->route('admin.payment-providers.index')
            ->with('success', 'Ödeme sağlayıcısı başarıyla oluşturuldu.');
    }

    /**
     * Payment provider düzenleme formu
     */
    public function edit(PaymentProvider $paymentProvider)
    {
        $supportedProviders = PaymentServiceFactory::getSupportedProviders();
        $currencies = ['TRY', 'USD', 'EUR', 'GBP'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'wallet', 'cash_on_delivery'];
        
        return view('admin.payment-providers.edit', compact('paymentProvider', 'supportedProviders', 'currencies', 'paymentMethods'));
    }

    /**
     * Payment provider güncelle
     */
    public function update(Request $request, PaymentProvider $paymentProvider)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_providers,code,' . $paymentProvider->id,
            'description' => 'nullable|string',
            'logo_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'supported_currencies' => 'required|array|min:1',
            'supported_currencies.*' => 'string|in:TRY,USD,EUR,GBP',
            'supported_payment_methods' => 'required|array|min:1',
            'supported_payment_methods.*' => 'string|in:credit_card,bank_transfer,wallet,cash_on_delivery',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gt:min_amount',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_fixed' => 'nullable|numeric|min:0',
            'test_mode' => 'boolean',
            'config' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Provider code'un desteklendiğini kontrol et
        if (!PaymentServiceFactory::isProviderSupported($request->code)) {
            return back()->withErrors(['code' => 'Bu ödeme sağlayıcısı desteklenmiyor.'])->withInput();
        }

        $paymentProvider->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'logo_url' => $request->logo_url,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $request->sort_order ?? 0,
            'supported_currencies' => $request->supported_currencies,
            'supported_payment_methods' => $request->supported_payment_methods,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'commission_rate' => $request->commission_rate ?? 0,
            'commission_fixed' => $request->commission_fixed ?? 0,
            'test_mode' => $request->boolean('test_mode'),
            'config' => $request->config ?? []
        ]);

        return redirect()->route('admin.payment-providers.index')
            ->with('success', 'Ödeme sağlayıcısı başarıyla güncellendi.');
    }

    /**
     * Payment provider sil
     */
    public function destroy(PaymentProvider $paymentProvider)
    {
        // Aktif transaction'ları kontrol et
        if ($paymentProvider->transactions()->whereIn('status', ['pending', 'processing'])->exists()) {
            return back()->with('error', 'Bu ödeme sağlayıcısının aktif işlemleri bulunuyor. Silme işlemi yapılamaz.');
        }

        $paymentProvider->delete();

        return redirect()->route('admin.payment-providers.index')
            ->with('success', 'Ödeme sağlayıcısı başarıyla silindi.');
    }

    /**
     * Payment provider durumunu değiştir
     */
    public function toggleStatus(PaymentProvider $paymentProvider)
    {
        $paymentProvider->update(['is_active' => !$paymentProvider->is_active]);

        $status = $paymentProvider->is_active ? 'aktif' : 'pasif';
        
        return back()->with('success', "Ödeme sağlayıcısı {$status} olarak işaretlendi.");
    }

    /**
     * Payment provider konfigürasyonu
     */
    public function config(PaymentProvider $paymentProvider)
    {
        $configFields = $this->getConfigFields($paymentProvider->code);
        
        return view('admin.payment-providers.config', compact('paymentProvider', 'configFields'));
    }

    /**
     * Payment provider konfigürasyonu güncelle
     */
    public function updateConfig(Request $request, PaymentProvider $paymentProvider)
    {
        $configFields = $this->getConfigFields($paymentProvider->code);
        $rules = [];

        foreach ($configFields as $field => $fieldConfig) {
            if ($fieldConfig['required'] ?? false) {
                $rules["config.{$field}"] = 'required';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $paymentProvider->update(['config' => $request->config ?? []]);

        return redirect()->route('admin.payment-providers.index')
            ->with('success', 'Ödeme sağlayıcısı konfigürasyonu güncellendi.');
    }

    /**
     * Provider code'a göre konfigürasyon alanlarını getir
     */
    private function getConfigFields(string $code): array
    {
        $configFields = [
            'payu' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'notify_url' => ['label' => 'Notify URL', 'type' => 'url', 'required' => true],
                'continue_url' => ['label' => 'Continue URL', 'type' => 'url', 'required' => true],
            ],
            'sipay' => [
                'merchant_key' => ['label' => 'Merchant Key', 'type' => 'text', 'required' => true],
                'merchant_secret' => ['label' => 'Merchant Secret', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'mokapos' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'cancel_url' => ['label' => 'Cancel URL', 'type' => 'url', 'required' => true],
                'webhook_url' => ['label' => 'Webhook URL', 'type' => 'url', 'required' => true],
            ],
            'paynet' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'odeal' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'papara' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'paycell' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'hepsipay' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'esnekpos' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'ininal' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'paytrek' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
        ];

        return $configFields[$code] ?? [];
    }

    /**
     * Payment provider bağlantı testi
     */
    public function testConnection(PaymentProvider $paymentProvider)
    {
        try {
            $service = PaymentServiceFactory::create($paymentProvider);
            
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment service oluşturulamadı.',
                    'details' => 'Service factory bu provider için service döndürmedi.'
                ]);
            }

            // Test bağlantısı yap
            $result = $service->testConnection();
            
            return response()->json([
                'success' => $result['success'] ?? false,
                'message' => $result['message'] ?? 'Bağlantı testi tamamlandı.',
                'details' => $result['details'] ?? null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı testi sırasında hata oluştu: ' . $e->getMessage(),
                'details' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);
        }
    }
}
