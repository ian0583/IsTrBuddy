<?
/**
 * Class to generate a CSV file from given data
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_CSV
{
	private $_file;

	private $_filename;

	private $_writemode;

	public function __construct($filename, $willOverwrite = false)
	{
		if (!$filename)
		{
			throw new Exception('Filename not specified for CSV output file.');
		}

		$this->_filename = $filename;

		if ($willOverwrite)
		{
			$this->_writeMode = 'w+';
		}
		else
		{
			$this->_writeMode = 'a+';
		}

		$this->_file = fopen($this->_filename, $this->_writeMode);

		if ($this->_file == false)
		{
			throw new Exception('Error opening filename provided for CSV output. ' . $this->_filename);
		}
	}

	public function writeRow($row)
	{
		fputcsv($this->_file, $row, ',', '"');
	}

	public function writeData($data)
	{
		foreach ($data as $row)
		{
			fputcsv($this->_file, $row, ',', '"');
		}
	}

	public function __destruct()
	{
		fclose($this->_file);
	}
}