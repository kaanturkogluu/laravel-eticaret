<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-posta Doğrulama - Basital.com</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #2563eb;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .content p {
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer p {
            margin: 5px 0;
        }
        .security-note {
            background-color: #e3f2fd;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .security-note p {
            margin: 0;
            font-size: 14px;
            color: #1976d2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Basital.com</h1>
            <p>E-posta Adresinizi Doğrulayın</p>
        </div>
        
        <div class="content">
            <h2>Merhaba {{ $user->name }}!</h2>
            
            <p>Basital.com'a hoş geldiniz! Hesabınızı aktifleştirmek için e-posta adresinizi doğrulamanız gerekiyor.</p>
            
            <p>Doğrulama işlemini tamamlamak için aşağıdaki butona tıklayın:</p>
            
            <div style="text-align: center;">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    E-posta Adresimi Doğrula
                </a>
            </div>
            
            <div class="security-note">
                <p><strong>Güvenlik Notu:</strong> Bu bağlantı 60 dakika geçerlidir. Eğer bu işlemi siz yapmadıysanız, bu e-postayı görmezden gelebilirsiniz.</p>
            </div>
            
            <p>Eğer yukarıdaki buton çalışmıyorsa, aşağıdaki bağlantıyı kopyalayıp tarayıcınızın adres çubuğuna yapıştırabilirsiniz:</p>
            
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                {{ $verificationUrl }}
            </p>
            
            <p>Doğrulama işlemi tamamlandıktan sonra:</p>
            <ul>
                <li>Alışveriş yapabilirsiniz</li>
                <li>Siparişlerinizi takip edebilirsiniz</li>
                <li>Favori ürünlerinizi kaydedebilirsiniz</li>
                <li>Kampanyalardan haberdar olabilirsiniz</li>
            </ul>
        </div>
        
        <div class="footer">
            <p><strong>Basital.com</strong> - Teknoloji ve Elektronik</p>
            <p>Bu e-posta otomatik olarak gönderilmiştir. Lütfen yanıtlamayın.</p>
            <p>© {{ date('Y') }} Basital.com. Tüm hakları saklıdır.</p>
        </div>
    </div>
</body>
</html>
