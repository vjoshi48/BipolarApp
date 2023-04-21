<?php

require('config.php');
session_start(); // Start the session

$user_id = $_SESSION['user_id'];

$data_date = $_POST['data_date'];

$sql1_1 = "CREATE VIEW hypomanic AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = $user_id AND Log_date = '$data_date'
        AND (Symptom_type = 'Manic' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight')";

$sql1_2 = "SELECT 
  CASE 
    WHEN COUNT(*) > 0 THEN 1 
    ELSE 0 
  END AS result 
FROM 
  hypomanic 
WHERE 
  ((('Irritability') IN (SELECT symptom_name FROM hypomanic)) OR ('Euphoria/elation' IN (SELECT symptom_name FROM hypomanic))) 
  AND 
  (('High energy' IN (SELECT symptom_name FROM hypomanic)) OR ('Increase in goal-directed activity/psychomotor agitation' IN (SELECT symptom_name FROM hypomanic)))
  AND (
	(('Euphoria/elation' IN (SELECT symptom_name FROM hypomanic)) AND ((SELECT COUNT(*) FROM hypomanic WHERE symptom_name NOT IN ('Euphoria/elation', 'Irritability', 'High Energy') AND Symptom_type = 'Manic')>=3))
	OR
    (('Irritability' IN (SELECT symptom_name FROM hypomanic)) AND ((SELECT COUNT(*) FROM hypomanic WHERE symptom_name NOT IN ('Euphoria/elation', 'Irritability', 'High Energy') AND Symptom_type = 'Manic')>=4))
  )
  AND
  (('Impairment of function' NOT IN (SELECT symptom_name FROM hypomanic)) AND ('Hospitalization/Psychosis' NOT IN (SELECT symptom_name FROM hypomanic)))
  AND ('Drugs used that may be causing symptoms' NOT IN (SELECT symptom_name FROM hypomanic))";

$sql1_3 = "DROP VIEW hypomanic";

$con->query($sql1_1);
$res1_2 = mysqli_query($con, $sql1_2);
$row1_2 = mysqli_fetch_assoc($res1_2);


if($row1_2['result'] == 1){
	echo "You had a hypomanic episode on this day<br><br>";
	
} else {
	echo "You did not have a hypomanic episode on this day<br><br>";
}

$con->query($sql1_3);




$sql2_1 = "CREATE VIEW manic AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = $user_id AND Log_date = '$data_date'
        AND (Symptom_type = 'Manic' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight')";

$sql2_2 = "SELECT 
  CASE 
    WHEN COUNT(*) > 0 THEN 1 
    ELSE 0 
  END AS result 
FROM 
  manic 
WHERE 
  (((('Irritability') IN (SELECT symptom_name FROM manic)) OR ('Euphoria/elation' IN (SELECT symptom_name FROM manic))) 
  AND 
  (('High energy' IN (SELECT symptom_name FROM manic)) OR ('Increase in goal-directed activity/psychomotor agitation' IN (SELECT symptom_name FROM manic)))
  AND (
	(('Euphoria/elation' IN (SELECT symptom_name FROM manic)) AND ((SELECT COUNT(*) FROM manic WHERE symptom_name NOT IN ('Euphoria/elation', 'Irritability', 'High Energy', 'Hospitalization/Psychosis') AND Symptom_type = 'Manic')>=3))
	OR
    (('Irritability' IN (SELECT symptom_name FROM manic)) AND ((SELECT COUNT(*) FROM manic WHERE symptom_name NOT IN ('Euphoria/elation', 'Irritability', 'High Energy', 'Hospitalization/Psychosis') AND Symptom_type = 'Manic')>=4))
  )
  AND
  (('Impairment of function' IN (SELECT symptom_name FROM manic)))
  AND ('Drugs used that may be causing symptoms' NOT IN (SELECT symptom_name FROM manic)))
  OR
  ('Hospitalization/Psychosis' IN (SELECT symptom_name FROM manic))";

$sql2_3 = "DROP VIEW manic";

$con->query($sql2_1);
$res2_2 = mysqli_query($con, $sql2_2);
$row2_2 = mysqli_fetch_assoc($res2_2);


if($row2_2['result'] == 1){
	echo "You had a manic episode on this day<br><br>";
	
} else {
	echo "You did not have a manic episode on this day<br><br>";
}

$con->query($sql2_3);



$sql3_1 = "CREATE VIEW depressed AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = $user_id AND Log_date = '$data_date'
        AND (Symptom_type = 'Depressive' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight')";

$sql3_2 = "SELECT 
  CASE 
    WHEN COUNT(*) > 0 THEN 1 
    ELSE 0 
  END AS result 
FROM 
  depressed 
WHERE 
  (((('Depressed mood') IN (SELECT symptom_name FROM depressed)) OR ('Loss of interest or pleasure' IN (SELECT symptom_name FROM depressed)))
  AND ((((SELECT COUNT(*) FROM depressed WHERE Symptom_type = 'Depressive')>=5)))
  AND (('Impairment of function' IN (SELECT symptom_name FROM depressed)))
  AND ('Drugs used that may be causing symptoms' NOT IN (SELECT symptom_name FROM depressed)))";

$sql3_3 = "DROP VIEW depressed";

$con->query($sql3_1);
$res3_2 = mysqli_query($con, $sql3_2);
$row3_2 = mysqli_fetch_assoc($res3_2);


if($row3_2['result'] == 1){
	echo "You had a depressive episode on this day<br><br>";
	
} else {
	echo "You did not have a depressive episode on this day<br><br>";
}

$con->query($sql3_3);





$sql4_1 = "CREATE VIEW mixed AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = $user_id AND Log_date = '$data_date'
        AND (Symptom_type = 'Depressive' OR Symptom_type = 'Other') 
        AND (Users_Have_Symptoms.Symptom_name != 'Weight')
        AND (Users_Have_Symptoms.Symptom_name NOT IN 
			('Diminished ability to concentrate', 'Weight loss/gain/appetite change', 'Insomnia/hypersomnia'))";

$sql4_2 = "SELECT 
  CASE 
    WHEN COUNT(*) > 0 THEN 1 
    ELSE 0 
  END AS result 
FROM 
  mixed 
WHERE
  (SELECT COUNT(*) FROM mixed WHERE Symptom_type = 'Depressive')>=3";

$sql4_3 = "DROP VIEW mixed";

$con->query($sql4_1);
$res4_2 = mysqli_query($con, $sql4_2);
$row4_2 = mysqli_fetch_assoc($res4_2);


if($row4_2['result'] == 1){
	echo "You had a mixed episode on this day<br><br>";
	
} else {
	echo "You did not have a mixed episode on this day<br><br>";
}

$con->query($sql4_3);

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