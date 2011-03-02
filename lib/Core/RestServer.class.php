<?
/**
 * Rest Server Class
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_RestServer
{

	protected $_supportedMethods = 'GET,HEAD,POST,PUT,DELETE';

	protected $_module;

	protected $_url;

	protected $_method;

	protected $_arguments = array();

	protected $_restParameters = array();

	protected $_restArguments = array();

	protected $_restFilters = array();

	protected $_accept;

	protected $_restParametersUrl;

	protected $_responseStatus = 200;

	protected $_restAuthHeaders = array('username' => '', 'password' => '');

	protected $_data = array();

	protected $_responseBody = '';

	public function __construct()
	{
		$this->_module = str_replace('Rest_', '', get_class($this));
		$this->_url = $this->getFullUrl($_SERVER);
		$this->_method = $_SERVER['REQUEST_METHOD'];
		$this->_accept = @$_SERVER['HTTP_ACCEPT'];
		$this->getArguments();
		$this->parseRestParameters();
	}

	public final function __get($var)
	{
		if (isset($this->_data[$var]))
		{
			return $this->_data[$var];
		}
		return null;
	}

	public final function __set($var, $value)
	{
		$this->_data[$var] = $value;
	}

	public function __destruct()
	{

	}

	protected final function getArguments()
	{
		switch ($this->_method)
		{
			case 'GET':
			case 'HEAD':
				$this->_arguments = $_GET;
				break;

			case 'POST':
				$this->_arguments = $_POST;
				break;

			case 'PUT':
			case 'DELETE':
				parse_str(file_get_contents('php://input'), $this->_arguments);
				break;

			default:
				header('Allow: ' . $this->_supportedMethods, true, 501);
				break;
		}
	}

	protected final function getFullUrl($_SERVER)
	{
		$protocol = @$_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
		$location = $_SERVER['REQUEST_URI'];

		if ($_SERVER['QUERY_STRING'])
		{
			$location = substr($location, 0, strrpos($location, $_SERVER['QUERY_STRING']) - 1);
		}

		return $protocol.'://'.$_SERVER['HTTP_HOST'].$location;
	}

	protected final function _methodNotAllowedResponse()
	{
		header('Allow: ' . $this->_supportedMethods, true, 405);
	}

	protected final function parseRestParameters()
	{
		$this->_restParametersUrl = trim(trim(str_replace(APP_URI . MODULE_FULLNAME . '/' . $this->_module, '', $this->_url)), '/');
		$this->_restParameters = explode('/', $this->_restParametersUrl);

		foreach ($this->_restParameters as $key => $parameter)
		{
			$matches = array();
			if (preg_match('/:\(.*\)/', $parameter, $matches))
			{
				$this->_restParameters[$key] = str_replace($matches[0], '', $parameter);
				$this->_restFilters = explode(',', substr($matches[0], 2, strlen($matches[0]) - 3));
			}
		}

		foreach ($this->_restParameters as $key => $value)
		{
			if (strpos($value, ';') !== false)
			{
				$this->_restParameters[$key] = substr($value, 0, strpos($value, ';'));
				$arguments = array();
				$restArguments = explode(';', substr($value, strpos($value, ';') + 1));

				foreach ($restArguments as $restArgument)
				{
					if ($restArgument)
					{
						list($restArgumentKey, $restArgumentValue) = explode('=', $restArgument);
						$arguments[$restArgumentKey] = $restArgumentValue;
					}
				}
				$this->_restArguments = $arguments;
			}
		}
	}

	protected final function getRequestAuth()
	{
		return array('username' => '' . @$_SERVER['PHP_AUTH_USER'], 'password' => '' . @$_SERVER['PHP_AUTH_PW']);
	}

	public final function get($var)
	{
		$var = '_' . $var;
		if (isset($this->$var))
		{
			return $this->$var;
		}
		else
		{
			return null;
		}
	}

	public function setStatusCode($code)
	{
		$this->_responseStatus = $code;
		header("HTTP/1.1 $code", true, $code);
	}
}