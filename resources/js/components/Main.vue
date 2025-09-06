<template>
  <div class="app-container">
    <Navbar />

    <div class="body-container">
      <Sidebar />

      <main class="main-content__dashboard">
        <div class="cards-container">
          <div class="card">
            <h3>Open</h3>
            <p>{{ statusCounts.open || 0 }}</p>
          </div>
          <div class="card">
            <h3>Closed</h3>
            <p>{{ statusCounts.closed || 0 }}</p>
          </div>
          <div class="card">
            <h3>Pending</h3>
            <p>{{ statusCounts.in_progress || 0 }}</p>
          </div>
          <div class="card">
            <h3>Categories</h3>
            <p>{{ categoryCount }}</p>
          </div>
        </div>

        <div class="Tickets-chart">
          <canvas id="ticketsPieChart" ref="chartRef"></canvas>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Sidebar from '@/components/Sidebar.vue'
import Navbar from '@/components/Navbar.vue'
import { Chart, PieController, ArcElement, Tooltip, Legend } from 'chart.js'

Chart.register(PieController, ArcElement, Tooltip, Legend)

const chartRef = ref(null)
let pieChart = null

const statusCounts = ref({ open: 0, closed: 0, pending: 0 })
const categoryCount = ref(0)

const fetchTicketData = async () => {
  try {
    const response = await fetch('/api/stats')
    const data = await response.json()

    statusCounts.value = data.response.byStatus || {}
    categoryCount.value = Object.keys(data.response.byCategory || {}).length

    const labels = ['Open', 'Closed', 'Pending']
    const values = [
      statusCounts.value.open || 0,
      statusCounts.value.closed || 0,
      statusCounts.value.in_progress || 0
    ]

    if (pieChart) pieChart.destroy()

    pieChart = new Chart(chartRef.value, {
      type: 'pie',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: ['#4caf50', '#f44336', '#ff9800']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: { label: ctx => `${ctx.label}: ${ctx.raw}` }
          }
        }
      }
    })
  } catch (error) {
    console.error('Error fetching ticket data:', error)
  }
}

onMounted(() => {
  fetchTicketData()
})
</script>

