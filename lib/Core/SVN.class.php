<?
/**
 * Subversion Class for Framework Tasks
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_SVN
{
	private $_workingCopy;

	public $_files = array();

	private $_forCommit = array();

	private $_statuses = array('', 'NONE', 'UNVERSIONED', 'NORMAL', 'ADDED', 'MISSING', 'DELETED', 'REPLACED', 'MODIFIED', 'MERGED', 'CONFLICTED', 'IGNORED', 'OBSTRUCTED', 'EXTERNAL', 'INCOMPLETE');

	private $_ignoreList = array(
	APP_TEMPLATES_CACHE,
	APP_TEMPLATES_COMPILED,
	APP_LOG,
	APP_UPLOAD,
	APP_DATA
	);

	public function __construct($workingCopy = null)
	{
		if (is_null($workingCopy))
		{
			$this->_workingCopy = APP_ROOT;
		}
		else
		{
			$this->_workingCopy = $workingCopy;
		}
	}

	private function _isIgnored($path)
	{
		$isIgnored = false;

		if (!is_file($path))
		{
			$path = $path . '/';
		}

		foreach ($this->_ignoreList as $ignored)
		{
			if (strpos($path, $ignored) !== false)
			{
				$isIgnored = true;
			}
		}

		return $isIgnored;
	}

	public function updateStatus()
	{
		$this->_files = array();
		$this->_forCommit = array();

		$files = svn_status($this->_workingCopy);

		// categorize files
		foreach ($files as $file)
		{
			// check against ignore list
			if (!$this->_isIgnored($file['path']))
			{
				$data = array();
				$data['path'] = $file['path'];
				$data['status'] = $file['text_status'];
				$data['status_text'] = $this->_statuses[$file['text_status']];

				$this->_files[] = $data;
				$this->_forCommit[] = $file['path'];
			}
		}
	}

	public function addUnversioned()
	{
		// refresh status;
		$this->updateStatus();

		foreach ($this->_files as $file)
		{
			if ($file['status_text'] == 'UNVERSIONED')
			{
				svn_add($file['path']);
			}
		}
	}

	public function commit($message)
	{
		$result = svn_commit($message, $this->_forCommit);

		return array('revision' => $result[0], 'name' => $result[2]);
	}

	public function getFiles()
	{
		return $this->_files;
	}
}