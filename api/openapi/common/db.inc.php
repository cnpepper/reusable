<?php
/**
 * SQL functions library for PHP5
 *
 * @author David Cramer <dcramer@gmail.com
 * @package phpdatabase
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright 2008 David Cramer
 * @version 0.1
 */

class MySQLdb extends Database
{
	/**
	* Connect to the MySQL Server
	*
	* @return boolean
	*/
	function connect($force=false)
	{
		parent::connect($force);
		if (!$this->__password)
		{
			$this->link_id = @mysqli_connect($this->__server, $this->__username);
		}
		else
		{
			$this->link_id = @mysqli_connect($this->__server, $this->__username, $this->__password);
		}
		
		if (!$this->link_id)
		{
			$this->error('Connection to database failed' . mysqli_connect_error());
			return false;
		}
		
		if(!$this->select_db($this->__database))
		{
			$this->close();
			return false;
		}
		
		return true;
	}

	/**
	* Selects the Database
	*
	* @param string $database
	* @return boolean
	*/
	function select_db($database)
	{
		parent::select_db($database);
		if(empty($this->__database)) return true;
		if (@mysqli_select_db($this->link_id, $this->__database))
			return true;
		$this->error('Cannot use database ' . $this->__database);
		return false;
	}

	function execute($sql)
	{
		parent::execute($sql);
		
		$result = mysqli_query($this->link_id, $sql);
		if($result) return $result;
		$this->error('Invalid SQL1: ' . $sql);
		
		return false;
	}

	function multi_query($sql)
	{
		$params = func_get_args();
		$params = array_slice($params, 1);
		
		if (count($params))
		{
			foreach ($params as $key=>$value)
				$params[$key] = $this->prepare_param($value);
			$sql = vsprintf($sql, $params) or $this->error('Invalid sprintf: ' . $sql ."\n".'Arguments: '. implode(', ', $params));
		}
		
		parent::execute($sql);
		
		if(mysqli_multi_query($this->link_id, $sql))
		{
			$result = mysqli_use_result($this->link_id);
			if($result)
				return $result;
			return true;
		}
		
		$this->error_num = mysqli_errno($this->link_id);
		if($this->error_num)
		{
			$this->error_desc = mysqli_error($this->link_id);
			logx("SQL ERROR: $sql code {$this->error_num} msg {$this->error_desc}", 'error');
		}
		
		while(@mysqli_more_results($this->link_id))
		{
			if(@mysqli_next_result($this->link_id))
			{
				$result = @mysqli_store_result($this->link_id);
				if($result)
					@mysqli_free_result($result);
			}
		}
		
		return NULL;
	}
	
	function next_result($id)
	{
		parent::free_result($id);
		
		@mysqli_free_result($id);
		
		if(@mysqli_more_results($this->link_id))
		{
			if(@mysqli_next_result($this->link_id))
			{
				$result = @mysqli_store_result($this->link_id);
				if($result)
					return $result;
			}
		}
		
		return NULL;
	}
	
	function fetch_array($id)
	{
		parent::fetch_array($id);
		return mysqli_fetch_array($id, MYSQLI_ASSOC);
	}

	function fetch_object($id)
	{
		parent::fetch_array($id);
		return mysqli_fetch_object($id);
	}

	function free_result($id)
	{
		parent::free_result($id);
		
		if($id) @mysqli_free_result($id);
		while(@mysqli_more_results($this->link_id))
		{
			if(@mysqli_next_result($this->link_id))
			{
				$result = @mysqli_store_result($this->link_id);
				if($result)
					@mysqli_free_result($result);
			}
			else
			{
				break;
			}
		}
	}

	function num_rows($id)
	{
		parent::num_rows($id);
		return mysqli_num_rows($id);
	}
	
	function affected_rows()
	{
		return mysqli_affected_rows($this->link_id);
	}
	
	function num_fields($id)
	{
		parent::num_fields($id);
		return mysqli_num_fields($id);
	}

	function field_name($id, $num)
	{
		parent::field_name($id);
		return mysqli_fetch_field_direct($id, $num);
	}

	function insert_id()
	{
		parent::insert_id();
		return mysqli_insert_id($this->link_id);
	}

