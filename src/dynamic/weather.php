<?php
$servername = "db";
$username = "user";
$password = "password";
$dbname = "weather_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT city, temperature, condition_text, last_updated FROM weather";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Weather Data</title>
    <link rel="stylesheet" href="/static/style.css">
</head>
<body>
    <h1>Текущая погода</h1>
    <p>Эти данные загружаются динамически из базы данных.</p>
    <a href="/static/index.html">На статическую страницу</a>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border=\"1\"><tr><th>City</th><th>Temperature (°C)</th><th>Condition</th><th>Last Updated</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row["city"]. "</td><td>" . $row["temperature"]. "</td><td>" . $row["condition_text"]. "</td><td>" . $row["last_updated"]. "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>
</body>
</html>
