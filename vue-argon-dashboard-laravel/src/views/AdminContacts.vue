<template>
  <div class="admin-contacts">
    <h2 class="mb-4">Pesan Masuk</h2>

    <!-- Filter -->
    <div class="mb-3">
      <select v-model="selectedStatus" class="form-select w-auto d-inline-block">
        <option value="">Semua Status</option>
        <option value="unread">Belum Dibaca</option>
        <option value="read">Sudah Dibaca</option>
        <option value="replied">Sudah Dibalas</option>
      </select>
    </div>

    <!-- Contacts List -->
    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Subjek</th>
            <th>Tanggal</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="contact in filteredContacts" :key="contact.id">
            <td>{{ contact.name }}</td>
            <td>{{ contact.email }}</td>
            <td>{{ contact.subject }}</td>
            <td>{{ formatDate(contact.created_at) }}</td>
            <td>
              <span class="badge" :class="getBadgeClass(contact.status)">
                {{ getStatusLabel(contact.status) }}
              </span>
            </td>
            <td>
              <button @click="viewContact(contact)" class="btn btn-info btn-sm me-2">Lihat</button>
              <button @click="markAsRead(contact)" v-if="contact.status === 'unread'" class="btn btn-warning btn-sm me-2">Tandai Dibaca</button>
              <button @click="deleteContact(contact.id)" class="btn btn-danger btn-sm">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="filteredContacts.length === 0" class="alert alert-info text-center">
      Tidak ada pesan
    </div>

    <!-- Detail Modal -->
    <div v-if="selectedContact" class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Detail Pesan</h5>
            <button @click="selectedContact = null" type="button" class="btn-close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Nama:</strong> {{ selectedContact.name }}</p>
            <p><strong>Email:</strong> {{ selectedContact.email }}</p>
            <p v-if="selectedContact.phone"><strong>Telepon:</strong> {{ selectedContact.phone }}</p>
            <p><strong>Subjek:</strong> {{ selectedContact.subject }}</p>
            <p><strong>Tanggal:</strong> {{ formatDate(selectedContact.created_at) }}</p>
            <hr>
            <p><strong>Pesan:</strong></p>
            <p>{{ selectedContact.message }}</p>
          </div>
          <div class="modal-footer">
            <button @click="selectedContact = null" type="button" class="btn btn-secondary">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminContacts',
  data() {
    return {
      contacts: [],
      selectedStatus: '',
      selectedContact: null
    }
  },
  computed: {
    filteredContacts() {
      return this.contacts.filter(contact => {
        if (!this.selectedStatus) return true;
        return contact.status === this.selectedStatus;
      });
    }
  },
  methods: {
    formatDate(date) {
      return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    getBadgeClass(status) {
      switch (status) {
        case 'unread':
          return 'bg-danger';
        case 'read':
          return 'bg-warning';
        case 'replied':
          return 'bg-success';
        default:
          return 'bg-secondary';
      }
    },
    getStatusLabel(status) {
      switch (status) {
        case 'unread':
          return 'Belum Dibaca';
        case 'read':
          return 'Sudah Dibaca';
        case 'replied':
          return 'Sudah Dibalas';
        default:
          return status;
      }
    },
    async fetchContacts() {
      try {
        const response = await this.$axios.get('/api/v2/contacts');
        this.contacts = response.data.data;
      } catch (error) {
        console.error('Error fetching contacts:', error);
      }
    },
    viewContact(contact) {
      this.selectedContact = contact;
    },
    async markAsRead(contact) {
      // capture id to avoid race conditions flagged by eslint
      const id = contact.id;
      try {
        await this.$axios.put(`/api/v2/contacts/${id}`, { status: 'read' });
        // update by finding the contact in the current array (avoids reassigning based on possibly stale reference)
        const idx = this.contacts.findIndex(c => c.id === id);
        if (idx !== -1) {
          this.contacts[idx].status = 'read';
        }
        alert('Pesan ditandai sebagai sudah dibaca');
      } catch (error) {
        alert('Gagal mengupdate status: ' + error.message);
      }
    },
    async deleteContact(id) {
      if (confirm('Anda yakin ingin menghapus pesan ini?')) {
        try {
          await this.$axios.delete(`/api/v2/contacts/${id}`);
          alert('Pesan berhasil dihapus!');
          this.fetchContacts();
        } catch (error) {
          alert('Gagal menghapus pesan: ' + error.message);
        }
      }
    }
  },
  mounted() {
    this.fetchContacts();
  }
}
</script>

<style scoped>
.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}
</style>
