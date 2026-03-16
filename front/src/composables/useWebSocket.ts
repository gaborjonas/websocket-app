import { ref, onMounted, onUnmounted } from 'vue'
import { WS_URL, RECONNECT_MS } from '@/config.ts'

export interface Machine {
  name: string
  state: string
}

export interface Log {
  timestamp: string
  machine: string
  state: string
}

export function useWebSocket() {
  const isConnected = ref(false)
  const connectionStatus = ref('Disconnected')
  const machines = ref<Map<string, Machine>>(new Map())
  const logs = ref<Log[]>([])

  let ws: WebSocket | null = null
  let reconnecting = false
  let reconnectTimer: number | null = null

  const addLog = (machineName: string, state: string) => {
    logs.value.push({
      timestamp: new Date().toLocaleTimeString(),
      machine: machineName,
      state: state,
    })
  }

  const connect = () => {
    try {
      ws = new WebSocket(WS_URL)

      ws.onopen = () => {
        isConnected.value = true
        connectionStatus.value = 'Connected'
        if (reconnecting) {
          addLog('System', 'RECONNECTED')
        } else {
          addLog('System', 'CONNECTED')
        }
        reconnecting = false
      }

      ws.onmessage = (event) => {
        try {
          const data = JSON.parse(event.data)

          if (data.machines) {
            data.machines.forEach((machine: Machine) => {
              machines.value.set(machine.name, machine)
            })
          } else {
            const { machine, state } = data
            if (machine && state) {
              const existingMachine = machines.value.get(machine)
              if (existingMachine) {
                existingMachine.state = state
              }
              addLog(machine, state)
            }
          }
        } catch (error) {
          console.error('Unable to parse message:', error)
        }
      }

      ws.onclose = () => {
        isConnected.value = false
        connectionStatus.value = 'Disconnected'

        if (!reconnecting) {
          reconnecting = true
          addLog('System', 'DISCONNECTED — retrying…')
          reconnectTimer = window.setTimeout(connect, RECONNECT_MS)
        }
      }

      ws.onerror = (event) => {
        console.error('WebSocket error:', event)
        isConnected.value = false
        connectionStatus.value = 'Connection Error'
      }
    } catch (error) {
      console.error('Failed to connect:', error)
      isConnected.value = false
      connectionStatus.value = 'Connection Failed'

      if (!reconnecting) {
        reconnecting = true
        addLog('System', 'CONNECTION FAILED — retrying…')
        reconnectTimer = window.setTimeout(connect, RECONNECT_MS)
      }
    }
  }

  const disconnect = () => {
    if (reconnectTimer) {
      clearTimeout(reconnectTimer)
    }
    if (ws) {
      ws.close()
    }
  }

  onMounted(() => {
    connect()
  })

  onUnmounted(() => {
    disconnect()
  })

  return {
    isConnected,
    connectionStatus,
    machines,
    logs,
    disconnect,
  }
}
