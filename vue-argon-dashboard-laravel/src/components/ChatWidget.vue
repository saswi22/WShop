<template>
  <div class="chat-widget">
    <!-- Floating Button -->
    <button 
      v-if="!showChat" 
      @click="toggleChat" 
      class="chat-button"
      :class="{ 'has-unread': hasUnreadMessages }"
    >
      <i class="fas fa-comments"></i>
      <span v-if="hasUnreadMessages" class="unread-badge"></span>
    </button>

    <!-- Chat Box -->
    <div v-if="showChat" class="chat-box">
      <!-- Header -->
      <div class="chat-header" :class="{ 'header-queued': queueStatus.status === 'queued' }">
        <div>
          <h6 class="mb-0">
            💬 Live Chat
            <span v-if="queueStatus.status === 'queued'" class="badge bg-warning text-dark ms-2">
              Antrian #{{ queueStatus.queue_position }}
            </span>
            <span v-if="queueStatus.status === 'active'" class="badge bg-success text-white ms-2 small">
              Online
            </span>
          </h6>
          <small class="text-white-50">
            <span v-if="queueStatus.status === 'active'">Admin siap membalas Anda</span>
            <span v-if="queueStatus.status === 'queued'">Menunggu giliran... ({{ queueStatus.active_chats || 3 }} chat sedang berlangsung)</span>
          </small>
        </div>
        <button @click="toggleChat" class="btn-close-chat">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Form untuk visitor info (jika belum ada room) -->
      <div v-if="!chatRoomId" class="chat-form p-3">
        <h6 class="mb-3">Mulai Chat</h6>
        <div class="mb-2">
          <input 
            v-model="visitorInfo.name" 
            type="text" 
            class="form-control form-control-sm" 
            placeholder="Nama Anda"
            @keyup.enter="createRoom"
          >
        </div>
        <div class="mb-2">
          <input 
            v-model="visitorInfo.email" 
            type="email" 
            class="form-control form-control-sm" 
            placeholder="Email Anda"
            @keyup.enter="createRoom"
          >
        </div>
        <div class="mb-3">
          <input 
            v-model="visitorInfo.phone" 
            type="text" 
            class="form-control form-control-sm" 
            placeholder="Nomor Telepon"
            @keyup.enter="createRoom"
          >
        </div>
        <button @click="createRoom" class="btn btn-primary btn-sm w-100" :disabled="loading">
          <span v-if="loading">Memulai...</span>
          <span v-else>Mulai Chat</span>
        </button>
      </div>

      <!-- Chat Messages -->
      <div v-else class="chat-messages" ref="messageContainer">
        <div v-if="messages.length === 0" class="text-center text-muted py-4">
          <i class="fas fa-comments fa-2x mb-2"></i>
          <p class="mb-0">Kirim pesan pertama Anda</p>
        </div>
        
        <div 
          v-for="message in messages" 
          :key="message.id" 
          class="message"
          :class="message.sender_type === 'user' ? 'message-user' : 'message-admin'"
        >
          <div class="message-bubble">
            <div class="message-text">{{ message.message }}</div>
            <div class="message-time">{{ formatTime(message.created_at) }}</div>
          </div>
        </div>
      </div>

      <!-- Message Input -->
      <div v-if="chatRoomId" class="chat-input">
        <input 
          v-model="newMessage" 
          type="text" 
          class="form-control form-control-sm" 
          :placeholder="queueStatus.status === 'queued' ? 'Anda di antrian...' : 'Ketik pesan...'"
          @keyup.enter="sendMessage"
          :disabled="sending || queueStatus.status === 'queued'"
        >
        <button @click="sendMessage" class="btn btn-primary btn-sm" :disabled="sending || !newMessage.trim() || queueStatus.status === 'queued'">
          <i class="fas fa-paper-plane"></i>
        </button>
      </div>

      <!-- Queue Status Banner -->
      <div v-if="queueStatus.status === 'queued'" class="queue-banner">
        <div class="d-flex align-items-center justify-content-center">
          <i class="fas fa-clock me-2"></i>
          <div>
            <strong>Antrian ke-{{ queueStatus.queue_position }}</strong>
            <br>
            <small>Sedang ada {{ queueStatus.active_chats || 3 }} percakapan aktif. Mohon tunggu...</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChatWidget',
  data() {
    return {
      showChat: false,
      chatRoomId: null,
      visitorInfo: {
        name: '',
        email: '',
        phone: ''
      },
      messages: [],
      newMessage: '',
      loading: false,
      sending: false,
      hasUnreadMessages: false,
      pollInterval: null,
      queueStatus: {
        status: 'active',
        queue_position: null
      },
      lastMessageCount: 0,
      notificationSound: null
    }
  },
  methods: {
    toggleChat() {
      this.showChat = !this.showChat;
      if (this.showChat && this.chatRoomId) {
        this.scrollToBottom();
        this.hasUnreadMessages = false;
      }
    },
    
    async createRoom() {
      if (!this.visitorInfo.name || !this.visitorInfo.email) {
        alert('Nama dan email wajib diisi');
        return;
      }

      this.loading = true;
      try {
        const response = await this.$axios.post('/api/v2/chat/room', {
          visitor_name: this.visitorInfo.name,
          visitor_email: this.visitorInfo.email,
          visitor_phone: this.visitorInfo.phone
        });
        this.chatRoomId = response.data.data.id;
        this.queueStatus.status = response.data.data.status;
        this.queueStatus.queue_position = response.data.data.queue_position;
        
        // Tampilkan pesan status
        if (response.data.message) {
          alert(response.data.message);
        }
        
        // Simpan room ID di localStorage
        localStorage.setItem('chatRoomId', this.chatRoomId);
        localStorage.setItem('visitorInfo', JSON.stringify(this.visitorInfo));
        
        // Mulai polling messages
        this.startPolling();
        
        // Load existing messages
        await this.loadMessages();
      } catch (error) {
        console.error('Error creating room:', error);
        alert('Gagal memulai chat. Silakan coba lagi.');
      } finally {
        this.loading = false;
      }
    },
    
    async loadMessages() {
      if (!this.chatRoomId) return;
      
      try {
        const response = await this.$axios.get(`/api/v2/chat/messages/${this.chatRoomId}`);
        const newMessages = response.data.data;
        
        // Check for new admin messages dan play sound
        if (newMessages.length > this.lastMessageCount && !this.showChat) {
          const hasNewAdminMessage = newMessages.some(msg => 
            msg.sender_type === 'admin' && 
            !this.messages.find(m => m.id === msg.id)
          );
          if (hasNewAdminMessage) {
            this.hasUnreadMessages = true;
            this.playNotificationSound();
          }
        }
        
        this.lastMessageCount = newMessages.length;
        this.messages = newMessages;
        
        if (this.showChat) {
          this.$nextTick(() => {
            this.scrollToBottom();
          });
        }
      } catch (error) {
        // Silent fail - only log if it's not a 404
        if (error.response && error.response.status !== 404) {
          console.error('Error loading messages:', error);
        }
      }
    },

    async loadQueueStatus() {
      if (!this.chatRoomId) return;
      
      try {
        const response = await this.$axios.get(`/api/v2/chat/queue-status/${this.chatRoomId}`);
        this.queueStatus.status = response.data.data.status;
        this.queueStatus.queue_position = response.data.data.queue_position;
        this.queueStatus.active_chats = response.data.data.active_chats || 3;
      } catch (error) {
        // Silent fail - room might not exist yet
        if (error.response && error.response.status === 404) {
          // Room not found, stop polling
          this.queueStatus.status = 'unknown';
        }
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return;
      
      this.sending = true;
      try {
        await this.$axios.post('/api/v2/chat/message', {
          room_id: this.chatRoomId,
          message: this.newMessage,
          sender_type: 'user'
        });
        
        this.newMessage = '';
        await this.loadMessages();
      } catch (error) {
        console.error('Error sending message:', error);
        alert('Gagal mengirim pesan. Silakan coba lagi.');
      } finally {
        this.sending = false;
      }
    },
    
    scrollToBottom() {
      if (this.$refs.messageContainer) {
        this.$nextTick(() => {
          const container = this.$refs.messageContainer;
          container.scrollTop = container.scrollHeight;
        });
      }
    },
    
    formatTime(datetime) {
      const date = new Date(datetime);
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');
      return `${hours}:${minutes}`;
    },
    
    startPolling() {
      // Poll every 3 seconds for new messages
      this.pollInterval = setInterval(() => {
        this.loadMessages();
        this.loadQueueStatus();
      }, 3000);
    },
    
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval);
        this.pollInterval = null;
      }
    },
    
    playNotificationSound() {
      // Play browser notification sound
      if (this.notificationSound) {
        this.notificationSound.play().catch(err => {
          console.log('Could not play notification sound:', err);
        });
      }
      
      // Browser notification jika ada permission
      if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Pesan Baru', {
          body: 'Admin telah membalas pesan Anda',
          icon: '/favicon.ico'
        });
      }
    },
    
    requestNotificationPermission() {
      if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
      }
    },
    
    loadFromLocalStorage() {
      const savedRoomId = localStorage.getItem('chatRoomId');
      const savedVisitorInfo = localStorage.getItem('visitorInfo');
      
      if (savedRoomId) {
        this.chatRoomId = savedRoomId;
        this.loadMessages();
        this.startPolling();
      }
      
      if (savedVisitorInfo) {
        this.visitorInfo = JSON.parse(savedVisitorInfo);
      }
    }
  },
  
  mounted() {
    // Load from localStorage jika sudah pernah chat
    this.loadFromLocalStorage();
    
    // Initialize notification sound
    this.notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiTYIG2m98OScTgwOUKjj8LdjHAU1jdXrz4IuBSh+zPLaizsKGGS66emkUBELTKXh7bhkGgU0iM3w1IU3CBxmt+rqmkkSDVCl4e+2ZBwFN4vQ7s+AMQUngMvw2og1CBdks+nln1ARCA==');
    
    // Request notification permission
    this.requestNotificationPermission();
  },
  
  beforeUnmount() {
    this.stopPolling();
  }
}
</script>

