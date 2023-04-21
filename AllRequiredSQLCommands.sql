USE bipolar;

-- Reading Records
	-- these commands should display all things a user has for a given date
	-- FOR THESE COMMANDS replace the date and user ID
	SELECT * FROM Users_Have_Symptoms WHERE User_ID = 1 AND Log_date = '2023-02-01';
	SELECT * FROM Users_Have_WarningSigns WHERE User_ID = 1 AND Log_date = '2023-02-01';
	SELECT * FROM Users_EngageIn_Interventions WHERE User_ID = 1 AND Log_date = '2023-02-01';
	SELECT * FROM Users_Take_Medications WHERE User_ID = 1 AND Log_date = '2023-02-01';
	SELECT * FROM Users_Use_CopingSkills WHERE User_ID = 1 AND Log_date = '2023-02-01';
	SELECT * FROM Medications_Have_SideEffects WHERE User_ID = 1 AND Log_date = '2023-02-01';

	-- THESE COMMANDS display the coping skills and interventions a given user has
	-- GET USER ID FROM USER
	-- For coping skills
	SELECT User_ID, Skill_name, Symptom_name FROM CopingSkills_Manage_Symptoms 
	JOIN Coping_Skills ON Coping_Skills.Skill_ID = CopingSkills_Manage_Symptoms.Skill_ID
	WHERE User_ID = 1;
	-- For interventions
	SELECT User_ID, Intervention_Name, Symptom_name FROM Interventions_Manage_Symptoms 
	JOIN Interventions ON Interventions.Intervention_ID = Interventions_Manage_Symptoms.Intervention_ID
	WHERE User_ID = 1;

-- NOTE:
-- All user_ID and log_date values must be replaced by the user_ID and log_date values pulled from user input
-- For example, if a user has an ID of 4 and wants to do something to the values on the 23rd day, all the values
-- in the below scripts MUST be replaced with the appropriate log date and user ID


