DROP DATABASE bipolar;
CREATE DATABASE bipolar;
USE bipolar;

CREATE TABLE Users
(
	User_ID INT PRIMARY KEY AUTO_INCREMENT,
    age INT
);

CREATE TABLE Symptoms
(
    Symptom_name
    ENUM('Depressed mood', 'Diminished ability to concentrate', 'Fatigue', 'Worthlessness or guilt',
    'Suicidal Ideation', 'Psychomotor agitation/retardation', 'Weight loss/gain/appetite change', 
    'Loss of interest or pleasure', 'Insomnia/hypersomnia',
    'Racing thoughts/Flight of ideas',
    'Increase in goal-directed activity/psychomotor agitation', 'Excessive high-risk activities', 
    'Increased talkativeness', 'Distractibility', 'Euphoria/elation', 'Irritability',
    'Increased self-esteem/grandiosity', 'Decreased need for sleep', 'Hospitalization/Psychosis', 'Impairment of function',
    'Drugs used that may be causing symptoms', 'High energy', 'Weight') PRIMARY KEY,
    Symptom_type ENUM('Depressive', 'Manic', 'Other') NOT NULL
);

CREATE TABLE Warning_Signs
(
	WarningSign_ID INT AUTO_INCREMENT PRIMARY KEY,
    WarningSign_Name VARCHAR(100) NOT NULL,
    WarningSign_Type ENUM('Manic', 'Depressive', 'Hypomanic')
);

-- CREATE TABLE Sobriety
-- (
-- 	Drug_ID INT AUTO_INCREMENT PRIMARY KEY,
--     Drug VARCHAR(100) NOT NULL
-- );

CREATE TABLE Coping_Skills
(
	Skill_ID INT AUTO_INCREMENT PRIMARY KEY,
    Skill_name VARCHAR(100) NOT NULL
);

CREATE TABLE Interventions
(
	Intervention_ID INT AUTO_INCREMENT PRIMARY KEY,
    Intervention_Name VARCHAR(100) NOT NULL
);

CREATE TABLE Medications
(	
	Medication_ID INT AUTO_INCREMENT PRIMARY KEY,
    Medication_Name VARCHAR(100) NOT NULL
);

CREATE TABLE Side_Effects
(
	SideEffect_ID INT AUTO_INCREMENT PRIMARY KEY,
    SideEffect_Name VARCHAR(100) NOT NULL
);

CREATE TABLE Users_Have_Symptoms
(
	User_ID INT,
	Symptom_name
    ENUM('Depressed mood', 'Diminished ability to concentrate', 'Fatigue', 'Worthlessness or guilt',
    'Suicidal Ideation', 'Psychomotor agitation/retardation', 'Weight loss/gain/appetite change', 
    'Loss of interest or pleasure', 'Insomnia/hypersomnia',
    'Racing thoughts/Flight of ideas',
    'Increase in goal-directed activity/psychomotor agitation', 'Excessive high-risk activities', 
    'Increased talkativeness', 'Distractibility', 'Euphoria/elation', 'Irritability',
    'Increased self-esteem/grandiosity', 'Decreased need for sleep', 'Hospitalization/Psychosis', 'Impairment of function',
    'Drugs used that may be causing symptoms', 'High energy', 'Weight'),
    PRIMARY KEY (User_ID, Symptom_name, Log_Date),
    Log_Date DATE NOT NULL,
    Severity INT NOT NULL,
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Symptom_name) REFERENCES Symptoms(Symptom_name)
);

CREATE TABLE Users_Have_WarningSigns
(
	User_ID INT,
    WarningSign_ID INT,
    Severity INT NOT NULL,
    Log_Date DATE NOT NULL,
    PRIMARY KEY (User_ID, WarningSign_ID, Log_Date),
	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (WarningSign_ID) REFERENCES Warning_Signs(WarningSign_ID) 
);

-- CREATE TABLE Users_Maintain_Sobriety
-- (
-- 	User_ID INT,
--     Drug_ID INT,
-- 	Log_Date DATE NOT NULL,
--     Days_Sober INT NOT NULL,
--     PRIMARY KEY(User_ID, Drug_ID),
-- 	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
--     FOREIGN KEY (Drug_ID) REFERENCES Sobriety(Drug_ID)     
-- );

CREATE TABLE Users_EngageIn_Interventions
(
	User_ID INT,
    Intervention_ID INT,
	Quantity FLOAT NOT NULL,
    Unit VARCHAR(50),
    Log_Date DATE NOT NULL,
    PRIMARY KEY(User_ID, Intervention_ID, Log_Date),
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Intervention_ID) REFERENCES Interventions(Intervention_ID)     
);

CREATE TABLE Users_Take_Medications
(
	User_ID INT,
    Medication_ID INT,
	Quantity FLOAT NOT NULL,
    Unit VARCHAR(50),
    Psychiatrist VARCHAR(100),
    Log_Date DATE NOT NULL,
    PRIMARY KEY(User_ID, Medication_ID, Log_Date),
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Medication_ID) REFERENCES Medications(Medication_ID)     
);

CREATE TABLE Users_Use_CopingSkills
(
	User_ID INT,
    Skill_ID INT,
    Log_Date DATE NOT NULL,
    PRIMARY KEY (User_ID, Skill_ID, Log_Date),
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Skill_ID) REFERENCES Coping_Skills(Skill_ID)    
);

CREATE TABLE Medications_Have_SideEffects
(
	User_ID INT,
	Medication_ID INT,
    SideEffect_ID INT,
    Severity INT NOT NULL,
    Log_Date DATE NOT NULL,
    PRIMARY KEY (User_ID, Medication_ID, SideEffect_ID, Log_Date),
	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Medication_ID) REFERENCES Medications(Medication_ID), 
    FOREIGN KEY (SideEffect_ID) REFERENCES Side_Effects(SideEffect_ID) 
);

