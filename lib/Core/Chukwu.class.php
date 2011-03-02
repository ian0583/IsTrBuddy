<?
/**
 * Main Framework class for module handling
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_Chukwu
{
	private $_parameters;

	private $_template;

	private $_displayType = 'html';

	private $_jsonData = '';

	private $_xmlData = '';

	public $hasbeendisplayed = false;

	public function __construct($parameters)
	{
		$this->_parameters = $parameters;
		if ($this->_parameters['allowdisplay'])
		{
			$this->_template = new Smarty();
			$this->_template->caching = APP_CACHING; // set to true to enable caching
			$this->_template->debugging = APP_DEBUG;
			$this->_template->template_dir = APP_TEMPLATES;
			$this->_template->compile_dir = APP_TEMPLATES_COMPILED;
			$this->_template->cache_dir = APP_TEMPLATES_CACHE;

			if (isset($this->_parameters['title']))
			{
				$this->assign('TEMPLATE_TITLE', APP_NAME . ' - ' . $this->_parameters['title']);
			}
			else
			{
				$this->assign('TEMPLATE_TITLE', APP_NAME);
			}

			$this->_setDisplayType($parameters['displaytype']);
		}
	}

	private function _setDisplayType($displaytype)
	{
		$this->_displayType = $displaytype;
	}

	public function display()
	{
		if ($this->_parameters['allowdisplay'])
		{
			switch ($this->_displayType)
			{
				case 'json':
					$json = Zend_Json::encode($this->_jsondata);
					if (isset($_GET['callback']))
					{
						echo $_GET['callback'] . "($json)";
					}
					else
					{
						echo $json;
					}
					exit();
					break;

				case 'xml':

					break;

				default:
					if (isset($this->_parameters['body']))
					{
						$this->_template->assign('body', APP_TEMPLATES . $this->_parameters['body']);
					}

					$this->_template->display($this->_parameters['template']);
					$this->hasbeendisplayed = true;
					break;
			}
		}
		else
		{
			// throw exception for "this is an action module"
			throw new Exception('Action modules cannot be displayed');
		}
	}

	public function assign($key, $value)
	{
		$this->_template->assign($key, $value);
	}

	public function load()
	{
		global $PARAMS;
		require APP_MODULES . $this->_parameters['path'];
	}

	public function trigger($issuccess = true)
	{
		if ($issuccess)
		{
			$this->forward($this->_parameters['success']);
		}
		else
		{
			$this->forward($this->_parameters['fail']);
		}
	}

	public function forward($url)
	{
		header('Location: ' . $url);
	}

	public function setmessage($message)
	{
		$_SESSION[APP_NAME]['message'] = $message;
	}

	public function hasmessage()
	{
		if (isset($_SESSION[APP_NAME]['message']))
		{
			return true;
		}
		return false;
	}

	public function getmessage()
	{
		if (isset($_SESSION[APP_NAME]['message']))
		{
			$temp = $_SESSION[APP_NAME]['message'];
			unset($_SESSION[APP_NAME]['message']);
			return $temp;
		}
		return false;
	}

	public function __destruct()
	{
		unset($this->_parameters);
	}
}