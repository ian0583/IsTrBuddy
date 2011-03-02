<?
/**
 * Prograss Bar Abstraction Class
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_Progress
{
	/**
	 * Progress Bar Object
	 *
	 * @var Zend_ProgressBar
	 */
	private $_progressBar;

	/**
	 * Class Constructor that sets the parameters for the progress bar
	 *
	 * @param int $start
	 * @param int $end
	 */
	public function __construct($start = 0, $end = 100)
	{
		$adapter = new Zend_ProgressBar_Adapter_Console();
		$this->_progressBar = new Zend_ProgressBar($adapter, $start, $end);
	}

	/**
	 * Updates the progress bar to a certain value
	 *
	 * @param int $value
	 */
	public function update($value)
	{
		$this->_progressBar->update($value);
	}

	/**
	 * Iterates the progress bar by 1 step
	 *
	 * @param int $value
	 */
	public function next($value = 1)
	{
		$this->_progressBar->next($value);
	}

	/**
	 * Class Destructor that removes the ProgressBar Object
	 *
	 */
	public function __destruct()
	{
		unset($this->_progressBar);
	}
}