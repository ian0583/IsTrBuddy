<?
$ignoreList = array();
$ignoreList[] = 'log';
$ignoreList[] = 'data';
$ignoreList[] = 'uploads';
$ignoreList[] = 'conf';
$ignoreList[] = 'constants.php';
$ignoreList[] = 'includes';
$ignoreList[] = 'application/views/';
$ignoreList[] = 'application/controllers/';
$ignoreList[] = 'application/models/';
$ignoreList[] = 'application/tests/';
$ignoreList[] = 'yuicompressor.jar';
$ignoreList[] = 'rmsvn.sh';
$ignoreList[] = '.htaccess';

function listFiles($path)
{
	$dh = opendir($path);

	$files = array();
	while ($file = readdir($dh))
	{
		if (!in_array($file, array('.', '..', '.svn')))
		{
			if (is_dir($path . $file))
			{
				$files = array_merge($files, listFiles($path . $file . '/'));
			}
			else
			{
				$files[] = $path . $file;
			}
		}
	}

	return $files;
}

$this->message("Creating List of core files...\n");
$files = listFiles(APP_ROOT);

$fileList = array();
foreach ($files as $file)
{
	$allowed = true;
	foreach ($ignoreList as $ignored)
	{
		if (strpos($file, APP_ROOT . $ignored) === 0)
		{
			$allowed = false;
		}
	}

	if ($allowed)
	{
		$fileList[] = str_replace(APP_ROOT, '', $file);
	}
}
unset($files);

$this->message("Writing List...");
$fh = fopen(APP_UPGRADE_FILELIST, 'w+');
fwrite($fh, serialize($fileList));
fclose($fh);