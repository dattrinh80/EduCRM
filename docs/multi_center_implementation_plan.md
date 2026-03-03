# Implementation Plan: Multi-Center Architecture Refactoring

## 1. Goal Description
Chuyển đổi hệ thống sang kiến trúc Multi-Center (Đa cơ sở). Trọng tâm của đợt refactor này là khả năng phân mảnh dữ liệu theo cơ sở (Center Context), gán `default_center_id` cho người dùng, sử dụng context session để phân quyền, đồng thời có cơ chế bypass xử lý dữ liệu toàn hệ thống cho nhóm tài khoản Super Admin.

## 2. Proposed Changes — Status

### 2.1. ✅ Cập nhật Database & Domain Models (User)
- ✅ Migration: `add_default_center_id_to_users_table` — tạo xong
- ✅ Domain Entity `User.php` — có thuộc tính `defaultCenterId`
- ✅ `EloquentUserRepository` — map `default_center_id`
- ✅ `CreateUserCommand` / `UpdateUserCommand` — có `defaultCenterId`
- ✅ `UserWebController` — validate + truyền `default_center_id` vào Command
- ✅ Giao diện User Management (Create/Edit Modal) — có dropdown "Cơ sở mặc định"
- ✅ `UserReadModel` — thêm method `hasRole()` để hỗ trợ middleware

### 2.2. ✅ Session Management & Bối cảnh (Context)
- ✅ `AuthController::login()` — set `session(['current_center_id' => $user->default_center_id])`
- ✅ `POST /auth/switch-center` route — đã đăng ký
- ✅ `AuthController::switchCenter()` — hỗ trợ cả chọn center cụ thể và clear context cho Super Admin
- ✅ Center Switcher UI trong header layout (`app.blade.php`) — hiển thị center hiện tại, cho phép đổi

### 2.3. ✅ Center Context Middleware
- ✅ `CenterContextMiddleware` — tạo xong, đăng ký vào web + api middleware
- ✅ An toàn khi `hasRole()` chưa tồn tại (sử dụng `method_exists`)
- ✅ Bind `center_id` và `is_super_admin` vào app container

### 2.4. ✅ Global Query Filtering (Scoped Database)
- ✅ Trait `BelongsToCenter` — tạo xong, bypass khi `is_super_admin == true`
- ✅ Áp dụng vào `LeadReadModel` (leads table có cột `center_id`)
- ✅ **GỠ BỎ** khỏi `SourceReadModel` — bảng sources KHÔNG có `center_id` (là dữ liệu lookup toàn cục)
- ✅ `CampaignReadModel` — KHÔNG áp dụng (campaigns cũng là dữ liệu toàn cục)

### 2.5. ✅ Refactor Form và Lô-gic Tạo (Create/Update Commands)
- ✅ Lead Create/Edit form (Web): Ẩn field center cho user thường, auto-fill từ context `app('center_id')`
- ✅ Lead Create/Edit (API): Cùng logic — center_id tự lấy từ session nếu không phải Super Admin
- ✅ Super Admin vẫn thấy dropdown chọn Center như hệ thống gốc

## 3. Verification Plan
- Chạy PHPUnit Tests kiểm tra việc tạo user, assign default_center_id có gán đúng vào bảng `users` ko.
- Đổi tài khoản test Sales => Kiểm tra Login xem hệ thống có chỉ lấy các Lead thuộc `default_center_id` của Sales đó không.
- Chuyển sang tài khoản Super Admin xem có nhìn thấy tất cả mọi thứ không, bao gồm Leads của các center khác nhau.
- Test thao tác tạo/nhập Lead Excel của user chỉ nằm đúng vào center được gán session.
- Test Center Switcher trên header: chuyển đổi center và kiểm tra data thay đổi.
