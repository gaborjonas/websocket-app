const WS_URL = import.meta.env.VITE_WS_URL ?? 'ws://localhost:8080'
const RECONNECT_MS = 3000

export { WS_URL, RECONNECT_MS }
