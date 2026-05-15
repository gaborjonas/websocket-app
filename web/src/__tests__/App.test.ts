import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import App from '../App.vue'

vi.mock('../composables/useWebSocket', () => ({
  useWebSocket: () => ({
    isConnected: true,
    connectionStatus: 'Connected',
    machines: new Map([
      [
        'Test Machine',
        {
          name: 'Test Machine',
          state: 'Producing',
        },
      ],
    ]),
    logs: [
      {
        timestamp: new Date().toISOString(),
        machine: 'Test Machine',
        state: 'Producing',
      },
    ],
  }),
}))

describe('App', () => {
  it('renders correctly', () => {
    const wrapper = mount(App)

    expect(wrapper.find('#app').exists()).toBe(true)
    expect(wrapper.find('main').exists()).toBe(true)
    expect(wrapper.find('.section-title').text()).toBe('Machine Status')
  })

  it('displays connection status correctly', () => {
    const wrapper = mount(App)

    const statusElement = wrapper.find('.status-bar span')
    expect(statusElement.exists()).toBe(true)
    expect(statusElement.text()).toBe('Connected')
    expect(statusElement.classes()).toContain('connected')
    expect(statusElement.classes()).not.toContain('disconnected')
  })

  it('renders child components', () => {
    const wrapper = mount(App)

    expect(wrapper.findComponent({ name: 'MachineStatus' }).exists()).toBe(true)
    expect(wrapper.findComponent({ name: 'LogViewer' }).exists()).toBe(true)
  })

  it('applies correct styling classes', () => {
    const wrapper = mount(App)

    expect(wrapper.find('.status-bar').exists()).toBe(true)
    expect(wrapper.find('.section-title').exists()).toBe(true)
  })
})
