<?php 
include_once '../CAPSTONE/templates/dash.php';
include __DIR__ . '/../models/fetch.php';

$employmentData = getEmploymentData($pdo);



$courses = [];
$employmentDataByYear = [];
$totalCounts = [
    'Regular' => 0,
    'Contractual' => 0,
    'Temporary' => 0,
    'Self-employed' => 0,
    'Casual' => 0,
    'Never Employed' => 0
];
$selectedCourse = $_GET['course'] ?? 'BSIT';

foreach ($employmentData as $data) {
    $course = trim($data['course']);
    if (!in_array($course, $courses)) {
        $courses[] = $course;
    }

    if ($data['course'] === $selectedCourse) {
        $year = $data['graduation_year'];
        $status = ucfirst(strtolower(trim($data['employment_status'])));
        if (isset($totalCounts[$status])) {
            $totalCounts[$status] += (int)$data['total'];
        }

        if (!isset($employmentDataByYear[$year])) {
            $employmentDataByYear[$year] = [
                'Regular' => 0,
                'Contractual' => 0,
                'Temporary' => 0,
                'Self-employed' => 0,
                'Casual' => 0,
                'Never Employed' => 0
            ];
        }

        if (isset($employmentDataByYear[$year][$status])) {
            $employmentDataByYear[$year][$status] += (int)$data['total'];
        }
    }
}
$years = array_keys($employmentDataByYear);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Employment Statistics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    .container {
        width: 80%;
        margin: 50px auto;
        background-color: #f9f9f9;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        padding: 20px;
    }

    h2,
    label {
        text-align: center;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .total-card {
        background-color: #4CAF50;
        color: #fff;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        font-size: 1.2rem;
        margin-bottom: 20px;
    }

    canvas {
        max-height: 500px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Employment Statistics</h2>
        <form method="GET">
            <label for="course">Select Course:</label>
            <select name="course" onchange="this.form.submit()">
                <?php foreach ($courses as $course): ?>
                <option value="<?= $course ?>" <?= $course === $selectedCourse ? 'selected' : '' ?>>
                    <?= $course ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
        <div class="total-card">
            Regular: <?= $totalCounts['Regular'] ?> | Contractual: <?= $totalCounts['Contractual'] ?> | Temporary:
            <?= $totalCounts['Temporary'] ?> | Self-employed: <?= $totalCounts['Self-employed'] ?> | Casual:
            <?= $totalCounts['Casual'] ?> | Never Employed: <?= $totalCounts['Never Employed'] ?>
        </div>
        <canvas id="employmentChart"></canvas>
    </div>

    <script>
    const ctx = document.getElementById('employmentChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($years) ?>,
            datasets: [{
                    label: 'Regular',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Regular')) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Contractual',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Contractual')) ?>,
                    backgroundColor: 'rgba(255, 206, 86, 0.8)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Temporary',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Temporary')) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Self-employed',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Self-employed')) ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.8)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Casual',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Casual')) ?>,
                    backgroundColor: 'rgba(255, 159, 64, 0.8)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2
                },
                {
                    label: 'Never Employed',
                    data: <?= json_encode(array_column($employmentDataByYear, 'Never Employed')) ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    stacked: false,
                    beginAtZero: true
                }
            }
        }
    });
    </script>
</body>

</html>