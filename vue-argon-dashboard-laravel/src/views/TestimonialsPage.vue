<template>
  <div class="testimonials-page container py-5">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold mb-3">Testimoni Pasien</h1>
      <p class="text-muted">Berbagi pengalaman Anda bersama kami</p>
    </div>

    <!-- Form Testimoni -->
    <div class="row justify-content-center mb-5">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <h4 class="card-title mb-4 text-center">
              <i class="fas fa-comments me-2"></i>Kirim Testimoni Anda
            </h4>
            <form @submit.prevent="submitTestimoni">
              <div class="mb-3">
                <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                <input 
                  v-model="form.patient_name" 
                  type="text" 
                  class="form-control form-control-lg" 
                  placeholder="Masukkan nama Anda"
                  required
                >
              </div>
              
              <div class="mb-3">
                <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                <div class="star-rating">
                  <i 
                    v-for="star in 5" 
                    :key="star"
                    @click="form.rating = star"
                    :class="['fas fa-star', star <= form.rating ? 'text-warning' : 'text-muted']"
                    style="cursor: pointer; font-size: 2rem; margin: 0 5px;"
                  ></i>
                </div>
                <div class="form-text">Klik bintang untuk memberikan rating</div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold">Testimoni <span class="text-danger">*</span></label>
                <textarea 
                  v-model="form.message" 
                  class="form-control" 
                  rows="5"
                  placeholder="Ceritakan pengalaman Anda dengan layanan kami..."
                  required
                ></textarea>
                <div class="form-text">Minimal 20 karakter</div>
              </div>
              <div class="d-grid">
                <button 
                  type="submit" 
                  class="btn btn-primary btn-lg"
                  :disabled="submitting"
                >
                  <span v-if="submitting">
                    <i class="fas fa-spinner fa-spin me-2"></i>Mengirim...
                  </span>
                  <span v-else>
                    <i class="fas fa-paper-plane me-2"></i>Kirim Testimoni
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Daftar Testimoni -->
    <div class="row">
      <div class="col-12 mb-4">
        <h3 class="text-center fw-bold">Apa Kata Mereka</h3>
        <p class="text-center text-muted">Pengalaman pasien kami</p>
      </div>
      <div class="col-md-4 mb-4" v-for="t in testimonials" :key="t.id">
        <div class="card h-100 shadow-sm border-0 hover-card">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <i class="fas fa-user-circle fa-4x text-primary"></i>
            </div>
            <h5 class="fw-bold">{{ t.patient_name }}</h5>
            <p class="small text-muted mb-3">
              <i class="fas fa-calendar-alt me-1"></i>{{ formatDate(t.created_at) }}
            </p>
            <div class="rating mb-2">
              <i 
                v-for="star in 5" 
                :key="star"
                :class="['fas fa-star', star <= t.rating ? 'text-warning' : 'text-muted']"
              ></i>
            </div>
            <p class="text-muted">{{ t.message }}</p>
          </div>
        </div>
      </div>
    </div>

    <div v-if="testimonials.length === 0" class="text-center text-muted py-5">
      <i class="fas fa-inbox fa-3x mb-3"></i>
      <p>Belum ada testimoni. Jadilah yang pertama!</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TestimonialsPage',
  data() {
    return { 
      testimonials: [],
      form: {
        patient_name: '',
        message: '',
        rating: 5
      },
      submitting: false
    };
  },
  methods: {
    formatDate(d) { 
      return new Date(d).toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      }); 
    },
    async submitTestimoni() {
      if (this.form.message.length < 20) {
        alert('Testimoni minimal 20 karakter');
        return;
      }

      this.submitting = true;
      try {
        await this.$axios.post('/api/v2/testimonials/public', {
          patient_name: this.form.patient_name,
          message: this.form.message,
          rating: this.form.rating,
          status: 'pending'
        });

        alert('Terima kasih! Testimoni Anda telah dikirim dan menunggu persetujuan admin.');
        
        // Reset form
        this.form.patient_name = '';
        this.form.message = '';
        this.form.rating = 5;
        
        // Refresh list
        this.fetchTestimonials();
      } catch (error) {
        alert('Gagal mengirim testimoni: ' + (error.response?.data?.message || error.message));
      } finally {
        this.submitting = false;
      }
    },
    async fetchTestimonials() {
      try {
        const res = await this.$axios.get('/api/v2/testimonials');
        this.testimonials = res.data.data;
      } catch (error) {
        // Error fetching testimonials
        this.testimonials = [];
      }
    }
  },
  mounted() {
    this.fetchTestimonials();
  }
}
</script>

<style scoped>
.hover-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
}

.rating {
  font-size: 0.9rem;
}

.card {
  border-radius: 15px;
}

.btn-primary {
  background: linear-gradient(135deg, #0d7377 0%, #0dd1c2 100%);
  border: none;
  border-radius: 50px;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(13, 115, 119, 0.3);
}

.star-rating {
  display: flex;
  gap: 5px;
  align-items: center;
}

.star-rating i {
  transition: all 0.2s ease;
}

.star-rating i:hover {
  transform: scale(1.2);
}

.star-rating i.text-warning {
  animation: starPulse 0.3s ease;
}

@keyframes starPulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.3);
  }
}
</style>
