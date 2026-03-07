Trường hợp 1 Lead → nhiều Students (anh/chị/em) trong CRM giáo dục không hề hiếm. Trong thực tế tuyển sinh:

Phụ huynh đăng ký cho 2–3 con cùng lúc

Phụ huynh đăng ký 1 con trước, sau đó quay lại đăng ký con thứ 2

Phụ huynh đăng ký lớp khác nhau cho nhiều con

Ở nhiều trung tâm K12 hoặc ngoại ngữ, tỷ lệ này có thể 5–20% leads.

Vì vậy kiến trúc CRM phải hỗ trợ 1 Lead → N Students.

1. Nguyên tắc domain quan trọng

Không nên thiết kế:

1 Lead → 1 Student

Mà nên thiết kế:

1 Lead → N Students

2. Database Schema khuyến nghị
leads
column	type
id	uuid
owner_id	uuid
source_id	uuid
status	varchar
students
column	type
id	uuid
customer_id	uuid
student_code	varchar
lead_conversions
column	type
id	uuid
lead_id	uuid
student_id	uuid
converted_by	uuid
converted_at	timestamp

Constraint:

unique(lead_id, student_id)
3. Flow convert thực tế
Lead
Lead: Nguyễn Văn A
Phone: 090xxx
Convert form

Sales nhập:

Guardian: Nguyễn Văn A

Students:
1. Nguyễn Thị B
2. Nguyễn Văn C
System tạo dữ liệu

customers

Nguyễn Văn A
Nguyễn Thị B
Nguyễn Văn C

students

Student B
Student C

student_guardians

B -> A
C -> A

lead_conversions

Lead A -> Student B
Lead A -> Student C
4. ERD logic
leads
   │
   └── lead_conversions
           │
           └── students
                    │
                    └── student_guardians
5. Conversion Rate tính như thế nào?

Có 2 loại conversion rate.

1️⃣ Lead Conversion Rate (chuẩn CRM)

Chỉ tính lead có convert hay không.

converted_leads / total_leads

Ví dụ:

100 leads
20 leads convert

Conversion rate:

20%

Lead A convert 2 students vẫn tính 1 lead converted.

2️⃣ Student Conversion Rate (dùng cho kinh doanh)
students_from_leads / leads

Ví dụ:

100 leads
30 students

Student conversion:

30%
6. Hoa hồng sales tính như thế nào?

Hoa hồng nên gắn với student hoặc enrollment.

Ví dụ:

Lead A:

Student B tuition: 20 triệu
Student C tuition: 20 triệu

Doanh thu:

40 triệu

Hoa hồng tính theo student.

7. Ví dụ report
Lead performance
sales	leads	converted leads
A	50	10
Student acquisition
sales	students	revenue
A	15	300tr
8. Dashboard thực tế nên có

CRM nên có 3 metric khác nhau.

Lead metrics
Leads created
Leads contacted
Leads converted
Student metrics
Students from leads
Students enrolled
Revenue metrics
Tuition
Commission
9. UI convert nên thiết kế thế nào

Convert form nên cho phép:

Lead convert

Guardian info
Student list (+ add more student)

Ví dụ UI:

Guardian
Nguyễn Văn A

Students
+ Nguyễn Thị B
+ Nguyễn Văn C
+ Add student
10. Kiến trúc final khuyến nghị
customers
leads
students
student_guardians
lead_conversions
11. Một điểm cực kỳ quan trọng (rất nhiều CRM làm sai)

Lead không nên tự động đóng sau khi tạo student đầu tiên.

Lead nên chuyển trạng thái:

Partially Converted

Khi còn học sinh khác.

Trạng thái chuẩn:

New
Contacted
Qualified
Partially Converted
Converted
Lost
12. Ví dụ lifecycle

Lead A:

1 student created → Partially Converted
2 students created → Converted
13. Kết luận

Trường hợp:

1 Lead → N Students

là bình thường trong giáo dục và nên được hỗ trợ ngay từ đầu.

Kiến trúc đúng:

leads
   │
   └── lead_conversions
           │
           └── students