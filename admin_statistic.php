<?php
include 'admin_auth.php';
include 'connection.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'admin_header.php'; ?>
    <title>Admin Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
    <style>
        body {
            padding: 2rem;
            background-color: #f9f9f9;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4 text-center">Admin Statistics Dashboard</h2>

        <div class="row mb-5">
            <div class="col-md-12">
                <div class="card p-4">
                    <h4 class="mb-4">📊 Plan Usage Over Time (Last 7 Days)</h4>
                    <canvas id="usageChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card p-4">
                    <h4 class="mb-4">📅 Activity Calendar View</h4>
                    <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        // Usage Chart (7-day plan activity)
        async function loadUsageChart() {
            const res = await fetch('chart_data.php');
            const data = await res.json();

            const labels = data.map(item => item.date);
            const dietCounts = data.map(item => item.diet_count);
            const exerciseCounts = data.map(item => item.exercise_count);

            new Chart(document.getElementById('usageChart'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Diet Plans',
                            data: dietCounts,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'Exercise Plans',
                            data: exerciseCounts,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.4
                        }
                    ]
                }
            });
        }

        // Calendar Activity View
        function loadCalendar() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '/fetch_calendar_events.php',
                height: 500
            });
            calendar.render();
        }

        loadUsageChart();
        loadCalendar();
    </script>
</body>

</html>