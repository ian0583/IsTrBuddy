<?
/**
 * Amazon S3 Helper Class
 *
 * @author Sheldon Senseng
 * @copyright Sheldon Senseng <sheldonsenseng@gmail.com>
 * @version 0.1
 *
 */

class Core_S3
{
	private $accesskey;

	private $secretkey;

	private $connection;

	private $bucket;

	public $stream;

	public $objects;

	public function __construct($accesskey, $secretkey, $bucket)
	{
		$this->accesskey = $accesskey;
		$this->secretkey = $secretkey;
		$this->bucket = $bucket;

		$this->connection = new Zend_Service_Amazon_S3($this->accesskey, $this->secretkey);
		$this->connection->registerStreamWrapper('s3');

		$this->stream = 's3://' . $this->bucket . '/';

		$this->getList();
	}

	public function getList()
	{
		$this->objects = $this->connection->getObjectsByBucket($this->bucket);

		return $this->objects;
	}

	public function getFile($file)
	{
		$data =  $this->connection->getObject($this->bucket . '/' . $file);
		return $data;
	}

	public function writeFile($filename, $contents)
	{
		$s3fh = fopen($this->stream . $filename, 'w+');
		fwrite($s3fh, $contents);
		return true;
	}

	public function copyFile($pathToFile)
	{
		if (file_exists($pathToFile))
		{
			$filename = basename($pathToFile);
			$fh = fopen($pathToFile, 'r');
			$contents = fread($fh, filesize($pathToFile));

			$s3fh = fopen($this->stream . $filename, 'w+');
			fwrite($s3fh, $contents);
			return true;
		}
		return false;
	}
}