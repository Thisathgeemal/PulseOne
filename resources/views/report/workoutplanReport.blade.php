<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Workout Plan Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 10px;
            padding: 0;
        }
        .page-container {
            border: 3px solid #000000; 
            padding: 20px;
            min-height: 900px;
            position: relative; 
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
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px; 
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
        footer {
            position: absolute;
            bottom: 10px;
            width: calc(100% - 40px); 
            border-top: 2px solid #4a0000;
            padding-top: 5px;
            color: #333;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <header>
            <h1>PULSEONE</h1>
        </header>

        <h2>Workout Plan Report - {{ $plan->plan_name }}</h2>

        <div class="report-info">
            <p><strong>Date:</strong> {{ $date }}</p>
            <p><strong>Member:</strong> 
                {{ trim(($plan->member->first_name ?? '') . ' ' . ($plan->member->last_name ?? '')) ?: 'N/A' }}
            </p>
            <p><strong>Trainer:</strong> 
                {{ trim(($plan->trainer->first_name ?? '') . ' ' . ($plan->trainer->last_name ?? '')) ?: 'N/A' }}
            </p>    
            <p><strong>Duration:</strong> {{ $plan->start_date->format('d-m-Y') }} to {{ $plan->end_date->format('d-m-Y') }}</p>
            <p><strong>Status:</strong> {{ $plan->status }}</p>
        </div>

        <hr>

        @foreach($groupedExercises as $day => $exercises)
            <h3>Day {{ $day }}</h3>
            <table>
                <thead>
                    <tr>
                        <th>Exercise</th>
                        <th>Description</th>
                        <th>Sets</th>
                        <th>Reps</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exercises as $exercise)
                    <tr>
                        <td>{{ $exercise->exercise->name }}</td>
                        <td>{{ $exercise->exercise->description }}</td>
                        <td>{{ $exercise->sets }}</td>
                        <td>{{ $exercise->reps }}</td>
                        <td>{{ $exercise->notes }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</body>
</html>
