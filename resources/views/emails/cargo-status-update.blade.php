<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kargo Durumu Güncellendi</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .status-update {
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin: 10px 0;
        }
        .status-created { background-color: #e3f2fd; color: #1976d2; }
        .status-picked_up { background-color: #e8f5e8; color: #2e7d32; }
        .status-in_transit { background-color: #fff3e0; color: #f57c00; }
        .status-out_for_delivery { background-color: #fce4ec; color: #c2185b; }
        .status-delivered { background-color: #e8f5e8; color: #2e7d32; }
        .status-exception { background-color: #ffebee; color: #d32f2f; }
        .status-returned { background-color: #f3e5f5; color: #7b1fa2; }
        
        .order-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .info-row:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .tracking-info {
            background-color: #f0f8ff;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .tracking-number {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            border: 2px dashed #2196f3;
        }
        .cargo-company {
            font-size: 16px;
            color: #1976d2;
            margin: 10px 0;
        }
        .tracking-link {
            display: inline-block;
            background-color: #2196f3;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 15px 0;
            transition: background-color 0.3s;
        }
        .tracking-link:hover {
            background-color: #1976d2;
        }
        .timeline {
            margin: 20px 0;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .timeline-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #4caf50;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .timeline-content {
            flex: 1;
        }
        .timeline-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .timeline-date {
            color: #666;
            font-size: 14px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .change-indicator {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .change-indicator h4 {
            margin-top: 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📦 Kargo Durumu Güncellendi!</h1>
            <p>Merhaba {{ $order->customer_name }}, siparişinizin kargo durumu güncellenmiştir.</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Durum Güncellemesi -->
            <div class="status-update">
                <h3 style="margin-top: 0; color: #2e7d32;">🔄 Kargo Durumu Güncellendi</h3>
                <p style="margin: 15px 0; color: #555;">
                    Siparişinizin kargo durumu güncellenmiştir:
                </p>
                <div class="status-badge status-{{ $newStatus }}">
                    @php
                        $statusLabels = [
                            'created' => 'Kargo Oluşturuldu',
                            'picked_up' => 'Kargo Alındı',
                            'in_transit' => 'Yolda',
                            'out_for_delivery' => 'Dağıtımda',
                            'delivered' => 'Teslim Edildi',
                            'exception' => 'Sorun Var',
                            'returned' => 'İade Edildi',
                        ];
                    @endphp
                    {{ $statusLabels[$newStatus] ?? $newStatus }}
                </div>
                @if($cargoTracking->description)
                    <p style="margin: 15px 0; color: #555; font-style: italic;">
                        "{{ $cargoTracking->description }}"
                    </p>
                @endif
                @if($cargoTracking->location)
                    <p style="margin: 10px 0; color: #555;">
                        📍 {{ $cargoTracking->location }}
                    </p>
                @endif
                <p style="margin: 15px 0; color: #666; font-size: 14px;">
                    Güncelleme Tarihi: {{ $cargoTracking->formatted_event_date }}
                </p>
            </div>

            <!-- Sipariş Bilgileri -->
            <div class="order-info">
                <h3 style="margin-top: 0; color: #667eea;">📦 Sipariş Bilgileri</h3>
                <div class="info-row">
                    <span class="info-label">Sipariş Numarası:</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sipariş Tarihi:</span>
                    <span class="info-value">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Toplam Tutar:</span>
                    <span class="info-value" style="font-weight: 600; color: #2e7d32;">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Kargo Takip Bilgileri -->
            <div class="tracking-info">
                <h3 style="margin-top: 0; color: #1976d2;">🚛 Kargo Takip Bilgileri</h3>
                <div class="cargo-company">{{ $cargoTracking->cargoCompany->name }}</div>
                <div class="tracking-number">{{ $cargoTracking->tracking_number }}</div>
                <p style="margin: 15px 0; color: #555;">
                    Siparişinizi takip etmek için aşağıdaki butona tıklayabilir veya takip numaranızı kargo şirketinin web sitesinde sorgulayabilirsiniz.
                </p>
                @if($cargoTracking->cargoCompany->tracking_url)
                    <a href="{{ $cargoTracking->cargoCompany->getTrackingUrl($cargoTracking->tracking_number) }}" 
                       class="tracking-link" target="_blank">
                        🔍 Kargo Takip Et
                    </a>
                @endif
            </div>

            <!-- Kargo Takip Geçmişi -->
            @if($cargoTracking->order->cargoTrackings->count() > 0)
                <div class="timeline">
                    <h3 style="color: #667eea; margin-bottom: 15px;">📈 Kargo Takip Geçmişi</h3>
                    @foreach($cargoTracking->order->cargoTrackings->sortBy('event_date') as $tracking)
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">{{ $tracking->status_label }}</div>
                                @if($tracking->description)
                                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">{{ $tracking->description }}</div>
                                @endif
                                @if($tracking->location)
                                    <div style="color: #666; font-size: 14px; margin-bottom: 5px;">📍 {{ $tracking->location }}</div>
                                @endif
                                <div class="timeline-date">{{ $tracking->formatted_event_date }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Önemli Bilgiler -->
            @if($newStatus === 'delivered')
                <div class="change-indicator">
                    <h4>🎉 Tebrikler! Siparişiniz Teslim Edildi</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #856404;">
                        <li>Siparişiniz başarıyla teslim edilmiştir.</li>
                        <li>Ürünlerinizi kontrol etmeyi unutmayın.</li>
                        <li>Herhangi bir sorunuz varsa bizimle iletişime geçebilirsiniz.</li>
                        <li>Memnun kaldıysanız değerlendirmenizi bekliyoruz.</li>
                    </ul>
                </div>
            @elseif($newStatus === 'out_for_delivery')
                <div class="change-indicator">
                    <h4>🚚 Siparişiniz Dağıtımda</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #856404;">
                        <li>Siparişiniz dağıtım aracında ve size doğru yolda.</li>
                        <li>Teslimat sırasında evde bulunmanız önerilir.</li>
                        <li>Kargo şirketi ile iletişime geçebilirsiniz.</li>
                    </ul>
                </div>
            @elseif($newStatus === 'exception')
                <div class="change-indicator">
                    <h4>⚠️ Kargo Durumunda Sorun</h4>
                    <ul style="margin: 0; padding-left: 20px; color: #856404;">
                        <li>Kargo durumunda bir sorun tespit edilmiştir.</li>
                        <li>Lütfen kargo şirketi ile iletişime geçin.</li>
                        <li>Gerekirse bizimle de iletişime geçebilirsiniz.</li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayın.<br>
                <a href="{{ route('home') }}">Web Sitemizi Ziyaret Edin</a> | 
                <a href="{{ route('cargo-tracking.track') }}">Kargo Takip</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px; color: #999;">
                © {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.
            </p>
        </div>
    </div>
</body>
</html>
