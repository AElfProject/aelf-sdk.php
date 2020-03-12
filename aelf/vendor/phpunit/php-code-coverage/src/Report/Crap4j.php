$package->getName().'/'.$package->getVersion().'-'.$package->getDistReference().'.'.$package->getDistType();
}
}
<?php











namespace Composer\Downloader;

use Composer\Package\PackageInterface;
use Composer\Util\Svn as SvnUtil;





class SvnDownloader extends VcsDownloader
{



public function doDownload(PackageInterface $package, $path)
{
$url = $package->getSourceUrl();
$ref = $package->getSourceReference();

$this->io->write("    Checking out ".$package->getSourceReference());
$this->execute($url, "svn co", sprintf("%s/%s", $url, $ref), null, $path);
}




public function doUpdate(PackageInterface $initial, PackageInterface $target, $path)
{
$url = $target->getSourceUrl();
$ref = $target->getSourceReference();

$this->io->write("    Checking out " . $ref);
$this->execute($url, "svn switch", sprintf("%s/%s", $url, $ref), $path);
}




public function getLocalChanges($path)
{
if (!is_dir($path.'/.svn')) {
return;
}

$this->process->execute('svn status --ignore-externals', $output, $path);

return preg_match('{^ *[^X ] +}m', $output) ? $output : null;
}













protected function execute($baseUrl, $command, $url, $cwd = null, $path = null)
{
$util = new SvnUtil($baseUrl, $this->io);
try {
return $util->execute($command, $url, $cwd, $path, $this->io->isVerbose());
} catch (\RuntimeException $e) {
throw new \RuntimeException(
'Package could not be downloaded, '.$e->getMessage()
);
}
}




protected function cleanChanges($path, $update)
{
if (!$changes = $this->getLocalChanges($path)) {
return;
}

if (!$this->io->isInteractive()) {
if (true === $this->config->get('discard-changes')) {
return $this->discardChanges($path);
}

return parent::cleanChanges($path, $update);
}

$changes = array_map(function ($elem) {
return '    '.$elem;
}, preg_split('{\s*\r?\n\s*}', $changes));
$this->io->write('    <error>The package has modified files:</error>');
$this->io->write(array_slice($changes, 0, 10));
if (count($changes) > 10) {
$this->io->write('    <info>'.count($changes) - 10 . ' more files modified, choose "v" to view the full list</info>');
}

while (true) {
switch ($this->io->ask('    <info>Discard changes [y,n,v,?]?</info> ', '?')) {
case 'y':
$this->discardChanges($path);
break 2;

case 'n':
throw new \RuntimeException('Update aborted');

case 'v':
$this->io->write($changes);
break;

case '?':
default:
$this->io->write(array(
'    y - discard changes and apply the '.($update ? 'update' : 'uninstall'),
'    n - abort the '.($update ? 'update' : 'uninstall').' and let you manually clean things up',
'    v - view modified files',
'    ? - print help',
));
break;
}
}
}




protected function getCommitLogs($fromReference, $toReference, $path)
{

 $fromRevision = preg_replace('{.*@(\d+)$}', '$1', $fromReference);
$toRevision = preg_replace('{.*@(\d+)$}', '$1', $toReference);

$command = sprintf('cd %s && svn log -r%s:%s --incremental', escapeshellarg($path), $fromRevision, $toRevision);

if (0 !== $this->process->execute($command, $output)) {
throw new \RuntimeException('Failed to execute ' . $command . "\n\n" . $this->process->getErrorOutput());
}

return $output;
}

protected function discardChanges($path)
{
if (0 !== $this->process->execute('svn revert -R .', $output, $path)) {
throw new \RuntimeException("Could not reset changes\n\n:".$this->process->getErrorOutput());
}
}
}
<?php











namespace Composer\Downloader;

use Composer\Util\Filesystem;










class PearPackageExtractor
{
private static $rolesWithoutPackageNamePrefix = array('php', 'script', 'www');

private $filesystem;
private $file;

public function __construct($file)
{
if (!is_file($file)) {
throw new \UnexpectedValueException('PEAR package file is not found at '.$file);
}

$this->filesystem = new Filesystem();
$this->file = $file;
}











public function extractTo($target, array $roles = array('php' => '/', 'script' => '/bin'), $vars = array())
{
$extractionPath = $target.'/tarball';

try {
$archive = new \PharData($this->file);
$archive->extractTo($extractionPath, null, true);

if (!is_file($this->combine($extractionPath, '/package.xml'))) {
throw new \RuntimeException('Invalid PEAR package. It must contain package.xml file.');
}

$fileCopyActions = $this->buildCopyActions($extractionPath, $roles, $vars);
$this->copyFiles($fileCopyActions, $extractionPath, $target, $roles, $vars);
$this->filesystem->removeDirectory($extractionPath);
} catch (\Exception $exception) {
throw new \UnexpectedValueException(sprintf('Failed to extract PEAR package %s to %s. Reason: %s', $this->file, $target, $exception->getMessage()), 0, $exception);
}
}










private function copyFiles($files, $source, $target, $roles, $vars)
{
foreach ($files as $file) {
$from = $this->combine($source, $file['from']);
$to = $this->combine($target, $roles[$file['role']]);
$to = $this->combine($to, $file['to']);
$tasks = $file['tasks'];
$this->copyFile($from, $to, $tasks, $vars);
}
}

private function copyFile($from, $to, $tasks, $vars)
{
if (!is_file($from)) {
throw new \RuntimeException('Invalid PEAR package. package.xml defines file that is not located inside tarball.');
}

$this->filesystem->ensureDirectoryExists(dirname($to));

if (0 == count($tasks)) {
$copied = copy($from, $to);
} else {
$content = file_get_contents($from);
$replacements = array();
foreach ($tasks as $task) {
$pattern = $task['from'];
$varName = $task['to'];
if (isset($vars[$varName])) {
if ($varName === 'php_bin' && false === strpos($to, '.bat')) {
$replacements[$pattern] = preg_replace('{\.bat$}', '', $vars[$varName]);
} else {
$replacements[$pattern] = $vars[$varName];
}
}
}
$content = strtr($content, $replacements);

$copied = file_put_contents($to, $content);
}

if (false === $copied) {
throw new \RuntimeException(sprintf('Failed to copy %s to %s', $from, $to));
}
}











private function buildCopyActions($source, array $roles, $vars)
{

$package = simplexml_load_file($this->combine($source, 'package.xml'));
if(false === $package)
throw new \RuntimeException('Package definition file is not valid.');

$packageSchemaVersion = $package['version'];
if ('1.0' == $packageSchemaVersion) {
