$ip = (Test-Connection -ComputerName (hostname) -Count 1).IPV4Address.IPAddressToString

Write-Host "Detected Local IP: $ip"

$envFile = ".env"
$content = Get-Content $envFile

# Update APP_URL
$content = $content -replace '^APP_URL=.*', "APP_URL=http://$($ip):8000"

# Update ZK_DEVICE_IP
$content = $content -replace '^ZK_DEVICE_IP=.*', "ZK_DEVICE_IP=$ip"

# Save File
$content | Set-Content $envFile

Write-Host "Updated .env with IP: $ip"

# Clear Config Cache
php artisan config:clear
