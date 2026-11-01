<?php

/**
 *	Survey
 *
 *	The class, Survey, handles survey backend functionalities which 
 *	communicates with Survey database.
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

class Survey {

	// ==========================================
	// 	Constant Variables
	// ==========================================

	/**
	 *	@var string Database name
	 */
	const DATABASE_NAME = "highwaygroup";

	/**
	 *	@var string Survey table name
	 */
	const SURVEY_TABLE = "survey";

	/**
	 *	@var string Survey question table name
	 */
	const SURVEY_QUESTION_TABLE = "survey_question";

	/**
	 *	@var string Survey question choice table name
	 */
	const SURVEY_QUESTION_CHOICE_TABLE = "survey_question_choice";

	/**
	 *	@var string Survey open answer table name
	 */
	const SURVEY_OPEN_ANSWER_TABLE = "survey_open_answer";

	/**
	 *	@var string Survey choice answer table name
	 */
	const SURVEY_CHOICE_ANSWER_TABLE = "survey_choice_answer";

	/**
	 *	@var string Survey based question table name
	 */
	const SURVEY_BASED_QUESTION_TABLE = "survey_based_question";

	/**
	 *	@var string Survey based question choice table name
	 */
	const SURVEY_BASED_QUESTION_CHOICE_TABLE = "survey_based_question_choice";

	/**
	 *	@var string Survey based open answer table name
	 */
	const SURVEY_BASED_OPEN_ANSWER_TABLE = "survey_based_open_answer";

	/**
	 *	@var string Survey based choice answer table name
	 */
	const SURVEY_BASED_CHOICE_ANSWER_TABLE = "survey_based_choice_answer";

	/**
	 *	@var string Survey participant table name
	 */
	const SURVEY_PARTICIPANT_TABLE = "survey_participant";

	/**
	 *	@var string Survey link table name
	 */
	const SURVEY_LINK_TABLE = "survey_link";

	/**
	 *	@var string Survey token table name
	 */
	const SURVEY_TOKEN_TABLE = "survey_token";

	/**
	 *	@var string Survey member table name
	 */
	const SURVEY_MEMBER_TABLE = "survey_member";

	/**
	 *	@var string Survey based type question
	 */
	const SURVEY_BASED_QUESTION = true;

	/**
	 *	@var int Survey availability value for token only
	 */
	const SURVEY_TOKEN_ONLY = 2;

	/**
	 *	@var int Survey availability value for member only
	 */
	const SURVEY_MEMBER_ONLY = 1;

	/**
	 *	@var int Survey avilability value for everyone. The registration or login is not required.
	 */
	const SURVEY_ANONYMOUS = 0;

	/**
	 *	@var string Answer type of survey question that allows to choose only one option.
	 *
	 *	This value is radio input type.
	 */
	const SURVEY_QUESTION_SINGLE_ANSWER = "single";

	/**
	 *	@var string Answer type of survey question that allows to choose multiple options.
	 *
	 *	This value is checkbox input type.
	 */
	const SURVEY_QUESTION_MULTIPLE_ANSWER = "multiple";

	/**
	 *	@var string Answer type of survey question that does not have options to be chosen.
	 *
	 *	This value is textarea input type.
	 */
	const SURVEY_QUESTION_OPEN_ANSWER = "open";

	/**
	 *	@var string Next question is based on the current answer
	 */
	const SURVEY_QUESTION_BASED_ANSWER = "based";

	/**
	 *	@var string Input type of multiple options question is text
	 *
	 *	Use this value for text based option question.
	 */
	const SURVEY_QUESTION_TEXT_INPUT = "text";

	/**
	 *	@var string Input type of multiple options question is image
	 *
	 *	Use this value for image based option question.
	 */
	const SURVEY_QUESTION_IMAGE_INPUT = "image";

	// ==========================================
	// 	Member Variables
	// ==========================================

	/**
 	 *	@var Database instance
	 */
	private $DB;

	/**
	 *	@var string[] Survey column fields
	 */
	private $surveyFields;

	/**
	 *	@var string[] Survey question fields
	 */
	private $surveyQuestionFields;

	/**
 	 *	@var string[] Survey question choice fields
	 */
	private $surveyQuestionChoiceFields;

	/**
	 *	@var string[] Survey open answer fields
	 */
	private $surveyOpenAnswerFields;

	/**
	 *	@var string[] Survey choice answer fields
	 */
	private $surveyChoiceAnswerFields;

	/**
	 *	@var string[] Survey based question fields
	 */
	private $surveyBasedQuestionFields;

	/**
 	 *	@var string[] Survey based question choice fields
	 */
	private $surveyBasedQuestionChoiceFields;

	/**
	 *	@var string[] Survey based open answer fields
	 */
	private $surveyBasedOpenAnswerFields;

	/**
	 *	@var string[] Survey based choice answer fields
	 */
	private $surveyBasedChoiceAnswerFields;

	/**
	 *	@var string[] Survey participant fields
	 */
	private $surveyParticipantFields;

	/**
	 *	@var string[] Survey token fields
	 */
	private $surveyTokenFields;

	/**
	 *	@var string[] Error messages for debugging
	 */
	private $errorMessages;

	// ==========================================
	// 	Constructor
	// ==========================================
	
	/**
 	 *	Constructor initiates all member variables
	 *	
	 *	@param $_DB Database instance
	 */
	public function __construct($_DB) {

		// Initiate error messages
		$this->errorMessages = array();
		
		// Make sure DB is not null or empty
		if ($_DB == null || isset($_DB) === false || empty($_DB) === true) {
			die("Database connection need to be set");
		}

		$this->DB = $_DB;
	
		// -----------------------------------------------

		// Initiate survey fields
		$this->surveyFields = array();

		// Get survey fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyFields);

		// -----------------------------------------------

		// Initiate survey question fields
		$this->surveyQuestionFields = array();

		// Get survey question fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_QUESTION_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyQuestionFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyQuestionFields);

		// -----------------------------------------------

		// Initiate survey question choice fields
		$this->surveyQuestionChoiceFields = array();

		// Get survey question choice fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_QUESTION_CHOICE_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyQuestionChoiceFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyQuestionChoiceFields);

		// -----------------------------------------------

		// Initiate survey open answer fields
		$this->surveyOpenAnswerFields = array();

		// Get survey open answer fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyOpenAnswerFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyOpenAnswerFields);

		// -----------------------------------------------

		// Initiate survey choice answer fields
		$this->surveyChoiceAnswerFields = array();

		// Get survey choice answer fields from database
		$query = $this->DB->query("SHOW COLUMNS from `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyChoiceAnswerFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyChoiceAnswerFields);

		// -----------------------------------------------

		// Initiate survey based question fields
		$this->surveyBasedQuestionFields = array();

		// Get survey question fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_QUESTION_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyBasedQuestionFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyBasedQuestionFields);

		// -----------------------------------------------

		// Initiate survey based question choice fields
		$this->surveyBasedQuestionChoiceFields = array();

		// Get survey question choice fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyBasedQuestionChoiceFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyBasedQuestionChoiceFields);

		// -----------------------------------------------

		// Initiate survey based open answer fields
		$this->surveyBasedOpenAnswerFields = array();

		// Get survey open answer fields from database
		$query = $this->DB->query("SHOW COLUMNS FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_OPEN_ANSWER_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyBasedOpenAnswerFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyBasedOpenAnswerFields);

		// -----------------------------------------------

		// Initiate survey based choice answer fields
		$this->surveyBasedChoiceAnswerFields = array();

		// Get survey choice answer fields from database
		$query = $this->DB->query("SHOW COLUMNS from `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_CHOICE_ANSWER_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyBasedChoiceAnswerFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyBasedChoiceAnswerFields);

		// -----------------------------------------------

		// Initiate survey participant fields
		$this->surveyParticipantFields = array();

		// Get survey participants fields from database
		$query = $this->DB->query("SHOW COLUMNS from `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_PARTICIPANT_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyParticipantFields[] = $row['Field'];
		}

		// Get rid of id as it is auto increment or uniqid
		array_shift($this->surveyParticipantFields);

		// -----------------------------------------------

		// Initiate survey token fields
		$this->surveyTokenFields = array();

		// Get survey Token fields from database
		$query = $this->DB->query("SHOW COLUMNS from `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE ."`");
		while($row = $this->DB->fetch_assoc($query)) {
			$this->surveyTokenFields[] = $row['Field'];
		}
	}

	// ==========================================
	//	Public Methods
	// ==========================================
	
	/**
 	 *	Method: addSurvey
	 *
	 *	This method adds new survey data into database
	 *
	 *	@param mix $data Data of survey
	 *	
	 *	@return int|false Return survey id if successful, false otherwise.
	 */
	public function addSurvey($data) {

		// Current date time
		$currentDate = date("Y-m-d H:i:s");

		// Add current date time to latest updated if missing
		if (isset($data['last_updated']) === false || empty($data['last_updated']) === true) {
			$data['last_updated'] = $currentDate;
		}

		// Add current date time to added date if missing
		if (isset($data['added_date']) === false || empty($data['added_date']) === true) {
			$data['added_date'] = $currentDate;
		}
      
		// Get survey links
		$survey_link = array();
   
		if (isset($data['survey_link']) === true && empty($data['survey_link']) === false) {
			
			$survey_link = $data['survey_link'];
			unset($data['survey_link']);
		}

		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		// Check if data fields are all in database
		// print_r(count($data));
		// print_r(count($this->surveyFields));exit;
		if (count($data) == count($this->surveyFields)) {

			// Error flag to check if data validation has passed
			$error = false;

			foreach($data as $k => $v) {
				if (in_array($k, $this->surveyFields) === false) {
					$error = true;
					$this->errorMessages["addSurvey"][] = "The posted data '". $k ."' is not in '" . Survey::SURVEY_TABLE ."'.";
				}
			}

			 // -------------------------------------------------
        // Check if survey link is unique
        // -------------------------------------------------
        if (!empty($survey_link)) {
            foreach ($survey_link as $link) {
                $query = sprintf(
                    "SELECT COUNT(*) AS count FROM `%s`.`%s` WHERE `link` = %s",
                    Survey::DATABASE_NAME,
                    Survey::SURVEY_LINK_TABLE,
                    formatSQLString($link)
                );

                $result = $this->DB->query($query);
                $row = $result->fetch_assoc();

                if (intval($row['count']) > 0) {
                    $error = true;
					session_start();
					$_SESSION['error_message'] = "The survey link '{$link}' already exists. Please provide a unique link.";
                    // $this->errorMessages["addSurvey"][] = "The survey link '{$link}' already exists. Please provide a unique link.";
                }
            }
        }

			if ($error === true) {
				return false;
			}

		} else {

			// Error
			$this->errorMessages["addSurvey"][] = "The number of survey posted data is not the same as the number of fields in table '". Survey::SURVEY_TABLE ."'.";
			return false;

		}

		// -------------------------------------------------
		//	Add to database
		// -------------------------------------------------

		// Consturct column fields and values
		$columnFields = array();	// Store all fields in array
		$columnValues = array();	// Store all values in array

		foreach($this->surveyFields as $field) {
			$columnFields[] = $field;
			$columnValues[] = formatSQLString($data[$field]);
		}

		$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
		$_columnValues = implode(", ", $columnValues); // Values in string
  
		// Query statement
		$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE . "` ({$_columnFields}) VALUES ({$_columnValues})";

		// Add survey into database
		$this->DB->query($statement);

		// Get this survey id
		$id = $this->DB->insert_id();

		if (empty($survey_link) === false) {
			foreach($survey_link as $link) {
				$this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE . "` (`survey_id`, `link`) VALUES (%d, %s)", intval($id), formatSQLString($link)));
			}
		}

		return $id;
	}

	/**
 	 *	Method: editSurvey
	 *
	 *	This method edits survey data in database
	 *
	 *	@param mix $data Data of survey. Survey ID is expected in data
	 *	
	 *	@return int|false Return survey id if successful, false otherwise.
	 */
	public function editSurvey($data) {

		// Survey ID
		$id = intval($data['id']);

		// Get rid of ID field
		unset($data['id']);

		// Current date time
		$currentDate = date("Y-m-d H:i:s");

		// Add current date time to latest updated if missing
		if (isset($data['last_updated']) === false || empty($data['last_updated']) === true) {
			$data['last_updated'] = $currentDate;
		}

		// Get survey links
		$survey_link = array();

		if (isset($data['survey_link']) === true && empty($data['survey_link']) === false) {
			$survey_link = $data['survey_link'];
			unset($data['survey_link']);
		}

		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		// Check if data fields are all in database
		if (count($data) == count($this->surveyFields)) {

			// Error flag to check if data validation has passed
			$error = false;

			foreach($data as $k => $v) {
				if (in_array($k, $this->surveyFields) === false) {
					$error = true;
					$this->errorMessages["addSurvey"][] = "The posted data '". $k ."' is not in '" . Survey::SURVEY_TABLE ."'.";
				}
			}

			if (!empty($survey_link)) {
				foreach ($survey_link as $link) {
					$query = sprintf(
						"SELECT COUNT(*) AS count FROM `%s`.`%s` WHERE `link` = %s AND `survey_id` != %d",
						Survey::DATABASE_NAME,
						Survey::SURVEY_LINK_TABLE,
						formatSQLString($link),
						$id // Exclude the current survey being edited
					);
	
					$result = $this->DB->query($query);
					$row = $result->fetch_assoc();
	
					if (intval($row['count']) > 0) {
						$error = true;
						session_start();
						$_SESSION['error_message'] = "The survey link '{$link}' already exists. Please provide a unique link.";
						// $this->errorMessages["addSurvey"][] = "The survey link '{$link}' already exists. Please provide a unique link.";
					}
				}
			}

			if ($error === true) {
				return false;
			}

		} else {

			// Error
			$this->errorMessages["addSurvey"][] = "The number of survey posted data is not the same as the number of fields in table '". Survey::SURVEY_TABLE ."'.";
			return false;

		}

		// -------------------------------------------------
		//	Update database
		// -------------------------------------------------

		// Consturct update data
		$updateData = array();

		foreach($this->surveyFields as $field) {
			$updateData[] = "`" . $field . "` = " . formatSQLString($data[$field]);
		}

		$_updateData = implode(", ", $updateData); // Strigify the array

		// Query statement
		$statement = "UPDATE `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE . "` SET {$_updateData} WHERE `id` = " . $id;
        //print_r($statement);exit;
		// Add survey into database
		$this->DB->query($statement);

		if (empty($survey_link) === false) {

			// Clear all links
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE . "` WHERE `survey_id` = " . $id);

			foreach($survey_link as $link) {
				$this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE . "` (`survey_id`, `link`) VALUES (%d, %s)", intval($id), formatSQLString($link)));
			}
		}

		// Return this survey id
		return $id;
	}

	/**
	 *	Method: getSurveyById
	 *
	 *	Get survey data by survey id
	 *
	 *	@param int $id Survey ID
	 *
	 *	@return mix Return empty if no survey data in database, else return survey data.
	 */
	public function getSurveyById($id) {

		// Get data from survey table
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE ."` WHERE `id` = %d", intval($id)), MYSQL_ASSOC);

		// Get survey links
		$survey_link= array();

		$query = $this->DB->query("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE ."` WHERE `survey_id` = " . intval($id));
		while($row = $this->DB->fetch_assoc($query)) {
			$survey_link[] = $row['link'];
		}

		if (empty($survey_link) === false) {
			$data['survey_link'] = $survey_link;
		}

		// Get number of participants
		$data['total_participants'] = count($this->getAllParticipantBySurveyId($id));

		// Return survey data
		return $data;
	}

	/**
	 *	Method: getAllSurvey
	 *
	 *	Get all survey from database
	 *
	 *	@param string $by     The column name to be ordered by. Default = 'id'
	 *	@param string $order  The order of result ASC or DESC. Default = 'DESC'.
	 *	@param int    $offset The beginning of result. Default = 0.
	 *	@param int 	  $limit  The limit of result. Default 0. 0 for no limit.
	 *
	 *	@return mix Survey data
	 */
	public function getAllSurvey($by = 'id', $order = 'DESC', $offset = 0, $limit = 0) {

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE ."` ORDER BY `{$by}` {$order}";

		if ($limit > 0) {
			$statement .= " LIMIT {$offset}, {$limit}";
		}

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {

			// Duplicate row for appending values
			$_row = $row;
			
			// Get survey links
			$survey_link= array();

			$surveyLinkQuery = $this->DB->query("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE ."` WHERE `survey_id` = " . intval($_row['id']));
			
			while($surveyLinkRow = $this->DB->fetch_assoc($surveyLinkQuery)) {
				$survey_link[] = $surveyLinkRow['link'];
				// print_r($survey_link);exit;
			}

			if (empty($survey_link) === false) {
				$_row['survey_link'] = $survey_link;
			}

			// Get number of participants
			$_row['total_participants'] = count($this->getAllParticipantBySurveyId($_row['id']));

			$data[] = $_row;
		}

		return $data;
	}

	/**
	 *	Method: searchSurveyByName
	 *
	 *	@param string $name Name of survey
	 *	@param string $by     The column name to be ordered by. Default = 'id'
	 *	@param string $order  The order of result ASC or DESC. Default = 'DESC'.
	 *	@param int    $offset The beginning of result. Default = 0.
	 *	@param int 	  $limit  The limit of result. Default 0. 0 for no limit.
	 *
	 *	@return mix Survey data
	 */
	public function searchSurveyByName($name, $by = 'id', $order = 'DESC', $offset = 0, $limit = 0) {

		$name = formatSQLString(strtolower("%" . $name . "%"));

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE ."` WHERE LOWER(`name`) LIKE {$name} ORDER BY `{$by}` {$order}";

		if ($limit > 0) {
			$statement .= " LIMIT {$offset}, {$limit}";
		}

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {

			// Duplicate row for appending values
			$_row = $row;
			
			// Get survey links
			$survey_link= array();

			$surveyLinkQuery = $this->DB->query("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE ."` WHERE `survey_id` = " . intval($_row['id']));
			while($surveyLinkRow = $this->DB->fetch_assoc($surveyLinkQuery)) {
				$survey_link[] = $surveyLinkRow['link'];
			}

			if (empty($survey_link) === false) {
				$_row['survey_link'] = $survey_link;
			}

			// Get number of participants
			$_row['total_participants'] = count($this->getAllParticipantBySurveyId($_row['id']));

			$data[] = $_row;
		}

		return $data;
	}

	/**
	 *	Method: surveyExists
	 *
	 *	Check if survey exists in database
	 *
	 *	@param int $id Survey id
	 *
	 *	@return boolean Return true if exists, false otherwise.
	 */
	public function surveyExists($id) {
		$data = $this->getSurveyById($id);
		return (!empty($data));
	}

	/**
	 *  Method: getSurveyIdByLink
	 *  
	 *  Get survey id by uri link
	 *  
	 *  @param string $uri Link of survey
	 *  
	 *  @return int Survey ID
	 */
	public function getSurveyIdByLink($uri) {
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_LINK_TABLE ."` WHERE `link`=%s", formatSQLString($uri)), MYSQL_ASSOC);
		return (count($data) > 0) ? $data['survey_id'] : 0;
	}

	/**
	 *  Method: getSurveyIdByTokenLink
	 *  
	 *  Get survey id by token link
	 *  
	 *  @param string $uri Token link
	 *  
	 *  @return int Survey ID
	 */
	public function getSurveyIdByTokenLink($uri) {
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE ."` WHERE `link`=%s", formatSQLString($uri)), MYSQL_ASSOC);
		return (count($data) > 0) ? $data['survey_id'] : 0;
	}

	/**
	 *	Method: getNumberOfSurvey
	 *
	 *	Get how many survey are in database
	 *
	 *	@return int Number of survey
	 */
	public function getNumberOfSurvey() {
		$data = $this->DB->query_first("SELECT COUNT(*) as 'total' FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TABLE ."`", MYSQL_ASSOC);
		return $data['total'];
	}

	/**
	 *	Method: addQuestion
	 *
	 *	This method adds new question into database
	 *
	 *	@param mixed[] $data  Data of questions
	 *	@param boolean $based True if it's based type.
	 *
	 *	@return int|false Return question id on success, false otherwise
	 */
	public function addQuestion($data, $based = false) {

		// To store question choice data
		$question_choice = array();

		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;

		// Fields variables
		$questionFields = ($based === true) ? $this->surveyBasedQuestionFields : $this->surveyQuestionFields;
		$questionChoiceFileds = ($based === true) ? $this->surveyBasedQuestionChoiceFields : $this->surveyQuestionChoiceFields;

		// Check if this question have option type
		if (isset($data['question_choice']) === true && empty($data['question_choice']) === false) {
			$question_choice = $data['question_choice'];
			unset($data['question_choice']);
		}

		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		// Check if data fields are all in database
		if (count($data) == count($questionFields)) {

			// Error flag to check if data validation has passed
			$error = false;

			foreach($data as $k => $v) {
				if (in_array($k, $questionFields) === false) {
					$error = true;
					$this->errorMessages["addQuestion"][] = "The posted data '". $k ."' is not in table '". $questionTable ."'.";
				}
			}

			// Check if question choice has all fields required for database
			if (empty($question_choice) === false) {

				// Get each question choice
				foreach($question_choice as $choice) {
					
					if ($based === true) {
						if (isset($choice['survey_based_question_id']) === false || empty($choice['survey_based_question_id']) === true) {
							$choice['survey_based_question_id'] = 0;
						}
					} else {
						if (isset($choice['survey_question_id']) === false || empty($choice['survey_question_id']) === true) {
							$choice['survey_question_id'] = 0; // Default value.
						}
					}

					if (count($choice) == count($questionChoiceFileds)) {

						foreach($choice as $k => $v) {
							if (in_array($k, $questionChoiceFileds) === false) {
								$error = true;
								$this->errorMessages["addQuestion"][] = "The posted data '". $k ."' of question choice is not in table '". $questionChoiceTable ."'.";
							}
						}

					} else {

						// Error
						$this->errorMessages["addQuestion"][] = "The number of survey choice question posted data is not the same as the number of fields in table '". $questionChoiceTable ."'.";
						return false;

					}

				}

			}

			if ($error === true) {
				return false;
			}

		} else {

			// Error
			$this->errorMessages["addQuestion"][] = "The number of survey question posted data is not the same as the number of fields in table '". $questionTable ."'.";
			return false;

		}

		// -------------------------------------------------
		//	Add to database
		// -------------------------------------------------

		// Consturct column fields and values
		$columnFields = array();	// Store all fields in array
		$columnValues = array();	// Store all values in array

		foreach($questionFields as $field) {
			$columnFields[] = $field;
			$columnValues[] = formatSQLString($data[$field]);
		}

		$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
		$_columnValues = implode(", ", $columnValues); 				// Values in string

		// Query statement
		$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". $questionTable . "` ({$_columnFields}) VALUES ({$_columnValues})";

		// Add survey into database
		$this->DB->query($statement);

		// Get question id
		$question_id = $this->DB->insert_id();

		// Save question choice data
		if (empty($question_choice) === false) {
			
			foreach($question_choice as $choice) {

				// Assign real question id
				if ($based === true) {
					$choice['survey_based_question_id'] = $question_id;
				} else {
					$choice['survey_question_id'] = $question_id;
				}

				// Construct column fields and values
				$columnFields = array();	// Store all fields in array
				$columnValues = array(); 	// Store all values in array

				foreach($questionChoiceFileds as $field) {
					$columnFields[] = $field;
					$columnValues[] = formatSQLString($choice[$field]);
				}

				$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
				$_columnValues = implode(", ", $columnValues); 				// Values in string

				// Query statement
				$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable . "` ({$_columnFields}) VALUES ({$_columnValues})";

				// Add survey into database
				$this->DB->query($statement);
			}

		}

		// Return question id
		return $question_id;
	}

	/**
	 *	Method: editQuestion
	 *
	 *	This method edits question in database
	 *
	 *	@param mixed[] $data  Data of questions. Question ID is expected in data
	 *	@param boolean $based True if based type question
	 *
	 *	@return int|false Return question id on success, false otherwise
	 */
	public function editQuestion($data, $based = false) {

		// Get question ID
		$id = intval($data['id']);
		unset($data['id']);

		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;

		// Fields variables
		$questionFields = ($based === true) ? $this->surveyBasedQuestionFields : $this->surveyQuestionFields;
		$questionChoiceFileds = ($based === true) ? $this->surveyBasedQuestionChoiceFields : $this->surveyQuestionChoiceFields;

		// To store question choice data
		$question_choice = array();

		// Check if this question have option type
		if (isset($data['question_choice']) === true && empty($data['question_choice']) === false) {
			$question_choice = $data['question_choice'];
			unset($data['question_choice']);
		}

		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		// Check if data fields are all in database
		if (count($data) == count($questionFields)) {

			// Error flag to check if data validation has passed
			$error = false;

			foreach($data as $k => $v) {
				if (in_array($k, $questionFields) === false) {
					$error = true;
					$this->errorMessages["addQuestion"][] = "The posted data '". $k ."' is not in table '". $questionTable ."'.";
				}
			}

			// Check if question choice has all fields required for database
			if (empty($question_choice) === false) {

				// Get each question choice
				foreach($question_choice as $choice) {
					
					if ($based === true) {
						if (isset($choice['survey_based_question_id']) === false || empty($choice['survey_based_question_id']) === true) {
							$choice['survey_based_question_id'] = 0;
						}
					} else {
						if (isset($choice['survey_question_id']) === false || empty($choice['survey_question_id']) === true) {
							$choice['survey_question_id'] = 0; // Default value.
						}
					}

					if (count($choice) == count($questionChoiceFileds)) {

						foreach($choice as $k => $v) {
							if (in_array($k, $questionChoiceFileds) === false) {
								$error = true;
								$this->errorMessages["addQuestion"][] = "The posted data '". $k ."' of question choice is not in table '". $questionChoiceTable ."'.";
							}
						}

					} else {

						// Error
						$this->errorMessages["addQuestion"][] = "The number of survey choice question posted data is not the same as the number of fields in table '". $questionChoiceTable ."'.";
						return false;

					}

				}

			}

			if ($error === true) {
				return false;
			}

		} else {

			// Error
			$this->errorMessages["addQuestion"][] = "The number of survey question posted data is not the same as the number of fields in table '". Survey::SURVEY_QUESTION_TABLE ."'.";
			return false;

		}

		// -------------------------------------------------
		//	Update database
		// -------------------------------------------------

		// Consturct update data
		$updateData = array();

		foreach($questionFields as $field) {
			$updateData[] = "`" . $field . "` = " . formatSQLString($data[$field]);
		}

		$_updateData = implode(", ", $updateData); // Strigify the array

		// Query statement
		$statement = "UPDATE `". Survey::DATABASE_NAME ."`.`". $questionTable . "` SET {$_updateData} WHERE `id` = " . $id;

		// Add survey into database
		$this->DB->query($statement);

		// Save question choice data
		if (empty($question_choice) === false) {

			// Fields name hacked for based question table
			$survey_question_id = ($based === true) ? "survey_based_question_id" : "survey_question_id";

			// Clear all current question choice if exists
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable . "` WHERE `". $survey_question_id ."` = " . $id);
			
			foreach($question_choice as $choice) {

				// Assign real question id
				if ($based === true) {
					$choice['survey_based_question_id'] = $id;
				} else {
					$choice['survey_question_id'] = $id;
				}

				// Construct column fields and values
				$columnFields = array();	// Store all fields in array
				$columnValues = array(); 	// Store all values in array

				foreach($questionChoiceFileds as $field) {
					$columnFields[] = $field;
					$columnValues[] = formatSQLString($choice[$field]);
				}

				$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
				$_columnValues = implode(", ", $columnValues); 				// Values in string

				// Query statement
				$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable . "` ({$_columnFields}) VALUES ({$_columnValues})";

				// Add survey into database
				$this->DB->query($statement);
			}

		}

		// Return question id
		return $id;
	}

	/**
	 *	Method: getQuestionById
	 *
	 *	Get question by its id
	 *
	 *	@param int $id Question ID
	 *
	 *	@return mix Question data if exists, empty array otherwise
	 */
	public function getQuestionById($id, $based = false) {

		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;

		// Fields name hacked for based question table
		$survey_question_id = ($based === true) ? "survey_based_question_id" : "survey_question_id";

		// Get question from survey table
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". $questionTable ."` WHERE `id` = %d", intval($id)), MYSQL_ASSOC);

		// Check if question type is multiple choice or not
		if (empty($data) === false && $data['type'] != Survey::SURVEY_QUESTION_OPEN_ANSWER) {
			
			// Get question choice data
			$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable ."` WHERE `". $survey_question_id ."` = %d ORDER BY `id` ASC", intval($data['id'])));
			
			// Initiate to store question choice data
			$data['question_choice'] = array();
			
			// Get all question choice data
			while($row = $this->DB->fetch_assoc($query)) {
				$data['question_choice'][] = $row;
			}

		}

		// Return survey data
		return $data;
	}

	/**
	 *	Method: getAllQuestion
	 *
	 *	Get all question from database
	 *
	 *	@param string $by     The column name to be ordered by. Default = 'id'
	 *	@param string $order  The order of result ASC or DESC. Default = 'DESC'.
	 *	@param int    $offset The beginning of result. Default = 0.
	 *	@param int 	  $limit  The limit of result. Default 0. 0 for no limit.
	 */
	public function getAllQuestion($by = 'id', $order = 'DESC', $offset = 0, $limit = 0) {

		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;

		// Fields name hacked for based question table
		$survey_question_id = ($based === true) ? "survey_based_question_id" : "survey_question_id";

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". $questionTable ."` ORDER BY `{$by}` {$order}";

		if ($limit > 0) {
			$statement .= " LIMIT {$offset}, {$limit}";
		}

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {
			
			// Copy row data for further check and data appending without interferring with current row data
			$_row = $row;

			// Check if question type is multiple choice or not
			if ($_row['type'] != Survey::SURVEY_QUESTION_OPEN_ANSWER) {

				// Get question choice data
				$questionChoiceQuery = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable ."` WHERE `". $survey_question_id ."` = %d ORDER BY `id` ASC", intval($_row['id'])));

				// Initiate to store question choice data
				$_row['question_choice'] = array();

				// Get all question choice data
				while($questionChoiceRow = $this->DB->fetch_assoc($questionChoiceQuery)) {
					$_row['question_choice'][] = $questionChoiceRow;
				}
			}

			$data[] = $_row;

		}

		return $data;
	}

	/**
	 *	Method: getQuestionBySurveyId
	 *
	 *	Get all questions from database by survey ID
	 *
	 *	@param int $survey_id  ID of survey
	 */
	public function getQuestionBySurveyId($survey_id) {

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_QUESTION_TABLE ."` WHERE `survey_id` = " . intval($survey_id). " ORDER BY `position`";

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {
			
			// Copy row data for further check and data appending without interferring with current row data
			$_row = $row;

			// Check if question type is multiple choice or not
			if ($_row['type'] != Survey::SURVEY_QUESTION_OPEN_ANSWER) {

				// Get question choice data
				$questionChoiceQuery = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_QUESTION_CHOICE_TABLE ."` WHERE `survey_question_id` = %d ORDER BY `id` ASC", intval($_row['id'])));

				// Initiate to store question choice data
				$_row['question_choice'] = array();

				// Get all question choice data
				while($questionChoiceRow = $this->DB->fetch_assoc($questionChoiceQuery)) {
					$_row['question_choice'][] = $questionChoiceRow;
				}
			}

			$data[] = $_row;

		}

		return $data;
	}

	/**
	 *	Method: getBasedQuestionByChoiceId
	 *
	 *	Get all questions from database by survey ID
	 *
	 *	@param int $chocie_id  ID of question choice
	 */
	public function getQuestionByChoiceId($choice_id) {

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_QUESTION_TABLE ."` WHERE `survey_question_choice_id` = " . intval($choice_id). " ORDER BY `position`";

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {
			
			// Copy row data for further check and data appending without interferring with current row data
			$_row = $row;

			// Check if question type is multiple choice or not
			if ($_row['type'] != Survey::SURVEY_QUESTION_OPEN_ANSWER) {

				// Get question choice data
				$questionChoiceQuery = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE ."` WHERE `survey_based_question_id` = %d ORDER BY `id` ASC", intval($_row['id'])));

				// Initiate to store question choice data
				$_row['question_choice'] = array();

				// Get all question choice data
				while($questionChoiceRow = $this->DB->fetch_assoc($questionChoiceQuery)) {
					$_row['question_choice'][] = $questionChoiceRow;
				}
			}

			$data[] = $_row;

		}

		return $data;
	}

	/**
	 *	Method: getQuestionChoiceById
	 *
	 *	@param int $id Question Choice ID
	 *  @param boolean $based True if based type quetion
	 *
	 *	@return mixed[] Question choice data
	 */
	public function getQuestionChoiceById($id, $based = false) {

		// Database table variables
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable ."` WHERE `id` = " . intval($id);

		// To store data result
		$data = array();

		// Get data from survey table
		$data = $this->DB->query_first($statement, MYSQL_ASSOC);

		return $data;
	}

	/**
	 *	Method: questionExists
	 *
	 *	Check if question with this id exists in database
	 *
	 *	@param int $id Question ID
	 *	@param boolean $based True if based type quetion
	 *
	 *	@return boolean Return true if it does, false otherwise
	 */
	public function questionExists($id, $based = false) {
		$data = $this->getQuestionById($id, $based);
		return (!empty($data));
	}

	/**
	 *  Method: deleteQuestion
	 *  
	 *  @param int $id Quetion ID
	 *	@param boolean $based True if based type quetion
	 * 
	 *  @return boolean True if successfully deleted, false otherwise.
	 */
	public function deleteQuestion($id, $based = false) {
		$id = intval($id);

		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;
		$questionChoiceTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_CHOICE_TABLE : Survey::SURVEY_QUESTION_CHOICE_TABLE;
		$openAnswerTable = ($based === true) ? Survey::SURVEY_BASED_OPEN_ANSWER_TABLE : Survey::SURVEY_OPEN_ANSWER_TABLE;
		$choiceAnswerTable = ($based === true) ? Survey::SURVEY_BASED_CHOICE_ANSWER_TABLE : Survey::SURVEY_CHOICE_ANSWER_TABLE;

		// Fields name hacked for based question table
		$survey_question_id = ($based === true) ? "survey_based_question_id" : "survey_question_id";

		if ($this->questionExists($id, $based)) {
			// Delete all questions and answers relating to this question
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". $questionTable ."` WHERE `id`=" . $id);
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". $questionChoiceTable ."` WHERE `". $survey_question_id ."`=" . $id);
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". $openAnswerTable ."` WHERE `". $survey_question_id ."`=" . $id);
			$this->DB->query("DELETE FROM `". Survey::DATABASE_NAME ."`.`". $choiceAnswerTable ."` WHERE `". $survey_question_id ."`=" . $id);

			return true;
		} else {
			return false;
		}
	}

	/**
	 *	Method: updateQuestionPosition
	 *
	 *	@param int $id Question ID
	 *	@param int $pos position
	 *	@param boolean $based True if based type quetion
	 */
	public function updateQuestionPosition($id, $pos, $based = false) {
		// Database table variables
		$questionTable = ($based === true) ? Survey::SURVEY_BASED_QUESTION_TABLE : Survey::SURVEY_QUESTION_TABLE;

		$this->DB->query(sprintf("UPDATE `". Survey::DATABASE_NAME ."`.`". $questionTable ."` SET `position` = %d WHERE `id` = %d", intval($pos), intval($id)));
	}

	/**
	 *	Method: getParticipantById
	 *	
	 *	@param string $id Participant ID
	 *
	 *	@return mix Participant information
	 */
	public function getParticipantById($id) {

		// Get question from survey table
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_PARTICIPANT_TABLE ."` WHERE `id` = %s", formatSQLString($id)), MYSQL_ASSOC);

		if (empty($data) === false) {
			$data['member_id'] = $this->getMemberIdByParticipantId($id);

			// Get the answers of this participant
			$data['answers'] = array();

			foreach($this->getQuestionBySurveyId($data['survey_id']) as $question) {

				$data['answers'][$question['id']] = array();

				if ($question['type'] == Survey::SURVEY_QUESTION_OPEN_ANSWER) {
					$query = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE ."` WHERE `survey_question_id` = %d AND `participant_id` = %s", intval($question['id']), formatSQLString($id)), MYSQL_ASSOC);
					$data['answers'][$question['id']][] = array('value' => $query['value']);
				} else {
					$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE ."` WHERE `survey_question_id` = %d AND `participant_id` = %s", intval($question['id']), formatSQLString($id)));
					while ($row = $this->DB->fetch_assoc($query)) {
						$data['answers'][$question['id']][] = array(
							'value' => $row['survey_question_choice_id'],
							'open_value' => $row['choice_open_value']
						);
					} 
				}
			}
		}

		// Return survey data
		return $data;
	}

	/**
	 *	Method: getAllParticipantBySurveyId($id)
	 *	
	 *	@param int $id Survey ID
	 *	@param int $offset Offset of participant table
	 *	@param int $limit Maximum number of record
	 *
	 *	@return mix Participant information
	 */
	public function getAllParticipantBySurveyId($id, $offset = 0, $limit = 0) {

		$data = array();

		$limitStatement = ($limit > 0) ? "LIMIT {$offset}, {$limit}" : "" ;

		// Get question from survey table
		$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_PARTICIPANT_TABLE ."` WHERE `survey_id` = %d ORDER BY `added_date` DESC {$limitStatement}", intval($id)));

		while($row = $this->DB->fetch_assoc($query)) {

			$_row = $row;

			// Check if this participant completed required questions
			// The only case it will be false is when the users experience bugs or
			// when all questions of survey are optional
			$answersMulti = $this->DB->query_first(sprintf("SELECT COUNT(*) as 'total' FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE ."` WHERE `participant_id` = %s", formatSQLString($row['id'])), MYSQL_ASSOC);
			$answersOpen = $this->DB->query_first(sprintf("SELECT COUNT(*) as 'total' FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE ."` WHERE `participant_id` = %s", formatSQLString($row['id'])), MYSQL_ASSOC);

			if (intval($answersMulti['total']) > 0 || intval($answersOpen['total']) > 0) {
				$_row['member_id'] = $this->getMemberIdByParticipantId($_row['id']);
				$data[] = $_row;
			}
		}

		// Return survey data
		return $data;
	}

	/**
	 *  Method: getMemberIdByParticipantId
	 *  
	 *  Get member id
	 *  
	 *  @param string $id Participant id
	 * 
	 *  @return int Member id if it is, 0 otherwise
	 */
	public function getMemberIdByParticipantId($id) {
		$data = array();

		$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_MEMBER_TABLE ."` WHERE `participant_id` = %s", formatSQLString($id)));

		while($row = $this->DB->fetch_assoc($query)) {
			$data[] = $row;
		}

		if (count($data) > 0) {
			return $data[0]['member_id'];
		}

		return 0;
	}

	/**
	 *	Method: participantExsits
	 *
	 *	@param int $id Participant ID
	 *
	 *	@return boolean True if it does, false otherwise
	 */
	public function participantExists($id) {
		$data = $this->getParticipantById($id);
		return (!empty($data));
	}

	/**
 	 *	Method: addAnswer
	 *
	 *	This method adds answers of survey into database
	 *
	 *	@param mix $data Data of survey
	 *	
	 *	@return string|false participant id if successful, false otherwise.
	 */
	public function addAnswer($data) {

		// Current date time
		$currentDate = date("Y-m-d H:i:s");

		// Participant ID
		$participantId = md5(uniqid());

		while ($this->participantExists($participantId)) {
			$participantId = md5(uniqid());
		}

		// Extract Survey ID
		$surveyId = intval($data['survey_id']);

		// Create participant data
		/*$this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_PARTICIPANT_TABLE . "` (`id`, `survey_id`, `added_date`) VALUES (%s,%d,%s)", formatSQLString($participantId), intval($surveyId), formatSQLString($currentDate)));

		// Check if the survye is answered by member
		if (isset($data['member']) === true && empty($data['member']) === false) {
			$this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_MEMBER_TABLE . "` (`participant_id`, `member_id`) VALUES (%s,%d)", formatSQLString($participantId), intval($data['member'])));
		}*/

		// Open answers data
		if (isset($data['open']) === true && empty($data['open']) === false) {
			$openAnswers = $data['open'];
		} else {
			$openAnswers = array();
		}

		// Choice answers data
		if (isset($data['choice']) === true && empty($data['choice']) === false) {
			$choiceAnswers = $data['choice'];
		} else {
			$choiceAnswers = array();
		}

		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		if (empty($openAnswers) === false) {
			if (is_array($openAnswers) === true) {
				foreach($openAnswers as &$oa) {
					$oa['participant_id'] = $participantId;

					if (count($oa) == count($this->surveyOpenAnswerFields)) {
						foreach($oa as $k => $v) {
							if (in_array($k, $this->surveyOpenAnswerFields) === false) {
								$error = true;
								$this->errorMessages["addAnswer"][] = "The posted data '". $k ."' is not in '" . Survey::SURVEY_OPEN_ANSWER_TABLE ."'.";
							}
						}
					} else {
						$error = true;
						$this->errorMessages["addAnswer"][] = "The number of open answer posted data is not the same as the number of fields in table '". Survey::SURVEY_OPEN_ANSWER_TABLE ."'.";
					}
				}
			} else {
				$error = true;
				$this->errorMessages["addAnswer"][] = "Open Answer data is not array.";
			}
		}

		if (empty($choiceAnswers) === false) {
			if (is_array($choiceAnswers) === true) {
				foreach($choiceAnswers as &$ca) {
					$ca['participant_id'] = $participantId;

					if (count($ca) == count($this->surveyChoiceAnswerFields)) {
						foreach($ca as $k => $v) {
							if (in_array($k, $this->surveyChoiceAnswerFields) === false) {
								$error = true;
								$this->errorMessages["addAnswer"][] = "The posted data '". $k ."' is not in '" . Survey::SURVEY_CHOICE_ANSWER_TABLE ."'.";
							}
						}
					} else {
						$error = true;
						$this->errorMessages["addAnswer"][] = "The number of choice answer posted data is not the same as the number of fields in table '". Survey::SURVEY_CHOICE_ANSWER_TABLE ."'.";
					}
				}
			} else {
				$error = true;
				$this->errorMessages["addAnswer"][] = "Open Answer data is not array.";
			}
		}

		if ($error == true) {
			return false;
		}

		// -------------------------------------------------
		//	Add to database
		// -------------------------------------------------

		// Create participant data
        $this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_PARTICIPANT_TABLE . "` (`id`, `survey_id`, `added_date`) VALUES (%s,%d,%s)", formatSQLString($participantId), intval($surveyId), formatSQLString($currentDate)));

        // Check if the survye is answered by member
        if (isset($data['member']) === true && empty($data['member']) === false) {
                $this->DB->query(sprintf("INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_MEMBER_TABLE . "` (`participant_id`, `member_id`) VALUES (%s,%d)", formatSQLString($participantId), intval($data['member'])));
        }

		// Open Answers
		if (empty($openAnswers) === false) {
			foreach($openAnswers as $answer) {
				// Consturct column fields and values
				$columnFields = array();	// Store all fields in array
				$columnValues = array();	// Store all values in array

				foreach($this->surveyOpenAnswerFields as $field) {
					$columnFields[] = $field;
					$columnValues[] = formatSQLString($answer[$field]);
				}

				$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
				$_columnValues = implode(", ", $columnValues); // Values in string

				// Query statement
				$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE . "` ({$_columnFields}) VALUES ({$_columnValues})";

				// Add survey into database
				$this->DB->query($statement);
			}
		}

		// Choice Answers
		if (empty($choiceAnswers) === false) {
			foreach($choiceAnswers as $answer) {
				// Consturct column fields and values
				$columnFields = array();	// Store all fields in array
				$columnValues = array();	// Store all values in array

				foreach($this->surveyChoiceAnswerFields as $field) {
					$columnFields[] = $field;
					$columnValues[] = formatSQLString($answer[$field]);
				}

				$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
				$_columnValues = implode(", ", $columnValues); // Values in string

				// Query statement
				$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE . "` ({$_columnFields}) VALUES ({$_columnValues})";

				// Add survey into database
				$this->DB->query($statement);
			}
		}

		return $participantId;
	}

	/**
 	 *	Method: addToken
	 *
	 *	This method adds token of survey into database
	 *
	 *	@param mix $data Data of survey
	 *	
	 *	@return int|false Return token id if successful, false otherwise.
	 */
	public function addToken($data) {

		// Current date time
		$currentDate = date("Y-m-d H:i:s");

		// Token
		$token = md5(uniqid());

		while ($this->tokenExists($token)) {
			$token = md5(uniqid());
		}

		// Construct link
		$data['link'] = rtrim($data['link'], "/");
		$data['link'] = $data['link'] . "/survey/t/" . $token;

		// Added date
		$data['added_date'] = $currentDate;

		// Complete status
		$data['complete'] = 0;

		// Default complete date
		$data['complete_date'] = $currentDate;

		$data['token'] = $token;


		// -------------------------------------------------
		//	Validation
		// -------------------------------------------------

		// Check if data fields are all in database
		if (count($data) == count($this->surveyTokenFields)) {

			// Error flag to check if data validation has passed
			$error = false;

			foreach($data as $k => $v) {
				if (in_array($k, $this->surveyTokenFields) === false) {
					$error = true;
					$this->errorMessages["addToken"][] = "The posted data '". $k ."' is not in '" . Survey::SURVEY_TOKEN_TABLE ."'.";
				}
			}

			if ($error === true) {
				return false;
			}

		} else {

			// Error
			$this->errorMessages["addToken"][] = "The number of token posted data is not the same as the number of fields in table '". Survey::SURVEY_TOKEN_TABLE ."'.";
			return false;

		}

		// -------------------------------------------------
		//	Add to database
		// -------------------------------------------------

		// Consturct column fields and values
		$columnFields = array();	// Store all fields in array
		$columnValues = array();	// Store all values in array

		foreach($this->surveyTokenFields as $field) {
			$columnFields[] = $field;
			$columnValues[] = formatSQLString($data[$field]);
		}

		$_columnFields = "`" . implode("`,`", $columnFields) . "`";	// Fields in string
		$_columnValues = implode(", ", $columnValues); // Values in string

		// Query statement
		$statement = "INSERT INTO `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE . "` ({$_columnFields}) VALUES ({$_columnValues})";

		// Add survey into database
		$this->DB->query($statement);

		return $token;
	}

	/**
 	 *	Method: editToken
	 *
	 *	This method edits token data in database. Only edit status
	 *
	 *	@param mix $data Data of token. Id and status
	 *	
	 *	@return int|false Return survey id if successful, false otherwise.
	 */
	public function editToken($data) {

		// status
		$status = intval($data['status']);

		// id
		$id = $data['id'];

		$completeStatus = 0;
		$completeDate = date("Y-m-d H:i:s");

		// Check if there is complete status
		if (isset($data['complete']) === true && empty($data['complete']) === false) {
			$completeStatus = intval($data['complete']);
		}

		// -------------------------------------------------
		//	Update database
		// -------------------------------------------------

		// Add survey into database
		$this->DB->query(sprintf("UPDATE `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE . "` SET `status`=%d, `complete`=%d, `complete_date`=%s WHERE `token`=%s", intval($status), intval($completeStatus), formatSQLString($completeDate), formatSQLString($id)));

		// Return this survey id
		return $id;
	}

	/**
	 *	Method: getToken
	 *
	 *	Get token data
	 *
	 *	@param string $token Token
	 *
	 *	@return mix Return empty if no survey data in database, else return survey data.
	 */
	public function getToken($token) {

		// Get data from survey table
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE ."` WHERE `token` = %s", formatSQLString($token)), MYSQL_ASSOC);

		// Return survey data
		return $data;
	}

	/**
	 *	Method: getTokenByLink
	 *
	 *	Get token data
	 *
	 *	@param string $link Token Link
	 *
	 *	@return mix Return empty if no survey data in database, else return survey data.
	 */
	public function getTokenByLink($link) {

		// Get data from survey table
		$data = $this->DB->query_first(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE ."` WHERE `link` = %s", formatSQLString($link)), MYSQL_ASSOC);

		// Return survey data
		return $data;
	}

	/**
	 *	Method: getAllToken
	 *
	 *	Get all token from database
	 *
	 *	@param string $by     The column name to be ordered by. Default = 'id'
	 *	@param string $order  The order of result ASC or DESC. Default = 'DESC'.
	 *	@param int    $offset The beginning of result. Default = 0.
	 *	@param int 	  $limit  The limit of result. Default 0. 0 for no limit.
	 */
	public function getAllToken($by = 'id', $order = 'DESC', $offset = 0, $limit = 0) {

		// Query Statement
		$statement = "SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_TOKEN_TABLE ."` ORDER BY `{$by}` {$order}";

		if ($limit > 0) {
			$statement .= " LIMIT {$offset}, {$limit}";
		}

		// To store data result
		$data = array();

		// Get data from survey table
		$query = $this->DB->query($statement);

		while($row = $this->DB->fetch_assoc($query)) {
			$data[] = $row;
		}

		return $data;
	}

	/**
	 *	Method: tokenExists
	 *
	 *	Check if token exists in database
	 *
	 *	@param int $token Token
	 *
	 *	@return boolean Return true if exists, false otherwise.
	 */
	public function tokenExists($token) {
		$data = $this->getToken($token);
		return (!empty($data));
	}

	/**
	 *	Method: tokenLinkExists
	 *
	 *	Check if token link exists in database
	 *
	 *	@param int $link Token Link
	 *
	 *	@return boolean Return true if exists, false otherwise.
	 */
	public function tokenLinkExists($link) {
		$data = $this->getTokenByLink($link);
		return (!empty($data));
	}

	/**
	 *	Method: completeToken
	 *
	 *	Deactivate token when token survey is completed
	 *
	 *	@param int $token Token
	 */
	public function completeToken($token) {
		$this->editToken(array("id" => $token, "status" => 0, "complete" => 1, "complete_date" => date("Y-m-d H:i:s")));
	}

	/**
	 *	Method: generateReport
	 *
	 *	Generate survey report
	 *
	 *	@param int $survey_id ID of survey
	 *
	 *	@return mix Report of survey
	 */
	public function generateReport($survey_id) {

		// Make sure survey id passed here is int value
		$survey_id = intval($survey_id);

		// Construct report data structure
		$data = array();

		$questions = $this->getQuestionBySurveyId($survey_id);

		foreach($questions as $question) {
			$data[$question['id']] = array();

			if ($question['type'] == Survey::SURVEY_QUESTION_OPEN_ANSWER) {
				// Get number of answers for this choice of question
				$answers = $this->DB->query_first(sprintf("
					SELECT COUNT(*) as 'total' 
					FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE ."` 
					WHERE `survey_question_id` = %d", 
					intval($question['id'])), MYSQL_ASSOC);

				$data[$question['id']]['responses'] = $answers['total'];
			} else {
				$data[$question['id']]['answers'] = array();

				foreach($question['question_choice'] as $choice) {
					// Get number of answers for this choice of question
					$answers = $this->DB->query_first(sprintf("
						SELECT COUNT(*) as 'total' 
						FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE ."` 
						WHERE `survey_question_id` = %d AND `survey_question_choice_id` = %d", 
						intval($question['id']), 
						intval($choice['id'])), MYSQL_ASSOC);

					$data[$question['id']]['answers'][$choice['id']] = $answers['total'];
				}


			}
		}

		return $data;
	}

	/**
	 *	Method: getOpenAnswerResponse
	 *
	 *	@param int question_id
	 *
	 *	@return mix All responses
	 */
	public function getOpenAnswerResponse($question_id) {
		$question_id = intval($question_id);

		$data = array();

		$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_OPEN_ANSWER_TABLE ."` WHERE `survey_question_id` = %d", intval($question_id)));
		while($row = $this->DB->fetch_assoc($query)) {
			$_row = $row;
			$_row['member_id'] = $this->getMemberIdByParticipantId($_row['participant_id']);

			$data[] = array(
				'value' => htmlspecialchars($row['value']),
				'member_id' => $_row['member_id']);
		}

		return $data;
	}

	/**
	 *	Method: getChoiceAnswerResponse
	 *
	 *	@param int question_id
	 *	@param int choice_id
	 *
	 *	@return mix All responses
	 */
	public function getChoiceAnswerResponse($question_id, $choice_id) {
		$question_id = intval($question_id);
		$chocie_id = intval($choice_id);

		$data = array();

		$query = $this->DB->query(sprintf("SELECT * FROM `". Survey::DATABASE_NAME ."`.`". Survey::SURVEY_CHOICE_ANSWER_TABLE ."` WHERE `survey_question_id` = %d AND `survey_question_choice_id` = %d", intval($question_id), intval($choice_id)));
		while($row = $this->DB->fetch_assoc($query)) {
			$_row = $row;
			$_row['member_id'] = $this->getMemberIdByParticipantId($_row['participant_id']);

			$data[] = array(
				'value' => htmlspecialchars($row['choice_open_value']),
				'member_id' => $_row['member_id']);

		};

		return $data;
	}

	/**
	 *	Method: getErrorMessages
	 *
	 *	Get error messages
	 *
	 *	@return mix Error messages
	 */
	public function getErrorMessages() {
		return $this->errorMessages;
	}
}

?>
