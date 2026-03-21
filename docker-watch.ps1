# docker-watch.ps1
# Script theo doi va dong bo file tu Windows vao Docker Container (edu_crm_app)

$containerName = "edu_crm_app"
$targetPath = "/var/www"
$sourcePath = (Get-Item .).FullName

Write-Host "======================================================" -ForegroundColor Cyan
Write-Host " TU DONG DONG BO CODE VAO DOCKER (THAY THE BIND-MOUNT) " -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan

# Kiem tra container dang chay
$isRunning = docker ps -q -f name=$containerName
if (-not $isRunning) {
    Write-Host "[LOI] Container '$containerName' khong chay. Hay goi 'docker-compose up -d' truoc." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "[Buoc 1] Dang dong bo TOAN BO Source Code lan dau tien..." -ForegroundColor Yellow
# Copy cac thu muc va file quan trong tung cai de loai bo chac chan 'vendor' va '.git'
$itemsToCopy = @("app", "bootstrap", "config", "database", "public", "resources", "routes", "tests", ".env", "composer.json", "composer.lock", "artisan")
foreach ($item in $itemsToCopy) {
    if (Test-Path $item) {
        docker cp $item "$containerName`:$targetPath/"
    }
}
# Chay lai composer install trong container neu thieu vendor
docker exec $containerName bash -c "chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache && if [ ! -d '/var/www/vendor/laravel' ]; then composer install --optimize-autoloader; fi"

Write-Host "[XONG] Da dong bo hoan tat toan bo Code!" -ForegroundColor Green

Write-Host ""
Write-Host "[Buoc 2] Dang lang nghe su thay doi file (Save/Delete/Rename)..." -ForegroundColor Yellow
Write-Host "Hay de cua so nay chay ngam va cu thoai mai Code ben Antigravity!" -ForegroundColor Green

$watcher = New-Object System.IO.FileSystemWatcher -ArgumentList $sourcePath, "*.*"
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

# Ham xu ly khi co su kien thay doi file
$action = {
    $conf = $Event.MessageData
    $containerName = $conf.containerName
    $targetPath = $conf.targetPath
    $sourcePath = $conf.sourcePath

    $path = $Event.SourceEventArgs.FullPath
    $relPath = $path.Substring($sourcePath.Length).Replace('\', '/')
    
    # Bo qua cac thu muc khong can sync lien tuc
    if ($relPath -match "^/\.git/" -or $relPath -match "^/vendor/" -or $relPath -match "^/node_modules/") {
        return
    }

    $changeType = $Event.SourceEventArgs.ChangeType
    
    if ($changeType -eq 'Deleted') {
        Write-Host "  [-] Xoa: $relPath" -ForegroundColor Red
        docker exec $containerName rm -rf "$targetPath$relPath"
    } else {
        Write-Host "  [+] Cap nhat: $relPath" -ForegroundColor Cyan
        # Copy file
        docker cp $path "$containerName`:$targetPath$relPath"
    }
}

$messageData = @{
    containerName = $containerName
    targetPath = $targetPath
    sourcePath = $sourcePath
}

Register-ObjectEvent -InputObject $watcher -EventName "Created" -Action $action -MessageData $messageData | Out-Null
Register-ObjectEvent -InputObject $watcher -EventName "Changed" -Action $action -MessageData $messageData | Out-Null
Register-ObjectEvent -InputObject $watcher -EventName "Deleted" -Action $action -MessageData $messageData | Out-Null
Register-ObjectEvent -InputObject $watcher -EventName "Renamed" -Action $action -MessageData $messageData | Out-Null

# Vong lap giu script chay lien tuc
try {
    while ($true) {
        Wait-Event -Timeout 1
    }
} finally {
    Unregister-Event -SourceIdentifier "Created" -ErrorAction SilentlyContinue
    Unregister-Event -SourceIdentifier "Changed" -ErrorAction SilentlyContinue
    Unregister-Event -SourceIdentifier "Deleted" -ErrorAction SilentlyContinue
    Unregister-Event -SourceIdentifier "Renamed" -ErrorAction SilentlyContinue
    $watcher.Dispose()
    Write-Host "Da dung theo doi." -ForegroundColor Yellow
}
