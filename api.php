<?php
include 'config.php';

header("Content-Type: application/json");

$apiKey = "4596f2af70fe4a9eae0170138260901";
$action = $_GET['action'] ?? '';

/* =========================================================
   1️⃣ FETCH WEATHER FROM WeatherAPI.com
========================================================= */
if ($action === 'fetch') {
    $city = trim($_GET['city']);
    $url = "http://api.weatherapi.com/v1/current.json?key=$apiKey&q=" . urlencode($city);

    $response = @file_get_contents($url);
    if ($response === FALSE) {
        echo json_encode(["error" => "API fetch failed"]);
        exit;
    }

    echo $response;
    exit;
}

/* =========================================================
   2️⃣ CREATE – SAVE WEATHER RECORD
========================================================= */
if ($action === 'save') {
    $stmt = $conn->prepare(
        "INSERT INTO weather_records (city, temperature, humidity, weather_condition)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sdis",
        $_POST['city'],
        $_POST['temp'],
        $_POST['humidity'],
        $_POST['condition']
    );

    $stmt->execute();
    echo json_encode(["status" => "saved"]);
    exit;
}

/* =========================================================
   3️⃣ READ – RECORDS WITH PAGINATION
========================================================= */
if ($action === 'records') {
    $page = intval($_GET['page'] ?? 1);
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $rows = [];
    $res = $conn->query(
        "SELECT * FROM weather_records
         ORDER BY recorded_at DESC
         LIMIT $limit OFFSET $offset"
    );

    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }

    $total = $conn->query("SELECT COUNT(*) c FROM weather_records")
                  ->fetch_assoc()['c'];

    echo json_encode([
        "rows" => $rows,
        "totalPages" => ceil($total / $limit)
    ]);
    exit;
}

/* =========================================================
   4️⃣ UPDATE – MODIFY RECORD
========================================================= */
if ($action === 'update') {
    $stmt = $conn->prepare(
        "UPDATE weather_records
         SET temperature=?, humidity=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "dii",
        $_POST['temp'],
        $_POST['humidity'],
        $_POST['id']
    );

    $stmt->execute();
    echo json_encode(["status" => "updated"]);
    exit;
}

/* =========================================================
   5️⃣ DELETE – REMOVE RECORD
========================================================= */
if ($action === 'delete') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM weather_records WHERE id=$id");
    echo json_encode(["status" => "deleted"]);
    exit;
}

/* =========================================================
   6️⃣ CHART DATA – MULTI CITY (CITY = LINE)
========================================================= */
if ($action === 'chart') {
    $res = $conn->query(
        "SELECT city, recorded_at, temperature
         FROM weather_records
         ORDER BY recorded_at"
    );

    $cities = [];

    while ($r = $res->fetch_assoc()) {
        $cities[$r['city']][] = [
            "time" => $r['recorded_at'],
            "temp" => $r['temperature']
        ];
    }

    echo json_encode($cities);
    exit;
}

/* =========================================================
   7️⃣ LEADERBOARD – HIGHEST TEMPERATURE
========================================================= */
if ($action === 'leaderboard') {
    $res = $conn->query(
        "SELECT city, MAX(temperature) AS max_temp
         FROM weather_records
         GROUP BY city
         ORDER BY max_temp DESC
         LIMIT 5"
    );

    $data = [];
    while ($r = $res->fetch_assoc()) {
        $data[] = $r;
    }

    echo json_encode($data);
    exit;
}

// api.php
if ($action === 'cities') {
    $res = $conn->query("SELECT DISTINCT city FROM weather_records");
    $cities = [];
    while ($r = $res->fetch_assoc()) {
        $cities[] = $r['city'];
    }
    echo json_encode($cities);
    exit;
}


/* =========================================================
   FALLBACK
========================================================= */
echo json_encode(["error" => "Invalid action"]);
