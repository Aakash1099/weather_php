<!DOCTYPE html>
<html>
<head>
<title>Weather Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
tr.clickable:hover { background:#f1f1f1; cursor:pointer; }
</style>
</head>

<body class="bg-light">
<div class="container mt-4">

<h3 class="text-center mb-4">🌦 Weather Dashboard</h3>

<!-- SEARCH -->
<div class="card p-3 mb-3">
    <input id="cityInput" class="form-control mb-2" placeholder="Enter city name">
    <button class="btn btn-primary" onclick="fetchWeather()">Get Weather</button>
</div>

<!-- WEATHER RESULT -->
<div id="weatherBox" class="card p-3 mb-3 d-none">
    <h5 id="wCity"></h5>
    <p>🌡 <span id="wTemp"></span> °C</p>
    <p>💧 <span id="wHumidity"></span> %</p>
    <p>☁ <span id="wCondition"></span></p>
    <button id="saveBtn" class="btn btn-success" onclick="saveWeather()" disabled>💾 Save</button>
</div>

<!-- RECORDS + LEADERBOARD -->
<div class="row">

<div class="col-md-8">
<div class="card p-3 mb-3">
<h5>📋 Saved Records</h5>

<table class="table table-bordered">
<thead>
<tr>
<th>City</th><th>Temp</th><th>Humidity</th><th>Actions</th>
</tr>
</thead>
<tbody id="records"></tbody>
</table>

<div id="pagination"></div>
</div>
</div>

<div class="col-md-4">
<div class="card p-3 mb-3">
<h5>🏆 Highest Temperature</h5>
<ul id="leaderboard" class="list-group"></ul>
</div>
</div>

</div>

<!-- CHART -->
<div class="card p-3 mb-3">
<h5>📈 Live Temperature (API • DB Cities)</h5>
<div style="height:260px">
<canvas id="chart"></canvas>
</div>
</div>

</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">✏ Edit Weather Record</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" id="editId">

<label>City</label>
<input id="editCity" class="form-control mb-2" disabled>

<label>Temperature (°C)</label>
<input id="editTemp" type="number" class="form-control mb-2">

<label>Humidity (%)</label>
<input id="editHumidity" type="number" class="form-control">
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" onclick="saveEdit()">Update</button>
</div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let weatherData = null;
let chart;
let cityDatasets = {};
let cityColors = {};
let currentPage = 1;
let highlightedCity = null;

/* COLOR PER CITY */
function getCityColor(city) {
    if (!cityColors[city]) {
        const hue = Math.floor(Math.random() * 360);
        cityColors[city] = `hsl(${hue},70%,50%)`;
    }
    return cityColors[city];
}

/* INIT CHART */
function initChart() {
    if (chart) chart.destroy();

    chart = new Chart(document.getElementById("chart"), {
        type: "line",
        data: { labels: [], datasets: [] },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    cityDatasets = {};
}

/* FETCH WEATHER */
function fetchWeather() {
    const city = cityInput.value.trim();
    if (!city) return;

    fetch(`api.php?action=fetch&city=${city}`)
    .then(r => r.json())
    .then(d => {
        if (d.error) return alert("City not found");

        weatherData = d;
        weatherBox.classList.remove("d-none");

        wCity.innerText = d.location.name;
        wTemp.innerText = d.current.temp_c;
        wHumidity.innerText = d.current.humidity;
        wCondition.innerText = d.current.condition.text;

        saveBtn.disabled = false;
    });
}

/* SAVE */
function saveWeather() {
    const fd = new FormData();
    fd.append("city", weatherData.location.name);
    fd.append("temp", weatherData.current.temp_c);
    fd.append("humidity", weatherData.current.humidity);
    fd.append("condition", weatherData.current.condition.text);

    fetch("api.php?action=save", { method:"POST", body:fd })
    .then(() => {
        saveBtn.disabled = true;
        loadRecords(currentPage);
        loadLeaderboard();
    });
}

/* LIVE CHART */
function updateLiveChart() {
    fetch("api.php?action=cities")
    .then(r => r.json())
    .then(cities => {

        const time = new Date().toLocaleTimeString();
        chart.data.labels.push(time);

        cities.forEach(city => {
            fetch(`api.php?action=fetch&city=${city}`)
            .then(r => r.json())
            .then(d => {
                if (d.error) return;

                if (!cityDatasets[city]) {
                    const color = getCityColor(city);
                    const ds = {
                        label: city,
                        data: [],
                        borderColor: highlightedCity && highlightedCity !== city ? "#ccc" : color,
                        backgroundColor: color,
                        borderWidth: highlightedCity === city ? 4 : 2,
                        tension: 0.3
                    };
                    cityDatasets[city] = ds;
                    chart.data.datasets.push(ds);
                }

                cityDatasets[city].data.push(d.current.temp_c);

                if (cityDatasets[city].data.length > 10)
                    cityDatasets[city].data.shift();

                chart.update();
            });
        });

        if (chart.data.labels.length > 10)
            chart.data.labels.shift();
    });
}

/* ROW CLICK */
function highlightCity(city) {
    highlightedCity = city;
    initChart();
    updateLiveChart();
}

/* DB UI */
function loadRecords(page=1){
    currentPage = page;
    fetch(`api.php?action=records&page=${page}`)
    .then(r=>r.json())
    .then(d=>{
        records.innerHTML = d.rows.map(r=>`
        <tr class="clickable" onclick="highlightCity('${r.city}')">
            <td>${r.city}</td>
            <td>${r.temperature}</td>
            <td>${r.humidity}</td>
            <td>
                <button class="btn btn-sm btn-warning me-1"
                    onclick="event.stopPropagation();
                    openEditModal(${r.id},'${r.city}',${r.temperature},${r.humidity})">✏</button>
                <button class="btn btn-sm btn-danger"
                    onclick="event.stopPropagation(); deleteRecord(${r.id})">❌</button>
            </td>
        </tr>`).join("");

        pagination.innerHTML = [...Array(d.totalPages).keys()]
        .map(i=>`<button class="btn btn-sm btn-outline-primary me-1"
        onclick="loadRecords(${i+1})">${i+1}</button>`).join("");
    });
}

function deleteRecord(id){
    fetch("api.php?action=delete",{method:"POST",body:new URLSearchParams({id})})
    .then(()=>{ loadRecords(currentPage); loadLeaderboard(); });
}

/* EDIT MODAL */
let editModal = new bootstrap.Modal(document.getElementById("editModal"));

function openEditModal(id, city, t, h) {
    editId.value = id;
    editCity.value = city;
    editTemp.value = t;
    editHumidity.value = h;
    editModal.show();
}

function saveEdit() {
    fetch("api.php?action=update", {
        method:"POST",
        body:new URLSearchParams({
            id:editId.value,
            temp:editTemp.value,
            humidity:editHumidity.value
        })
    }).then(()=>{
        editModal.hide();
        loadRecords(currentPage);
        loadLeaderboard();
    });
}

function loadLeaderboard(){
    fetch("api.php?action=leaderboard")
    .then(r=>r.json())
    .then(d=>{
        leaderboard.innerHTML=d.map(r=>`
        <li class="list-group-item">${r.city} 🔥 ${r.max_temp} °C</li>`).join("");
    });
}

/* INIT */
initChart();
loadRecords();
loadLeaderboard();
setInterval(updateLiveChart, 10000);
</script>

</body>
</html>
