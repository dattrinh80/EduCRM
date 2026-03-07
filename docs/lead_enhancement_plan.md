# Kế hoạch nâng cấp Module Lead & Vệ tinh

## Giai đoạn 1: Chuẩn bị các Module vệ tinh (System Dictionaries)
- [x] **Tạo Module `Source` (Nguồn Lead)**: Quản lý nguồn khách hàng (Facebook, Website, Referral, Walk-in,...)
    - Model: `id` (uuid), `name`, `code`, `is_active`
    - Cấu trúc thư mục: `Modules/CRM/Source`
    - Chức năng: CRUD (Web UI + API JSON)
- [x] **Tạo Module `InterestType` (Nhu cầu/Loại quan tâm)**: Quản lý loại hình dịch vụ khách quan tâm (English,...)
    - Model: `id` (uuid), `name`, `description`, `is_active`
    - Cấu trúc thư mục: `Modules/CRM/InterestType`
    - Chức năng: CRUD (Web UI + API JSON)
- [x] **Cập nhật Module `Campaign`**: Đảm bảo lưu lịch sử chiến dịch trả về lead
    - Đảm bảo có CRUD cơ bản nếu chưa có.

## Giai đoạn 2: Bổ sung cấu trúc dữ liệu Module `Lead`
- [x] **Tạo Migration `add_detailed_fields_to_leads_table`**: Bổ sung các cột mới
    - `dob` (date, nullable)
    - `source_id` (uuid, nullable)
    - `campaign_id` (uuid, nullable)
    - `interest_type_id` (uuid, nullable)
    - `assigned_to` (uuid, nullable)
- [x] **Core Domain Layer & Repository**: Cập nhật Domain Entity `Lead.php` và Contract Repository.
- [x] **Infrastructure Layer**: Cập nhật Eloquent ReadModel thêm relations và Repository Implementation.
- [x] **Application Layer (CQRS)**: Cập nhật Command Create/Update và Handler để đón mapping data cột mới.
- [x] **Presentation Layer (Web UI & API)**:
    - Cập nhật Form Web thêm Selectbox (Source, Campaign, InterestType, AssignedTo)
    - Cập nhật Data API Resources.

## Giai đoạn 3: Tính năng mở rộng cho Sales & Marketing
- [x] **Tạo Lead Manual**: Đã cover ở GĐ 2.
- [x] **Tự động tạo lead (API/Webhook)**: Xây dựng endpoint nhận JSON từ FB/Website => Tự động map ID => Create Lead.
- [x] **Import Lead Excel**: Cài package excel, tạo Command Import và màn hình Import UI.
- [x] **Assign Lead**:
    - Thêm tính năng chọn hàng loạt, hiển thị Assign To trên bảng.
    - Modal Bulk Assign.
    - Cập nhật Handler AssignLeadCommandHandler.
- [x] **Merge Duplicate Leads**: Logic gộp Lead trùng SĐT/Email. Thay đổi trạng thái sang "Merged". Thêm Bulk Merge Modal.

---
*Tiến trình sẽ được tracking trực tiếp tại file này bằng dấu check `[x]` sau mỗi đầu mục hoàn tất.*
