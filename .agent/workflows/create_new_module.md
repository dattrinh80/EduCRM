---
description: Hướng dẫn chi tiết tạo Module hoặc chức năng mới trong hệ thống EduCRM
---

# Hướng dẫn tạo Module hoặc chức năng mới

Dự án EduCRM sử dụng kiến trúc **Modular Monolith** kết hợp **Domain-Driven Design (DDD)**, **CQRS (Command Query Responsibility Segregation)** và **Clean Architecture**. Nhờ đó, tính đóng gói rất cao, việc bảo trì và mở rộng sẽ cực kỳ dễ dàng.

Bất cứ AI Agent nào khi tham gia phát triển dự án này **bắt buộc** phải tuân thủ nghiêm ngặt các yêu cầu và trình tự thực hiện sau đây khi tạo một Module hoặc một tính năng mới.

# Hướng dẫn tạo Module hoặc chức năng mới

Dự án EduCRM sử dụng kiến trúc **Modular Monolith** kết hợp **Domain-Driven Design (DDD)**, **CQRS (Command Query Responsibility Segregation)** và **Clean Architecture**. Nhờ đó, tính đóng gói rất cao, việc bảo trì và mở rộng sẽ cực kỳ dễ dàng.

Bất cứ AI Agent nào khi tham gia phát triển dự án này **bắt buộc** phải tuân thủ nghiêm ngặt các yêu cầu và trình tự thực hiện sau đây khi tạo một Module hoặc một tính năng mới.

## Nguyên tắc cốt lõi:
- **Inside-Out Development**: Viết nghiệp vụ từ lõi (Domain) ra ngoài (Presentation).
- **Single Responsibility**: Tách biệt rõ ràng logic nghiệp vụ (Handlers) khỏi việc điều hướng (Controllers).
- **Premium UI Standard**: Giao diện phải hiện đại, sử dụng **`rounded-3xl`**, đổ bóng nhẹ và tương tác mượt mà qua AlpineJS.

## Các bước thực hiện bắt buộc:

### Bước 1: Tạo Database & Permission (Hạ tầng)
1. **Migration**: Tạo bảng trong `Modules/{ModuleName}/Database/Migrations`. Sử dụng UUID cho Primary Key.
2. **Permissions**: Khai báo các khóa quyền (`view-*`, `create-*`, `edit-*`, `delete-*`) và cập nhật vào `PermissionSeeder` của hệ thống.
3. **Seeders**: Tạo dữ liệu mẫu hoặc cấu hình ban đầu nếu cần.

### Bước 2: Khai báo lõi Domain Layer (Nghiệp vụ thuần)
1. **Entity**: Tạo class trong `Modules/{ModuleName}/Domain/` (Ví dụ: `Task.php`). 
   - Sử dụng PHP 8 constructor promotion.
   - Luôn có phương thức `static create()` và các phương thức `update()`, `changeStatus()` chứa logic kiểm tra nghiệp vụ.
2. **Repository Interface**: Định nghĩa hợp đồng lưu trữ trong cùng thư mục Domain. Ví dụ: `TaskRepositoryInterface.php`.

### Bước 3: Khai báo Infrastructure Layer (Lưu trữ & Truy vấn)
1. **Read Model**: Tạo Eloquent model trong `Infrastructure/ReadModels` để phục vụ Query. Model này có thể chứa các Scope để filter dữ liệu.
2. **Repository Implementation**: Tạo class trong `Infrastructure/Persistence/` để thực thi Interface ở Bước 2. 
   - Nhiệm vụ: Chuyển đổi (Map) dữ liệu giữa **Domain Entity** và **Read Model** khi `save()` hoặc `findById()`.

### Bước 4: Xác định Use Case qua Application Layer (CQRS)
1. **Commands (Ghi)**: Nằm tại `Modules/{ModuleName}/Application/Commands/`.
   - `Command`: Đối tượng chứa dữ liệu đầu vào (DTO).
   - `Handler`: Nhận Command, gọi Repository để lấy/lưu Entity, thực thi logic nghiệp vụ.
2. **Queries (Đọc)**: Nằm tại `Modules/{ModuleName}/Application/Queries/`.
   - Tối ưu hiệu năng bằng cách gọi trực tiếp **Read Model** (Eloquent) để trả về dữ liệu cho danh sách hoặc chi tiết.

### Bước 5: Presentation Layer (Web & API)
**Lưu ý**: Phải triển khai song song Web và API (Mobile App).

1. **Web Controller**: Đặt tại `Presentation/Web/`.
   - **AJAX Support**: Các hàm `show()`, `create()`, `edit()` phải kiểm tra `$request->ajax()` và trả về **Partial View** (ví dụ: `partials.create_form`) để load vào Modal động.
2. **Views & UI Components**:
   - Sử dụng hệ thống component `<x-ui.*>`.
   - **Thiết kế Premium**: Luôn dùng **`rounded-3xl`** cho các container chính và Card.
   - **Sticky Modal**: Form trong modal bắt buộc theo cấu trúc: Fixed Header -> Scrollable Content -> Fixed Footer (chứa nút Action) để đảm bảo nút bấm luôn lộ diện.
3. **API Controller**: Đặt tại `Presentation/API/`. Trả về JSON thông qua Laravel Resources.
4. **Icons**: Sử dụng Lucide Icons qua `data-lucide` và gọi `lucide.createIcons()` sau mỗi lần load nội dung bằng AJAX.

### Bước 6: Cấu hình Routing & ServiceProvider
1. **Routes**: Cấu hình trong `Modules/{ModuleName}/routes/web.php` và `api.php`.
2. **ServiceProvider**: Đăng ký module và quan trọng nhất là **Bind Repository Interface** vào **Eloquent Implementation**:
   ```php
   $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
   ```
3. Đăng ký các thư mục Views, Migrations và Translations.

## Checklist khi hoàn thành:
- [ ] Code tuân thủ `declare(strict_types=1);`.
- [ ] Không gọi Eloquent Model trực tiếp trong Command Handler.
- [ ] Modal có thanh cuộn nội dung riêng và Footer cố định.
- [ ] Giao diện sử dụng bo góc `rounded-3xl`.
- [ ] Đã có đầy đủ phân quyền bằng Middleware.
- [ ] API đã sẵn sàng cho Mobile.

---
*Cập nhật bởi Antigravity cho EduCRM - 2026*
