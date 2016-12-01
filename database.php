<?php


/*
 * Database is a class used to abstract the Model architecture
 */

class MDB
{

     private $connection = array();
     private $query;
     private $result;
     private $current;
     private $previous = '';

     function __construct()
     {

     }

     function __destruct()
     {
     
     }

     public function connect($name, $host, $username, $password)
     {

          if ($this->connection[$name] = mysql_connect($host, $username, $password))
               return true;
          else
               return false;

     }

     public function selectConnection($name)
     {
		if (array_key_exists($name, $this->connection))
		{
		     $this->previous = $this->previous;
			$this->current = $name;
			return true;
		}
		else
			return false;

	}

	public function selectPreviousConnection()
	{
	     $previous = $this->previous;
		$this->previous = $this->current;
		$this->current = $previous;
	}

     public function selectDatabase($database)
     {
          if (mysql_select_db($database, $this->connection[$this->current]))
               return true;
          else
               return false;
     }

     public function query($query)
     {
          $this->query = $query;
          $result = mysql_query($query, $this->connection[$this->current]);

          if ($result)
          {
               $this->result = $result;
               return $result;
          }
          else
               return false;
     }

     public function singleRowQuery($query)
     {
          $this->query = $query;
          $result = mysql_query($query, $this->connection[$this->current]);

          if ($result)
          {
               $this->result = $result;
               return mysql_fetch_assoc($result);
          }
          else
               return false;
     }

     public function runLastQuery()
     {
          $query = $this->query;
          $result = mysql_query($query, $this->connection[$this->current]);

          if ($result)
          {
               $this->result = $result;
               return $result;
          }
          else
               return false;
     }

     public function getResult()
     {
          return $this->result;
     }

     public function getArray()
     {
          $array = array();

          if ($this->result)
          {
               while ($row = mysql_fetch_assoc($this->result))
               {
                    array_push($array, $row);
               }

               return $array;
          }

          else
               return false;
     }

     public function getAffectedRows()
     {
          return mysql_affected_rows($this->connection[$this->current]);
     }

     public function getNumRows()
     {
		return mysql_num_rows($this->result);
	}

	public function getErrors()
	{
	     if (mysql_errno($this->connection[$this->current]) || mysql_error($this->connection[$this->current]))
		{
			return array(
				'mysql_errno' => mysql_errno($this->connection[$this->current]),
				'mysql_error' => mysql_error($this->connection[$this->current]));
		}
		else
		     return array();
	}

     public function disconnect()
     {
          if (mysql_close($this->connection[$this->current]))
               return true;
          else
               return false;
     }

     public function prepare($data)
     {
		if (!empty($data))
		{
	          if (get_magic_quotes_gpc())
	               $data = stripslashes($data);

	          return mysql_real_escape_string(trim($data), $this->connection[$this->current]);
          }

          return false;
     }

	public function freeLastResult()
	{
		mysql_free_result($this->result);
	}
}
