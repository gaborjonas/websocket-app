<template>
  <div class="machine-status">
    <table>
      <thead>
        <tr>
          <th>Machine Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="[name, machine] in machines"
          :key="name"
          :id="'row-' + name.replace(/\s+/g, '-')"
        >
          <td>
            <div class="td-name">
              <span class="machine-name-text">{{ machine.name }}</span>
            </div>
          </td>
          <td>
            <span
              :class="['state-badge', machine.state]"
              :id="'badge-' + name.replace(/\s+/g, '-')"
            >
              <span class="badge-text" :style="{ backgroundColor: getStateColor(machine.state) }">
                {{ machine.state }}
              </span>
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup lang="ts">
interface Machine {
  name: string
  state: string
}

defineProps<{
  machines: Map<string, Machine>
}>()

const getStateColor = (state: string): string => {
  const colors: Record<string, string> = {
    Producing: '#10b981',
    Idle: '#f59e0b',
    Starved: '#ef4444',
    Connected: '#10b981',
    Disconnected: '#6b7280',
    'Connection Error': '#ef4444',
    'Connection Failed': '#ef4444',
  }
  return colors[state] || '#6b7280'
}
</script>

<style scoped>
.machine-status {
  margin: 20px 0;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

th,
td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

th {
  background-color: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.td-name {
  font-weight: 500;
}

.state-badge {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 0.875rem;
  font-weight: 500;
}

.badge-text {
  color: white;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}
</style>
