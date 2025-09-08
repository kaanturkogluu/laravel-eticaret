<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişiniz Kargoya Verildi</title>
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
            background-color: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .tracking-number {
            font-size: 20px;
            font-weight: bold;
            color: #2e7d32;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            border: 2px dashed #4caf50;
        }
        .cargo-company {
            font-size: 18px;
            color: #2e7d32;
            margin: 10px 0;
        }
        .tracking-link {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 600;
            margin: 15px 0;
            transition: background-color 0.3s;
        }
        .tracking-link:hover {
            background-color: #45a049;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .item-code {
            color: #666;
            font-size: 14px;
        }
        .item-quantity {
            color: #666;
            font-size: 14px;
        }
        .item-price {
            font-weight: 600;
            color: #2e7d32;
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
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-shipped {
            background-color: #e3f2fd;
            color: #1976d2;
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
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🚚 Siparişiniz Kargoya Verildi!</h1>
            <p>Merhaba {{ $order->customer_name }}, siparişiniz kargoya verilmiştir.</p>
        </div>

        <!-- Content -->
        <div class="content">
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
                    <span class="info-label">Durum:</span>
                    <span class="info-value">
                        <span class="status-badge status-shipped">{{ $order->status_label }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Toplam Tutar:</span>
                    <span class="info-value" style="font-weight: 600; color: #2e7d32;">{{ $order->formatted_total }}</span>
                </div>
            </div>

            <!-- Kargo Takip Bilgileri -->
            <div class="tracking-info">
                <h3 style="margin-top: 0; color: #2e7d32;">🚛 Kargo Takip Bilgileri</h3>
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

            <!-- Sipariş Kalemleri -->
            <div class="order-items">
                <h3 style="color: #667eea; margin-bottom: 15px;">📋 Sipariş Kalemleri</h3>
                @foreach($order->items as $item)
                    <div class="order-item">
                        @if($item->product->images->count() > 0)
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                 alt="{{ $item->product_name }}" class="item-image">
                        @else
                            <div class="item-image" style="background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #999;">
                                📦
                            </div>
                        @endif
                        <div class="item-details">
                            <div class="item-name">{{ $item->product_name }}</div>
                            @if($item->product_code)
                                <div class="item-code">Kod: {{ $item->product_code }}</div>
                            @endif
                            <div class="item-quantity">Miktar: {{ $item->quantity }} adet</div>
                        </div>
                        <div class="item-price">{{ $item->formatted_total_price }}</div>
                    </div>
                @endforeach
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

            <!-- Teslimat Adresi -->
            <div class="order-info">
                <h3 style="margin-top: 0; color: #667eea;">🏠 Teslimat Adresi</h3>
                <div style="line-height: 1.8;">
                    <strong>{{ $order->customer_name }}</strong><br>
                    {{ $order->shipping_address }}<br>
                    @if($order->shipping_city)
                        {{ $order->shipping_city }}
                        @if($order->shipping_district)
                            / {{ $order->shipping_district }}
                        @endif
                        @if($order->shipping_postal_code)
                            - {{ $order->shipping_postal_code }}
                        @endif
                        <br>
                    @endif
                    @if($order->customer_phone)
                        📞 {{ $order->customer_phone }}<br>
                    @endif
                    📧 {{ $order->customer_email }}
                </div>
            </div>

            <!-- Bilgilendirme -->
            <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0;">
                <h4 style="margin-top: 0; color: #856404;">ℹ️ Önemli Bilgiler</h4>
                <ul style="margin: 0; padding-left: 20px; color: #856404;">
                    <li>Kargo takip numaranızı not almayı unutmayın.</li>
                    <li>Kargo şirketinin web sitesinden detaylı takip yapabilirsiniz.</li>
                    <li>Teslimat sırasında evde bulunmanız önerilir.</li>
                    <li>Herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz.</li>
                </ul>
            </div>
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
