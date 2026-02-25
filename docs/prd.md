Functional Requirements (Chi tiết chức năng)
MODULE 1: Quản lý Cơ sở (Branch Management)
Mục tiêu

Quản lý hệ thống đa cơ sở.

Chức năng
1.1 Quản lý cơ sở

Fields:

branch_id
branch_name
address
phone
manager_id
status
created_at
1.2 Phân quyền theo cơ sở

User có thể thuộc:

1 hoặc nhiều cơ sở

Use cases:

Manager chỉ xem dữ liệu cơ sở mình

Director xem tất cả

MODULE 2: Quản lý Lead
Mục tiêu

Quản lý toàn bộ khách hàng tiềm năng.

2.1 Lead Fields
lead_id
full_name
phone
email
dob
source (Facebook, Website, Referral, Walk-in)
campaign_id
branch_id
assigned_to
status
interest_type
    - English course
    - Study abroad
    - Both
created_at
2.2 Lead Status Pipeline
New Lead
Contacted
Consulting
Interested
Not Interested
Converted
Lost
2.3 Chức năng

Tạo lead manual

Import lead Excel

Tự động tạo lead từ:

Facebook

Website

Landing page

Assign lead:

Manual

Auto assign rule

Merge duplicate leads

MODULE 3: Quản lý Khách hàng (Customer Management)

Lead → Convert → Customer

3.1 Customer fields
customer_id
full_name
phone
email
dob
gender
address
branch_id
owner_id
customer_type
    - Student
    - Parent
    - Study abroad applicant
3.2 Relationship

Một customer có thể là:

học viên

phụ huynh

người đăng ký du học

MODULE 4: Quản lý Học viên (Student Management)

Student là subtype của Customer

4.1 Student fields
student_id
customer_id
student_code
branch_id
academic_status
level
school
notes
MODULE 5: Quản lý Khóa học (Course Management)
5.1 Course
course_id
course_name
course_type
level
duration
tuition_fee
description
status

Course type:

IELTS
TOEIC
Kids English
Communication
Study Abroad Prep
MODULE 6: Quản lý Lớp học (Class Management)
6.1 Class
class_id
course_id
teacher_id
branch_id
start_date
end_date
schedule
room
max_students
status
MODULE 7: Enrollment (Ghi danh)
7.1 Enrollment
enrollment_id
student_id
class_id
enrollment_date
status
tuition_fee
discount
final_fee

Status:

Active
Completed
Dropped
Deferred
MODULE 8: Study Abroad Management

Đây là module quan trọng riêng cho trung tâm du học.

8.1 Study Abroad Application
application_id
customer_id
destination_country
school_name
program
intake
consultant_id
status
created_at

Status pipeline:

New
Consulting
Preparing Documents
Applied
Offer Received
Visa Processing
Visa Approved
Completed
Rejected
8.2 Document Management
document_id
application_id
document_type
file_url
status

Document types:

Passport
Transcript
Certificate
IELTS
Bank statement
Offer letter
Visa
8.3 Timeline tracking

Track:

Apply date
Offer date
Visa date
Departure date
MODULE 9: Sales Management
9.1 Deal Management
deal_id
customer_id
value
stage
owner_id
branch_id
expected_close_date

Stages:

New
Consulting
Trial class
Negotiation
Closed Won
Closed Lost
MODULE 10: Task Management
10.1 Task
task_id
title
description
assigned_to
related_type
related_id
due_date
status
priority

Related to:

Lead
Customer
Deal
Application
Student
MODULE 11: Marketing Management
11.1 Campaign
campaign_id
campaign_name
channel
budget
start_date
end_date
status

Channel:

Facebook
Google
TikTok
Email
Offline
11.2 Lead source tracking

Track ROI:

campaign → leads → deals → revenue
MODULE 12: Communication Management
12.1 Call logs
call_id
customer_id
user_id
duration
result
notes
12.2 Email logs
12.3 SMS logs
12.4 Chat logs

Integration:

Facebook Messenger
Zalo
WhatsApp
MODULE 13: Finance Management
13.1 Invoice
invoice_id
student_id
amount
due_date
status
13.2 Payment
payment_id
invoice_id
amount
method
payment_date
MODULE 14: Reporting
14.1 Sales reports

Revenue by branch

Revenue by course

Revenue by consultant

14.2 Marketing reports

Leads by source

Conversion rate

14.3 Study abroad reports

Applications by country

Visa success rate

14.4 Consultant performance

Leads handled

Deals won

Revenue generated

MODULE 15: Multi-branch support

System must support:

Multiple branches

Data isolation by branch

Cross branch reporting

MODULE 16: Permission System

RBAC model

Roles:

Admin
Director
Branch Manager
Consultant
Study Abroad Consultant
Teacher
Accountant
Marketing

Permissions:

create
read
update
delete
assign
approve
view reports
MODULE 17: Workflow Automation

Examples:

Auto assign lead:

IF lead.source = Facebook
THEN assign to Facebook team

Auto create task:

IF new lead created
THEN create follow up task

Auto notify:

IF visa approved
THEN notify consultant and manager
MODULE 18: Document Management

Store:

contracts
visa
student documents

Support:

upload
preview
versioning
MODULE 19: Integration

External integrations:

Facebook Lead API
Google Sheets
Email
SMS
Payment gateway
Accounting software
LMS
Website
Landing page
MODULE 20: Dashboard

Role-based dashboards:

Consultant dashboard:

My leads
My deals
My tasks
Revenue

Manager dashboard:

Branch revenue
Consultant performance
Conversion rate

Director dashboard:

Total revenue
All branches performance
Study abroad pipeline