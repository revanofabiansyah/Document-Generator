# Quick Reference Guide - Document Wizard

## File Locations

### Main Files
- **Wizard View:** `resources/views/user/document-wizard.blade.php`
- **Controller:** `app/Http/Controllers/UserDocumentController.php`
- **Models:** `app/Models/Document.php`, `app/Models/DocumentPart.php`
- **Routes:** `routes/web.php`

## JavaScript Functions

### updateLivePreview()
- **Purpose:** Render all 5 document steps in real-time
- **Triggered:** On every form input change
- **Reads:** All `.form-input-preview` fields + images from sessionStorage
- **Updates:** `#previewContent` div

### updateReviewStatus()
- **Purpose:** Update Step 5 review indicators
- **Triggered:** On form input change
- **Updates:** Status badges, progress bar, percentage
- **Calculates:** Completion based on filled sections

### saveImage(fieldName)
- **Purpose:** Handle file uploads and preview display
- **Triggered:** By Save button after file selection
- **Stores:** Base64 image data in sessionStorage
- **Calls:** updateLivePreview() automatically

### goToStep(step)
- **Purpose:** Navigate to specific step (1-5)
- **Hides:** All other steps, shows selected step
- **Updates:** Step indicators
- **Example:** `goToStep(2)`

### nextStep() / previousStep()
- **Purpose:** Navigate forward/backward
- **Validates:** Current step (optional)
- **Calls:** goToStep() internally

## CSS Classes

- `.form-input-preview` - Applied to all form fields that trigger preview update
- `.a4-preview` - Container for A4 paper simulation
- `.step-section` - Container for each step (display: none by default)
- `.sticky-top` - Makes preview stick to top on scroll

## Key IDs in HTML

### Form Inputs
- `#kop_instansi`, `#kop_nama`, `#kop_alamat`, `#kop_telp`, `#kop_email`
- `#header_tempat_tanggal`, `#header_judul`, `#header_nomor`, etc.
- `#body_paragraph1`, `#body_hari`, `#body_tanggal`, etc.
- `#footer_jabatan_kiri`, `#footer_nama_kiri`, etc.

### File Inputs
- `#kop_left_input`, `#kop_right_input`
- `#footer_signature_kiri_input`, `#footer_signature_kanon_input`, `#footer_signature_opsional_input`

### Buttons & Displays
- `#kop_left_save`, `#kop_right_save`, etc. (Save buttons)
- `#kop_left_name`, `#kop_right_name`, etc. (Filename displays)
- `#previewContent` (Live preview container)
- `#review-*-status` (Review section badges)
- `#review-completion` (Percentage display)
- `#review-progress` (Progress bar)

## Data Storage

### sessionStorage Keys
- `preview_kop_left` - Left header image (base64)
- `preview_kop_right` - Right header image (base64)
- `preview_footer_signature_kiri` - Left signature (base64)
- `preview_footer_signature_kanon` - Right signature (base64)
- `preview_footer_signature_opsional` - Optional signature (base64)

### Database (DocumentPart table)
- `document_id` - Foreign key to Document
- `part_name` - One of: 'kop', 'header', 'body', 'footer'
- `content` - Serialized JSON with all fields for that part

## API Endpoints

### User Routes
```
GET  /user-{user:name}/documents
     → UserDocumentController@list
     → Shows all published documents

GET  /user-{user:name}/doc/{document:id}/fill?step={1-5}
     → UserDocumentController@fill
     → Shows wizard with specific step

POST /user-{user:name}/doc/{document:id}/save
     → UserDocumentController@save
     → Saves filled form data
```

## Form Submission Flow

1. User fills all 5 steps
2. Clicks "Download & Simpan" button (step 5)
3. Form POSTs to `/user-{name}/doc/{id}/save`
4. Controller validates all 27 fields
5. Images uploaded to `storage/app/public/documents/`
6. DocumentPart records created/updated
7. Success redirect to documents list

## Adding New Fields

To add a new field to the wizard:

1. **Add to Form Step (HTML)**
   ```blade
   <input type="text" name="new_field" class="form-control form-input-preview" value="">
   ```

2. **Add to updateLivePreview() function**
   ```javascript
   const newField = document.querySelector('input[name="new_field"]')?.value || '';
   ```

3. **Add to live preview HTML**
   ```javascript
   ${newField ? `<p>${newField}</p>` : ''}
   ```

4. **Add to validation in Controller (save method)**
   ```php
   'new_field' => 'nullable|string|max:500',
   ```

5. **Add to form input submission handling**
   ```php
   'new_field' => $request->new_field,
   ```

## Debugging

### Check Browser Console
- `sessionStorage` - View stored images
- Network tab - Check POST submission
- Elements - Inspect form field classes

### Check Form Output
```php
// In controller
dd($request->all()); // Shows all submitted fields
dd($document->parts); // Shows saved database records
```

### Check Live Preview
```javascript
// In browser console
document.getElementById('previewContent').innerHTML
// Shows rendered HTML
```

## Styling Customization

### Preview Font
```css
.a4-content {
    font-family: 'Times New Roman', serif;
    font-size: 10px;
}
```

### Preview Colors
```css
.a4-preview {
    background: #f0f0f0; /* Paper color */
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

#previewContent {
    background: white;
    color: #333;
}
```

### Button Colors
```css
.btn-primary { /* Simpan & Lanjut button */
    background: #0d6efd;
}

.btn-success { /* Download & Simpan button */
    background: #198754;
}
```

## Performance Tips

- Live preview updates on every keystroke (use debounce if needed)
- Images stored in sessionStorage only (not persisted)
- A4 aspect ratio uses padding-bottom (no JS resize)
- Sticky preview uses CSS (no scroll listener)

## Troubleshooting Checklist

- [ ] All inputs have `form-input-preview` class
- [ ] updateLivePreview() function called on input events
- [ ] saveImage() called for file uploads
- [ ] sessionStorage keys match getItem() calls
- [ ] HTML structure matches querySelector targets
- [ ] CSS A4 ratio: padding-bottom: 141.4%
- [ ] Controller validation rules match form fields
- [ ] Database migrations create correct tables
- [ ] Storage directory writable: `storage/app/public/`
- [ ] Routes model binding works: `{user:name}`, `{document:id}`

## Dependencies

- Laravel 10+ (Blade templating)
- Bootstrap 5 (CSS framework)
- PHP 8.1+ (for nullsafe operator `?.`)
- Modern browser (ES6+, sessionStorage, FileReader API)

---

**Last Updated:** 2025-01-15
**Integration Status:** Complete
