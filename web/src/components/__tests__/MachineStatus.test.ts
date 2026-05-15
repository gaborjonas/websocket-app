import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import MachineStatus from '../MachineStatus.vue'

describe('MachineStatus', () => {
  it('renders correctly with no machines', () => {
    const wrapper = mount(MachineStatus, {
      props: {
        machines: new Map(),
      },
    })

    expect(wrapper.find('.machine-status').exists()).toBe(true)
    expect(wrapper.find('table').exists()).toBe(true)
    expect(wrapper.findAll('tbody tr')).toHaveLength(0)
  })

  it('renders machine cards correctly', () => {
    const mockMachines = new Map([
      [
        'Machine 1',
        {
          name: 'Machine 1',
          state: 'Producing',
        },
      ],
      [
        'Machine 2',
        {
          name: 'Machine 2',
          state: 'Idle',
        },
      ],
    ])

    const wrapper = mount(MachineStatus, {
      props: {
        machines: mockMachines,
      },
    })

    const machineRows = wrapper.findAll('tbody tr')
    expect(machineRows).toHaveLength(2)

    expect(machineRows[0].find('.machine-name-text').text()).toBe('Machine 1')
    expect(machineRows[0].find('.state-badge').text()).toContain('Producing')

    expect(machineRows[1].find('.machine-name-text').text()).toBe('Machine 2')
    expect(machineRows[1].find('.state-badge').text()).toContain('Idle')
  })

  it('applies correct CSS classes based on machine state', () => {
    const mockMachines = new Map([
      [
        'Running Machine',
        {
          name: 'Running Machine',
          state: 'Producing',
        },
      ],
      [
        'Stopped Machine',
        {
          name: 'Stopped Machine',
          state: 'Idle',
        },
      ],
    ])

    const wrapper = mount(MachineStatus, {
      props: {
        machines: mockMachines,
      },
    })

    const machineRows = wrapper.findAll('tbody tr')

    expect(machineRows[0].find('.state-badge').classes()).toContain('Producing')
    expect(machineRows[0].find('.state-badge').classes()).not.toContain('Idle')

    expect(machineRows[1].find('.state-badge').classes()).toContain('Idle')
    expect(machineRows[1].find('.state-badge').classes()).not.toContain('Producing')
  })
})
