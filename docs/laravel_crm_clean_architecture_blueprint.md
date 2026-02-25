# Laravel CRM Blueprint (Clean Architecture, API-first, Web Admin via Blade)

## 1. Overview

This blueprint defines a **production-grade CRM system** built with **Laravel**, following **Clean Architecture**, **API-first design**, and supporting:

- Web Admin (Laravel Blade)
- Mobile App (future)
- Third-party integrations

The system is designed to be:

- Maintainable
- Testable
- Scalable
- Mobile-ready
- AI-Agent friendly

---

## 2. Core Principles

### 2.1 Architecture Principles

- Clean Architecture (Dependency Rule enforced)
- API-first (ALL business logic exposed via API)
- Separation of concerns
- Domain-centric design
- Framework-independent business logic

### 2.2 Dependency Rule

Dependencies MUST flow inward:

```
Presentation → Application → Domain
Infrastructure → Application → Domain
```

Domain layer MUST NOT depend on Laravel or any framework.

---

## 3. Technology Stack

### Backend

- Language: PHP 8.2+
- Framework: Laravel 11+
- API: REST
- Authentication: Laravel Sanctum
- Database: PostgreSQL (recommended) or MySQL
- Cache: Redis
- Queue: Redis
- Storage: S3-compatible

### Frontend (Admin)

- Blade (Laravel)
- Alpine.js (optional)
- TailwindCSS (recommended)

### Mobile (future)

- Flutter / React Native
- Connect via REST API

---

## 4. High-Level Architecture

```
src/
 ├── Domain/
 ├── Application/
 ├── Infrastructure/
 └── Presentation/

laravel/
 ├── app/
 ├── routes/
 ├── config/
 └── bootstrap/
```

Laravel acts as the **delivery mechanism**, not the core business logic.

---

## 5. Layer Responsibilities

## 5.1 Domain Layer (Core Business Logic)

Location:

```
src/Domain/
```

Contains:

- Entities
- Value Objects
- Repository interfaces
- Domain services
- Domain exceptions

Example:

```
src/Domain/Customer/
 ├── Customer.php
 ├── CustomerRepository.php
 ├── ValueObjects/
 │    ├── CustomerId.php
 │    └── Email.php
 └── Exceptions/
```

Example Entity:

```php
class Customer
{
    private CustomerId $id;
    private string $name;
    private Email $email;

    public function __construct(CustomerId $id, string $name, Email $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
    }
}
```

NO Laravel code allowed here.

---

## 5.2 Application Layer (Use Cases)

Location:

```
src/Application/
```

Contains:

- Use cases
- DTOs
- Interfaces
- Application services

Example:

```
src/Application/Customer/
 ├── CreateCustomerUseCase.php
 ├── DTO/
 │    └── CreateCustomerDTO.php
 └── CustomerService.php
```

Example Use Case:

```php
class CreateCustomerUseCase
{
    public function __construct(
        private CustomerRepository $repository
    ) {}

    public function execute(CreateCustomerDTO $dto): Customer
    {
        $customer = new Customer(...);

        return $this->repository->save($customer);
    }
}
```

---

## 5.3 Infrastructure Layer

Location:

```
src/Infrastructure/
```

Contains:

- Database implementation
- Repository implementation
- External services
- Cache
- Queue

Example:

```
src/Infrastructure/Persistence/
 ├── Eloquent/
 │    ├── Models/
 │    └── CustomerRepository.php
```

Example Repository Implementation:

```php
class EloquentCustomerRepository implements CustomerRepository
{
    public function save(Customer $customer): Customer
    {
        // convert to Eloquent model
        // save
        // return domain entity
    }
}
```

---

## 5.4 Presentation Layer

Location:

```
src/Presentation/
```

Contains:

- API Controllers
- Web Controllers
- Requests
- Resources

Example:

```
src/Presentation/Api/
 ├── Controllers/
 └── Resources/

src/Presentation/Web/
 ├── Controllers/
 └── Views/
```

---

## 6. Laravel Integration Layer

Laravel acts as the delivery framework.

### 6.1 Controllers

