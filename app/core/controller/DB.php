<?php
namespace core\controller;

defined('APP_OWNER') or exit('No direct script access allowed');

// $this->_mysqli->real_connect( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE );
class DB {
	private $connection;

    private	$query;

    private	$show_errors = true;

    private	$query_closed = true;

    public  $query_count = 0;

    public  static $instance;

    public  static $error = [];

    public  $affected_rows = 0;

	public  static function getInstance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new DB();
		}

		return self::$instance;
	}

	public static function getTableColumns( string $tableName ) : ?array
	{
		$query = "SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ?";

		if ( ! $column = self::getInstance()->query($query, $tableName )?->fetchAll() ) {
			return null;
		} else {
			return $column;
		}
	}

	private function __construct() {
        error_reporting(E_ALL);

 		// PDO
		/* 
		$db_options = [
			'MYSQLI_OPT_INT_AND_FLOAT_NATIVE' => true
		];
		$dsn = "mysql:host=" .HOSTNAME . ";dbname=" . DATABASE;

		try {
			$this->connection = new PDO($dsn, USERNAME, PASSWORD, $db_options);
		} catch (PDOException $e) {
			die('{"errors":["ERROR!: "' . $e->getMessage() . '"]}');
		} 
		*/

		// MySQLi
		$charset = 'utf8';

        if ( function_exists( 'mysqli_init' ) && extension_loaded('mysqli') ) {
            $this->connection = mysqli_init();
        } else {
            trigger_error("Pastikan extension mySQLi di-install dan enabled.");
        }

        if ( !$this->connection->real_connect( DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE ) ){
			trigger_error( $this->connection->connect_error );
		}

		$this->connection->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

        $this->connection->set_charset($charset);
	}

	/**
	 * @param $query, $where1, $whereN
	 * @param $query, array( $w1, .. $wN)
	 * @return $this 
	 */
	public function query($query) 
    {
		// if ( ! $this->query_closed ) { @$this->query->close(); }

		try {
			$this->query = $this->connection->prepare($query);

			if (func_num_args() > 1) {
				$x        = func_get_args();
				$args     = array_slice($x, 1);
				$types    = '';
				$args_ref = array();

				foreach ($args as $k => &$arg) {
					if (is_array($args[$k])) {
						foreach ($args[$k] as $j => &$a) {
							$types .= $this->_gettype($args[$k][$j]);

							$args_ref[] = &$a;
						}
					} else {
						$types .= $this->_gettype($args[$k]);

						$args_ref[] = &$arg;
					}
				}

				array_unshift($args_ref, $types);

				call_user_func_array(array($this->query, 'bind_param'), $args_ref);
			}

			$this->query->execute();

			$this->affected_rows = $this->connection->affected_rows;

			$this->query_closed = FALSE;

			$this->query_count++;
		} catch (\mysqli_sql_exception $e) {
			App::responGagal('E1:' . $e->getMessage());
		}
		
		catch( \Exception $e ) {
			App::responGagal('E2:' . $e->getMessage());
		}

		return $this;
	}

	public function ping()
	{
		if ( $this->connection->ping() ) return true;

		return false;
	}

	/**
	 * Query for multiple rows
	 * @param $callback funtion()
	 */
	public function fetchAll($callback = null)
    {
		if (!$this->checkError()) return false;

        $params = array();
		$row    = array();
		$meta   = $this->query->result_metadata();

        while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}

        call_user_func_array(array($this->query, 'bind_result'), $params);

        $result = array();

		$_result = $this->query->get_result();

		// while ($this->query->fetch()) {
		while ($row = $_result->fetch_array(MYSQLI_ASSOC)) {
			$r = array();
			// print_r($row);

            foreach ($row as $key => $val) {
				$r[$key] = $val;
			}

            if ($callback != null && is_callable($callback)) {
				$value = call_user_func($callback, $r);
				if ($value == 'break') break;
			} else {
				$result[] = $r;
			}
		}		

        $this->query->close();
		$this->query_closed = TRUE;

        if (empty($result)) return 0;
		else return $result;
	}

	public function prepare( string $query )
	{
		return $this->connection->prepare($query);		
	}

	public function beginTransaction() 
    {
		$this->connection->begin_transaction();
		// return $this;
	}

	public function commit()
    {
		$this->connection->commit();
		// return $this;
	}

	public function rollback()
    {
		$this->connection->rollback();
		// return $this;
	}

	/**
	 * Query for 1 row
	 */
	public function fetchArray()
    {
		if (!$this->checkError()) return false;

        $params = array();
		$row = array();
		$meta = $this->query->result_metadata();

        while ($field = $meta->fetch_field()) {
			$params[] = &$row[$field->name];
		}

        call_user_func_array(array($this->query, 'bind_result'), $params);

        $result = array();

		$_result = $this->query->get_result();
        
		// while ($this->query->fetch()) {
		while ($row = $_result->fetch_array(MYSQLI_ASSOC)) {
			foreach ($row as $key => $val) {
				$result[$key] = $val;
			}
		}

		$this->query->close();
		$this->query_closed = TRUE;

        return $result;
	}

	public function close()
    {
		return $this->connection->close();
	}

	public function numRows()
    {
		if (!$this->checkError()) return false;

		$this->query->store_result();

        return $this->query->num_rows;
	}

	protected function checkError()
    {
		if ($this->connection->errno) {
			return false;
		} else {
			return true;
		}
	}

	public function affectedRows()
    {
		if (!$this->checkError()) return false;
		return $this->connection->affected_rows;
	}

	public function lastInsertID()
    {
		return $this->connection->insert_id;
	}

	public function error($error)
    {
		if ($this->show_errors) {
			self::$error = $error;
		}
	}

	private function _gettype($var)
    {
		if (is_string($var)) return 's';
		if (is_float($var)) return 'd';
		if (is_int($var)) return 'i';
		return 'b';
	}

	private static function getCallerInfo()
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

		// Check if there is a caller
		if (isset($trace[1])) {
			$caller = $trace[1];

			// Check if the caller is a method in a class
			if (isset($caller['class']) && isset($caller['function'])) {
				$callingClass = $caller['class'];
				$callingMethod = $caller['function'];
				return "Called method '$callingMethod' from class '$callingClass'.";
			} else {
				return "Called from outside a class or method.";
			}
		} else {
			return "";
		}
	}

}

