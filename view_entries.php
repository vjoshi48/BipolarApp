<?php

require('config.php');

// Start the session
session_start();

// Get user_id and date
$user_id = $_SESSION['user_id'];

$data_date = $_POST['data_date'];

echo"<br>";
echo"Symptoms";
$sql1 = "SELECT * FROM Users_Have_Symptoms WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res1 = mysqli_query($con, $sql1) or die("Bad Query: $sql1");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Symptom Name</td><td>Log date</td><td>Severity</td><tr>";

while($row = mysqli_fetch_assoc($res1)) {
	echo"<tr><td>{$row['User_ID']}</td><td>{$row['Symptom_name']}</td><td>{$row['Log_Date']}</td><td>{$row['Severity']}</td><tr>";
}
echo"</table>";

echo"<br>";


echo"Warning Signs";
$sql2 = "SELECT * FROM Users_Have_WarningSigns JOIN Warning_Signs ON Warning_Signs.WarningSign_ID = Users_Have_WarningSigns.WarningSign_ID WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res2 = mysqli_query($con, $sql2) or die("Bad Query: $sql2");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Warning Sign Name</td><td>Severity</td><td>Log_Date</td><tr>";

while($row = mysqli_fetch_assoc($res2)) {
	echo"<tr><td>{$row['User_ID']}</td><td>{$row['WarningSign_Name']}</td><td>{$row['Severity']}</td><td>{$row['Log_Date']}</td><tr>";
}
echo"</table>";

echo"<br>";


echo"Interventions used";
$sql3 = "SELECT * FROM Users_EngageIn_Interventions JOIN Interventions ON Interventions.Intervention_ID = Users_EngageIn_Interventions.Intervention_ID WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res3 = mysqli_query($con, $sql3) or die("Bad Query: $sql3");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Intervention Name</td><td>Quantity</td><td>Unit</td><td>Log_Date</td><tr>";

while($row = mysqli_fetch_assoc($res3)) {
	echo"<tr><td>{$row['User_ID']}</td><td>{$row['Intervention_Name']}</td><td>{$row['Quantity']}</td><td>{$row['Unit']}</td><td>{$row['Log_Date']}</td><tr>";
}
echo"</table>";


echo"<br>";


echo"Medications";
$sql4 = "SELECT * FROM Users_Take_Medications JOIN Medications ON Medications.Medication_ID = Users_Take_Medications.Medication_ID WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res4 = mysqli_query($con, $sql4) or die("Bad Query: $sql4");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Medication Name</td><td>Quantity</td><td>Unit</td><td>Psychiatrist</td><td>Log_Date</td><tr>";

while($row = mysqli_fetch_assoc($res4)) {
	echo"<tr><td>{$row['User_ID']}</td><td>{$row['Medication_Name']}</td><td>{$row['Quantity']}</td><td>{$row['Unit']}</td><td>{$row['Psychiatrist']}</td><td>{$row['Log_Date']}</td><tr>";
}
echo"</table>";



echo"<br>";


echo "Coping Skills used";
$sql5 = "SELECT Users_Use_CopingSkills.User_ID, Coping_Skills.Skill_Name, Users_Use_CopingSkills.Log_Date FROM Users_Use_CopingSkills JOIN Coping_Skills ON Coping_Skills.Skill_ID = Users_Use_CopingSkills.Skill_ID WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res5 = mysqli_query($con, $sql5) or die("Bad Query: $sql5");
  echo "<table border='1'>";
  echo "<tr><td>User ID</td><td>Skill Name</td><td>Log_Date</td><tr>";

  while ($row = mysqli_fetch_assoc($res5)) {
    echo "<tr><td>{$row['User_ID']}</td><td>{$row['Skill_Name']}</td><td>{$row['Log_Date']}</td><tr>";
  }

  echo "</table>";



echo"<br>";


echo"Side Effects from Medications";
$sql6 = "SELECT * FROM Medications_Have_SideEffects JOIN Medications ON Medications.Medication_ID = Medications_Have_SideEffects.Medication_ID JOIN Side_effects on Side_effects.SideEffect_ID = Medications_Have_SideEffects.SideEffect_ID WHERE User_ID = $user_id AND Log_date = '$data_date'";
$res6 = mysqli_query($con, $sql6) or die("Bad Query: $sql6");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Medication Name</td><td>Side Effect Name</td><td>Severity</td><td>Log_Date</td><tr>";

while($row = mysqli_fetch_assoc($res6)) {
	echo"<tr><td>{$row['User_ID']}</td><td>{$row['Medication_Name']}</td><td>{$row['SideEffect_Name']}</td><td>{$row['Severity']}</td><td>{$row['Log_Date']}</td><tr>";
}
echo"</table>";

echo"<br>";


echo"Coping Skills that can help with symptoms";
$sql7 = "SELECT Users_Have_Symptoms.User_ID as ID, Users_Have_Symptoms.Symptom_name as Symptom, Coping_Skills.Skill_Name as Skill
FROM Users_Have_Symptoms 
JOIN CopingSkills_Manage_Symptoms ON CopingSkills_Manage_Symptoms.user_ID = $user_id 
AND CopingSkills_Manage_Symptoms.Symptom_name = Users_Have_Symptoms.Symptom_name
JOIN Coping_Skills ON CopingSkills_Manage_Symptoms.Skill_ID = Coping_Skills.Skill_ID
WHERE Users_Have_Symptoms.user_ID = $user_id AND log_date = '$data_date'";

$res7 = mysqli_query($con, $sql7) or die("Bad Query: $sql7");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Symptom Name</td><td>Skill name</td><tr>";

while($row = mysqli_fetch_assoc($res7)) {
	echo"<tr><td>{$row['ID']}</td><td>{$row['Symptom']}</td><td>{$row['Skill']}</td><tr>";
}
echo"</table>";

echo"<br>";


echo"Interventions that can help with symptoms";
$sql8 = "SELECT Users_Have_Symptoms.User_ID as ID, Users_Have_Symptoms.Symptom_name as Symptom, Interventions.Intervention_name as Intervention
FROM Users_Have_Symptoms
JOIN Interventions_Manage_Symptoms ON Interventions_Manage_Symptoms.user_ID = 1 
AND Interventions_Manage_Symptoms.Symptom_name = Users_Have_Symptoms.Symptom_name
JOIN Interventions ON Interventions.Intervention_ID = Interventions_Manage_Symptoms.Intervention_ID
WHERE Users_Have_Symptoms.user_ID = $user_id AND log_date = '$data_date'";


$res8 = mysqli_query($con, $sql8) or die("Bad Query: $sql8");

echo"<table border='1'>";
echo"<tr><td>User ID</td><td>Symptom Name</td><td>Intervention Name</td><tr>";

while($row = mysqli_fetch_assoc($res8)) {
	echo"<tr><td>{$row['ID']}</td><td>{$row['Symptom']}</td><td>{$row['Intervention']}</td><tr>";
}
echo"</table>";

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