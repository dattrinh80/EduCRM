<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">DANH SÁCH HỌC VIÊN</div>
        <div>Ngày xuất: {{ date('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">STT</th>
                <th width="10%">Mã HV</th>
                <th width="20%">Họ và Tên</th>
                <th width="12%">Điện thoại</th>
                <th width="15%">Email</th>
                <th width="10%">Ngày sinh</th>
                <th width="8%">G.Tính</th>
                <th width="10%">Trạng thái</th>
                <th width="10%">Trung tâm</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->student_code }}</td>
                    <td>{{ $student->customer?->name }}</td>
                    <td>{{ $student->customer?->phone }}</td>
                    <td>{{ $student->customer?->email }}</td>
                    <td>{{ $student->customer?->dob ? \Carbon\Carbon::parse($student->customer->dob)->format('d/m/Y') : '' }}</td>
                    <td>{{ $student->customer?->gender === 'MALE' ? 'Nam' : (($student->customer?->gender === 'FEMALE') ? 'Nữ' : 'Khác') }}</td>
                    <td>{{ $student->status }}</td>
                    <td>{{ $student->customer?->center?->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
