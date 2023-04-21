<?php
require('config.php');

// Select data from the databases
$data_date = $_POST['data_date'];

$sql = 
"SELECT Log_Date, COUNT(Symptom_Name) AS Symptoms
FROM Users_Have_Symptoms
WHERE User_ID = 1
AND Log_Date >= '$data_date'
AND Log_Date <= DATE_ADD('$data_date', INTERVAL 13 DAY)
GROUP BY Log_Date ORDER BY Log_date;
";
$result = mysqli_query($con, $sql);

// Print the SQL query

// Fetch the data
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

file_put_contents('data.json', json_encode($data));

// Call the Python script with the data as a command-line argument
$command = "python load_model.py data.json";
$output = shell_exec($command);

// Check if the output is not null
if ($output !== null) {
    // Decode the output from the Python script
    $prediction = json_decode($output);
} else {
    // Handle the case where the Python script failed to execute
    echo "Failed to execute Python script";
}

echo "Below are the probabilities that you are in a prodromal phase for the given episode types:<br>";

// Check if the output was successfully decoded and is an array
if (is_array($prediction)) {
    // Loop through the array and echo the values to the screen
    foreach ($prediction as $value) {
        foreach ($value as $key => $val) {
            echo $key . ': ' . $val . '<br>';
        }
    }
} else {
    // Handle the case where the output was not successfully decoded
    echo "Failed to decode output";
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>My Page</title>
</head>
<body>
	<p><a href="welcome.html">Go back to welcome page</a></p>
</body>
</html>