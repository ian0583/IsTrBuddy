<?
$ignoreList = array();
$ignoreList[] = 'application/views/.smarty/';

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

$this->message("Creating list of files to backup...\n");
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

$fh = fopen(PACKAGE_LIST, 'w+');

foreach ($fileList as $file)
{
	fputs($fh, $file . "\n");
}

fclose($fh);

$package = APP_UPLOAD . 'backup_' . date('Ymd_His') . '.tgz';

$this->message("Creating tar ball...\n");

exec("tar -cvzf $package -T " . PACKAGE_LIST);

$this->message("Cleaning up...\n");
unlink(PACKAGE_LIST);

$this->message("Backup is now ready and can be found at: $package");