Location:

```
app/Http/Controllers/
```

Controllers must only:

- Validate request
- Call UseCase
- Return response

Example:

```php
class CreateCustomerController
{
    public function __invoke(Request $request)
    {
        return $this->useCase->execute(...);
    }
}
```

---

## 7. API Structure

```
/api/v1/

/api/v1/customers
/api/v1/users
/api/v1/tasks
/api/v1/departments
```

Rules:

- RESTful
- Stateless
- JSON only

---

## 8. Web Admin Architecture

IMPORTANT RULE:

Web Admin MUST use API, NOT direct database.

Flow:

```
Blade → Web Controller → API Client → API → UseCase
```

DO NOT call repositories directly from Blade controllers.

---

## 9. Folder Structure (Full)

```
project-root/

src/
 ├── Domain/
 ├── Application/
 ├── Infrastructure/
 └── Presentation/

app/
 ├── Http/
 ├── Providers/

routes/
 ├── api.php
 └── web.php

resources/
 └── views/
```

---

## 10. Module Structure Example

Customer module:

```
src/
 ├── Domain/Customer/
 ├── Application/Customer/
 ├── Infrastructure/Persistence/Customer/
 └── Presentation/Api/Customer/
```

---

## 11. Authentication

Use:

- Laravel Sanctum

Supports:

- Web session
- Mobile tokens

---

## 12. Database Design Principles

- UUID primary keys
- created_at, updated_at required
- soft delete supported

Example:

```
customers
 ├── id
 ├── name
 ├── email
 ├── created_at
```

---

## 13. Service Container Binding

Location:

```
app/Providers/AppServiceProvider.php
```

Example:

```php
$this->app->bind(
    CustomerRepository::class,
    EloquentCustomerRepository::class
);
```

---

## 14. API Response Standard

Success:

```
{
  "success": true,
  "data": {},
  "meta": {}
}
```

Error:

```
{
  "success": false,
  "error": {
     "code": "CUSTOMER_NOT_FOUND",
     "message": "Customer not found"
  }
}
```

---

## 15. Rules for AI Agent

AI Agent MUST:

- Follow Clean Architecture strictly
- Never mix domain and infrastructure
- Never access database directly from controller
- Always use UseCase
- Always use repository interface

---

## 16. Coding Standards

- PSR-12
- SOLID
- Strict typing

Required:

```
declare(strict_types=1);
```

---

## 17. Example Flow

Create Customer:

```
POST /api/v1/customers

Controller
 → UseCase
   → Repository Interface
     → Repository Implementation
       → Database
```

---

## 18. Scalability Ready

Supports:

- Mobile app
- Microservices migration
- Horizontal scaling

---

## 19. Testing Strategy

Unit tests:

- Domain
- Application

Feature tests:

- API

---

## 20. AI Agent Implementation Instructions

When generating code:

1. Create Domain
2. Create Application
3. Create Infrastructure
4. Create API Controller
5. Bind repository
6. Create migration

NEVER skip layers.

---

## 21. CRM Core Modules

Required modules:

- Users
- Roles
- Permissions
- Customers
- Leads
- Tasks
- Departments

---

## 22. Deployment Ready

Supports:

- Docker
- CI/CD
- Cloud deployment

---

## ---

# XXV. Production-Optimized CQRS Simplification Rules

The following rules simplify CQRS to avoid overengineering while preserving future microservice readiness.

These rules OVERRIDE previous CQRS sections when conflicts exist.

---

# XXVI. Simplified CQRS Principles

CQRS MUST be applied logically, but WITHOUT unnecessary abstraction layers.

Mandatory separation:

- Commands → mutate state
- Queries → read state

But avoid introducing:

- CommandBus (NOT REQUIRED)
- QueryBus (NOT REQUIRED)
- Event sourcing (NOT REQUIRED)
- Messaging infrastructure (NOT REQUIRED initially)

Handlers are invoked directly via Laravel container.

---

# XXVII. Simplified Module Structure (Production Optimized)

