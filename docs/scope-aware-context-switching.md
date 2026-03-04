# Scope-Aware Context Switching & Navigation

When a user switches their active scope (e.g., from SYSTEM to a specific CENTER) while on a restricted page, they currently encounter a 403 Forbidden error and lose access to the global UI (including the switcher). Instead of showing an error page, the system must intelligently redirect the user to a valid location within their new scope, recalculate permissions, and rebuild the navigation menu.

## Proposed Changes

### Background Session Management
-   Update `CenterContextMiddleware` (or the Switcher Controller) to explicitly store both `active_scope_level` (SYSTEM, REGION, CENTER) and `active_scope_id` (null, region_id, center_id) in the user's session.

### Permission Engine Updates
-   Refactor `hasPermission` in `DatabaseAuthorizationService` to prioritize taking the `active_scope_level` from the session context when evaluating rules, rather than indiscriminately checking all assigned user roles.

### Intelligent Middleware (Route Guard)
-   Modify `CheckPermission` middleware (or create a new `CheckRoutePermission` guard).
-   **Logic:**
    1.  Call `hasPermission(user, required_permission, active_scope_level)`.
    2.  If `true`, proceed.
    3.  If `false`:
        -   Do **not** immediately throw `abort(403)`.
        -   Determine the fallback URL based on the `active_scope_level`:
            -   `SYSTEM` -> `route('admin.dashboard')`
            -   `CENTER` -> (Create a fallback or use the main center dashboard route).
        -   Redirect to the fallback URL with a flash message: *"Bạn đã chuyển phạm vi hoạt động. Một số chức năng không còn khả dụng."*

### UI & Navigation
-   **Root Layout (`layouts.app.blade.php`):** Ensure the Scope Switcher component is placed high up in the root layout and its visibility/rendering is *never* wrapped in a restricted `@can` block. It must always be accessible.
-   **Dynamic Menus:** Ensure all sidebar menu items (`@can` directives) in Blade templates correctly resolve against the *active scope* in the session, effectively rebuilding the navigation UI automatically when the scope changes.

## Verification Plan

### Test Scenarios
1.  **Scope Downgrade Stranding:**
    -   User has `Admin` (SYSTEM) and `Manager` (CENTER_1).
    -   Select SYSTEM scope. Navigate to `/admin/users` (Requires SYSTEM).
    -   Use the Switcher to select `CENTER_1`.
    -   **Expected Validation:** The system redirects the user to the generic Dashboard (or Center dashboard) with a flash message, rather than showing a 403 error. The sidebar menu updates to hide User Management.
2.  **Valid Scope Navigation:**
    -   User selects SYSTEM scope.
    -   Navigates to `/admin/users`.
    -   **Expected Validation:** Page loads successfully.
3.  **UI Switcher Presence:**
    -   Manually force a 403 by typing an unauthorized URL.
    -   **Expected Validation:** The 403 page still displays the top navigation bar with the Scope Switcher intact.
