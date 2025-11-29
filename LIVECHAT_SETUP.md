# Setup Live Chat untuk Rumah Sakit

## Opsi 1: Tawk.to (Gratis & Direkomendasikan)

### Langkah Setup:

1. **Buat Akun Tawk.to**
   - Kunjungi: https://www.tawk.to/
   - Klik "Sign Up For Free"
   - Daftar dengan email Anda

2. **Buat Property**
   - Setelah login, klik "Add Property"
   - Masukkan nama: "RSIZ Muhammadiyah Jatibarang"
   - Masukkan website: `http://127.0.0.1` atau `http://rsiz.local` (untuk development)
   - **Catatan**: Tawk.to tidak menerima `localhost`, gunakan `127.0.0.1` atau domain apapun
   - Bisa diubah nanti saat sudah punya domain production

3. **Dapatkan Widget Code**
   - Di dashboard Tawk.to, klik property Anda
   - Pilih "Administration" > "Channels" > "Chat Widget"
   - Copy kode yang terlihat seperti:
     ```
     https://embed.tawk.to/XXXXXXXXXXXXXXXX/YYYYYYYYYYYY
     ```

4. **Integrasikan ke Website**
   - Buka file: `src/views/UserDashboard.vue`
   - Cari baris:
     ```javascript
     s1.src = 'https://embed.tawk.to/YOUR_PROPERTY_ID/YOUR_WIDGET_ID';
     ```
   - Ganti dengan kode dari Tawk.to Anda

5. **Build Frontend**
   ```bash
   cd c:\wamp64\www\RSIZ\vue-argon-dashboard-laravel
   npm run build
   ```

6. **Test Live Chat**
   - Buka website di browser
   - Widget chat akan muncul di pojok kanan bawah
   - Test dengan mengirim pesan

### Fitur Tawk.to:
- ✅ Gratis selamanya
- ✅ Unlimited agents (admin)
- ✅ Unlimited chat
- ✅ Mobile apps (iOS & Android)
- ✅ Desktop apps (Windows, Mac, Linux)
- ✅ File sharing
- ✅ Screen sharing
- ✅ Pre-chat form
- ✅ Offline messages
- ✅ Chat history
- ✅ Customizable widget
- ✅ Analytics & reports

### Kustomisasi Widget:
Di dashboard Tawk.to:
1. **Appearance** - Ubah warna, posisi, ukuran
2. **Pre-chat** - Form sebelum chat dimulai
3. **Offline** - Pesan saat admin offline
4. **Auto-messages** - Pesan otomatis
5. **Triggers** - Popup otomatis berdasarkan kondisi

---

## Opsi 2: WhatsApp Floating Button

Alternatif sederhana menggunakan WhatsApp Business:

### Tambahkan WhatsApp Float Button:
File: `src/views/UserDashboard.vue`

```vue
<template>
  <div class="user-dashboard">
    <!-- existing content -->
    
    <!-- WhatsApp Float Button -->
    <a 
      href="https://wa.me/6281234567890?text=Halo, saya ingin bertanya tentang layanan rumah sakit"
      target="_blank"
      class="whatsapp-float"
      title="Chat via WhatsApp"
    >
      <i class="fab fa-whatsapp"></i>
    </a>
  </div>
</template>

<style>
.whatsapp-float {
  position: fixed;
  width: 60px;
  height: 60px;
  bottom: 20px;
  right: 20px;
  background-color: #25d366;
  color: #FFF;
  border-radius: 50px;
  text-align: center;
  font-size: 30px;
  box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.whatsapp-float:hover {
  background-color: #128c7e;
  transform: scale(1.1);
}
</style>
```

Ganti nomor `6281234567890` dengan nomor WhatsApp rumah sakit.

---

## Opsi 3: Crisp Chat

Alternatif lain yang juga bagus:

1. Daftar di: https://crisp.chat/
2. Buat website
3. Copy Website ID
4. Tambahkan di UserDashboard.vue:

```javascript
loadCrisp() {
  window.$crisp = [];
  window.CRISP_WEBSITE_ID = "YOUR_WEBSITE_ID";
  (function(){
    var d = document;
    var s = d.createElement("script");
    s.src = "https://client.crisp.chat/l.js";
    s.async = 1;
    d.getElementsByTagName("head")[0].appendChild(s);
  })();
}
```

---

## Rekomendasi:

**Untuk Rumah Sakit, saya rekomendasikan Tawk.to karena:**
1. Gratis selamanya dengan fitur lengkap
2. Multiple agents (bisa banyak admin yang handle chat)
3. Mobile & desktop apps untuk admin
4. Professional dan terpercaya
5. Chat history tersimpan
6. Analytics untuk monitoring

**Setup hanya butuh 5 menit!** 🚀
