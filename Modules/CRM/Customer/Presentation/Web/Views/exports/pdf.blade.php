<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Danh sách Khách hàng - Customers</title>
    <style>
        @page {
            margin: 20px;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
            color: #1e293b;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            text-align: left;
            word-wrap: break-word;
            vertical-align: top;
        }
        th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Column Widths (total ~ 100%) */
        .col-stt { width: 3%; text-align: center; }
        .col-name { width: 15%; font-weight: bold; }
        .col-phone { width: 10%; }
        .col-email { width: 15%; }
        .col-dob { width: 8%; }
        .col-gender { width: 7%; }
        .col-address { width: 22%; }
        .col-center { width: 12%; }
        .col-date { width: 8%; }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>BÁO CÁO DANH SÁCH KHÁCH HÀNG (PHỤ HUYNH)</h1>
        <p>Xuất này hệ thống lúc: {{ now()->format('d/m/Y H:i:s') }} | Tổng số: {{ count($customers) }} khách hàng</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-stt">STT</th>
                <th class="col-name">Họ và Tên</th>
                <th class="col-phone">Số điện thoại</th>
                <th class="col-email">Email</th>
                <th class="col-dob">Ngày sinh</th>
                <th class="col-gender">Giới tính</th>
                <th class="col-address">Địa chỉ</th>
                <th class="col-center">Cơ sở</th>
                <th class="col-date">Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
            <tr>
                <td class="col-stt">{{ $index + 1 }}</td>
                <td class="col-name">{{ $customer->name }}</td>
                <td class="col-phone">{{ $customer->phone }}</td>
                <td class="col-email">{{ $customer->email }}</td>
                <td class="col-dob">{{ $customer->dob ? \Carbon\Carbon::parse($customer->dob)->format('d/m/Y') : '' }}</td>
                <td class="col-gender">
                    {{ $customer->gender === 'MALE' ? 'Nam' : ($customer->gender === 'FEMALE' ? 'Nữ' : ($customer->gender === 'OTHER' ? 'Khác' : '')) }}
                </td>
                <td class="col-address">{{ $customer->address }}</td>
                <td class="col-center">{{ $customer->center_id ? ($centers[$customer->center_id] ?? '') : '' }}</td>
                <td class="col-date">{{ $customer->created_at?->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Hệ thống Quản lý Giáo dục EduCRM
    </div>

</body>
</html>