<style scoped>
.chat-widget {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 1000;
}

.chat-button {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  color: white;
  font-size: 24px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
}

.chat-button:hover {
  transform: scale(1.1);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

.chat-button.has-unread {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

.unread-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 12px;
  height: 12px;
  background: #ff4757;
  border-radius: 50%;
  border: 2px solid white;
}

.chat-box {
  width: 350px;
  height: 500px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-header.header-queued {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.chat-header .badge {
  font-size: 11px;
  padding: 4px 8px;
  animation: badge-pulse 1.5s infinite;
}

@keyframes badge-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.btn-close-chat {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-close-chat:hover {
  background: rgba(255, 255, 255, 0.3);
}

.chat-form {
  background: #f8f9fa;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
  background: #f8f9fa;
}

.chat-messages::-webkit-scrollbar {
  width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
  background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 3px;
}

.message {
  margin-bottom: 12px;
  display: flex;
}

.message-user {
  justify-content: flex-end;
}

.message-admin {
  justify-content: flex-start;
}

.message-bubble {
  max-width: 70%;
  padding: 10px 14px;
  border-radius: 16px;
  word-wrap: break-word;
}

.message-user .message-bubble {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-bottom-right-radius: 4px;
}

.message-admin .message-bubble {
  background: white;
  color: #333;
  border-bottom-left-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-text {
  font-size: 14px;
  line-height: 1.4;
  margin-bottom: 4px;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
}

.chat-input {
  display: flex;
  gap: 8px;
  padding: 12px;
  background: white;
  border-top: 1px solid #e9ecef;
}

.chat-input input {
  flex: 1;
}

.chat-input button {
  width: 36px;
  height: 36px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}

.queue-banner {
  background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
  color: #856404;
  padding: 15px 16px;
  text-align: center;
  font-size: 13px;
  border-top: 2px solid #ffc107;
  animation: pulse-banner 2s infinite;
}

.queue-banner strong {
  font-size: 15px;
  color: #d68910;
}

.queue-banner small {
  font-size: 12px;
  opacity: 0.9;
}

@keyframes pulse-banner {
  0%, 100% {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe8a1 100%);
  }
  50% {
    background: linear-gradient(135deg, #ffe8a1 0%, #ffd966 100%);
  }
}

/* Mobile responsive */
@media (max-width: 576px) {
  .chat-box {
    width: calc(100vw - 40px);
    height: calc(100vh - 40px);
    max-height: 600px;
  }
}
</style>
