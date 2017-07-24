<?php

class EKEQuery {

  /**
   * @var integer
   */
  private $limit, $max, $min;

  /**
   * @var string
   */
  private $query, $table, $type;

  /**
   * @var matrix
   */
  private $parameters, $fields;

  /**
   * @var array
   */
  private $last_result;

  /**
   * @var array
   */
  private $par_types = ['i','d','s','b'];

  /**
   * @var int
   */
  private $affected_rows, $result_rows;

  /**
   * @var matrix
   */
  private $parameters_whitelist;

  /**
   * @var boolean
   */
  private $use_selection = false;

  public function __construct($parameters_whitelist) {

    $this->parameters_whitelist = $parameters_whitelist;

  }

  /**
   * Execute the query
   *
   * @param MySQLi db connection
   * @return query result
   */
  public function run($db) {

    $result = [];

    if ($stmt = $db->prepare($this->query)) {

        // evaluate parameters type
        $stmt = $this->evaluateParameters($stmt);

        // run the query
        $stmt->execute();
        $db_result = $stmt->get_result();

        // store affected rows & number of result rows
        $this->affected_rows = $stmt->affected_rows;
        $this->result_rows = $db_result->num_rows;

        if ($db_result) {

          // retrieve real result data (array)
          $result = $db_result->fetch_assoc();

        }

        $stmt->close();

    }

    // keep track of last query result
    $this->last_result = $result;

    return $result;

  }

  /**
   * Evaluate and bind parameters
   * (one way binding)
   *
   * @param object prepared statement
   * @return object prepared statement
   */
  private function evaluateParameters($stmt) {

    if (is_null($this->parameters) || empty($this->parameters)) {

      return $stmt;

    }

    // Generate types & values to bind
    $types = '';
    $values = '';

    foreach ($this->parameters as $par => $value) {

      $types .= $parameters_whitelist[$par];
      $values .= ',' . $value;

    }

    // Execute parameters binding
    $stmt->{'bind_param(' . $types . $values . ')'};

    return $stmt;

  }

  /**
   * Query builder
   *
   * @return EKEQuery 'this'
   */
  public function build() {

    // do not start execution if values are not set
    if ($this->type === null ||
        $this->fields === null ||
        $this->table === null) {

      throw new Exception("bad query request", 1);

    }

    // switch according to query type
    switch ($this->type) {
      case 'select':

        $this->query = "SELECT " . $this->stringifyFields() . " FROM " . $this->table;

        // add requested selection
        if ($this->use_selection) {

          // stringify parameters
          // ...

          $this->query = " WHERE " . $parameters_string;

        }

        break;

      case 'insert':

        // Tried to create insert query
        $this->query = "INSERT INTO ". $this->table ."(". $this->stringifyFields() .") VALUES (". $this->parameters_string;

        break;

      case 'update':

        // Tried to create update query
        $this->query = "UPDATE ". $this->table ." SET ". $this->stringifyFields() ." WHERE ". $this->parameters_string;

        break;

      default:
        throw new Exception("bad db request", 1);
        break;
    }

    // Append properties
    foreach (['limit','max','min'] as $property) {

      $this->append($property);

    }

    // End query
    $this->query .= ';';

    return $this;

  }

  /**
   * Direct dirty query
   * Still parametized query support
   *
   * @return array query result
   */
  public function dirty($db, $query, $options) {

    $result = [];

    if ($stmt = $db->prepare($query)) {

      // Execute parameters binding
      if ($options !== null) {

        $this->bind_parameters($stmt, $options['types'], $options['params']);

      }

      // run the query
      $stmt->execute();
      $db_result = $stmt->get_result();

      // store affected rows
      $this->affected_rows = $stmt->affected_rows;

      if ($db_result) {

        // store number of result rows
        $this->result_rows = $db_result->num_rows;

        // retrieve real result data (array)
        $result = $db_result->fetch_all(MYSQLI_ASSOC);

      }

      $stmt->close();

    }

    // keep track of last query result
    $this->last_result = $result;

    return $result;

  }


  /**
   * Bind values for prepared statement
   * @todo verify usefulness of method makeValuesReferenced
   */
  private function bind_parameters(&$stmt, $types, $params) {

    $options = array_merge($types, $params);
    return call_user_func_array([&$stmt, 'bind_param'], $this->makeValuesReferenced($options));

  }
  private function makeValuesReferenced($arr) {

    $refs = [];

    foreach($arr as $key => $value) {

      $refs[$key] = &$arr[$key];

    }

    return $refs;

  }


