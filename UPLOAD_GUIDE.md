# Panduan Upload Gambar di Admin

## Backend Setup ✅
Upload endpoint sudah dibuat di: `/api/v2/upload` (protected dengan auth:api)

**Response format:**
```json
{
  "success": true,
  "url": "http://127.0.0.1:8000/uploads/1234567890_abc123.jpg",
  "filename": "1234567890_abc123.jpg"
}
```

## Cara Menggunakan di Form Admin

### 1. Update Template (HTML)
Ganti input text URL dengan file input:

**Sebelum:**
```html
<input v-model="form.photo" type="text" class="form-control" placeholder="URL foto">
```

**Sesudah:**
```html
<input type="file" @change="handleFileUpload" accept="image/*" class="form-control" ref="photoInput">
<small v-if="form.photo" class="text-muted d-block mt-1">
  File terpilih: {{ form.photo.split('/').pop() }}
</small>
<small v-if="uploadingImage" class="text-info d-block mt-1">Mengupload gambar...</small>
```

### 2. Update Data
Tambahkan `uploadingImage` flag:

```javascript
data() {
  return {
    uploadingImage: false,
    form: {
      // ... fields lainnya
      photo: '' // atau logo, image, featured_image, dll
    }
  }
}
```

### 3. Tambahkan Method Upload
```javascript
methods: {
  async handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    this.uploadingImage = true;
    const formData = new FormData();
    formData.append('image', file);

    try {
      const response = await this.$axios.post('/api/v2/upload', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      
      if (response.data.success) {
        this.form.photo = response.data.url; // Ganti 'photo' sesuai field (logo, image, dll)
        alert('Gambar berhasil diupload!');
      }
    } catch (error) {
      alert('Gagal mengupload gambar: ' + error.message);
      this.$refs.photoInput.value = '';
    } finally {
      this.uploadingImage = false;
    }
  }
}
```

### 4. Update resetForm() 
Tambahkan reset file input:

```javascript
resetForm() {
  this.form = { /* ... */ };
  this.editingId = null;
  
  // Reset file input
  if (this.$refs.photoInput) {
    this.$refs.photoInput.value = '';
  }
}
```

### 5. Disable Button Saat Upload
```html
<button type="submit" class="btn btn-success" :disabled="uploadingImage">Simpan</button>
```

## Form yang Perlu Diupdate

- ✅ **AdminDoctors.vue** - field: `photo`
- ⬜ **AdminServices.vue** - field: `icon` atau `image`
- ⬜ **AdminNews.vue** - field: `featured_image`
- ⬜ **AdminPages.vue** - field: `featured_image`
- ⬜ **AdminPartners.vue** (jika ada) - field: `logo`
- ⬜ **AdminFacilities.vue** (jika ada) - field: `image`
- ⬜ **AdminGalleries.vue** (jika ada) - field: `image`

## Konfigurasi Server
- Upload folder: `laravel-json-api/public/uploads/`
- Max file size: 2MB
- Allowed types: jpeg, png, jpg, gif
- File naming: `timestamp_random.ext`

## Testing
1. Login sebagai admin
2. Buka halaman Dokter
3. Klik "Tambah Dokter"
4. Pilih foto menggunakan file input
5. Gambar akan otomatis diupload dan URL tersimpan
