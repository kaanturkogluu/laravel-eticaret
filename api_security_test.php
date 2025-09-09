<?php

echo "API Güvenlik Testi\n";
echo "==================\n\n";

$baseUrl = 'http://127.0.0.1:8000/api';

// Test 1: Health Check (Public endpoint)
echo "1. Health Check Test (Public)\n";
echo "-----------------------------\n";
$response = file_get_contents($baseUrl . '/health');
if ($response) {
    $data = json_decode($response, true);
    echo "✅ Status: " . ($data['status'] ?? 'Unknown') . "\n";
    echo "✅ Response: " . $response . "\n";
} else {
    echo "❌ Health check başarısız!\n";
}
echo "\n";

// Test 2: Protected endpoint without token
echo "2. Protected Endpoint Without Token Test\n";
echo "---------------------------------------\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);

echo "HTTP Code: " . $httpCode . "\n";
echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
echo "Message: " . ($data['message'] ?? 'No message') . "\n";

if ($httpCode === 401) {
    echo "✅ Güvenlik çalışıyor - Token gerekli!\n";
} else {
    echo "❌ Güvenlik açığı - Token olmadan erişim sağlandı!\n";
}
echo "\n";

// Test 3: Invalid token test
echo "3. Invalid Token Test\n";
echo "--------------------\n";
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer invalid-token-123'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);

echo "HTTP Code: " . $httpCode . "\n";
echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
echo "Message: " . ($data['message'] ?? 'No message') . "\n";

if ($httpCode === 401) {
    echo "✅ Geçersiz token reddedildi!\n";
} else {
    echo "❌ Geçersiz token kabul edildi!\n";
}
echo "\n";

// Test 4: Login test
echo "4. Login Test\n";
echo "------------\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'admin@example.com',
    'password' => 'password',
    'device_name' => 'Security Test',
    'abilities' => ['read', 'write'],
    'expires_in_days' => 30
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$data = json_decode($response, true);

echo "HTTP Code: " . $httpCode . "\n";
echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
echo "Message: " . ($data['message'] ?? 'No message') . "\n";

if ($data['success'] && isset($data['data']['token'])) {
    echo "✅ Login başarılı!\n";
    $token = $data['data']['token'];
    echo "Token: " . substr($token, 0, 20) . "...\n\n";
    
    // Test 5: Valid token test
    echo "5. Valid Token Test\n";
    echo "------------------\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/me');
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);
    
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    
    if ($data['success']) {
        echo "✅ Geçerli token ile erişim sağlandı!\n";
        echo "User: " . ($data['data']['user']['name'] ?? 'Unknown') . "\n";
    } else {
        echo "❌ Geçerli token reddedildi!\n";
        echo "Message: " . ($data['message'] ?? 'No message') . "\n";
    }
    echo "\n";
    
    // Test 6: Products API test
    echo "6. Products API Test\n";
    echo "-------------------\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/products');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);
    
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    
    if ($data['success']) {
        echo "✅ Products API erişimi başarılı!\n";
        echo "Products Count: " . (isset($data['data']) ? count($data['data']) : 'Unknown') . "\n";
    } else {
        echo "❌ Products API erişimi başarısız!\n";
        echo "Message: " . ($data['message'] ?? 'No message') . "\n";
    }
    echo "\n";
    
    // Test 7: Token list test
    echo "7. Token List Test\n";
    echo "-----------------\n";
    curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/tokens');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $data = json_decode($response, true);
    
    echo "HTTP Code: " . $httpCode . "\n";
    echo "Success: " . ($data['success'] ? 'Yes' : 'No') . "\n";
    
    if ($data['success']) {
        echo "✅ Token listesi alındı!\n";
        echo "Token Count: " . (isset($data['data']) ? count($data['data']) : 'Unknown') . "\n";
    } else {
        echo "❌ Token listesi alınamadı!\n";
        echo "Message: " . ($data['message'] ?? 'No message') . "\n";
    }
    echo "\n";
    
} else {
    echo "❌ Login başarısız!\n";
    echo "Message: " . ($data['message'] ?? 'No message') . "\n";
}

curl_close($ch);

echo "Test tamamlandı!\n";
echo "================\n";
echo "API güvenlik sistemi test edildi.\n";