	function close()
	{
		parent::close();
		return mysqli_close($this->link_id);
	}

	function escape_string($string)
	{
		parent::escape_string($string);
		return mysqli_real_escape_string($this->link_id, $string);
	}

	function ping()
	{
		return mysqli_ping($this->link_id);
	}

	function error($message)
	{
		if($this->link_id)
		{
			$this->error_num = mysqli_errno($this->link_id);
			$this->error_desc = mysqli_error($this->link_id);
			
			$message .= "\t code: " . $this->error_num . ' msg:' . $this->error_desc;
		}
		parent::error($message);
	}

}

class PostgreSQLdb extends Database
{
	/**
	* Connect to the PostgreSQL Server
	*
	* @return boolean
	*/
	function connect($force=false)
	{
		parent::connect();
		$params = array(
			'host='.$this->__server,
			'user='.$this->__username,
			'dbname='.$this->__database,
		);
		if ($this->__password)
			$params[] = 'password='.$this->__password;
			
		if ($this->__pconnect == 1)
			$this->link_id = @pg_connect(implode(' ', $params));
		else
			$this->link_id = @pg_pconnect(implode(' ', $params));
		if (!$this->link_id)
		{
			$this->error('Connection to database failed');
			return false;
		}
		return true;
	}

	/**
	* Selects the Database
	*
	* @param string $database
	* @return boolean
	*/
	function select_db($database)
	{
		parent::select_db($database);
		pg_close($this->link_id);
		$this->__database = $database;
		$this->connect();
		return true;
	}

	function execute($sql)
	{
		parent::execute($sql);
		$result_id = pg_query($this->link_id, $sql);
		if (!$result_id) $this->error('Invalid SQL: '.$sql);
		return $result_id;
	}

	function fetch_array($id)
	{
		parent::fetch_array($id);
		return @pg_fetch_array($id);
	}

	function free_result($id)
	{
		parent::free_result($id);
		return @pg_free_result($id);
	}

	function num_rows($id)
	{
		parent::num_rows($id);
		return pg_num_rows($id);
	}

	function num_fields($id)
	{
		parent::num_fields($id);
		return pg_num_fields($id);
	}

	function field_name($id, $num)
	{
		parent::field_name($id, $num);
		return pg_field_name($id, $num);
	}

	function insert_id()
	{
		throw new FatalError("This method does not work under PostgreSQL");
	}

	function close()
	{
		parent::close();
		return pg_close($this->link_id);
	}


	function escape_string($string)
	{
		parent::escape_string($string);
		return pg_escape_string($string);
	}

	function error($message)
	{
		if ($this->link_id)
		{
			$message .= "\t" . pg_last_error($this->link_id);
		}
		parent::error($message);
	}
}

/**
* The base Database class -- this must be extended.
* <pre>
*     $db = new db('localhost', 'root', 'mysupersecretpassword');
*     $db->query_result("SELECT field FROM database WHERE name = %s", 'my name');
* </pre>
* @abstract
*/
abstract class Database
{
	/**
	* Link ID
	*/
	public $link_id = 0;

	//public $queries = array();

	public $debug = false;

	protected $__server;
	protected $__user;
	protected $__pass;
	protected $__pconnect;
	protected $__database;

	/**
	* Class constructor
	* <pre>
	*     $db = new db('localhost', 'root', 'mysupersecretpassword');
	* </pre>
	*
	* @param string $server MySQL host or path.
	* @param string $username Username.
	* @param string $password Password.
	* @param boolean $pconnect Use persistent connections.
	* @param string $database Database name.
	*/
	function __construct($server, $username, $password='', $pconnect=false, $database='')
	{
		$this->__server = $server;
		$this->__username = $username;
		$this->__password = $password;
		$this->__pconnect = $pconnect;
		$this->__database = $database;
	}

	function get_server()
	{
		return $this->__server;
	}

	/**
	* Connect to the MySQL Server.
	*
	* @abstract
	* @return boolean
	*/
	function connect($force=false)
	{
		if ($this->link_id && !$force) return true;
	}

