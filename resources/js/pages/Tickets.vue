<template>
  <div class="app-container">
    <Navbar />

    <div class="body-container">
      <Sidebar />

      <main class="main-content">
        <div class="actions-bar">
          <button id="openModalBtn" @click="openNewTicketModal">New Ticket</button>
          <button @click="exportCSV" class="export-btn">Export CSV</button>
        </div>

        <div v-if="newTicketModalOpen" class="modal-overlay" @click.self="closeNewTicketModal">
          <div class="modal-content">
            <h3>New Ticket</h3>
            <label>Subject</label>
            <input type="text" v-model="newTicket.subject" />
            <label>Body</label>
            <textarea v-model="newTicket.body"></textarea>
            <button @click="createTicket">Create</button>
            <button @click="closeNewTicketModal">Close</button>
          </div>
        </div>

        <div class="filters">
          <input v-model="searchQuery" type="text" placeholder="Search tickets..." class="search-bar-table" />
          <select v-model="statusFilter">
            <option value="">All Status</option>
            <option value="open">Open</option>
            <option value="in_progress">In Progress</option>
            <option value="closed">Closed</option>
          </select>
        </div>

        <table class="tickets-table">
          <thead>
            <tr>
              <th>Subject</th>
              <th>Body</th>
              <th>Status</th>
              <th>Category</th>
              <th>Confidence</th>
              <th>Created at</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in paginatedTickets" :key="ticket.id">
              <td>{{ ticket.subject }} </td>
              <td>{{ ticket.body }}</td>
              <td class="ticket-list__table-row"> <button class="ticket-list__status"> {{ ticket.status }}</button></td>
              <td>{{ ticket.category || '—' }}</td>
              <td>{{ ticket.confidence != null ? Number(ticket.confidence).toFixed(2) : '—' }}</td>
              <td>{{ new Date(ticket.created_at).toLocaleString() }}</td>

              <td class="list__flex">
                <button class="ticket-list__classfy" @click="classifyTicket(ticket)" :disabled="ticket.loading">
                  <span class="loader" v-if="ticket.loading"></span>
                  <span v-else>Classify</span>
                </button>
                <button class="ticket-list__edit" @click="openModal(ticket)">Edit</button>
              </td>
            </tr>
          </tbody>
        </table>

        <div class="pagination">
          <button @click="prevPage" :disabled="currentPage === 1">Prev</button>
          <span>Page {{ currentPage }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>

        <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
          <div class="modal-content">
            <h3>Edit Ticket</h3>

            <label>Subject</label>
            <input v-model="selectedTicket.subject" disabled type="text" />
            <label>Body</label>
            <input v-model="selectedTicket.body" disabled type="text" />
            <label>Category</label>
            <input v-model="selectedTicket.category" type="text" />
            <label>Note</label>
            <textarea v-model="selectedTicket.note"></textarea>
            <label>Confidence</label>
            <input v-model="selectedTicket.confidence" disabled type="text" />
            <label>Explaination</label>
            <input v-model="selectedTicket.explanation" disabled type="text" />


            <button @click="saveTicket(selectedTicket)">Save</button>
            <button @click="closeModal">Close</button>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script>
import Sidebar from "@/components/Sidebar.vue";
import Navbar from "@/components/Navbar.vue";

export default {
  name: "TicketsPage",
  components: { Sidebar, Navbar },
  data() {
    return {
      tickets: [],
      searchQuery: "",
      statusFilter: "",
      currentPage: 1,
      pageSize: 10,
      modalOpen: false,
      newTicketModalOpen: false,
      newTicket: { subject: "", body: "" },
      selectedTicket: null,
      csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    };
  },
  computed: {
    filteredTickets() {
      let filtered = this.tickets;
      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          t => t.subject.toLowerCase().includes(q) || t.body.toLowerCase().includes(q)
        );
      }
      if (this.statusFilter) {
        filtered = filtered.filter(t => t.status === this.statusFilter);
      }
      return filtered;
    },
    totalPages() {
      return Math.ceil(this.filteredTickets.length / this.pageSize);
    },
    paginatedTickets() {
      const start = (this.currentPage - 1) * this.pageSize;
      return this.filteredTickets.slice(start, start + this.pageSize);
    },
  },
  methods: {
    openNewTicketModal() {
      this.newTicket = { subject: "", body: "" };
      this.newTicketModalOpen = true;
    },
    closeNewTicketModal() {
      this.newTicketModalOpen = false;
      this.newTicket = { subject: "", body: "" };
    },
    async createTicket() {
      if (!this.newTicket.subject || !this.newTicket.body) return alert('Please fill all fields');
      try {
        const res = await fetch("/api/Newtickets", {
          method: "POST",
          headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": this.csrfToken },
          credentials: "same-origin",
          body: JSON.stringify(this.newTicket)
        });
        if (!res.ok) { const text = await res.text(); console.error(text); return alert("Failed to create ticket"); }
        const data = await res.json();
        if (data.status === "success") { this.tickets.unshift({ ...data.response, loading: false }); this.closeNewTicketModal(); }
        else alert("Error creating ticket");
      } catch (err) { console.error(err); alert("Network error"); }
    },
    async fetchTickets() {
      try {
        const response = await fetch("/api/tickets");
        const data = await response.json();
        if (data.status === "success") this.tickets = data.response.map(t => ({ ...t, loading: false }));
      } catch (err) { console.error(err); }
    },
    nextPage() {
      if (this.currentPage < this.totalPages)
        this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1)
        this.currentPage--;
    },
    async classifyTicket(ticket) {
      ticket.loading = true;
      try {
        const res = await fetch(`/api/tickets/${ticket.id}/classify`, {
          method: "POST",
          headers: { "X-CSRF-TOKEN": this.csrfToken, "Content-Type": "application/json" }
        });
        const data = await res.json();
        if (data.status === "success") {
          const updated = await fetch(`/api/tickets/${ticket.id}`);
          const json = await updated.json();
          if (json.status === "success") Object.assign(ticket, json.response);
        }
      } catch (err) { console.error(err); }
      finally { ticket.loading = false; }
    },
    openModal(ticket) {
      this.selectedTicket = { ...ticket }
      this.modalOpen = true
    },


    closeModal() { this.modalOpen = false; this.selectedTicket = null; },
    async saveTicket(ticket) {
      if (!ticket.id) return alert("No ticket selected!");
      try {
        const res = await fetch(`/api/tickets/${ticket.id}`, {
          method: "PATCH",
          headers: { "X-CSRF-TOKEN": this.csrfToken, "Content-Type": "application/json" },
          body: JSON.stringify({ category: ticket.category, note: ticket.note })
        });
        const data = await res.json();
        if (data.status === "success") {
          const index = this.tickets.findIndex(t => t.id === ticket.id);
          if (index !== -1) this.tickets.splice(index, 1, data.response);
          this.closeModal();
        }
      } catch (err) { console.error(err); }
    },

    exportCSV() {
      if (!this.filteredTickets.length) return alert("No tickets to export!");
      const headers = ["Subject", "Body", "Status", "Category", "Confidence", "Created At"];
      const rows = this.filteredTickets.map(t => [
        t.subject, t.body, t.status, t.category || "", t.confidence != null ? Number(t.confidence).toFixed(2) : "", new Date(t.created_at).toLocaleString()
      ]);
      const csvContent = [headers, ...rows].map(r => r.join(",")).join("\n");
      const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.setAttribute("download", `tickets_${new Date().toISOString().slice(0, 10)}.csv`);
      link.click();
    }
  },
  mounted() { this.fetchTickets(); }
};
</script>