  /**
   * Helper function to add fields in the query
   *
   * @return string
   */
  private function stringifyFields() {

    $fields = '';
    $n = count($this->fields);
    $i = 1;

    foreach ($this->fields as $value) {
      $fields .= $value;
      if ($i !== $n) { $fields .= ','; }
      $i++;
    }

    return $fields;

  }

  /**
   * Validate parameters when they are set
   *
   * @param array parameters
   * @return boolean
   */
  private function validateParameters($params) {

    $is_valid = false;

    // ...

    return $is_valid;

  }

  /**
   * Validate fields when they are set
   *
   * @param array fields
   * @return boolean
   */
  private function validateFields($fields) {

    $is_valid = false;

    foreach ($fields as $field) {

      foreach ($this->parameters_whitelist as $key => $value) {

        if ($key == $field) {

          $is_valid = true;
          break;

        }

      }

    }

    return $is_valid;

  }

  /**
   * Query Builder helper
   *
   * @param string name of the property
   * @return void
   */
  private function append($property) {

    $p_ = $this->{$property};

    if ($p_ !== null) {

      $this->query .= $p_;

    }

  }

  /**
   * Return the query
   *
   * @return string
   */
  public function toString() {

    return $this->getQuery();

  }

  /**
   * SETTERS
   */

  // Set a custom query
  public function setQuery($query) {

    $this->query = $query;

  }

  public function setLimit($n) {

    $this->limit = $n;

  }

  public function setMax($n) {

    $this->max = $n;

  }

  public function setMin($n) {

    $this->min = $n;

  }

  public function setUseSelection($s) {

    $this->use_selection = $s;

  }

  public function setParameters($p) {

    if ($this->validateParameters($p)) {

      $this->parameters = $p;

    }

  }

  public function setFields($f) {

    if ($this->validateFields($f)) {

      $this->fields = $f;

    }

  }

  public function setType($t) {

    $this->type = $t;

  }

  public function setTable($t) {

    $this->table = $t;

  }

  /**
   * GETTERS
   */

  public function getQuery() {

    return $this->query;

  }

  public function getLimit() {

    return $this->limit;

  }

  public function getMax() {

    return $this->max;

  }

  public function getMin() {

    return $this->min;

  }

  public function getLastResult() {

    return $this->last_result;

  }

  public function getResultNum() {

    return $this->result_rows;

  }

  public function getAffectedNum() {

    return $this->affected_rows;

  }

  public function getUseSelection() {

    return $this->user_selection;

  }

  public function getParameters() {

    return $this->parameters;

  }

  public function getFields() {

    return $this->fields;

  }

  public function getType() {

    return $this->type;

  }

  public function getTable() {

    return $this->table;

  }


  /*
   *
   *  METHODS THAT I'VE BEEN ADDING AND THAT I'M NOT SURE ABOUT
   *  CHECK THE FOLLOWING METHODS, THEY MAY BE USEFUL
   *
   */

  /**
   * This method takes as parameter an array of arrays. There is a master table (aka base table) which is the one on which all the joins will converge. The remaining of the array are simple arrays containing the table to join with and the columns on which the join is performed
   EXAMPLE: master => agrishop__products
                 1 => agrishop__categories, category, id
                 2 => agrishop__areas, area, id
   */
  public function Join($tables = null, $master_table = null) {

    $master_table="agrishop__products";
    $tables = [];
    array_push($tables, array("table"=>"agrishop__areas","base_column"=>"area","column"=>"id"));
    array_push($tables, array("table"=>"agrishop__categories","base_column"=>"category","column"=>"id"));

    $join = $master_table ." ";

    foreach ($tables as $joining) {

      $join .= "JOIN ". $joining["table"] ." ON ". $master_table .".". $joining["base_column"] ."=". $joining["table"] .".". $joining["column"] ." ";

    }

    echo "\n\n".$join."\n\n";

  }

  /**
   * @param String name of the table
   *
   */
  public function addJOINBase($table) {

    $this->master_table=$table;
  }

  /**
   * @param array containing a table name, the column name of the base table (defined via the above method) and the column of the current table on which the join will be perfomed
   */
  public function addJOIN($table) {

    // TODO: Check that the table exists and that the column exists (for both the master table and the secondary table, aka this one)
    $this->array_push($join_tables, $table);
  }

}