	/**
	* Selects the Database
	*
	* @abstract
	* @param string $database
	* @return boolean
	*/
	function select_db($database)
	{
		if (!$this->link_id) return false;
		
		$this->__database = $database;
		return true;
	}

	/**
	* @abstract
	*/
	function execute($sql)
	{
		if (!$this->link_id)
			$this->connect();
	}

	/**
	* Queries the SQL server. Allows you to use sprintf-like
	* format and automatically escapes variables.
	* <pre>
	*    $db->query('SELECT %s FROM %s WHERE myfield = %s, 'field_name', 'table_name', 5);
	* </pre>
	*
	* @param string $string
	* @param {string | integer} $param1 first parameter
	* @param {string | integer} $param2 second parameter
	* @param {string | integer} $param3 ... 
	* @return unknown Resource ID
	*/
	function query($string, $params=null)
	{
		if (!is_array($params))
		{
			$params = func_get_args();
			$params = array_slice($params, 1);
		}
		if (count($params))
		{
			foreach ($params as $key=>$value)
			{
				$params[$key] = $this->prepare_param($value);
			}
			
			$string = vsprintf($string, $params) or 
				$this->error('Invalid sprintf: ' . $string ."\n".'Arguments: '. implode(', ', $params));
		}
		
		$timing = microtime(true);
		$id = $this->execute($string, $this->link_id);
		$timing = (int)((microtime(true)-$timing)*1000);
		
		$this->lastquery = $string;

		return $id;
	}

	/**
	* Internal handler for parameters. Returns an
	* escaped parameter.
	*
	* @param {string | integer} $param
	* @return {string | integer} Escaped parameter.
	*/
	function prepare_param($param)
	{
		if ($param === null) return 'NULL';
		elseif (is_integer($param)) return $param;
		elseif (is_bool($param)) return $param ? 1 : 0;
		return "'".$this->escape_string($param)."'";
	}

	/**
	* Returns an array from the given Result ID
	*
	* @abstract
	* @param integer $id
	* @return unknown
	*/
	function fetch_array($id) { }

	/**
	* Frees a Result from memory
	*
	* @param integer $id
	* @return unknown
	*/
	function free_result($id) { }

	/**
	* Queries the SQL server, returning a single row
	*
	* @param string $string SQL Query.
	* @return unknown
	*/
	function query_result($string)
	{
		$params = func_get_args();
		$id = call_user_func_array(array($this, 'query'), $params);
		$result = $this->fetch_array($id);
		$this->free_result($id);
		return $result;
	}

	/**
	* Queries the SQL server, returning the defined (or first) select value
	* from the first row.
	*
	* @param string $string SQL Query.
	* @param string $value Optional select field name to return.
	* @return unknown
	*/
	function query_result_single($string, $def = false)
	{
		$params = func_get_args();
		array_splice($params, 1, 1);
		
		$id = call_user_func_array(array($this, 'query'), $params);
		if(!$id) return FALSE;
		
		$result = $this->fetch_array($id);
		$this->free_result($id);
		
		if (!$result) return $def;
		
		return array_shift($result);
	}

	/**
	* Returns the number of rows
	*
	* @abstract
	* @param unknown $id
	* @return unknown
	*/
	function num_rows($id) { }

	/**
	* Returns the number of fields
	*
	* @abstract
	* @param integer $id
	* @return unknown
	*/
	function num_fields($id) { }

	/**
	* Returns the field name
	*
	* @abstract
	* @param integer $id
	* @param integer $num
	* @return unknown
	*/
	function field_name($id, $num) { }

	/**
	* Returns the Row ID of the last inserted row
	*
	* @abstract
	* @return integer
	*/
	function insert_id() { }

	/**
	* Close the connection
	*
	* @abstract
	* @return boolean
	*/
	function close() { }

	/**
	* Runs mysql_escape_string to stop SQL injection
	*
	* @abstract
	* @param string $string
	* @return string
	*/
	function escape_string($string) { }

	/**
	* Output the error
	*
	* @param string $string
	*/
	function error($string)
	{
		logx("SQL ERROR: $string", 'error');
	}

	function error_code()
	{
		return $this->error_num;
	}

	function error_msg()
	{
		return @$this->error_desc;
	}
}

?>
