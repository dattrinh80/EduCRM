---
description: Detailed guide for creating new Modules or features in the EduCRM system
---

# Guide to Creating a New Module or Feature

The EduCRM project uses a **Modular Monolith** architecture combined with **Domain-Driven Design (DDD)**, **CQRS (Command Query Responsibility Segregation)**, and **Clean Architecture**. This ensures high encapsulation, ease of maintenance, and extensibility.

Any AI Agent or developer participating in the project **must** strictly follow these steps and standards when creating a new Module or feature.

## Core Principles:
- **Inside-Out Development**: Write logic from the core (Domain) outwards (Presentation).
- **Single Responsibility**: Clearly separate business logic (Handlers) from routing and coordination (Controllers).
- **Premium UI Standard**: Ensure modern visuals using **`rounded-3xl`**, subtle shadows, and smooth AlpineJS interactions.

## Mandatory Implementation Steps:

### Step 1: Database & Permissions (Infrastructure)
1. **Migration**: Create the table in `Modules/{ModuleName}/Database/Migrations`. Practice using UUID for Primary Keys.
2. **Permissions**: Define keys like `view-*`, `create-*`, `edit-*`, `delete-*` and update the system's `PermissionSeeder`.
3. **Seeders**: Create sample data or initial configurations as needed.

### Step 2: Define Domain Layer (Pure Business Core)
1. **Entity**: Create the core Class in `Modules/{ModuleName}/Domain/` (e.g., `Task.php`).
   - Use PHP 8 constructor promotion.
   - Always implement a `static create()` method and relevant state change methods (e.g., `update()`, `changeStatus()`) containing business rules.
2. **Repository Interface**: Define the data contract in the same Domain directory (e.g., `TaskRepositoryInterface.php`).

### Step 3: Define Infrastructure Layer (Storage & Persistence)
1. **Read Model**: Create a specialized Eloquent model in `Infrastructure/ReadModels` for Query operations. This model can include specialized Scopes for filtering.
2. **Repository Implementation**: Create the implementation class in `Infrastructure/Persistence/` that satisfies the Step 2 Interface.
   - **Responsibility**: Map data between the **Domain Entity** and the **Read Model** during `save()` or `findById()`.

### Step 4: Define Use Cases via Application Layer (CQRS)
1. **Commands (Writes)**: Located in `Modules/{ModuleName}/Application/Commands/`.
   - **Command**: A DTO (Data Transfer Object) containing input data.
   - **Handler**: Receives the Command, calls the Repository to fetch/save the Entity, and executes specific business logic.
2. **Queries (Reads)**: Located in `Modules/{ModuleName}/Application/Queries/`.
   - Optimize for performance by calling the **Read Model** (Eloquent) directly to return data for lists or details.

### Step 5: Presentation Layer (Web & API)
**Note**: Both Web and API (Mobile App) must be implemented concurrently.

1. **Web Controller**: Located in `Presentation/Web/`.
   - **AJAX Support**: Methods like `show()`, `create()`, and `edit()` must check `$request->ajax()` and return a **Partial View** (e.g., `partials.create_form`) for dynamic modal loading.
2. **Views & UI Components**:
   - Use the `<x-ui.*>` component system.
   - **Premium Design**: Always use **`rounded-3xl`** for main containers and Cards.
   - **Sticky Modal**: Forms in modals must follow the structure: Fixed Header -> Scrollable Content -> Fixed Footer (Action buttons) to ensure buttons are always visible.
3. **API Controller**: Located in `Presentation/API/`. Return JSON responses via Laravel Resources.
4. **Icons**: Use Lucide Icons via `data-lucide` and trigger `lucide.createIcons()` after every AJAX content load.

### Step 6: Routing & ServiceProvider Configuration
1. **Routes**: Define endpoints in `Modules/{ModuleName}/routes/web.php` and `api.php`.
2. **ServiceProvider**: Register the module and, crucially, **Bind the Repository Interface** to its **Eloquent Implementation**:
   ```php
   $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
   ```
3. Load the module's Views, Migrations, and Translations.

## Completion Checklist:
- [ ] Code strictly follows `declare(strict_types=1);`.
- [ ] No direct Eloquent calls inside Command Handlers.
- [ ] Modals feature separate scrollable content and a sticky Footer.
- [ ] Interface uses **`rounded-3xl`** border radius.
- [ ] Permissions are enforced via Middleware.
- [ ] API is ready for Mobile consumption.

## 🛡️ Safe Modification Rule (Impact Analysis)
When changing any existing component (function, service, UI element, route...):
1. **Traceability**: Find all project files using that component (use `grep` or Global Search).
2. **Analysis**: Evaluate if the change (renaming, parameter changes, data types) impacts logic elsewhere.
3. **Synchronized Update**: Update all affected locations simultaneously before committing.
4. **Verification**: Re-test related features to ensure no cascading failures (Regression).

---
*Updated by Antigravity for EduCRM - 2026*
