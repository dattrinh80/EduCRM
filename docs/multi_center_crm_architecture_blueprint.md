# Multi-Center Architecture Blueprint for CRM

## 1. Purpose
This document defines the architecture for a **multi-center CRM system** where:

- Each center operates independently
- Financial accounting is separated by center
- Staff may work at multiple centers with different roles
- The system must remain scalable and ready for microservices in the future

The design integrates:

- Clean Architecture
- CQRS
- RBAC with scoped roles
- Center Context pattern

This blueprint is intended for use by an AI coding agent to implement or refactor the system.

---

# 2. Core Concepts

## 2.1 Center

A **Center** represents an operational branch of the organization.

Examples:

- Hanoi Center
- Haiphong Center
- Ho Chi Minh Center

Each center has:

- independent staff
- independent leads
- independent sales pipeline
- independent reporting


## 2.2 Center Context

Every request in the system executes within a **Center Context**.

```
Center Context = current_center_id
```

The center context is stored in user session after login.

```
session:

user_id
current_center_id
```

All domain operations must respect this context.


## 2.3 Scoped RBAC

Authorization is implemented using **Role-Based Access Control with scope**.

Roles are assigned per center.

Example:

```
User A

Manager @ Center A
Accountant @ Center B
```


## 2.4 Principle

All operational data must belong to exactly **one center**.

Example tables:

```
leads
contacts
deals
students
classes
```

All contain:

```
center_id
```


---

# 3. High Level Architecture

```
Client
  ↓
Web Controller / API Controller
  ↓
Center Context Middleware
  ↓
Authorization Middleware
  ↓
Application Layer (CQRS)
  ↓
Domain Layer
  ↓
Infrastructure Layer
  ↓
Database
```


---

# 4. Authentication and Center Selection

## Login Flow

User logs in with:

```
username
password
center_id
```

Or selects center after login.


## Session Structure

```
session

user_id
current_center_id
```


## Switching Center

Users may switch center if they have permission.

Endpoint:

```
POST /auth/switch-center
```

Validation rule:

```
user must have role assignment in that center
```


---

# 5. Center Context Middleware

Middleware responsibility:

1. Read center_id from session
2. Attach center_id to request context
3. Make center available to application services

Pseudo implementation:

```
class CenterContextMiddleware
{
    public function handle($request, Closure $next)
    {
        $centerId = session('current_center_id');

        if (!$centerId) {
            throw new Exception('Center not selected');
        }

        app()->instance('center_id', $centerId);

        return $next($request);
    }
}
```

Usage inside services:

```
$centerId = app('center_id');
```


---

# 6. RBAC Data Model

## users

```
id
name
email
password
```


## centers

```
id
name
code
status
```


## roles

```
id
name
```


## permissions

```
id
key
name
```


## role_permissions

```
role_id
permission_id
```


## role_assignments/role_users

```
id
user_id
role_id
center_id
```

Meaning:

```
User has role at specific center
```


---

# 7. Authorization Service

Central service responsible for permission checks.

Interface:

```
AuthorizationService
```

Functions:

```
can(userId, permissionKey, centerId)
getUserCenters(userId)
getUserPermissions(userId, centerId)
```

Example usage:

```
$auth->can($userId, 'import_lead', $centerId);
```


---

# 8. CQRS Structure

Each module follows CQRS pattern.

```
Modules/
  Lead/
    Application/
      Commands/
      CommandHandlers/
      Queries/
      QueryHandlers/

    Domain/
      Entities/
      Repositories/

    Infrastructure/
      Repositories/

    Http/
      Controllers/
```


---

# 9. Center-Aware Domain Rules

All center-bound entities must include:

```
center_id
```

Examples:

```
Lead
Student
Course
Enrollment
Payment
```


---

# 10. Repository Pattern

Repositories must enforce center isolation.

Example:

```
LeadRepository
```

Query pattern:

```
SELECT * FROM leads
WHERE center_id = current_center
```


---

# 11. Lead Import Architecture

## Import Flow

```
User login
   ↓
Center Context
   ↓
Upload file
   ↓
Authorization check
   ↓
Process rows
   ↓
Save leads with center_id
```


## File Structure

Import file does NOT contain center_id.

Example:

```
name
phone
email
source
note
```


## Import Logic

```
lead.center_id = current_center_id
```


## Authorization

Before import:

```
auth->can(user_id, 'import_lead', current_center_id)
```


---

# 12. Query Filtering Pattern

All queries must automatically filter by center.

Recommended approaches:

Option A: Repository filter

Option B: Global query scope

Example:

```
class CenterScope
{
    public function apply($builder)
    {
        $builder->where('center_id', app('center_id'));
    }
}
```


---

# 13. Cross-Center Operations

Certain roles may operate across centers.

Example roles:

```
Super Admin
Regional Director
Finance Director
```

These roles may have:

```
ALL centers permission
```

Implementation approach:

AuthorizationService bypass center restriction.


---

# 14. Database Indexing Strategy

To ensure performance, add composite indexes.

Examples:

```
leads(center_id, created_at)
leads(center_id, status)
deals(center_id, stage)
```


---

# 15. Caching Strategy

Cache user permissions.

Key format:

```
user_permissions:{userId}:{centerId}
```

Cache store:

```
Redis
```


---

# 16. Security Rules

System must enforce:

1. User cannot access another center's data
2. Center context must always exist
3. Authorization checks must use center_id


---

# 17. API Design

API endpoints do NOT require center_id.

Example:

```
POST /leads
GET /leads
POST /leads/import
```

Center is resolved from context.


---

# 18. Logging

All logs should include center_id.

Example:

```
user_id
center_id
action
entity
```


---

# 19. Migration Path to Microservices

Future architecture may split services:

```
Auth Service
Lead Service
Student Service
Finance Service
```

Center Context becomes part of request metadata.

Example header:

```
X-Center-ID
```


---

# 20. Implementation Checklist

AI Agent must ensure:

- CenterContextMiddleware implemented
- AuthorizationService implemented
- role_assignments table exists
- All domain entities include center_id
- Repository queries filter by center
- Import processes attach center automatically
- Indexes include center_id


---

# End of Blueprint

