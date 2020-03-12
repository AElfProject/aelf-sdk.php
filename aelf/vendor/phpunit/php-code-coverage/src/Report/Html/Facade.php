alidArgumentException('Package '.$package->getPrettyName().' is missing reference information');
}

$this->io->write("  - Installing <info>" . $package->getName() . "</info> (<comment>" . VersionParser::formatVersion($package) . "</comment>)");
$this->filesystem->removeDirectory($path);
$this->doDownload($package, $path);
$this->io->write('');
}




public function update(PackageInterface $initial, PackageInterface $target, $path)
{
if (!$target->getSourceReference()) {
throw new \InvalidArgumentException('Package '.$target->getPrettyName().' is missing reference information');
}

$name = $target->getName();
if ($initial->getPrettyVersion() == $target->getPrettyVersion()) {
if ($target->getSourceType() === 'svn') {
$from = $initial->getSourceReference();
$to = $target->getSourceReference();
} else {
$from = substr($initial->getSourceReference(), 0, 7);
$to = substr($target->getSourceReference(), 0, 7);
}
$name .= ' '.$initial->getPrettyVersion();
} else {
$from = VersionParser::formatVersion($initial);
$to = VersionParser::formatVersion($target);
}

$this->io->write("  - Updating <info>" . $name . "</info> (<comment>" . $from . "</comment> => <comment>" . $to . "</comment>)");

$this->cleanChanges($path, true);
try {
$this->doUpdate($initial, $target, $path);
} catch (\Exception $e) {

 $this->reapplyChanges($path);

throw $e;
}
$this->reapplyChanges($path);


 if ($this->io->isVerbose()) {
$message = 'Pulling in changes:';
$logs = $this->getCommitLogs($initial->getSourceReference(), $target->getSourceReference(), $path);

if (!trim($logs)) {
$message = 'Rolling back changes:';
$logs = $this->getCommitLogs($target->getSourceReference(), $initial->getSourceReference(), $path);
}

if (trim($logs)) {
$logs = implode("\n", array_map(function ($line) {
return '      ' . $line;
}, explode("\n", $logs)));

$this->io->write('    '.$message);
$this->io->write($logs);
}
}

$this->io->write('');
}




public function remove(PackageInterface $package, $path)
{
$this->io->write("  - Removing <info>" . $package->getName() . "</info> (<comment>" . $package->getPrettyVersion() . "</comment>)");
$this->cleanChanges($path, false);
if (!$this->filesystem->removeDirectory($path)) {

 if (!defined('PHP_WINDOWS_VERSION_BUILD') || (usleep(250) && !$this->filesystem->removeDirectory($path))) {
throw new \RuntimeException('Could not completely delete '.$path.', aborting.');
}
}
}





public function setOutputProgress($outputProgress)
{
return $this;
}









protected function cleanChanges($path, $update)
{

 if (null !== $this->getLocalChanges($path)) {
throw new \RuntimeException('Source directory ' . $path . ' has uncommitted changes.');
}
}







protected function reapplyChanges($path)
{
}







abstract protected function doDownload(PackageInterface $package, $path);








abstract protected function doUpdate(PackageInterface $initial, PackageInterface $target, $path);







abstract public function getLocalChanges($path);









abstract protected function getCommitLogs($fromReference, $toReference, $path);
}
<?php











namespace Composer\Downloader;

use Composer\Config;
use Composer\Cache;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionParser;
use Composer\Util\Filesystem;
use Composer\Util\GitHub;
use Composer\Util\RemoteFilesystem;








class FileDownloader implements DownloaderInterface
{
private static $cacheCollected = false;
protected $io;
protected $config;
protected $rfs;
protected $filesystem;
protected $cache;
protected $outputProgress = true;










public function __construct(IOInterface $io, Config $config, Cache $cache = null, RemoteFilesystem $rfs = null, Filesystem $filesystem = null)
{
$this->io = $io;
$this->config = $config;
$this->rfs = $rfs ?: new RemoteFilesystem($io);
$this->filesystem = $filesystem ?: new Filesystem();
$this->cache = $cache;

if ($this->cache && !self::$cacheCollected && !mt_rand(0, 50)) {
$this->cache->gc($config->get('cache-ttl'), $config->get('cache-files-maxsize'));
}
self::$cacheCollected = true;
}




public function getInstallationSource()
{
return 'dist';
}




public function download(PackageInterface $package, $path)
{
$url = $package->getDistUrl();
if (!$url) {
throw new \InvalidArgumentException('The given package is missing url information');
}

$this->filesystem->ensureDirectoryExists($path);

$fileName = $this->getFileName($package, $path);

$this->io->write("  - Installing <info>" . $package->getName() . "</info> (<comment>" . VersionParser::formatVersion($package) . "</comment>)");

$processedUrl = $this->processUrl($package, $url);
$hostname = parse_url($processedUrl, PHP_URL_HOST);

if (strpos($hostname, '.github.com') === (strlen($hostname) - 11)) {
$hostname = 'github.com';
}

try {
try {
if (!$this->cache || !$this->cache->copyTo($this->getCacheKey($package), $fileName)) {
if (!$this->outputProgress) {
$this->io->write('    Downloading');
}


 $retries = 3;
while ($retries--) {
try {
$this->rfs->copy($hostname, $processedUrl, $fileName, $this->outputProgress);
break;
} catch (TransportException $e) {

 if (0 !== $e->getCode() || !$retries) {
throw $e;
}
if ($this->io->isVerbose()) {
$this->io->write('    Download failed, retrying...');
}
usleep(500000);
}
}

if ($this->cache) {
$this->cache->copyFrom($this->getCacheKey($package), $fileName);
}
} else {
$this->io->write('    Loading from cache');
}
} catch (TransportException $e) {
if (in_array($e->getCode(), array(404, 403)) && 'github.com' === $hostname && !$this->io->hasAuthentication($hostname)) {
$message = "\n".'Could not fetch '.$processedUrl.', enter your GitHub credentials '.($e->getCode() === 404 ? 'to access p