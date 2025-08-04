<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>
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
            text-align: center;
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

        <h2>Attendance Summary Report</h2>
        <div class="report-info">
            <p><strong>Date:</strong> {{ $date  }}</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Member Name</th>
                    <th>User Role</th>
                    <th>Date</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</td>
                    <td>
                       @if ($attendance->user && $attendance->user->roles && $attendance->user->roles->isNotEmpty())
                            @php
                                $filteredRoles = $attendance->user->roles->whereIn('role_name', ['Member', 'Trainer'])->pluck('role_name');
                            @endphp

                            @if ($filteredRoles->isNotEmpty())
                                {{ $filteredRoles->join(', ') }}
                            @else
                                N/A
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}</td>
                    <td>
                        @if ($attendance->check_out_time)
                            {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
