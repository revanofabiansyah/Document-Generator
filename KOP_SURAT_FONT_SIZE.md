# Kop Surat Customization - Font Size per Field

## Overview
User sekarang bisa customize ukuran font setiap field di bagian Kop Surat secara individual.

## Fitur

### 1. Field yang Bisa Dikustomisasi
- **Instansi/Perusahaan** - Font size default: 12px
- **Nama Instansi/Cabang/Dinas** - Font size default: 10px
- **Jalan/Alamat** - Font size default: 9px
- **Telp/Kontak** - Font size default: 9px
- **Email** - Font size default: 9px

### 2. Font Size Options
Setiap field memiliki pilihan ukuran font:
- **Instansi**: 10px, 12px, 14px, 16px, 18px
- **Nama Instansi**: 9px, 10px, 11px, 12px
- **Alamat**: 8px, 9px, 10px, 11px
- **Kontak/Email**: 8px, 9px, 10px

### 3. Live Preview
Ketika user mengubah:
- Teks di field → Preview update otomatis
- Font size dropdown → Preview update otomatis dengan ukuran baru

### 4. Visual Design
Setiap field di-group dengan background color berbeda (bg-light) agar lebih terorganisir:
- Instansi field: Highlighted dengan border
- Nama field: Highlighted dengan border
- Alamat field: Highlighted dengan border
- Kontak field: Grouped with telp + email

## Implementation Details

### Form Fields
```blade
<!-- Instansi -->
<textarea name="kop_instansi" class="form-control form-input-preview"></textarea>
<select name="kop_instansi_font_size" class="form-select form-input-preview"></select>

<!-- Nama -->
<textarea name="kop_nama" class="form-control form-input-preview"></textarea>
<select name="kop_nama_font_size" class="form-select form-input-preview"></select>

<!-- Alamat -->
<textarea name="kop_alamat" class="form-control form-input-preview"></textarea>
<select name="kop_alamat_font_size" class="form-select form-input-preview"></select>

<!-- Telp -->
<input name="kop_telp" class="form-control form-input-preview">
<select name="kop_telp_font_size" class="form-select form-input-preview"></select>

<!-- Email -->
<input name="kop_email" class="form-control form-input-preview">
<select name="kop_email_font_size" class="form-select form-input-preview"></select>
```

### JavaScript Handling
Dalam `updateLivePreview()` function:
1. Get nilai dari setiap field
2. Get nilai dari setiap font size select
3. Build HTML dengan style `font-size: ${variableName}`
4. Update preview container dengan hasil HTML

### CSS Classes
- `.form-input-preview` - Trigger update saat ada perubahan input
- `.bg-light` - Background warna untuk field grouping
- `.border` - Border untuk visual separation
- `.rounded` - Border radius untuk tampilan lebih rapi

## User Experience Flow

1. **User ke Step 1: Kop Surat**
2. **User isi teks di field** (Instansi, Nama, Alamat, Telp, Email)
3. **Preview muncul sebelah kanan dengan default font size**
4. **User ubah font size di dropdown setiap field**
5. **Preview update otomatis dengan font size baru**
6. **User upload foto kiri/kanan**
7. **Foto appear di preview**
8. **User puas → Click "Simpan & Lanjut"**

## Database Saving
Font size values tersimpan sebagai field terpisah:
- `kop_instansi_font_size`
- `kop_nama_font_size`
- `kop_alamat_font_size`
- `kop_telp_font_size`
- `kop_email_font_size`

## Future Enhancements
- Add font size untuk Header, Body, Footer fields
- Add color picker untuk setiap field
- Add alignment selector (left, center, right)
- Add bold/italic/underline toggle
- Add line height adjustment
- Preset templates untuk styling

---

**Status**: ✅ IMPLEMENTED & READY TO TEST
**Last Updated**: December 20, 2025
