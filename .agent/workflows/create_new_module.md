---
description: Hướng dẫn chi tiết tạo Module hoặc chức năng mới trong hệ thống EduCRM
---

# Hướng dẫn tạo Module hoặc chức năng mới

Dự án EduCRM sử dụng kiến trúc **Modular Monolith** kết hợp **Domain-Driven Design (DDD)**, **CQRS (Command Query Responsibility Segregation)** và **Clean Architecture**. Nhờ đó, tính đóng gói rất cao, việc bảo trì và mở rộng sẽ cực kỳ dễ dàng.

Bất cứ AI Agent nào khi tham gia phát triển dự án này **bắt buộc** phải tuân thủ nghiêm ngặt các yêu cầu và trình tự thực hiện sau đây khi tạo một Module hoặc một tính năng mới.

## Nguyên tắc cốt lõi:
- Viết code theo hướng "Từ lõi ra ngoài" (Inside-Out).
- Đảm bảo "Single Responsibility" (Đơn trách nhiệm). Bóc tách logic ra khỏi Controller.
- Không để logic Database (Eloquent) lẫn vào trong tầng Domain (Cốt lõi).

## Các bước thực hiện bắt buộc:

### Bước 1: Tạo Database Migration (Hạ tầng Database)
1. Tạo file migration trong thư mục `Modules/{ModuleName}/Database/Migrations`.
2. Tạo các cột cần thiết cho bảng cơ sở dữ liệu (ví dụ: `name`, `description`, `is_active`...).
3. Thực thi migration.
4. (Tuỳ chọn) Tạo Database Seeder hoặc Factory nếu cần thiết để mock dữ liệu.

### Bước 2: Khai báo lõi Domain Layer (Thiết kế nghiệp vụ)
1. Định nghĩa "Thực thể" chính (ví dụ Tạo module "Task"). Khởi tạo class Entity trong thư mục `Modules/{ModuleName}/Domain/` (Ví dụ: `Task.php`).
2. Khai báo các thuộc tính, constructor, các hành vi bên trong của Entity đó (không được sử dụng framework cụ thể trong file này ngoài PHP thuần).
3. Tạo ra Interface kết nối (Ví dụ `TaskRepositoryInterface.php`) bên trong `Modules/{ModuleName}/Domain/`. Định nghĩa các hàm hợp đồng như `save(Task $task): void`, `findById(int $id): ?Task`, `delete(Task $task): void`...

### Bước 3: Khai báo Infrastructure Layer (Hạ tầng lưu trữ Eloquent)
1. Tạo Model Eloquent (Read Model) trong `Modules/{ModuleName}/Infrastructure/ReadModels` chuyên để serve nhu cầu đọc danh sách. Model này sử dụng extends từ `Illuminate\Database\Eloquent\Model`.
2. Tạo Implement class (Ví dụ `EloquentTaskRepository.php`) tại `Modules/{ModuleName}/Infrastructure/Persistence/` đóng vai trò implements interface định nghĩa ở Bước 2 (`TaskRepositoryInterface`). Lớp này thực sự gọi Eloquent Model để thực hiện lệnh tạo/sửa/xoá trên DB.

### Bước 4: Xác định Use Case thông qua Application Layer (CQRS)
1. **Thao tác Ghi (Commands - Create/Update/Delete)**: 
   - Nằm tại `Modules/{ModuleName}/Application/Commands/`.
   - Tạo class Command chứa các thuộc tính/dữ liệu đầu vào (DTO).
   - Tạo class Handler nhận Command, map sang Domain Entity và lưu dữ liệu thông qua Repository Interface.
   - Khi xử lý Create/Update phải đảm bảo logic kiểm tra Data hoặc Business rules ngay trong phần Constructor của Entity.
2. **Thao tác Đọc (Queries - Get List/Show detail)**:
   - Nằm tại `Modules/{ModuleName}/Application/Queries/`.
   - Tạo các class Query chứa các params điều kiện tìm kiếm.
   - Tạo class Handler thực trực tiếp gọi thẳng Eloquent Model ở `ReadModels` với tốc độ truy xuất siêu nhanh, nhằm bỏ qua bước biến map lại thành Object Entity.

### Bước 5: Phân quyền bằng Roles & Permissions (Rất quan trọng)
1. Bất kể tạo Module gì, đều sinh ra các keys phân quyền theo cấu trúc module (VD: `view-task`, `create-task`, `edit-task`, `delete-task`).
2. Cập nhật các quyền này vào hệ thống Seeder hoặc Migration cho quyền, liên kết với `Role` hiện tại trong module permission `Modules/Core/Permission`.
3. Đảm bảo ở Controller sau này, mọi hàm đều phải được check middleware `permission:key-quyen`.

### Bước 6: Khai báo giao diện Presentation Layer (BẮT BUỘC CÓ CẢ WEB VÀ API)
**Lưu ý quan trọng**: Tất cả các module và chức năng mới đều CẦN PHẢI triển khai hệ thống API song song với Web UI để phục vụ cho việc kết nối trên app Mobile trong tương lai.
1. Khởi tạo Controllers phục vụ cho Web ở `Modules/{ModuleName}/Presentation/Web/`. Nếu có view sẽ được đặt ở `Modules/{ModuleName}/Presentation/Web/Views`.
2. Khởi tạo Controllers phục vụ cho App Mobile (API) ở `Modules/{ModuleName}/Presentation/API/`. Trả về JSON Data Resources theo chuẩn.
3. Controller tuyệt đối cấm chứa business logic. Trách nhiệm duy nhất của nó lúc này chỉ là lấy Form Request, bắt validation request nếu cần thiết, rồi khởi tạo một Command/Query => Đẩy vào class Handler => Render kết quả lấy được sang cho HTML view blade hoặc JSON.

### Bước 7: Cấu hình Routing & ServiceProvider
1. Cấu hình HTTP endpoint trong `Modules/{ModuleName}/routes/web.php` hoặc `api.php`.
2. Đăng ký module trong `ServiceProvider.php` tại gốc của module (`Modules/{ModuleName}/ServiceProvider.php`):
   - Hàm `register()`: Rất quan trọng, phải Bind RepositoryInterface chạy bằng Implemention Repository thực tế. (`$this->app->bind(...)`).
   - Hàm `boot()`: Đăng ký Router, Migration, View và Translation.
