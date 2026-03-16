import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import LogViewer from '../LogViewer.vue'

describe('LogViewer', () => {
  it('renders correctly with no logs', () => {
    const wrapper = mount(LogViewer, {
      props: {
        logs: [],
      },
    })

    expect(wrapper.find('.log-viewer').exists()).toBe(true)
    expect(wrapper.find('.log-box').exists()).toBe(true)
    expect(wrapper.findAll('.log-entry')).toHaveLength(0)
  })

  it('renders log entries correctly', () => {
    const mockLogs = [
      {
        timestamp: '2024-01-01T10:00:00Z',
        machine: 'System',
        state: 'Producing',
      },
      {
        timestamp: '2024-01-01T10:01:00Z',
        machine: 'Network',
        state: 'Idle',
      },
    ]

    const wrapper = mount(LogViewer, {
      props: {
        logs: mockLogs,
      },
    })

    const logEntries = wrapper.findAll('.log-entry')
    expect(logEntries).toHaveLength(2)

    expect(logEntries[0].find('.log-ts').text()).toBe('2024-01-01T10:00:00Z')
    expect(logEntries[0].text()).toContain('System')
    expect(logEntries[0].find('.log-state').text()).toBe('→ Producing')

    expect(logEntries[1].find('.log-ts').text()).toBe('2024-01-01T10:01:00Z')
    expect(logEntries[1].text()).toContain('Network')
    expect(logEntries[1].find('.log-state').text()).toBe('→ Idle')
  })

  it('applies correct CSS classes based on log state', () => {
    const mockLogs = [
      {
        timestamp: new Date().toISOString(),
        machine: 'Test Machine 1',
        state: 'Producing',
      },
      {
        timestamp: new Date().toISOString(),
        machine: 'Test Machine 2',
        state: 'Idle',
      },
      {
        timestamp: new Date().toISOString(),
        machine: 'Test Machine 3',
        state: 'Starved',
      },
    ]

    const wrapper = mount(LogViewer, {
      props: {
        logs: mockLogs,
      },
    })

    const logEntries = wrapper.findAll('.log-entry')

    expect(logEntries[0].classes()).toContain('producing')
    expect(logEntries[1].classes()).toContain('idle')
    expect(logEntries[2].classes()).toContain('starved')
  })

  it('formats timestamps correctly', () => {
    const mockLogs = [
      {
        timestamp: '2024-01-01T10:30:45Z',
        machine: 'Test Machine',
        state: 'Producing',
      },
    ]

    const wrapper = mount(LogViewer, {
      props: {
        logs: mockLogs,
      },
    })

    const timestampElement = wrapper.find('.log-ts')
    expect(timestampElement.exists()).toBe(true)
    expect(timestampElement.text()).toBe('2024-01-01T10:30:45Z')
  })
})
