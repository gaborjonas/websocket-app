<template>
  <div class="log-viewer">
    <h3>Activity Log</h3>
    <div class="log-box" ref="logBox">
      <div
        v-for="(log, index) in logs"
        :key="index"
        :class="['log-entry', log.state.toLowerCase()]"
      >
        <span class="log-ts">{{ log.timestamp }}</span>
        <span>{{ log.machine }}</span>
        <span class="log-state">→ {{ log.state }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'

interface Log {
  timestamp: string
  machine: string
  state: string
}

const props = defineProps<{
  logs: Log[]
}>()

const logBox = ref<HTMLElement>()

const scrollToBottom = () => {
  nextTick(() => {
    if (logBox.value) {
      logBox.value.scrollTop = logBox.value.scrollHeight
    }
  })
}

watch(() => props.logs.length, scrollToBottom)
</script>

<style scoped>
.log-viewer {
  margin: 20px 0;
}

.log-viewer h3 {
  margin-bottom: 12px;
  color: #374151;
  font-weight: 600;
}

.log-box {
  height: 200px;
  overflow-y: auto;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  padding: 12px;
  font-family: 'Courier New', monospace;
  font-size: 0.875rem;
}

.log-entry {
  display: flex;
  gap: 12px;
  padding: 4px 0;
  border-bottom: 1px solid #f3f4f6;
}

.log-entry:last-child {
  border-bottom: none;
}

.log-ts {
  color: #6b7280;
  min-width: 80px;
}

.log-state {
  color: #059669;
  font-weight: 500;
}

.log-entry.producing .log-state {
  color: #059669;
}

.log-entry.idle .log-state {
  color: #d97706;
}

.log-entry.starved .log-state {
  color: #dc2626;
}
</style>
