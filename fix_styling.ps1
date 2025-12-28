# Read the file
$filePath = "resources\views\user\document-wizard.blade.php"
$content = Get-Content $filePath -Raw

# Replace all B/I/U button inline styles with style-btn class
$content = $content -replace 'style="font-weight: bold; width: 32px; padding: 4px;"', 'class="style-btn" style="font-weight: bold;"'
$content = $content -replace 'style="font-style: italic; width: 32px; padding: 4px;"', 'class="style-btn" style="font-style: italic;"'
$content = $content -replace 'style="text-decoration: underline; width: 32px; padding: 4px;"', 'class="style-btn" style="text-decoration: underline;"'

# Replace all font-size select dropdowns
$content = $content -replace 'class="form-select form-select-sm form-input-preview" style="width: auto; max-width: 80px;"', 'class="form-select form-select-sm form-input-preview font-size-select"'

# Save the file
$content | Set-Content $filePath -Encoding UTF8

Write-Host "All styling replacements completed!"
