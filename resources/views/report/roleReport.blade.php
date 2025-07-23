<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Role Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 10px;
            padding: 0;
        }
        .page-container {
            border: 2px solid #000000;
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
            width: 33%; 
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

        <h2>User Role Management Report</h2>
        <div class="report-info">
            <p><strong>Date:</strong> {{ $formattedDate }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Total Users</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->role_name }}</td>
                    <td>{{ $role->users_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
