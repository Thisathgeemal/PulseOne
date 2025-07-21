<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 10px;
            padding: 0;
        }
        .page-container {
            padding: 20px;
            min-height: 900px;
            border: 3px solid #000000; 
        }
        header {
            display: flex;
            align-items: center;
            justify-content: start;
            background-color: #a10000;
            color: white;
            padding: 10px 20px;
            border-bottom: 2px solid #4a0000;
            height: 40px; 
        }
        header h1 {
            font-size: 24px;
            margin: 0;
            text-align: center;
        }
        header img {
            height: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #333;
            width: 20%; 
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            color: #000000;
            margin-top: 25px; 
        }
        .report-info {
            margin: 0px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header>
            <h1>PULSEONE</h1>
        </header>

        <h2>Admin Management Report</h2>
        <div class="report-info">
            <p><strong>Date:</strong> {{ $formattedDate }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $admin)
                <tr>
                    <td>{{ $admin->first_name }}</td>
                    <td>{{ $admin->last_name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->mobile_number }}</td>
                    <td>{{ $admin->roles->first()?->pivot->is_active ? 'Active' : 'Inactive' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
