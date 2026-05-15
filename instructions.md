
# Software Engineer – Coding Challenge

## Assessment Description

A production line is composed of many machines that sequentially manipulate material flowing through the process, resulting in a finished product. Machines situated within a process can be in one of the following operational states:

- **PRODUCING** – The machine is currently working.
- **IDLE** – The machine is waiting for work.
- **STARVED** – There is no material available for the machine to complete its work.

Production managers and technicians would like to view the state of machines remotely in real-time, to ensure that production is running smoothly.

```
Machine (Subject)   Machine (Subject)   Machine (Subject)
        \                  |                  /
         \                 |                 /
          \                ↓                /
                   Technician (Observer)
```

Using any language that supports Object-Oriented programming, complete the following tasks:

---

## Task 1 – Observer Pattern

### Class Diagram

**Subject**
| Member | Type |
|---|---|
| `state` | String |
| `setState(String s)` | method |
| `attach(Observer o)` | method |
| `notifyAllObservers()` | method |

**Observer**
| Member | Type |
|---|---|
| `name` | String |
| `update(String state, String from)` | method |

**Machine** *(extends Subject)*
| Member | Type |
|---|---|
| `name` | String |

**Employee** *(extends Observer)*
| Member | Type |
|---|---|
| `role` | String |
| `update(String state, String from)` | method |

### Requirements

Using the Observer design pattern, implement the above classes to simulate Employees (subclass of `Observer`) registering their interest in a Machine (subclass of `Subject`) and receiving notifications when a machine's state changes.

- Override the `update` method within the `Employee` class to output:
    - The employee's name
    - Their employee role
    - The machine's updated state
    - The machine's name

In your `main` function:
1. Define some `Machine` and `Employee` instances.
2. Register the employees' interest in the machine using the `attach` method.
3. Make changes to the subjects' state.

---

## Challenge Task (Optional) – Real-Time Dashboard

### Class Diagram

**Dashboard** *(extends Observer)*
| Member | Type |
|---|---|
| `update(String state, String from)` | method |

### Requirements

1. Create a new class named `Dashboard` that extends the `Observer` class.
2. Override the `update` method to **emit changes** in state and the machine's name via a **WebSocket**, using the `state` and `from` parameters.
3. Create a small front-end web application using vanilla JavaScript or a framework of your choice (React, Vue, etc.) that displays a table:

| Machine Name | State |
|---|---|
| Machine A | PRODUCING |
| Machine B | IDLE |
| Machine C | STARVED |

When a machine's state is received, update the **text and colour** of the `State` cell for the machine that sent the update. By default, a machine's state is `IDLE`.

### Notes

> **Note 1:** The WebSocket server must include three instances of `Machine`, one instance of `Dashboard`, and a loop/timer that periodically changes each machine's state to one of the three defined states using `setState`.

> **Note 2:** Use the following colours when updating the state cell:

| State | Colour |
|---|---|
| PRODUCING | 🟢 Green |
| IDLE | 🟡 Yellow |
| STARVED | 🔴 Red |

> **Note 3:** Do not spend too much time on the design of the dashboard — the focus is on your ability to integrate the server-side code with the front-end.