CREATE TABLE CopingSkills_Manage_Symptoms
(
	User_ID INT,
	Skill_ID INT,
    Symptom_name
    ENUM('Depressed mood', 'Diminished ability to concentrate', 'Fatigue', 'Worthlessness or guilt',
    'Suicidal Ideation', 'Psychomotor agitation/retardation', 'Weight loss/gain/appetite change', 
    'Loss of interest or pleasure', 'Insomnia/hypersomnia',
    'Racing thoughts/Flight of ideas',
    'Increase in goal-directed activity/psychomotor agitation', 'Excessive high-risk activities', 
    'Increased talkativeness', 'Distractibility', 'Euphoria/elation', 'Irritability',
    'Increased self-esteem/grandiosity', 'Decreased need for sleep', 'Hospitalization/Psychosis', 'Impairment of function',
    'Drugs used that may be causing symptoms', 'High energy', 'Weight'),
    PRIMARY KEY (User_ID, Skill_ID, Symptom_name),
	FOREIGN KEY (Symptom_name) REFERENCES Symptoms(Symptom_name),
	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Skill_ID) REFERENCES Coping_Skills(Skill_ID)   
);

CREATE TABLE Interventions_Manage_Symptoms
(
	User_ID INT,
	Intervention_ID INT,
	Symptom_name
    ENUM('Depressed mood', 'Diminished ability to concentrate', 'Fatigue', 'Worthlessness or guilt',
    'Suicidal Ideation', 'Psychomotor agitation/retardation', 'Weight loss/gain/appetite change', 
    'Loss of interest or pleasure', 'Insomnia/hypersomnia',
    'Racing thoughts/Flight of ideas',
    'Increase in goal-directed activity/psychomotor agitation', 'Excessive high-risk activities', 
    'Increased talkativeness', 'Distractibility', 'Euphoria/elation', 'Irritability',
    'Increased self-esteem/grandiosity', 'Decreased need for sleep', 'Hospitalization/Psychosis', 'Impairment of function',
    'Drugs used that may be causing symptoms', 'High energy', 'Weight'),
    PRIMARY KEY(User_ID, Intervention_ID, Symptom_name),
	FOREIGN KEY (Symptom_name) REFERENCES Symptoms(Symptom_name),
	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
    FOREIGN KEY (Intervention_ID) REFERENCES Interventions(Intervention_ID)   
);

-- CREATE TABLE CopingSkills_HelpWith_Sobriety
-- (
-- 	User_ID INT,
-- 	Skill_ID INT,
--     Successful_Uses INT DEFAULT 1,
--     Drug_ID INT,
--     PRIMARY KEY (User_ID, Skill_ID, Drug_ID),
-- 	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
--     FOREIGN KEY (Skill_ID) REFERENCES Coping_Skills(Skill_ID),
--     FOREIGN KEY (Drug_ID) REFERENCES Sobriety(Drug_ID)  
-- );

-- CREATE TABLE Interventions_HelpWith_Sobriety
-- (
-- 	User_ID INT,
-- 	Intervention_ID INT,
--     Successful_Uses INT DEFAULT 1,
--     Drug_ID INT,
--     PRIMARY KEY(User_ID, Intervention_ID, Drug_ID),
-- 	FOREIGN KEY (User_ID) REFERENCES Users(User_ID), 
--     FOREIGN KEY (Intervention_ID) REFERENCES Interventions(Intervention_ID),
--     FOREIGN KEY (Drug_ID) REFERENCES Sobriety(Drug_ID)  
-- );

-- CREATE TABLE WarningSigns_Predict_Symptoms
-- (
-- 	User_ID INT,
--     Symptom_name
--     ENUM('Depressed mood', 'Diminished ability to concentrate', 'Fatigue', 'Worthlessness or guilt',
--     'Suicidal Ideation', 'Psychomotor agitation/retardation', 'Weight loss/gain/appetite change', 
--     'Loss of interest or pleasure', 'Insomnia/hypersomnia',
--     'Racing thoughts/Flight of ideas',
--     'Increase in goal-directed activity/psychomotor agitation', 'Excessive high-risk activities', 
--     'Increased talkativeness', 'Distractibility', 'Euphoria/elation', 'Irritability',
--     'Increased self-esteem/grandiosity', 'Decreased need for sleep', 'Hospitalization/Psychosis', 'Impairment of function',
--     'Drugs used that may be causing symptoms', 'High energy', 'Weight'),
--     WarningSign_ID INT,
--     PRIMARY KEY(User_ID, Symptom_name, WarningSign_ID),
--     FOREIGN KEY (User_ID) REFERENCES Users(User_ID),
--     FOREIGN KEY (Symptom_name) REFERENCES Symptoms(Symptom_name),
--     FOREIGN KEY (WarningSign_ID) REFERENCES Warning_Signs(WarningSign_ID)
-- );

-- CREATE TABLE WarningSigns_Predict_Relapses
-- (
-- 	User_ID INT,
--     Drug_ID INT,
--     WarningSign_ID INT,
--     PRIMARY KEY(User_ID, Drug_ID, WarningSign_ID),
--     FOREIGN KEY (User_ID) REFERENCES Users(User_ID),
--     FOREIGN KEY (Drug_ID) REFERENCES Sobriety(Drug_ID),
--     FOREIGN KEY (WarningSign_ID) REFERENCES Warning_Signs(WarningSign_ID)
-- );