```
Modules/

    CRM/

        Lead/

            Domain/
                Lead.php
                LeadRepositoryInterface.php

            Application/

                Commands/
                    CreateLeadCommand.php
                    CreateLeadHandler.php

                    UpdateLeadCommand.php
                    UpdateLeadHandler.php

                Queries/
                    GetLeadByIdQuery.php
                    GetLeadByIdHandler.php

                    GetLeadsPaginatedQuery.php
                    GetLeadsPaginatedHandler.php

            Infrastructure/

                Persistence/
                    EloquentLeadRepository.php

                ReadModels/
                    LeadReadModel.php

            Presentation/

                API/
                    LeadApiController.php

                Web/
                    LeadWebController.php
```

Removed unnecessary layers:

- No DTO layer required initially
- No Mapper layer required initially
- No Bus layer required initially

These may be added later if needed.

---

# XXVIII. Repository Strategy (Simplified)

Write Repository:

```
LeadRepositoryInterface
EloquentLeadRepository
```

Read Model:

```
LeadReadModel (Eloquent)
```

Query Handlers MAY directly use ReadModels.

Example:

```php
class GetLeadsPaginatedHandler
{
    public function handle(GetLeadsPaginatedQuery $query)
    {
        return LeadReadModel::query()
            ->paginate($query->perPage);
    }
}
```

This is acceptable and recommended.

---

# XXIX. Domain Entity Simplification

Domain Entities MAY use Laravel Eloquent indirectly via Repository.

Avoid creating artificial abstraction layers.

Example acceptable approach:

Domain Entity:

```php
class Lead
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email
    ) {}
}
```

Repository handles Eloquent mapping.

---

# XXX. Controller Rules (Strict)

Controllers MUST:

- use CommandHandler for writes
- use QueryHandler for reads

Controllers MUST NOT:

- access DB directly
- access Eloquent models directly
- contain business logic

---

# XXXI. When to Introduce More Advanced CQRS Components

Add ONLY when needed:

CommandBus → when > 50 commands exist

QueryBus → when > 50 queries exist

Separate Read Database → when performance bottleneck appears

Microservices → when team scaling requires independent deployments

Event sourcing → ONLY if audit/event replay required

---

# XXXII. Performance Optimization Strategy

Optimization order:

Phase 1

Laravel monolith
Single database
CQRS logical separation

Phase 2

Add Redis cache

Phase 3

Add read replicas

Phase 4

Extract read services

Phase 5

Extract write services

---

# XXXIII. What NOT to Build Initially

AI Agent MUST NOT implement:

CommandBus
QueryBus
EventBus
Microservices
Kafka
RabbitMQ
Event sourcing

These are explicitly forbidden in initial implementation.

---

# XXXIV. Final Production Architecture (Optimized)

```
Laravel

Modules

    CRM

        Lead

            Domain

            Application
                Commands
                Queries

            Infrastructure
                Persistence
                ReadModels

            Presentation
                API
                Web
```

This provides:

- clean architecture
- CQRS separation
- high performance
- maintainability
- microservice-ready design

WITHOUT unnecessary complexity.

---

END OF BLUEPRINT



---

# XI. CQRS Architecture Integration (Command Query Responsibility Segregation)

This system MUST implement CQRS from the beginning to ensure scalability, performance optimization, and future microservices extraction.

CQRS separates:

- Commands → write operations (mutations)
- Queries → read operations (data retrieval)

Commands and Queries MUST NOT share the same classes.

---

# XII. CQRS Layer Structure

Each module MUST implement the following structure:

```
Modules/

    CRM/

        Lead/

            Domain/
                Entities/
                ValueObjects/
                Events/
                Repositories/

            Application/

                Commands/
                    CreateLead/
                        CreateLeadCommand.php
                        CreateLeadHandler.php

                    UpdateLead/
                        UpdateLeadCommand.php
                        UpdateLeadHandler.php

                    DeleteLead/
                        DeleteLeadCommand.php
                        DeleteLeadHandler.php

                Queries/
                    GetLeadById/
                        GetLeadByIdQuery.php
                        GetLeadByIdHandler.php

                    GetLeadsPaginated/
                        GetLeadsPaginatedQuery.php
                        GetLeadsPaginatedHandler.php

                DTO/

            Infrastructure/

                Persistence/
                    EloquentLeadRepository.php

                ReadModels/
                    LeadReadModel.php

                Projections/

            Presentation/

                API/
                Web/
```

