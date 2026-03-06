# Edu CRM -- Lead Management Implementation Plan (UUID)

## 1. Objective

Module Lead Management quản lý toàn bộ vòng đời của khách hàng tiềm
năng:

Lead Created → Lead Assigned → Lead Contacted → Lead Qualified → Lead
Converted → Student

Module cần hỗ trợ:

-   Multi-center
-   Multi-role
-   Lead pipeline
-   Activity timeline
-   Lead assignment
-   Lead conversion

------------------------------------------------------------------------

# 2. UUID Strategy

Tất cả các bảng sử dụng:

CHAR(36)

UUID format:

xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx

Laravel sử dụng:

Str::uuid()

Mọi bảng:

id CHAR(36) PRIMARY KEY

------------------------------------------------------------------------

# 3. Database Schema

## leads

``` sql
CREATE TABLE leads (
    id CHAR(36) PRIMARY KEY,
    center_id CHAR(36),

    full_name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),

    source_id CHAR(36),
    status_id CHAR(36),
    owner_id CHAR(36),
    campaign_id CHAR(36),
    interest_type_id CHAR(36),

    created_by CHAR(36),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

Indexes

``` sql
CREATE INDEX idx_leads_center ON leads(center_id);
CREATE INDEX idx_leads_owner ON leads(owner_id);
CREATE INDEX idx_leads_phone ON leads(phone);
CREATE INDEX idx_leads_status ON leads(status_id);
```

------------------------------------------------------------------------

## lead_sources

``` sql
CREATE TABLE lead_sources (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

------------------------------------------------------------------------

## lead_statuses

``` sql
CREATE TABLE lead_statuses (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(100),
    stage VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

Example statuses

  name         stage
  ------------ ----------
  New          pipeline
  Contacted    pipeline
  Interested   pipeline
  Qualified    pipeline
  Converted    closed
  Lost         closed

------------------------------------------------------------------------

## lead_activities

``` sql
CREATE TABLE lead_activities (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36),
    activity_type VARCHAR(50),
    description TEXT,
    created_by CHAR(36),
    created_at TIMESTAMP
);
```

Activity types

call\
meeting\
sms\
email\
note\
status_change\
assignment\
conversion

------------------------------------------------------------------------

## lead_notes

``` sql
CREATE TABLE lead_notes (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36),
    content TEXT,
    created_by CHAR(36),
    created_at TIMESTAMP
);
```

------------------------------------------------------------------------

## lead_tags

``` sql
CREATE TABLE lead_tags (
    id CHAR(36) PRIMARY KEY,
    name VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

------------------------------------------------------------------------

## lead_tag_pivot

``` sql
CREATE TABLE lead_tag_pivot (
    lead_id CHAR(36),
    tag_id CHAR(36),
    PRIMARY KEY (lead_id, tag_id)
);
```

------------------------------------------------------------------------

## lead_assignments

``` sql
CREATE TABLE lead_assignments (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36),
    assigned_to CHAR(36),
    assigned_by CHAR(36),
    assigned_at TIMESTAMP
);
```

------------------------------------------------------------------------

## lead_conversions

``` sql
CREATE TABLE lead_conversions (
    id CHAR(36) PRIMARY KEY,
    lead_id CHAR(36),
    student_id CHAR(36),
    converted_by CHAR(36),
    converted_at TIMESTAMP
);
```

------------------------------------------------------------------------

# 4. Laravel Model Design

Tất cả models sử dụng:

``` php
use Illuminate\Database\Eloquent\Concerns\HasUuids;
```

Example

``` php
class Lead extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;
}
```

------------------------------------------------------------------------

# 5. API Design

## Create Lead

POST /api/v1/leads

Payload

``` json
{
  "full_name": "Nguyen Van A",
  "phone": "0901234567",
  "email": "example@gmail.com",
  "source_id": "uuid"
}
```

------------------------------------------------------------------------

## List Leads

GET /api/v1/leads

Filters

-   center_id
-   status_id
-   owner_id
-   source_id
-   keyword
-   date_range

------------------------------------------------------------------------

## Lead Detail

GET /api/v1/leads/{uuid}

Return

-   lead info
-   activities
-   notes
-   tags
-   owner
-   source
-   status

------------------------------------------------------------------------

## Update Lead

PUT /api/v1/leads/{uuid}

------------------------------------------------------------------------

## Change Lead Status

POST /api/v1/leads/{uuid}/change-status

``` json
{
  "status_id": "uuid"
}
```

------------------------------------------------------------------------

## Assign Lead

POST /api/v1/leads/{uuid}/assign

``` json
{
  "assigned_to": "uuid"
}
```

Rules

-   create record in lead_assignments
-   update owner_id
-   log activity

------------------------------------------------------------------------

## Add Note

POST /api/v1/leads/{uuid}/notes

------------------------------------------------------------------------

## Add Activity

POST /api/v1/leads/{uuid}/activities

------------------------------------------------------------------------

## Convert Lead

POST /api/v1/leads/{uuid}/convert

Flow

1.  create parent
2.  create student
3.  link student-parent
4.  create lead_conversion
5.  update lead status

------------------------------------------------------------------------

# 6. Application Layer Structure

    app
     └── Domains
          └── Lead
               ├── Models
               ├── Repositories
               ├── Services
               └── Policies

------------------------------------------------------------------------

# 7. Lead Pipeline

Stages

NEW\
CONTACTED\
INTERESTED\
QUALIFIED\
CONVERTED\
LOST

Rules

Converted → immutable\
Lost → cannot convert

------------------------------------------------------------------------

# 8. Assignment Modes

manual assignment\
round-robin\
auto assignment by source

Example

Facebook leads → Marketing\
Website leads → Admissions

------------------------------------------------------------------------

# 9. Activity Timeline

Every action generates activity

-   lead created
-   lead assigned
-   status changed
-   note added
-   call logged
-   meeting
-   converted

UI: Timeline

------------------------------------------------------------------------

# 10. Permissions

Roles

Admin\
Center Manager\
Admissions\
Marketing

Admin: full access

Center Manager:

-   view center leads
-   assign leads
-   convert leads

Admissions:

-   view assigned leads
-   update status
-   add notes

Marketing:

-   create leads
-   view leads

------------------------------------------------------------------------

# 11. UI Screens

## Lead List

Filters

-   status
-   source
-   owner
-   date
-   keyword

Columns

Name\
Phone\
Source\
Owner\
Status\
Created

------------------------------------------------------------------------

## Lead Detail

Sections

Lead Info\
Activity Timeline\
Notes\
Tags\
Assignment History

------------------------------------------------------------------------

## Lead Pipeline

Kanban

New\
Contacted\
Interested\
Qualified\
Converted\
Lost

Drag & Drop status change

------------------------------------------------------------------------

# 12. Events

LeadCreated\
LeadAssigned\
LeadStatusChanged\
LeadNoteAdded\
LeadConverted

Used for

-   notification
-   analytics
-   automation

------------------------------------------------------------------------

# 13. AI Automation (Future)

lead scoring\
auto assignment\
sentiment analysis\
auto response

------------------------------------------------------------------------

# 14. Implementation Phases

Phase 1

lead CRUD\
lead_source\
lead_status

Phase 2

lead_activity\
lead_notes\
timeline

Phase 3

lead_assignment

Phase 4

lead_conversion

Phase 5

pipeline UI

------------------------------------------------------------------------

# 15. AI Agent Execution Order

1 migrations\
2 models\
3 repositories\
4 services\
5 policies\
6 controllers\
7 api routes\
8 ui views

------------------------------------------------------------------------

# 16. Acceptance Criteria

✓ create lead\
✓ assign lead\
✓ change status\
✓ add note\
✓ log activity\
✓ convert lead\
✓ pipeline view\
✓ permission control
