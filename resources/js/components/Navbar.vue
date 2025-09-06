<template>
  <div class="navbar">
    <h1 class="navbar-title">My App</h1>

    <button @click="toggleTheme" class="theme-toggle__btn" :aria-label="`Switch to ${isDark ? 'Light' : 'Dark'} theme`">
      {{ isDark ? 'Light' : ' Dark' }}
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'



const isDark = ref(false)

onMounted(() => {
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme === 'dark') {
    isDark.value = true
    document.documentElement.classList.add('dark-theme')
  }
})

function toggleTheme() {
  isDark.value = !isDark.value
  if (isDark.value) {
    document.documentElement.classList.add('dark-theme')
    localStorage.setItem('theme', 'dark')
  } else {
    document.documentElement.classList.remove('dark-theme')
    localStorage.setItem('theme', 'light')
  }
}
</script>
