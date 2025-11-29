<template>
  <div class="contact-page">
    <div class="container py-5">
      <h1 class="mb-5">Hubungi Kami</h1>
      
      <div class="row">
        <!-- Contact Form -->
        <div class="col-lg-6 mb-5">
          <h4 class="mb-4">Kirim Pesan</h4>
          <form @submit.prevent="submitForm">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap *</label>
              <input v-model="form.name" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input v-model="form.email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nomor Telepon</label>
              <input v-model="form.phone" type="tel" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Subjek *</label>
              <input v-model="form.subject" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Pesan *</label>
              <textarea v-model="form.message" class="form-control" rows="5" required></textarea>
            </div>
            <button :disabled="isSubmitting" type="submit" class="btn btn-primary w-100">
              <span v-if="!isSubmitting">Kirim Pesan</span>
              <span v-else>
                <span class="spinner-border spinner-border-sm me-2"></span>Mengirim...
              </span>
            </button>
            <div v-if="submitMessage" class="alert" :class="submitMessage.type === 'success' ? 'alert-success' : 'alert-danger'" role="alert" style="margin-top: 15px;">
              {{ submitMessage.text }}
            </div>
          </form>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-6">
          <h4 class="mb-4">Informasi Kontak</h4>
          
          <div class="contact-info mb-4" v-if="settings.contact_address">
            <h6 class="mb-2">📍 Alamat</h6>
            <p>{{ settings.contact_address }}</p>
          </div>

          <div class="contact-info mb-4" v-if="settings.contact_phone">
            <h6 class="mb-2">📞 Telepon</h6>
            <p>
              <a :href="'tel:' + settings.contact_phone">{{ settings.contact_phone }}</a>
            </p>
          </div>

          <div class="contact-info mb-4" v-if="settings.contact_email">
            <h6 class="mb-2">📧 Email</h6>
            <p>
              <a :href="'mailto:' + settings.contact_email">{{ settings.contact_email }}</a>
            </p>
          </div>

          <div class="contact-info mb-4" v-if="settings.contact_hours">
            <h6 class="mb-2">🕐 Jam Operasional</h6>
            <p>{{ settings.contact_hours }}</p>
          </div>

          <!-- Map -->
          <div class="map-container mt-4" style="border-radius: 5px; overflow: hidden;">
            <iframe 
              v-if="mapEmbedUrl"
              width="100%" 
              height="300" 
              :src="mapEmbedUrl"
              style="border:0;"
              allowfullscreen="" 
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
            <iframe 
              v-else
              width="100%" 
              height="300" 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322635!2d106.8270899!3d-6.175092!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e6e6e6e6e7%3A0x0!2sJakarta!5e0!3m2!1sen!2sid!4v1234567890" 
              allowfullscreen="" 
              loading="lazy">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactPage',
  data() {
    return {
      form: {
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      },
      isSubmitting: false,
      submitMessage: null,
      settings: {}
    }
  },
  computed: {
    mapEmbedUrl() {
      const loc = this.settings.general?.map_location || this.settings.contact?.map_location;
      if (!loc) return '';
      
      // Jika link Google Maps embed
      if (loc.includes('maps/embed') || loc.includes('output=embed')) {
        return loc;
      }
      // Jika link Google Maps biasa
      if (loc.startsWith('https://www.google.com/maps')) {
        return loc.replace('/maps?', '/maps/embed?');
      }
      // Jika koordinat
      if (/^-?\d+\.\d+,-?\d+\.\d+$/.test(loc.trim())) {
        return `https://maps.google.com/maps?q=${loc.trim()}&z=15&output=embed`;
      }
      // Default
      return loc;
    }
  },
  methods: {
    async fetchSettings() {
      try {
        const response = await this.$axios.get('/api/v2/settings');
        this.settings = response.data.data;
      } catch (error) {
        // Silent fail
      }
    },
    async submitForm() {
      this.isSubmitting = true;
      this.submitMessage = null;

      try {
        const response = await this.$axios.post('/api/v2/contacts', this.form);
        
        if (response.data.success) {
          this.submitMessage = {
            type: 'success',
            text: 'Pesan Anda telah dikirim. Terima kasih! Kami akan segera menghubungi Anda.'
          };
          
          // Reset form
          this.form = {
            name: '',
            email: '',
            phone: '',
            subject: '',
            message: ''
          };
        }
      } catch (error) {
        this.submitMessage = {
          type: 'error',
          text: 'Gagal mengirim pesan. Silakan coba lagi nanti.'
        };
        console.error('Error submitting contact:', error);
      } finally {
        this.isSubmitting = false;
      }
    }
  },
  mounted() {
    this.fetchSettings();
  }
}
</script>

<style scoped>
.contact-info {
  padding: 15px;
  background: #f8f9fa;
  border-radius: 5px;
}

.contact-info h6 {
  color: #0d6efd;
  font-weight: bold;
}

.contact-info a {
  color: #0d6efd;
  text-decoration: none;
}

.contact-info a:hover {
  text-decoration: underline;
}

.map-container {
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
</style>
