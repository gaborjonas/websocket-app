<template>
  <div id="app">
    <main>
      <h1 class="section-title">Machine Status</h1>
      <div class="status-bar">
        <h3>
          Connection status:
          <span :class="{ connected: isConnected, disconnected: !isConnected }">
            {{ connectionStatus }}
          </span>
        </h3>
      </div>

      <MachineStatus :machines="machines"></MachineStatus>

      <LogViewer :logs="logs"></LogViewer>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useWebSocket } from './composables/useWebSocket'
import MachineStatus from './components/MachineStatus.vue'
import LogViewer from './components/LogViewer.vue'

const { isConnected, connectionStatus, machines, logs } = useWebSocket()
</script>

<style>
#app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9fafb;
  min-height: 100vh;
}

main {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.section-title {
  color: #111827;
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 24px;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 12px;
}

.status-bar {
  background: #f3f4f6;
  padding: 16px;
  border-radius: 8px;
  margin-bottom: 24px;
  border-left: 4px solid #3b82f6;
}

.status-bar h3 {
  margin: 0;
  color: #374151;
  font-size: 1.125rem;
}

.connected {
  color: #10b981;
  font-weight: 600;
}

.disconnected {
  color: #ef4444;
  font-weight: 600;
}

body {
  margin: 0;
  background-color: #f9fafb;
}

* {
  box-sizing: border-box;
}
</style>