-- Detecting Hypomanic Episodes
CREATE VIEW hypomanic AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = 28 AND Log_date = '2023-02-14'
        AND (Symptom_type = 'Manic' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight');

SELECT 
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
  AND ('Drugs used that may be causing symptoms' NOT IN (SELECT symptom_name FROM hypomanic));        
DROP VIEW hypomanic;

-- Detecting Manic Episodes
CREATE VIEW manic AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = 59 AND Log_date = '2023-02-16'
        AND (Symptom_type = 'Manic' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight');

SELECT 
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
  ('Hospitalization/Psychosis' IN (SELECT symptom_name FROM manic));

DROP VIEW manic;

-- Detecting Depressive Episodes
CREATE VIEW depressed AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = 1 AND Log_date = '2023-02-15'
        AND (Symptom_type = 'Depressive' OR Symptom_type = 'Other') AND (Users_Have_Symptoms.Symptom_name != 'Weight');

SELECT 
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
  AND ('Drugs used that may be causing symptoms' NOT IN (SELECT symptom_name FROM depressed)));

DROP VIEW depressed;

-- Detecting mixed episodes: ASSUMES CRITERIA FOR MANIC OR HYPOMANIC IS MET
CREATE VIEW mixed AS
SELECT 
    Users_Have_Symptoms.Symptom_name, Symptoms.Symptom_type
FROM
    Users_Have_Symptoms
        JOIN
    Symptoms ON Users_Have_Symptoms.symptom_name = Symptoms.symptom_name
WHERE
    User_ID = 1 AND Log_date = '2023-02-13'
        AND (Symptom_type = 'Depressive' OR Symptom_type = 'Other') 
        AND (Users_Have_Symptoms.Symptom_name != 'Weight')
        AND (Users_Have_Symptoms.Symptom_name NOT IN 
			('Diminished ability to concentrate', 'Weight loss/gain/appetite change', 'Insomnia/hypersomnia'));

SELECT 
  CASE 
    WHEN COUNT(*) > 0 THEN 1 
    ELSE 0 
  END AS result 
FROM 
  mixed 
WHERE
  (SELECT COUNT(*) FROM mixed WHERE Symptom_type = 'Depressive')>=3;

DROP VIEW mixed;

-- Offering coping skills that have helped in the past
SELECT Symptom_name, Coping_Skills.Skill_name FROM CopingSkills_Manage_Symptoms 
JOIN Coping_Skills ON Coping_skills.Skill_ID  = CopingSkills_Manage_Symptoms.Skill_ID
WHERE User_ID = 1;

-- Offering interventions that have helped in the past
SELECT Symptom_name, Interventions.Intervention_name FROM Interventions_Manage_Symptoms 
JOIN Interventions ON Interventions.Intervention_ID  = Interventions_Manage_Symptoms.Intervention_ID
WHERE User_ID = 1;

-- Deleting Records
	-- Users have symptoms: simply get user_id, the log_date, and the symptom_name they want to delete for that day
		DELETE FROM Users_Have_Symptoms WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Symptom_name = 'Depressed mood';

	-- Users have warning signs: simply get user_id, warningSign_ID and log_date that they want to delete
		-- to get warning sign ID, will have to search warning sign table using warning sign name user inputs
		DELETE FROM Users_Have_WarningSigns WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND WarningSign_ID = 6;

	-- Users engage in interventions: get user_Id, intervention_ID and date
		-- to get intervention id, search interventions table using the intervention name user inputs
        DELETE FROM Users_EngageIn_Interventions WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Intervention_ID = 5;
	
    -- Users take medications: get user_id, medication ID, and log date
		-- to get medication ID, search medication table using the medication name user inputs
		DELETE FROM Users_Take_Medications WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Medication_ID = 3;
        
	-- Users use coping skills: get user_id, skill_id, and log_date
		-- to get skill id, search the coping skills table using coping skill name user inputs
		DELETE FROM Users_Use_CopingSkills WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Skill_ID = 5;
	
	-- medications have sideeffects: user_id, medication_id, sideeffect_id, and log_date
		-- to get side effect and medication ID: search side effect and medication tables using names user inputs
        DELETE FROM Medications_Have_SideEffects WHERE User_ID = 1 AND Log_Date = '2023-02-15' AND Medication_ID = 1 AND SideEffect_ID = 1;
    
    -- interventions manage symptoms: get user_id, intervention_ID, and symptom_name
		-- to get intervention_ID, search intervention table with intervention name that user inputs
		DELETE FROM Interventions_Manage_Symptoms WHERE User_ID = 1 AND Intervention_ID = 2 AND Symptom_name = 'Racing thoughts/Flight of ideas';
        
	-- coping skills manage symptoms: user_ID, skill_ID, symptom_name
    SELECT * FROM CopingSkills_Manage_Symptoms WHERE User_ID = 1;
		-- to get skill_id, search coping_skills table with coping skill name user inputs
		DELETE FROM CopingSkills_Manage_Symptoms WHERE User_ID = 9 AND Skill_ID = 2 AND Symptom_name = 'Decreased need for sleep';

-- Updating records
	-- updating warning signs
	-- TWO DIFFERENT UPDATES: to severity OR adding a new entry
	-- TO GET WARNING SIGN ID: select the warning sign ID from warningsigns table where the warningsign_name = user_input
		-- SEVERITY UPDATE:
			-- INPUT REQUIRED FROM USER: their ID, the DATE, what SEVERITY to update to, and which WARNING SIGN ID
			UPDATE Users_Have_WarningSigns
			SET Severity = 4 -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND WarningSign_ID = 2 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER
		
		-- ADDING A NEW WARNING SIGN:
			-- FIRST CHECK IF THIS SIGN EXISTS ALREADY BY SEEING IF THE BELOW STATEMENT RETURNS AN EMPTY SET:
			SELECT * FROM Warning_Signs WHERE WarningSign_Name = 'increased sex drive'; -- USER WILL INPUT NAME

			-- IF SIGN HAS ALREADY BEEN ADDED BY ANOTHER USER (get the ID from the below statement):
			SELECT WarningSign_ID FROM Warning_Signs WHERE WarningSign_Name = 'increased sex drive'; -- getting ID; user will input sign name
			INSERT INTO Users_Have_WarningSigns(User_ID, WarningSign_ID, Log_Date, Severity)
			VALUES(1,5, '2023-02-01', 4); -- USER WILL ENTER ALL THESE VALUES EXCEPT warningSing ID

			-- IF SIGN HAS NOT BEEN ADDED BY ANY OTHER USERS:
			-- first, insert sign and get the ID:
			INSERT INTO Warning_Signs(WarningSign_Name) VALUES('sadness'); -- inserts into table
			SELECT WarningSign_ID FROM Warning_Signs WHERE WarningSign_Name = 'sadness'; -- gets ID
			INSERT INTO Users_Have_WarningSigns(User_ID, WarningSign_ID, Log_Date, Severity)
			VALUES(1,6, '2023-02-01', 6);

	-- update interventions
	-- THREE DIFFERENT UPDATES: to quantity, unit, OR adding a new entry
	-- TO GET INTERVENTION ID: select the intervention ID from intervention table where the intervention_name = user_input
		-- QUANTITY UPDATE:
			-- INPUT REQUIRED FROM USER: their ID, the DATE, what QUANTITY to update to, and which INTERVENTION
			UPDATE Users_EngageIn_Interventions
			SET Quantity = 25 -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND Intervention_ID = 2 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER

		-- UNIT UPDATE:
			-- INPUT REQUIRED FROM USER: their ID, the DATE, what UNIT to update to, and which INTERVENTION ID
			UPDATE Users_EngageIn_Interventions
			SET Unit = 'G' -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND Intervention_ID = 2 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER

		-- ADDING A NEW INTERVENTION:
			-- FIRST CHECK IF THIS INTERVENTION EXISTS ALREADY BY SEEING IF THE BELOW STATEMENT RETURNS AN EMPTY SET:
			SELECT * FROM Interventions WHERE Intervention_name = 'Exercise'; -- USER WILL INPUT INTERVENTION NAME

			-- IF INTERVENTION HAS ALREADY BEEN ADDED BY ANOTHER USER (get the ID from the below statement):
			SELECT Intervention_ID FROM Interventions WHERE Intervention_name = 'Exercise'; -- getting ID; user will input intervention name
			INSERT INTO Users_EngageIn_Interventions(User_ID, Intervention_ID, Quantity, Unit, Log_Date)
			VALUES(1, 1, 45, 'Unit', '2023-02-01'); -- USER WILL ENTER ALL THESE VALUES EXCEPT intervention ID

			-- IF INTERVENTION HAS NOT BEEN ADDED BY ANY OTHER USERS:
			-- first, insert intervention and get the ID:
			INSERT INTO Interventions(Intervention_Name) VALUES('Table Tennis'); -- inserts into table
			SELECT Intervention_ID FROM Interventions WHERE Intervention_name = 'Table Tennis'; -- gets ID
			INSERT INTO Users_EngageIn_Interventions(User_ID, Intervention_ID, Quantity, Unit, Log_Date)
			VALUES(1, 5, 45, 'Unit', '2023-02-01'); -- USER WILL ENTER ALL THESE VALUES EXCEPT intervention ID: that will be from above statements

	-- updating medications
	-- FOUR POSSIBLE UPDATES: Quantity, Unit, Psychiatrist, OR adding a new medication
	-- input needed from USER: ID, Medication name (can use this to get medication ID), and date, as well as value they want to update
	-- to get medication ID, search medications table, doing SELECT ID WHERE name=user_input
		-- updating quantity
			UPDATE Users_Take_Medications
			SET Quantity = 25 -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND Medication_ID = 1 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER
		-- updating Unit
			UPDATE Users_Take_Medications
			SET Unit = 'MM' -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND Medication_ID = 1 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER
		
		-- updating psychiatrist
			UPDATE Users_Take_Medications
			SET Psychiatrist = 'Dr. Chad Chad' -- THIS VALUE SHOULD BE GOTTEN FROM USER
			WHERE User_ID = 1 AND Medication_ID = 1 AND Log_Date = '2023-02-01'; -- ALL 3 OF THESE VALUES SHOULD BE GOTTEN FROM USER
		
		-- adding a new medication
			-- FIRST CHECK IF THIS MEDICATION EXISTS ALREADY BY SEEING IF THE BELOW STATEMENT RETURNS AN EMPTY SET:
			SELECT * FROM Medications WHERE Medication_name = 'Lamotrigine'; -- USER WILL INPUT MEDICATION NAME

			-- IF MEDICATION HAS ALREADY BEEN ADDED BY ANOTHER USER (get the ID from the below statement):
			SELECT Medication_ID FROM Medications WHERE Medication_Name = 'Lamotrigine'; -- getting ID; user will input medication name
			INSERT INTO Users_Take_Medications(User_ID, Medication_ID, Quantity, Unit, Psychiatrist, Log_Date)
			VALUES(1, 1, 45, 'MG', 'Dr. Chad Chad','2023-02-01'); -- USER WILL ENTER ALL THESE VALUES EXCEPT medication ID

			-- IF INTERVENTION HAS NOT BEEN ADDED BY ANY OTHER USERS:
			-- first, insert medication and get the ID:
			INSERT INTO Medications(Medication_name) VALUES('Pure Meth'); -- inserts into table
			SELECT Medication_ID FROM Medications WHERE Medication_name = 'Pure Meth'; -- gets ID
			INSERT INTO Users_Take_Medications(User_ID, Medication_ID, Quantity, Unit, Psychiatrist, Log_Date)
			VALUES(1, 3, 45, 'MG', 'Dr. Chad Chad','2023-02-01'); -- USER WILL ENTER ALL THESE VALUES EXCEPT medication ID -- USER WILL ENTER ALL THESE VALUES EXCEPT intervention ID: that will be from above statements

	-- updating coping skill usage
	-- Here, the user can either ADD a skill to a certain day or UPDATE a skill on a certain day
	-- There are two steps, checking if the skill they want to update/add already exists, and then updating/adding that skill
	-- TWO POSSIBLE UPDATES: changing which coping skill was used OR adding a new coping skill
		-- FIRST: check if the coping skill the user wants to enter in exists:
		SELECT * FROM Coping_Skills WHERE CopingSkills_name = 'CBT'; -- User will enter the name of the skill
		-- IF NAME IS IN COPING SKILLS TABLE ALREADY:
			-- changing which coping skill was used: user will input coping skill name: must get ID by searching coping skills table
			-- this case is where there is already an entry, but they want to change which skill they used
			UPDATE Users_Use_CopingSkills SET Skill_ID = 1 -- get this from user: search coping skills table
			WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Skill_ID = 2; -- get which skill the user wants to change (eg changing DBT to CBT)
			
			-- adding a coping skill to a day: user will input coping skill name: must get ID by searching coping skills table
			-- this case is where the user wants to ADD a coping skill they used to a day, not update an existing coping skill
			INSERT INTO Users_Use_CopingSkills(User_ID, Skill_ID, Log_Date) 
			VALUES(1, 3, '2023-02-01'); -- get skill ID for skill
			
		-- IF COPING SKILL WAS NOT IN COPING SKILLS TABLE ALREADY:
			-- adding a new coping skill to coping skills table
			INSERT INTO Coping_Skills(Skill_name) VALUES('Doing meth'); -- get ID for this skill to enter it
			SELECT Skill_ID FROM Coping_Skills WHERE Skill_name = 'Doing meth'; -- get ID for this skill to enter it
			-- user can either UPDATE a coping skill they did on a certain day OR ADD a coping skill they did:
				-- this case is where there is already an entry, but they want to change which skill they used
				UPDATE Users_Use_CopingSkills SET Skill_ID = 5 -- get this from user: search coping skills table
				WHERE User_ID = 1 AND Log_Date = '2023-02-01' AND Skill_ID = 1; -- get which skill the user wants to change (eg changing DBT to CBT)
				
				-- adding a coping skill to a day: user will input coping skill name: must get ID by searching coping skills table
				-- this case is where the user wants to ADD a coping skill they used to a day, not update an existing coping skill
				INSERT INTO Users_Use_CopingSkills(User_ID, Skill_ID, Log_Date) 
				VALUES(1, 5, '2023-02-02'); -- get skill ID for skill
	-- updating coping skills manage symptoms
	-- TWO CASES: user wants to update SKILL or SYMPTOM_NAME
		-- SKILL may EXIST or it may NOT EXIST
	-- UPDATING SYMPTOM_NAME: simply get date, user_id, and skill_id from user (skill_id can be gotten from searching coping_skills table using skill_ID)
		UPDATE CopingSkills_Manage_Symptoms SET Symptom_Name = 'Insomnia/hypersomnia'
		WHERE User_ID = 1 AND Skill_ID = 2; -- get these from user input
	-- UPDATING SKILL: first check if skill exists in skill table:
	SELECT * FROM Coping_Skills WHERE Skill_name = 'Doing Meth';
		-- UPDATING EXISTING skill: get user_id, skill_id, and symptom_name
			UPDATE CopingSkills_Manage_Symptoms SET skill_id = 5 -- get skill_id by searching coping_skills table using skill name user inputs
			WHERE User_ID = 1 AND Skill_ID = 2 AND Symptom_name = 'Insomnia/hypersomnia'; -- get these from user input
		-- UPDATING NON-EXISTENT SKILL: get user_id, skill_id, and symptom_name
			INSERT INTO Coping_Skills(Skill_name) VALUES('Licking toes'); -- insert new skill
			UPDATE CopingSkills_Manage_Symptoms SET skill_id = 5 -- get skill_id by searching coping_skills table using skill name user inputs
			WHERE User_ID = 1 AND Skill_ID = 6 AND Symptom_name = 'Insomnia/hypersomnia'; -- get these from user input
	-- updating interventions manage symptoms
	-- TWO CASES: user wants to update INTERVENTION or SYMPTOM_NAME
		-- INTERVENTION may EXIST or it may NOT EXIST
	-- UPDATING SYMPTOM_NAME: simply get date, user_id, and intervention_id from user (intervention_id can be gotten from searching intervetions table using intervention_id)
		UPDATE Interventions_Manage_Symptoms SET Symptom_Name = 'Loss of interest or pleasure'
		WHERE User_ID = 1 AND Intervention_ID = 3; -- get these from user input
	-- UPDATING INTERVENTION: first check if INTERVENTION exists in skill table:
	SELECT * FROM Interventions WHERE Intervention_name = 'Table Tennis';
		-- UPDATING EXISTING skill: get user_id, skill_id, and symptom_name
			UPDATE Interventions_Manage_Symptoms SET intervention_id = 5 -- get skill_id by searching coping_skills table using skill name user inputs
			WHERE User_ID = 1 AND intervention_id = 3 AND Symptom_name = 'Loss of interest or pleasure'; -- get these from user input
		-- UPDATING NON-EXISTENT SKILL: get user_id, skill_id, and symptom_name
			INSERT INTO Interventions(Intervention_name) VALUES('Licking toes'); -- insert new skill
			UPDATE Interventions_Manage_Symptoms SET intervention_id = 6 -- get skill_id by searching coping_skills table using skill name user inputs
			WHERE User_ID = 1 AND intervention_id = 5 AND Symptom_name = 'Loss of interest or pleasure'; -- get these from user input

-- updating medications have side effects (assume that medication already exists in database)
	-- 2 cases: User wants to update an entry OR User wants to log another side effect for that day
		-- For each of those: User either wants to add/update an existing side effect or add/update an unseen side effect
	-- get medication_ID by searching medication table with medication name provided by user
    SELECT Medication_ID FROM medications WHERE medication_name = 'Lamotrigine';
	SELECT * FROM Medications_Have_SideEffects WHERE Log_date = '2023-02-01' AND user_id = 1;
    
    -- FIRST: check if side effect exists already:
		-- this statement will not return empty if side effect exists
        SELECT * FROM Side_effects WHERE sideEffect_name = 'Rash'; -- replace rash with side effect user enters
    -- IF SIDE EFFECT DOES NOT EXIST:
		-- add it first and then do the below steps
        INSERT INTO Side_Effects(sideEffect_name) VALUES('Pain');
        -- get id from newly created side_effect
        SELECT sideeffect_iD from side_Effects WHERE sideeffect_name = 'Pain';
    -- User wants to add/update a side effect
		-- FIRST: get side effect ID by searching side effect table:
        SELECT SideEffect_ID FROM side_effects WHERE sideEffect_name = 'Rash';
        -- updating (for this, get all values from table/user that already exist + the value they want to change)
			-- updating severity
			UPDATE Medications_Have_SideEffects SET severity = '7' 
            WHERE User_ID = 1 AND Medication_ID = 1 AND SideEffect_ID = 2 AND log_date = '2023-02-16';
            -- updating medication
			UPDATE Medications_Have_SideEffects SET medication_ID = 2 
            WHERE User_ID = 1 AND Medication_ID = 1 AND SideEffect_ID = 2 AND log_date = '2023-02-16';
            -- updating side effect
			UPDATE Medications_Have_SideEffects SET sideEffect_ID = 5 
            WHERE User_ID = 1 AND Medication_ID = 2 AND SideEffect_ID = 2 AND log_date = '2023-02-16';
        -- adding: get medication_ID, side_effect_ID (search side effect table), and severity
        INSERT INTO Medications_Have_SideEffects(User_ID, Medication_ID, SideEffect_ID, Severity, Log_Date) 
        VALUES(1, 1, 2, '6', '2023-02-16'); -- should be given user_id and date; other information from user

-- UPDATING Records (for these, assume the symptom the user is updating already exists)
SET @User_ID = 1; -- get input from user
SET @Log_Date = '2023-02-01'; -- get input from user
-- UPDATING symptoms (for all the below, get severity from user)
-- User_ID, Symptom_name, Log_Date, Severity
SELECT * FROM Users_Have_Symptoms WHERE Log_date = '2023-02-01' AND user_id = 1;
-- 'Depressed mood'
	-- if the new value the user wants to enter is <= 4, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 3  -- use what severity the user inputs
    WHERE Symptom_name = 'Depressed mood' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is > 4, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Depressed Mood' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Diminished ability to concentrate'
	-- if the new value the user wants to enter is >= 1, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 1 
    WHERE Symptom_name = 'Diminished ability to concentrate' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is < 1, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Diminished ability to concentrate' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Fatigue'
	-- if the new value the user wants to enter is >= 1, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 1
    WHERE Symptom_name = 'Fatigue' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is < 1, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Fatigue' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Worthlessness or guilt'
	-- if the new value the user wants to enter is >= 1, use the following statement:
    UPDATE Users_Have_Symptoms SET Severity = 1 
    WHERE Symptom_name = 'Worthlessness or guilt' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is < 1, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Worthlessness or guilt' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Suicidal Ideation'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Suicidal Ideation' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Suicidal Ideation' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Psychomotor agitation/retardation'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Psychomotor agitation/retardation' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Psychomotor agitation/retardation' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Weight loss/gain/appetite change'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Weight loss/gain/appetite change' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Weight loss/gain/appetite change' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Loss of interest or pleasure'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Weight loss/gain/appetite change' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Weight loss/gain/appetite change' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Insomnia/hypersomnia'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1
	WHERE Symptom_name = 'Insomnia/hypersomnia' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Insomnia/hypersomnia' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Racing thoughts/Flight of ideas'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Racing thoughts/Flight of ideas' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Racing thoughts/Flight of ideas' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Increase in goal-directed activity/psychomotor agitation'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Increase in goal-directed activity/psychomotor agitation' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Increase in goal-directed activity/psychomotor agitation' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Excessive high-risk activities'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Excessive high-risk activities' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Excessive high-risk activities' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Increased talkativeness'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Increased talkativeness' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Increased talkativeness' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Distractibility'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Distractibility' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Distractibility' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Euphoria/elation'
	-- if the new value the user wants to enter is >= 7, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 7  -- use what severity the user inputs
    WHERE Symptom_name = 'Euphoria/elation' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is  < 7, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Euphoria/elation' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Irritability'
	-- if the new value the user wants to enter is >= 7, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 7  -- use what severity the user inputs
    WHERE Symptom_name = 'Irritability' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is  < 7, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Irritability' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Increased self-esteem/grandiosity'
	-- if the new value the user wants to enter is >= 7, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 7  -- use what severity the user inputs
    WHERE Symptom_name = 'Increased self-esteem/grandiosity' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is  < 7, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Increased self-esteem/grandiosity' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Decreased need for sleep'
	-- if the new value the user wants to enter is <= 5, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 5  -- use what severity the user inputs
    WHERE Symptom_name = 'Decreased need for sleep' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is  > 5, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Decreased need for sleep' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Hospitalization/Psychosis'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Hospitalization/Psychosis' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Hospitalization/Psychosis' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Impairment of function'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Hospitalization/Psychosis' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Hospitalization/Psychosis' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Drugs used that may be causing symptoms'
	-- if the new value the user wants to enter is >= 1, use following statement:
	UPDATE Users_Have_Symptoms SET Severity = 1 
	WHERE Symptom_name = 'Drugs used that may be causing symptoms' AND User_ID = @User_ID AND Log_date = @Log_Date;
	-- if the new value the user wants to enter is < 1, use following statement:
	DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'Drugs used that may be causing symptoms' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'High energy'
	-- if the new value the user wants to enter is >= 7, use following statement:
    UPDATE Users_Have_Symptoms SET Severity = 7  -- use what severity the user inputs
    WHERE Symptom_name = 'High energy' AND User_ID = @User_ID AND Log_date = @Log_Date;
    -- if the new value the user wants to enter is  < 7, use following statement:
    DELETE FROM Users_Have_Symptoms WHERE Symptom_name = 'High energy' AND User_ID = @User_ID AND Log_date = @Log_Date;
-- 'Weight'
	UPDATE Users_Have_Symptoms SET Severity = '155'  -- use what severity the user inputs
    WHERE Symptom_name = 'Weight' AND User_ID = @User_ID AND Log_date = @Log_Date;
    
-- CREATING Records
SET @User_ID = 1; -- get input from user
SET @Log_Date = '2023-02-01'; -- get input from user
-- 'Depressed mood'
-- User_ID, Symptom_name, Log_Date, Severity
SELECT * FROM Users_Have_Symptoms WHERE Log_date = @Log_Date AND user_id = @User_ID;
-- 'Depressed mood'
	-- if user this value is less than or equal to 4, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Depressed mood', @Log_Date, 3);
-- 'Diminished ability to concentrate'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Diminished ability to concentrate', @Log_Date, 1); -- get severity value from user
-- 'Fatigue'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Fatigue', @Log_Date, 1); -- get severity value from user
-- 'Worthlessness or guilt'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Worthlessness or guilt', @Log_Date, 1); -- get severity value from user
-- 'Suicidal Ideation'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Suicidal Ideation', @Log_Date, 1); -- get severity value from user
-- 'Psychomotor agitation/retardation'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Psychomotor agitation/retardation', @Log_Date, 1); -- get severity value from user
-- 'Weight loss/gain/appetite change'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Weight loss/gain/appetite change', @Log_Date, 1); -- get severity value from user
-- 'Loss of interest or pleasure'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Loss of interest or pleasure', @Log_Date, 1); -- get severity value from user
-- 'Insomnia/hypersomnia'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Insomnia/hypersomnia', @Log_Date, 1); -- get severity value from user
-- 'Racing thoughts/Flight of ideas'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Racing thoughts/Flight of ideas', @Log_Date, 1); -- get severity value from user
-- 'Increase in goal-directed activity/psychomotor agitation'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Increase in goal-directed activity/psychomotor agitation', @Log_Date, 1); -- get severity value from user
-- 'Excessive high-risk activities'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Excessive high-risk activities', @Log_Date, 1); -- get severity value from user
-- 'Increased talkativeness'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Increased talkativeness', @Log_Date, 1); -- get severity value from user
-- 'Distractibility'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Distractibility', @Log_Date, 1); -- get severity value from user
-- 'Euphoria/elation'
	-- if user this value is greater than or equal to 7, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Euphoria/elation', @Log_Date, 7); -- get severity value from user
-- 'Irritability'
	-- if user this value is greater than or equal to 7, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Irritability', @Log_Date, 7); -- get severity value from user
-- 'Increased self-esteem/grandiosity'
	-- if user this value is greater than or equal to 7, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Increased self-esteem/grandiosity', @Log_Date, 7); -- get severity value from user
-- 'Decreased need for sleep'
	-- if user this value is less than or equal to 5, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Decreased need for sleep', @Log_Date, 4); -- get severity value from user
-- 'Hospitalization/Psychosis'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Hospitalization/Psychosis', @Log_Date, 1); -- get severity value from user
-- 'Impairment of function'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Impairment of function', @Log_Date, 1); -- get severity value from user
-- 'Drugs used that may be causing symptoms'
	-- if user this value is greater than or equal to 1, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Drugs used that may be causing symptoms', @Log_Date, 1); -- get severity value from user
-- 'High energy'
	-- if user this value is greater than or equal to 7, use following statement:
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'High energy', @Log_Date, 7); -- get severity value from user
-- 'Weight'
    INSERT INTO Users_Have_Symptoms(User_ID, Symptom_name, Log_Date, Severity)
    VALUES (@User_ID, 'Weight', @Log_Date, 177); -- get severity value from user