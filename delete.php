<?php
require('config.php');
// check if the form has been submitted
if (isset($_POST['submit'])) {
  // retrieve the log date from the form
  $log_date = $_POST['log_date'];
  
  // check for errors
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
  }
  
  // delete all data from the Users_Have_Symptoms table for the specified log date
  $sql = "DELETE FROM Users_Have_Symptoms WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // delete all data from the Users_Have_WarningSigns table for the specified log date
  $sql = "DELETE FROM Users_Have_WarningSigns WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // delete all data from the Users_EngageIn_Interventions table for the specified log date
  $sql = "DELETE FROM Users_EngageIn_Interventions WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // delete all data from the Users_Take_Medications table for the specified log date
  $sql = "DELETE FROM Users_Take_Medications WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // delete all data from the Users_Use_CopingSkills table for the specified log date
  $sql = "DELETE FROM Users_Use_CopingSkills WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // delete all data from the Medications_Have_SideEffects table for the specified log date
  $sql = "DELETE FROM Medications_Have_SideEffects WHERE Log_Date='$log_date'";
  $con->query($sql);
  
  // close the database conection
  $con->close();
  
  // redirect back to the delete.html form
  header("Location: delete.html");
  exit();
}
?>
