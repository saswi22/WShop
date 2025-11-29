<template>
  <div class="admin-login-page">
    <div class="container">
      <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5">
          <div class="card shadow">
            <div class="card-body p-5">
              <h3 class="card-title text-center mb-4">🏥 Admin Login</h3>
              
              <form @submit.prevent="submitLogin">
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input v-model="credentials.email" type="email" class="form-control" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input v-model="credentials.password" type="password" class="form-control" required>
                </div>

                <button :disabled="isLoading" type="submit" class="btn btn-primary w-100 mb-3">
                  <span v-if="!isLoading">Login</span>
                  <span v-else>
                    <span class="spinner-border spinner-border-sm me-2"></span>Logging in...
                  </span>
                </button>

                <div v-if="errorMessage" class="alert alert-danger mb-0" role="alert">
                  {{ errorMessage }}
                </div>
              </form>

              <hr>
              <p class="text-center text-muted small">
                Demo Credentials:<br>
                Email: admin@example.com<br>
                Password: password
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminLogin',
  data() {
    return {
      credentials: {
        email: 'admin@example.com',
        password: 'password'
      },
      isLoading: false,
      errorMessage: null
    }
  },
  methods: {
    async submitLogin() {
      this.isLoading = true;
      this.errorMessage = null;

      console.log('Attempting login with:', this.credentials);

      try {
        const response = await this.$axios.post('/api/v2/login', this.credentials);

        console.log('Login response:', response);
        console.log('Response data:', response.data);

        // The backend uses Passport /oauth/token and returns access_token
        const token = response.data?.access_token || response.data?.token || response.data?.accessToken;
        if (token) {
          localStorage.setItem('token', token);
          this.$axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          this.$router.push('/admin/dashboard');
          return;
        }

        // If we reach here there was no token in the response
        this.errorMessage = response.data?.message || 'Login gagal. Silakan coba lagi.';
      } catch (error) {
        console.error('Login error details:', {
          message: error.message,
          status: error.response?.status,
          statusText: error.response?.statusText,
          data: error.response?.data,
          headers: error.response?.headers
        });
        
        // Extract error message from JSON:API format
        let errorMsg = 'Login gagal. Silakan coba lagi.';
        if (error.response?.data?.errors && error.response.data.errors.length > 0) {
          const firstError = error.response.data.errors[0];
          errorMsg = firstError.detail || firstError.title || errorMsg;
          console.error('JSON:API Error:', firstError);
        } else if (error.response?.data?.message) {
          errorMsg = error.response.data.message;
        }
        
        this.errorMessage = errorMsg;
      } finally {
        this.isLoading = false;
      }
    }
  },
  mounted() {
    // If already authenticated, redirect to dashboard
    if (localStorage.getItem('token')) {
      this.$router.push('/admin/dashboard');
    }
  }
}
</script>

<style scoped>
.admin-login-page {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
}

.card {
  border: none;
}

.card-title {
  color: #667eea;
  font-weight: bold;
}
</style>