---

# XIII. Command Definition Standard

Example:

CreateLeadCommand.php

```php
class CreateLeadCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $phone,
        public readonly ?string $email,
        public readonly int $centerId
    ) {}
}
```

Command is a pure data carrier.

No business logic allowed.

---

# XIV. Command Handler Standard

Example:

CreateLeadHandler.php

```php
class CreateLeadHandler
{
    public function __construct(
        private LeadRepositoryInterface $repository
    ) {}

    public function handle(CreateLeadCommand $command): Lead
    {
        $lead = Lead::create(
            name: $command->name,
            phone: $command->phone,
            email: $command->email,
            centerId: $command->centerId
        );

        $this->repository->save($lead);

        return $lead;
    }
}
```

Handler contains business logic.

---

# XV. Query Definition Standard

Example:

GetLeadsPaginatedQuery.php

```php
class GetLeadsPaginatedQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 20
    ) {}
}
```

---

# XVI. Query Handler Standard

Example:

```php
class GetLeadsPaginatedHandler
{
    public function __construct(
        private LeadReadRepositoryInterface $repository
    ) {}

    public function handle(GetLeadsPaginatedQuery $query): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $query->page,
            $query->perPage
        );
    }
}
```

Query handlers MUST NOT modify state.

---

# XVII. Read Model Design

Read models are optimized for queries.

Example:

```php
class LeadReadModel extends Model
{
    protected $table = 'leads';

    public function center()
    {
        return $this->belongsTo(Center::class);
    }
}
```

Read models may include:

- joins
- denormalized fields
- computed fields

---

# XVIII. Repository Separation

Write repository:

```
LeadRepositoryInterface
```

Read repository:

```
LeadReadRepositoryInterface
```

This allows future migration to:

- read replicas
- Elasticsearch
- separate read database

---

# XIX. Controller Integration with CQRS

API Controller example:

```php
class LeadApiController
{
    public function store(Request $request, CreateLeadHandler $handler)
    {
        $command = new CreateLeadCommand(
            name: $request->name,
            phone: $request->phone,
            email: $request->email,
            centerId: $request->center_id
        );

        $lead = $handler->handle($command);

        return response()->json($lead);
    }

    public function index(GetLeadsPaginatedHandler $handler)
    {
        $query = new GetLeadsPaginatedQuery();

        return response()->json(
            $handler->handle($query)
        );
    }
}
```

---

# XX. Web Controller Integration

Example:

```php
class LeadWebController
{
    public function index(GetLeadsPaginatedHandler $handler)
    {
        $query = new GetLeadsPaginatedQuery();

        $leads = $handler->handle($query);

        return view('leads.index', compact('leads'));
    }
}
```

---

# XXI. CQRS Benefits for Future Microservices

This design enables easy extraction into microservices:

Commands → write service
Queries → read service

Future architecture:

```
Lead Command Service
Lead Query Service
```

Minimal refactoring required.

---

# XXII. Optional Future Enhancements

Can add later:

- Command Bus
- Query Bus
- Event Bus
- Event Sourcing
- Projection Workers
- Async processing (Redis, Queue)

---

# XXIII. Mandatory CQRS Rules

AI Agent MUST follow:

- Never mix Commands and Queries
- Never use same class for read and write
- Commands MUST go through CommandHandler
- Queries MUST go through QueryHandler
- Controllers MUST NOT access repositories directly
- Controllers MUST use handlers only

---

# XXIV. Final Architecture Summary

```
Laravel

Modules

    Domain

    Application
        Commands
        Queries
        Handlers

    Infrastructure
        WriteRepositories
        ReadRepositories
        ReadModels

    Presentation
        API
        Web
```

This is mandatory architecture for entire system.

---

END OF BLUEPRINT