/**
 * CONTOH PEMAKAIAN!
 */

/* 
// QUERY
$dodol = DB::getInstance()->query("SELECT * FROM z_users WHERE id = ? ", 1 )->fetchAll();
if  ( $dodol !== false ) {
	echo "TRUES\n";
	// App::dump( $dodol );
	App::dump( $dodol );
} else {
	echo "FALES\n";
	App::dump( DB::$error);
}
*/

/* 
// INSERT
$dodol = DB::getInstance()->query("INSERT INTO test1 SET namas = ? ", "COBA1" )->affectedRows();
if ( $dodol !== false ) { echo "TRUEEE\n"; App::dump( $dodol ); }
else { echo "FALES\n" ; App::dump( DB::$error ); }
 */

/*
// Transaction
try {

	$db = DB::getInstance();
	$db->beginTransaction();
	$stmt1 = DB::getInstance()->query("INSERT INTO test1 SET nama = ?", "COBA4")->affectedRows();
	$stmt2 = DB::getInstance()->query("INSERT INTO test2 SET namas = ?", "COBA2")->affectedRows();
	if ( $stmt1 !== false && $stmt2 !== false ) { echo "TRUEEE\n"; $db->commit(); }
	else { echo "FALESSS\n"; App::dump(DB::$error); $db->rollback(); }

}
catch ( mysqli_sql_exception $e ) {

	echo "ADA CATCH ERROR - ROLLBACK!\n";
	App::dump( $e->getMessage() );
	$db->rollback();

}
catch ( Exception $e ) {

	App::dump( $e->getMessage() );
	$db->rollback();

}
*/

?>