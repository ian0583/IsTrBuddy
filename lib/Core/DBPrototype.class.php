<?
/**
 * Class for Automatic Class Generation for Database Tables
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_DBPrototype
{
	public static function _data()
	{
		require APP_CONF . 'DB.conf.php';

		$db = new Core_DB();
		$table = substr(get_class(), 3);

		// check if table exists
		$dataset = $db->dataset;
		$exists = false;
		foreach ($dataset as $tableName => $data)
		{
			if ($table == $tableName)
			{
				$exists = true;
				if (!$data['primary'])
				{
					throw new Exception("Primary Key for the table does not exist.<br/>\nDatabase: " . $DATABASES[DB_MAIN]['db'] . "<br/>\nTable: " . $table);
				}
				$primary = $data['primary'];
			}
		}
		if (!$exists)
		{
			throw new Exception("Table does not exist.<br/>\nDatabase: " . $DATABASES[DB_MAIN]['db'] . "<br/>\nTable: " . $table);
		}

		return array($db, $table, $primary);
	}

	public static function bySql($where = '', $columns = '*')
	{
		list($db, $table, $primary) = Core_DBPrototype::_data();

		if ($where != '')
		{
			$where = ' where ' . $where;
		}

		return $db->getArray("select $columns from " . $table . " " . $where, array());
	}

	public static function byPrimary($id)
	{
		list($db, $table, $primary) = Core_DBPrototype::_data();

		$query = "select * from `" . $table . "` where `" . $primary . "` = '$id'";
		return $db->getRow($query, array());
	}

	public static function byField($data = array())
	{
		list($db, $table, $primary) = Core_DBPrototype::_data();

		if (count($data) < 1)
		{
			Core_DBPrototype::bySql();
		}

		$query = "select * from `" . $table ."` ";

		$where = "where ";
		foreach ($data as $field => $value)
		{
			if (is_array($value))
			{
				// value should be checked as 'in'
				$where .= "`$field` in (";
				foreach ($value as $in_value)
				{
					$where .= "'$in_value', ";
				}
				$where = substr($where, 0, strlen($where) - 2) . ") and ";
			}
			elseif (strpos($value, '%') !== false)
			{
				// value should be checked as 'like'
				$where .= "`$field` like '$value' and ";
			}
			else
			{
				// value should be checked as '='
				$where .= "`$field` = '$value' and ";
			}
		}
		$where = substr($where, 0, strlen($where) - 4);

		$query .= $where;

		return $db->getArray($query);
	}

	public static function update($data)
	{
		list($db, $table, $primary) = Core_DBPrototype::_data();

		if (!isset($data[$primary]))
		{
			$db->autoexecute($table, $data);
		}
		else
		{
			$db->autoexecute($table, $data, array($primary => $data[$primary]));
		}
	}

	public static function delete($data = array())
	{
		list($db, $table, $primary) = Core_DBPrototype::_data();

		if (count($data) < 1)
		{
			return false;
		}

		$query = "delete from `" . $table ."` ";

		$where = "where ";
		foreach ($data as $field => $value)
		{
			if (is_array($value))
			{
				// value should be checked as 'in'
				$where .= "`$field` in (";
				foreach ($value as $in_value)
				{
					$where .= "'$in_value', ";
				}
				$where = substr($where, 0, strlen($where) - 2) . ") and ";
			}
			elseif (strpos($value, '%') !== false)
			{
				// value should be checked as 'like'
				$where .= "`$field` like '$value' and ";
			}
			else
			{
				// value should be checked as '='
				$where .= "`$field` = '$value' and ";
			}
		}
		$where = substr($where, 0, strlen($where) - 4);

		$query .= $where;

		$db->execute($query);

		return true;
	}

}