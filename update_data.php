<?php
// Connect to the database
require 'config.php';


// define a custom error handling function
//function handle_sql_errors($errno, $errstr, $errfile, $errline) {
    // ignore errors that are not related to SQL commands
    //if (!preg_match('/^(SQLSTATE|PDOException)/', $errstr)) {
     // return false;
    //}
    
    // log the error or perform other actions as necessary
    //error_log($errstr, 0);
    
    // return true to prevent the error from being displayed to the user
    //return true;
  //}
  
  // set the global error handler to the custom function
//  set_error_handler("handle_sql_errors");
  

// Start the session
session_start();

// Get user_id and date
$user_id = $_SESSION['user_id'];

if(isset($_POST['log_date']) && !empty($_POST['log_date']))
{
    $log_date = $_POST['log_date'];


    if(isset($_POST['mood']) && !empty($_POST['mood']))
    {
        $severity = $_POST['mood'];
        $symptom_name = "Euphoria/Elation";
        $opposite_symptom_name = "Depressed mood";

        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $opposite_symptom_name, $log_date);
        $query->execute();
        $result1 = $query->get_result();
        if ($severity <= 6 And $severity >= 5)
        {
            if ($result->num_rows > 0) // if elevated mood is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            } else if ($result1->num_rows > 0) //if depressed mood is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $opposite_symptom_name, $log_date);
            }
            $result = $query->execute();
        }
        else {
        // If user already has a log entry for this symptom on this date
        if ($result->num_rows > 0 || $result1->num_rows > 0) {
        if ($severity >= 7) { // if user is happy
            if ($result1->num_rows > 0) { // if depressed mood is currently entered
            $query = $con->prepare("UPDATE users_have_symptoms SET severity = ?, symptom_name = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
            $query->bind_param('ssiss', $severity, $symptom_name, $user_id, $opposite_symptom_name, $log_date);
            } else if ($result->num_rows > 0) { // if elevated mood is currently entered
            $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
            $query->bind_param('siss', $severity, $user_id, $symptom_name, $log_date);
            }
        } else if ($severity <= 4) { // if user is depressed
            if ($result->num_rows > 0) { // if elevated mood is currently entered
            $query = $con->prepare("UPDATE users_have_symptoms SET severity = ?, symptom_name = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
            $query->bind_param('isiss', $severity, $opposite_symptom_name, $user_id, $symptom_name, $log_date);
            } else if ($result1->num_rows > 0) { // if depressed is currently entered
            $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
            $query->bind_param('siss', $severity, $user_id, $opposite_symptom_name, $log_date);
            }
        }

        // Execute the query
        $result = $query->execute();
        } else
        { //if no existing entry
            if ($severity >= 7)
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
                $result = $query->execute();

            } else if ($severity <= 4)
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('sssi', $user_id, $opposite_symptom_name, $log_date, $severity);
                $result = $query->execute();
            }
        }
        }

    }


    if(isset($_POST['irritability']) && !empty($_POST['irritability']))
    {
        $severity = $_POST['irritability'];
        $symptom_name = "Irritability";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity < 7)
        {
            if ($result->num_rows > 0) // if irritable mood is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if irritability is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['self-feelings']) && !empty($_POST['self-feelings']))
    {
        $severity = $_POST['self-feelings'];
        $symptom_name = "Increased self-esteem/grandiosity";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity < 7)
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['sleep-hours']) && !empty($_POST['sleep-hours']))
    {
        $severity = $_POST['sleep-hours'];
        $symptom_name = "Decreased need for sleep";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity > 5)
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['energy']) && !empty($_POST['energy']))
    {
        $severity = $_POST['energy'];
        $symptom_name = "High energy";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity < 7)
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['social-interaction']) && !empty($_POST['social-interaction']))
    {
        $severity = $_POST['social-interaction'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Increased talkativeness";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['thoughts-racing']) && !empty($_POST['thoughts-racing']))
    {
        $severity = $_POST['thoughts-racing'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Racing thoughts/Flight of ideas";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['distracted']) && !empty($_POST['distracted']))
    {
        $severity = $_POST['distracted'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Distractibility";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['goals']) && !empty($_POST['goals']))
    {
        $severity = $_POST['goals'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Increase in goal-directed activity/psychomotor agitation";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['restless']) && !empty($_POST['restless']))
    {
        $severity = $_POST['restless'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Psychomotor agitation/retardation";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['risky-behaviors']) && !empty($_POST['risky-behaviors']))
    {
        $severity = $_POST['risky-behaviors'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Excessive high-risk activities";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['drugs']) && !empty($_POST['drugs']))
    {
        $severity = $_POST['drugs'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Drugs used that may be causing symptoms";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['hospitalized']) && !empty($_POST['hospitalized']))
    {
        $severity = $_POST['hospitalized'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Hospitalization/Psychosis";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['interest']) && !empty($_POST['interest']))
    {
        $severity = $_POST['interest'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Loss of interest or pleasure";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['appetite']) && !empty($_POST['appetite']))
    {
        $severity = $_POST['appetite'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Weight loss/gain/appetite change";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['weight']) && !empty($_POST['weight']))
    {
        $symptom_name = "Weight";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();
        if (($result->num_rows >=1)) //if symptom is already entered
        {
            $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
            $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
        } else 
        {
            // Insert a new entry into the users_have_symptoms table
            $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
            $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
        }
        $result = $query->execute();
    }

    if(isset($_POST['sleeping']) && !empty($_POST['sleeping']))
    {
        $severity = $_POST['sleeping'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Insomnia/hypersomnia";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['low_energy']) && !empty($_POST['low_energy']))
    {
        $severity = $_POST['low_energy'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Fatigue";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['guilt']) && !empty($_POST['guilt']))
    {
        $severity = $_POST['guilt'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Worthlessness or guilt";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['concentration']) && !empty($_POST['concentration']))
    {
        $severity = $_POST['concentration'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Diminished ability to concentrate";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['suicidal']) && !empty($_POST['suicidal']))
    {
        $severity = $_POST['suicidal'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Suicidal Ideation";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

    if(isset($_POST['impairment']) && !empty($_POST['impairment']))
    {
        $severity = $_POST['impairment'];
        if ($severity == 'yes')
        {
            $severity = 1;
        } else if ($severity == 'no')
        {
            $severity = 0;
        } else
        {
            echo "error error error severity is not 1 or 0";
        }
        $symptom_name = "Impairment of function";
        // Check if the user already has a log entry for this symptom on this date
        $query = $con->prepare("SELECT * FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $symptom_name, $log_date);
        $query->execute();
        $result = $query->get_result();

        if ($severity == 0) // deleting symptom if no longer meets criteria
        {
            if ($result->num_rows > 0) // if symptom is entered
            {
                $query = $con->prepare("DELETE FROM users_have_symptoms WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('iss', $user_id, $symptom_name, $log_date);
            }
        } else {
            if (($result->num_rows >=1)) //if symptom is already entered
            {
                $query = $con->prepare("UPDATE users_have_symptoms SET severity = ? WHERE user_id = ? AND symptom_name = ? AND log_date = ?");
                $query->bind_param('ssis', $severity, $user_id, $symptom_name, $log_date);
            } else 
            {
                // Insert a new entry into the users_have_symptoms table
                $query = $con->prepare("INSERT INTO users_have_symptoms (user_id, symptom_name, log_date, severity) VALUES (?, ?, ?, ?)");
                $query->bind_param('issi', $user_id, $symptom_name, $log_date, $severity);
            }
        }
        $result = $query->execute();

    }

if(isset($_POST['warning_sign']) && !empty($_POST['warning_sign']))
{
    // prepare the SQL query to check if the warning sign exists in the database
    $warning_sign = $_POST['warning_sign'];

    $episode = $_POST['episode'];

    $severity = $_POST['severity'];

    $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

    // execute the SQL query
    $result = mysqli_query($con, $sql);

    // check if any rows were returned
    if (mysqli_num_rows($result) > 0) {
        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];

        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();

        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        // the warning sign does not exist in the database
        $query = $con->prepare("INSERT INTO Warning_Signs (WarningSign_Name, WarningSign_Type) VALUES (?, ?)");
        $query->bind_param('ss', $warning_sign, $episode);
        $result = $query->execute();

        $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

        // execute the SQL query
        $result = mysqli_query($con, $sql);

        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];
        
        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();
        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
}

if(isset($_POST['warning_sign1']) && !empty($_POST['warning_sign1']))
{
    // prepare the SQL query to check if the warning sign exists in the database
    $warning_sign = $_POST['warning_sign1'];

    $episode = $_POST['episode1'];

    $severity = $_POST['severity1'];

    $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

    // execute the SQL query
    $result = mysqli_query($con, $sql);

    // check if any rows were returned
    if (mysqli_num_rows($result) > 0) {
        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];

        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();

        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        // the warning sign does not exist in the database
        $query = $con->prepare("INSERT INTO Warning_Signs (WarningSign_Name, WarningSign_Type) VALUES (?, ?)");
        $query->bind_param('ss', $warning_sign, $episode);
        $result = $query->execute();

        $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

        // execute the SQL query
        $result = mysqli_query($con, $sql);

        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];
        
        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();
        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
}


if(isset($_POST['warning_sign2']) && !empty($_POST['warning_sign2']))
{
    // prepare the SQL query to check if the warning sign exists in the database
    $warning_sign = $_POST['warning_sign2'];

    $episode = $_POST['episode2'];

    $severity = $_POST['severity2'];

    $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

    // execute the SQL query
    $result = mysqli_query($con, $sql);

    // check if any rows were returned
    if (mysqli_num_rows($result) > 0) {
        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];

        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();

        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        // the warning sign does not exist in the database
        $query = $con->prepare("INSERT INTO Warning_Signs (WarningSign_Name, WarningSign_Type) VALUES (?, ?)");
        $query->bind_param('ss', $warning_sign, $episode);
        $result = $query->execute();

        $sql = "SELECT * FROM Warning_Signs WHERE WarningSign_Name = '$warning_sign' AND WarningSign_Type = '$episode'";

        // execute the SQL query
        $result = mysqli_query($con, $sql);

        // the warning sign exists in the database
        $row = mysqli_fetch_assoc($result);
        $warning_sign_id = $row['WarningSign_ID'];
        
        $query = $con->prepare("DELETE FROM Users_Have_WarningSigns WHERE user_id = ? AND warningsign_ID = ? AND log_date = ?");
        $query->bind_param('iss', $user_id, $warning_sign_id, $log_date);
        $result = $query->execute();
        // prepare the SQL query to insert the user ID and warning sign ID into the Users_have_warningsign table
        $sql = "INSERT INTO Users_have_warningsigns (user_id, WarningSign_ID, log_date, severity) VALUES ('$user_id', '$warning_sign_id', '$log_date', '$severity')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
}

if(isset($_POST['coping_skill']) && !empty($_POST['coping_skill'])){
    // prepare the SQL query to check if the coping skill exists in the database
    $coping_skill = $_POST['coping_skill'];
    $symptoms = $_POST['symptoms_dropdown'];

    echo $coping_skill;

    echo $symptoms;

    $sql = "SELECT * FROM coping_skills WHERE skill_name = '$coping_skill'";
    
    // execute the SQL query
    $result = mysqli_query($con, $sql);
    
    // check if any rows were returned

    if (mysqli_num_rows($result) > 0) {
        // the coping skill exists in the database
        $row = mysqli_fetch_assoc($result);
        $skill_id = $row['Skill_ID'];

        if(empty($skill_id)){
            echo "Error: Skill ID not set";
            exit;
        }

        $sql = "SELECT * FROM users_use_copingskills WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND log_date = '$log_date'";
    
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned
    
        if (mysqli_num_rows($result) > 0) {

        $sql = "DELETE FROM users_use_copingskills WHERE user_id = '$user_id' AND log_date = '$log_date'";
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record deleted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        }
        // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
        $sql = "INSERT INTO users_use_copingskills (user_id, skill_id, log_date) VALUES ('$user_id', '$skill_id', '$log_date')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        // the coping skill does not exist in the database
        $query = $con->prepare("INSERT INTO coping_skills (skill_name) VALUES (?)");
        $query->bind_param('s', $coping_skill);
        $result = $query->execute();

        if ($result) {
            // the coping skill was successfully inserted into the database
            $sql = "SELECT * FROM coping_skills WHERE skill_name = '$coping_skill'";
            
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            $skill_id = $row['Skill_ID'];

            
            $sql = "SELECT * FROM users_use_copingskills WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND log_date = '$log_date'";
    
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            
            // check if any rows were returned
        
            if (mysqli_num_rows($result) > 0) {
    
            $sql = "DELETE FROM users_use_copingskills WHERE user_id = '$user_id' AND log_date = '$log_date'";
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record deleted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
            
            // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
            $sql = "INSERT INTO users_use_copingskills (user_id, skill_id, log_date) VALUES ('$user_id', '$skill_id', '$log_date')";
            if (mysqli_query($con, $sql)) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }

    $sql = "SELECT * FROM copingskills_manage_symptoms WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND Symptom_name = '$symptoms'";

    // execute the SQL query
    $result = mysqli_query($con, $sql);

    // check if any rows were returned

    if (mysqli_num_rows($result) <= 0)
    {
    $sql = "INSERT INTO copingskills_manage_symptoms (user_id, skill_id, symptom_name) VALUES ('$user_id', '$skill_id', '$symptoms')";
    // execute the SQL query
    if (mysqli_query($con, $sql)) {
        echo "Record inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
    }
}

if(isset($_POST['coping_skill1']) && !empty($_POST['coping_skill1'])){
    // prepare the SQL query to check if the coping skill exists in the database
    $coping_skill = $_POST['coping_skill1'];
    $symptoms = $_POST['symptoms_dropdown1'];

    echo $coping_skill;

    echo $symptoms;

    $sql = "SELECT * FROM coping_skills WHERE skill_name = '$coping_skill'";
    
    // execute the SQL query
    $result = mysqli_query($con, $sql);
    
    // check if any rows were returned

    if (mysqli_num_rows($result) > 0) {
        // the coping skill exists in the database
        $row = mysqli_fetch_assoc($result);
        $skill_id = $row['Skill_ID'];

        if(empty($skill_id)){
            echo "Error: Skill ID not set";
            exit;
        }

        $sql = "SELECT * FROM users_use_copingskills WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND log_date = '$log_date'";
    
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned
    
        if (mysqli_num_rows($result) > 0) {

        //$sql = "DELETE FROM users_use_copingskills WHERE user_id = '$user_id' AND log_date = '$log_date'";
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record deleted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }
        // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
        $sql = "INSERT INTO users_use_copingskills (user_id, skill_id, log_date) VALUES ('$user_id', '$skill_id', '$log_date')";
        
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    } else {
        // the coping skill does not exist in the database
        $query = $con->prepare("INSERT INTO coping_skills (skill_name) VALUES (?)");
        $query->bind_param('s', $coping_skill);
        $result = $query->execute();

        if ($result) {
            // the coping skill was successfully inserted into the database
            $sql = "SELECT * FROM coping_skills WHERE skill_name = '$coping_skill'";
            
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            $skill_id = $row['skill_id'];

            
            $sql = "SELECT * FROM users_use_copingskills WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND log_date = '$log_date'";
    
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            
            // check if any rows were returned
        
            if (mysqli_num_rows($result) > 0) {
    
            //$sql = "DELETE FROM users_use_copingskills WHERE user_id = '$user_id' AND log_date = '$log_date'";
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record deleted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
            
            // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
            $sql = "INSERT INTO users_use_copingskills (user_id, skill_id, log_date) VALUES ('$user_id', '$skill_id', '$log_date')";
            if (mysqli_query($con, $sql)) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
    }

    $sql = "SELECT * FROM copingskills_manage_symptoms WHERE user_id = '$user_id' AND skill_id = '$skill_id' AND Symptom_name = '$symptoms'";

    // execute the SQL query
    $result = mysqli_query($con, $sql);

    // check if any rows were returned

    if (mysqli_num_rows($result) <= 0)
    {
    $sql = "INSERT INTO copingskills_manage_symptoms (user_id, skill_id, symptom_name) VALUES ('$user_id', '$skill_id', '$symptoms')";
    // execute the SQL query
    if (mysqli_query($con, $sql)) {
        echo "Record inserted successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
    }
}

if(isset($_POST['intervention-name']) && !empty($_POST['intervention-name']))
{
        // prepare the SQL query to check if the coping skill exists in the database
        $intervention = $_POST['intervention-name'];
        $quantity = $_POST['intervention-dosage'];
        $unit = $_POST['unit'];
        $symptoms = $_POST['symptoms'];
    
        $sql = "SELECT * FROM interventions WHERE intervention_name = '$intervention'";
        
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned
    
        if (mysqli_num_rows($result) > 0) {
            // the intervention exists in the database
            $row = mysqli_fetch_assoc($result);
            $intervention_id = $row['Intervention_ID'];

            $sql = "SELECT * FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
    
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            
            // check if any rows were returned
        
            if (mysqli_num_rows($result) > 0) {
    
            $sql = "DELETE FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record deleted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
            }
            
            // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
            $sql = "INSERT INTO Users_EngageIn_Interventions (user_id, intervention_id, quantity, unit, log_date) VALUES ('$user_id', '$intervention_id', '$quantity','$unit','$log_date')";
            
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        } else {
            // the intervention does not exist in the database
            $query = $con->prepare("INSERT INTO Interventions (skill_name) VALUES (?)");
            $query->bind_param('s', $intervention);
            $result = $query->execute();
    
            if ($result) {
                // the intervention was successfully inserted into the database
                $sql = "SELECT * FROM interventions WHERE intervention_name = '$intervention'";
                
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($result);
                $intervention_id = $row['skill_id'];

                $sql = "SELECT * FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
    
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                
                // check if any rows were returned
            
                if (mysqli_num_rows($result) > 0) {
        
                $sql = "DELETE FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
                // execute the SQL query
                if (mysqli_query($con, $sql)) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
                }
                
                // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
                $sql = "INSERT INTO Users_EngageIn_Interventions (user_id, intervention_id, quantity, unit, log_date) VALUES ('$user_id', '$intervention_id', '$quantity','$unit','$log_date')";
                if (mysqli_query($con, $sql)) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    
        $sql = "SELECT * FROM interventions_manage_symptoms WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND Symptom_name = '$symptoms'";
    
        // execute the SQL query
        $result = mysqli_query($con, $sql);
    
        // check if any rows were returned
    
        if (mysqli_num_rows($result) <= 0)
        {
        $sql = "INSERT INTO interventions_manage_symptoms (user_id, intervention_ID, symptom_name) VALUES ('$user_id', '$intervention_id', '$symptoms')";
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        }
}

if(isset($_POST['intervention-name1']) && !empty($_POST['intervention-name1']))
{
        // prepare the SQL query to check if the coping skill exists in the database
        $intervention = $_POST['intervention-name1'];
        $quantity = $_POST['intervention-dosage1'];
        $unit = $_POST['unit1'];
        $symptoms = $_POST['symptoms1'];
    
        $sql = "SELECT * FROM interventions WHERE intervention_name = '$intervention'";
        
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned
    
        if (mysqli_num_rows($result) > 0) {
            // the intervention exists in the database
            $row = mysqli_fetch_assoc($result);
            $intervention_id = $row['Intervention_ID'];

            $sql = "SELECT * FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
    
            // execute the SQL query
            $result = mysqli_query($con, $sql);
            
            // check if any rows were returned
        
            if (mysqli_num_rows($result) > 0) {
    
            $sql = "DELETE FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record deleted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
            }
            
            // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
            $sql = "INSERT INTO Users_EngageIn_Interventions (user_id, intervention_id, quantity, unit, log_date) VALUES ('$user_id', '$intervention_id', '$quantity','$unit','$log_date')";
            
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        } else {
            // the intervention does not exist in the database
            $query = $con->prepare("INSERT INTO Interventions (intervention_name) VALUES (?)");
            $query->bind_param('s', $intervention);
            $result = $query->execute();
    
            if ($result) {
                // the intervention was successfully inserted into the database
                $sql = "SELECT * FROM interventions WHERE intervention_name = '$intervention'";
                
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($result);
                $intervention_id = $row['Intervention_ID'];

                $sql = "SELECT * FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
    
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                
                // check if any rows were returned
            
                if (mysqli_num_rows($result) > 0) {
        
                $sql = "DELETE FROM Users_Engagein_Interventions WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND log_date = '$log_date'";
                // execute the SQL query
                if (mysqli_query($con, $sql)) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
                }
                
                // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
                $sql = "INSERT INTO Users_EngageIn_Interventions (user_id, intervention_id, quantity, unit, log_date) VALUES ('$user_id', '$intervention_id', '$quantity','$unit','$log_date')";
                if (mysqli_query($con, $sql)) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    
        $sql = "SELECT * FROM interventions_manage_symptoms WHERE user_id = '$user_id' AND intervention_id = '$intervention_id' AND Symptom_name = '$symptoms'";
    
        // execute the SQL query
        $result = mysqli_query($con, $sql);
    
        // check if any rows were returned
    
        if (mysqli_num_rows($result) <= 0)
        {
        $sql = "INSERT INTO interventions_manage_symptoms (user_id, intervention_ID, symptom_name) VALUES ('$user_id', '$intervention_id', '$symptoms')";
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record inserted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        }
}

if(isset($_POST['medication-name']) && !empty($_POST['medication-name']))
{
        $sql = "SELECT * FROM Users_take_medications WHERE user_id = '$user_id' AND log_date = '$log_date'";
        
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned

        if (mysqli_num_rows($result) > 0) {

        $sql = "DELETE FROM Users_take_medications WHERE user_id = '$user_id' AND log_date = '$log_date'";
        // execute the SQL query
        if (mysqli_query($con, $sql)) {
            echo "Record deleted successfully.";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($con);
        }
        }
        // prepare the SQL query to check if the coping skill exists in the database
        $medication = $_POST['medication-name'];
        $quantity = $_POST['medication-dosage'];
        $unit = $_POST['unit'];
        $doctor = $_POST['prescribing-doctor'];
    
        $sql = "SELECT * FROM Medications WHERE Medication_Name = '$medication'";
        
        // execute the SQL query
        $result = mysqli_query($con, $sql);
        
        // check if any rows were returned
    
        if (mysqli_num_rows($result) > 0) {
            // the medication exists in the database
            $row = mysqli_fetch_assoc($result);
            $medication_id = $row['Medication_ID'];

            
            
            // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
            $sql = "INSERT INTO users_take_medications (user_id, medication_id, quantity, unit, Psychiatrist, log_date) VALUES ('$user_id', '$medication_id', '$quantity','$unit','$doctor' ,'$log_date')";
            
            // execute the SQL query
            if (mysqli_query($con, $sql)) {
                echo "Record inserted successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        } else {
            // the medication does not exist in the database
            $query = $con->prepare("INSERT INTO Medications (Medication_name) VALUES (?)");
            $query->bind_param('s', $medication);
            $result = $query->execute();
    
            if ($result) {
                // the medication was successfully inserted into the database
                $sql = "SELECT * FROM medications WHERE medication_name = '$medication'";
                
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                $row = mysqli_fetch_assoc($result);
                $medication_id = $row['medication_id'];
                
                // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
                $sql = "INSERT INTO users_take_medications (user_id, medication_id, quantity, unit, Psychiatrist, log_date) VALUES ('$user_id', '$medication_id', '$quantity','$unit','$doctor' ,'$log_date')";
                if (mysqli_query($con, $sql)) {
                    echo "Record inserted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }

        if(isset($_POST['side-effect-name']) && !empty($_POST['side-effect-name']))
        {
                // prepare the SQL query to check if the coping skill exists in the database
                $sideeffect = $_POST['side-effect-name'];
                $severity = $_POST['side-effect-severity'];

                $sql = "SELECT * FROM Medications_have_sideeffects WHERE user_id = '$user_id' AND log_date = '$log_date'";
        
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                
                // check if any rows were returned
        
                if (mysqli_num_rows($result) > 0) {
        
                $sql = "DELETE FROM Medications_have_sideeffects WHERE user_id = '$user_id' AND log_date = '$log_date'";
                // execute the SQL query
                if (mysqli_query($con, $sql)) {
                    echo "Record deleted successfully.";
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($con);
                }
                }
            
                $sql = "SELECT * FROM Side_Effects WHERE SideEffect_name = '$sideeffect'";
                
                // execute the SQL query
                $result = mysqli_query($con, $sql);
                
                // check if any rows were returned
            
                if (mysqli_num_rows($result) > 0) {
                    // the side effect exists in the database
                    $row = mysqli_fetch_assoc($result);
                    $sideeffect_id = $row['SideEffect_ID'];
                    
                    // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
                    $sql = "INSERT INTO Medications_Have_SideEffects (user_id, medication_id, sideeffect_ID, severity, log_date) VALUES ('$user_id', '$medication_id', '$sideeffect_id','$severity','$log_date')";
                    
                    // execute the SQL query
                    if (mysqli_query($con, $sql)) {
                        echo "Record inserted successfully.";
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($con);
                    }
                } else {
                    // the side effect does not exist in the database
                    $query = $con->prepare("INSERT INTO Side_Effects (SideEffect_name) VALUES (?)");
                    $query->bind_param('s', $sideeffect);
                    $result = $query->execute();
            
                    if ($result) {
                        // the intervention was successfully inserted into the database
                        $sql = "SELECT * FROM side_effects WHERE sideEffect_name = '$sideeffect'";
                        
                        // execute the SQL query
                        $result = mysqli_query($con, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $sideeffect_id = $row['SideEffect_IDs'];
                        
                        // prepare the SQL query to insert the user ID and coping skill ID into the Users_have_copingskill table
                        $sql = "INSERT INTO Medications_Have_SideEffects (user_id, medication_id, sideeffect_ID, severity, log_date) VALUES ('$user_id', '$medication_id', '$sideeffect_id','$severity','$log_date')";
                        if (mysqli_query($con, $sql)) {
                            echo "Record inserted successfully.";
                        } else {
                            echo "Error: " . $sql . "<br>" . mysqli_error($con);
                        }
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($con);
                    }
                }
        }
}

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