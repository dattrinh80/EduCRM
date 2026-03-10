# Entity Relationship Diagram (ERD)

This document describes the database schema of the Edu CRM system.

## Database Schema

```mermaid
erDiagram
    USERS ||--o{ LEADS : assigned_to
    USERS ||--o{ TASKS : assigned_to
    USERS ||--o{ TASKS : assigned_by
    USERS ||--o{ USER_ROLES : has
    
    ROLES ||--o{ USER_ROLES : assigned_to
    ROLES ||--o{ ROLE_PERMISSIONS : has
    PERMISSIONS ||--o{ ROLE_PERMISSIONS : assigned_to
    PERMISSION_GROUPS ||--o{ PERMISSIONS : groups

    CENTERS ||--o{ LEADS : belongs_to
    CENTERS ||--o{ TASKS : belongs_to
    CENTERS ||--o{ CAMPAIGNS : belongs_to

    LEAD_SOURCES ||--o{ LEADS : source
    CAMPAIGNS ||--o{ LEADS : campaign
    INTEREST_TYPES ||--o{ LEADS : interest

    CUSTOMERS ||--o{ STUDENTS : has
    LEADS ||--o{ LEAD_ACTIVITIES : logs
    LEADS ||--o{ LEAD_NOTES : has

    USERS {
        uuid id PK
        string name
        string email
        string password
        uuid default_center_id FK
    }

    ROLES {
        uuid id PK
        string name
        boolean is_system_role
    }

    PERMISSIONS {
        uuid id PK
        string name
        uuid group_id FK
    }

    CENTERS {
        uuid id PK
        string name
        string code
    }

    LEADS {
        uuid id PK
        string name
        string phone
        string email
        date dob
        string status
        uuid center_id FK
        uuid source_id FK
        uuid campaign_id FK
        uuid interest_type_id FK
        uuid assigned_to FK
    }

    CUSTOMERS {
        uuid id PK
        string name
        string phone
        string email
    }

    STUDENTS {
        uuid id PK
        uuid customer_id FK
        string student_code
        string status
    }

    TASKS {
        uuid id PK
        string title
        text description
        date due_date
        string status
        string priority
        uuid assigned_to FK
        uuid assigned_by FK
        uuid center_id FK
        uuid relation_id "Polymorphic"
        string relation_type "Polymorphic"
    }

    CAMPAIGNS {
        uuid id PK
        string name
        uuid center_id FK
    }

    LEAD_SOURCES {
        uuid id PK
        string name
    }

    INTEREST_TYPES {
        uuid id PK
        string name
    }

    LEAD_ACTIVITIES {
        uuid id PK
        uuid lead_id FK
        string activity_type
        text description
    }

    LEAD_NOTES {
        uuid id PK
        uuid lead_id FK
        text content
    }
```

## Key Relationships

### Core Authentication & RBAC
- **USERS** are assigned **ROLES** through the `user_roles` table.
- **ROLES** carry multiple **PERMISSIONS** via the `role_permissions` table.
- **PERMISSIONS** are organized into **PERMISSION_GROUPS** for easier management.

### CRM & Lead Management
- **LEADS** are the core entry point for potential customers.
- Each **LEAD** belongs to a **CENTER** and can be assigned to a **USER** (Staff/Manager).
- **LEADS** track their origin through **LEAD_SOURCES** and **CAMPAIGNS**.
- **LEAD_ACTIVITIES** and **LEAD_NOTES** provide a history of interactions with the lead.

### Education
- When a **LEAD** is converted, it becomes a **CUSTOMER** (the payer/parent).
- A **CUSTOMER** can have one or more **STUDENTS** (the learners).

### Task Management
- **TASKS** can be assigned to **USERS** and are scoped to a **CENTER**.
- **TASKS** use a polymorphic relationship (`relation_id`, `relation_type`) to link to various entities like **LEADS**, **CUSTOMERS**, or **STUDENTS**.
