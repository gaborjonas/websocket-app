import { config } from '@vue/test-utils'
import { vi } from 'vitest'

globalThis.ResizeObserver = vi.fn().mockImplementation(() => ({
  observe: vi.fn(),
  unobserve: vi.fn(),
  disconnect: vi.fn(),
}))

// Configure Vue Test Utils
config.global.stubs = {
  // Add any global component stubs here
}
