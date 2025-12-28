# Fix HTML class attributes - merge style-btn into the existing class attribute
$filePath = "resources\views\user\document-wizard.blade.php"
$content = Get-Content $filePath -Raw

# Fix button classes by merging the separate class="style-btn" into the main class attribute
# Pattern: class="btn btn-sm btn-outline-secondary text-decoration-none" class="style-btn"
# Should become: class="btn btn-sm btn-outline-secondary style-btn text-decoration-none"

$content = $content -replace 'class="btn btn-sm btn-outline-secondary text-decoration-none" class="style-btn"', 'class="btn btn-sm btn-outline-secondary style-btn text-decoration-none"'
$content = $content -replace 'class="btn btn-sm btn-outline-secondary" class="style-btn"', 'class="btn btn-sm btn-outline-secondary style-btn"'

# Save the file
$content | Set-Content $filePath -Encoding UTF8

Write-Host "HTML class attributes fixed!"
