<template>
  <div class="admin-layout d-flex">
    <!-- Sidebar -->
    <aside class="sidebar text-white">
      <div class="sidebar-header p-3">
        <div class="brand mb-3 d-flex align-items-center">
          <span class="me-2 brand-icon">🏥</span>
          <strong>Admin Panel</strong>
        </div>
      </div>

      <div class="sidebar-menu">
        <ul class="nav flex-column px-3">
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/dashboard">
              <span class="me-2">📊</span>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/doctors">
              <span class="me-2">👨‍⚕️</span>
              <span>Dokter</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/services">
              <span class="me-2">🧰</span>
              <span>Layanan</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/news">
              <span class="me-2">📰</span>
              <span>Berita</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/careers">
              <span class="me-2">💼</span>
              <span>Karir</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/applications">
              <span class="me-2">📝</span>
              <span>Lamaran</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/facilities">
              <span class="me-2">🏥</span>
              <span>Fasilitas</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/contacts">
              <span class="me-2">✉️</span>
              <span>Pesan</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/partners">
              <span class="me-2">🤝</span>
              <span>Mitra</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/pages">
              <span class="me-2">📄</span>
              <span>Tentang Kami</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/testimonials">
              <span class="me-2">💬</span>
              <span>Testimoni</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/chat">
              <span class="me-2">💭</span>
              <span>Live Chat</span>
              <span v-if="unreadChatCount > 0" class="badge bg-danger ms-2">{{ unreadChatCount }}</span>
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/admin/settings">
              <span class="me-2">⚙️</span>
              <span>Pengaturan</span>
            </router-link>
          </li>
        </ul>
      </div>

      <div class="sidebar-footer p-3">
        <button class="btn btn-danger w-100" @click="logout">Logout</button>
      </div>
    </aside>

    <!-- Content -->
    <main class="content flex-grow-1 p-4">
      <router-view />
    </main>
  </div>
  
</template>

<script>
export default {
  name: 'AdminLayout',
  data() {
    return {
      unreadChatCount: 0,
      chatPollInterval: null,
      lastChatCount: 0,
      notificationSound: null
    }
  },
  methods: {
    logout() {
      if (confirm('Anda yakin ingin logout?')) {
        localStorage.removeItem('token');
        this.$router.push('/admin/login');
      }
    },
    
    async checkNewChats() {
      try {
        const response = await this.$axios.get('/api/v2/chat/rooms');
        const rooms = response.data.data;
        
        // Count unread chats
        this.unreadChatCount = rooms.filter(room => !room.is_read).length;
        
        // Detect new chat (total bertambah)
        if (rooms.length > this.lastChatCount && this.lastChatCount > 0) {
          this.playNotificationSound();
          this.showBrowserNotification();
        }
        
        this.lastChatCount = rooms.length;
      } catch (error) {
        console.error('Error checking chats:', error);
      }
    },
    
    playNotificationSound() {
      if (this.notificationSound) {
        this.notificationSound.play().catch(err => {
          console.log('Could not play sound:', err);
        });
      }
    },
    
    showBrowserNotification() {
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Chat Baru! 💬', {
          body: 'Ada pesan baru dari pasien di Live Chat',
          icon: '/favicon.ico',
          badge: '/favicon.ico',
          tag: 'new-chat',
          requireInteraction: false
        });
      }
    },
    
    startChatPolling() {
      // Check immediately
      this.checkNewChats();
      
      // Poll every 5 seconds
      this.chatPollInterval = setInterval(() => {
        this.checkNewChats();
      }, 5000);
    },
    
    stopChatPolling() {
      if (this.chatPollInterval) {
        clearInterval(this.chatPollInterval);
        this.chatPollInterval = null;
      }
    }
  },
  mounted() {
    if (!localStorage.getItem('token')) {
      this.$router.push('/admin/login');
      return;
    }
    
    // Initialize notification sound
    this.notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKjj8LdjHAU1jdXrz4IuBSh+zPLaizsKGGS66emkUBELTKXh7bhkGgU0iM3w1IU3CBxmt+rqmkkSDVCl4e+2ZBwFN4vQ7s+AMQUngMvw2og1CBdks+nln1ARCA==');
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
      Notification.requestPermission();
    }
    
    // Start polling for new chats
    this.startChatPolling();
  },
  
  beforeUnmount() {
    this.stopChatPolling();
  }
}
</script>

<style scoped>
.admin-layout { 
  min-height: 100vh; 
}

.sidebar {
  width: 260px;
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  background: linear-gradient(180deg, #0d7377 0%, #0dd1c2 100%);
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

.sidebar-header {
  flex-shrink: 0;
}

.sidebar-menu {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding-bottom: 20px;
}

/* Custom scrollbar untuk sidebar */
.sidebar-menu::-webkit-scrollbar {
  width: 6px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

.sidebar-footer {
  flex-shrink: 0;
  border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.brand-icon { 
  font-size: 20px; 
}

.nav-link {
  color: #e6fffb;
  padding: 10px 12px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  transition: all 0.3s ease;
  text-decoration: none;
  margin-bottom: 5px;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}

.nav-link.router-link-active {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  font-weight: 600;
}

.content { 
  background: #f5f7fa;
  margin-left: 260px;
  min-height: 100vh;
  overflow-y: auto;
}

.btn-danger {
  background: #e63946;
  border: none;
  padding: 10px;
  font-weight: 600;
}

.btn-danger:hover {
  background: #d62828;
}
</style>
