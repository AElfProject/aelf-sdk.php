t post-update-cmd</info>
EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$script = $input->getArgument('script');
if (!in_array($script, array(
ScriptEvents::PRE_INSTALL_CMD,
ScriptEvents::POST_INSTALL_CMD,
ScriptEvents::PRE_UPDATE_CMD,
ScriptEvents::POST_UPDATE_CMD,
))) {
if (defined('Composer\Script\ScriptEvents::'.str_replace('-', '_', strtoupper($script)))) {
throw new \InvalidArgumentException(sprintf('Script "%s" cannot be run with this command', $script));
}

throw new \InvalidArgumentException(sprintf('Script "%s" does not exist', $script));
}

$this->getComposer()->getEventDispatcher()->dispatchCommandEvent($script, $input->getOption('dev') || !$input->getOption('no-dev'));
}
}
<?php











namespace Composer\Command;

use Composer\Composer;
use Composer\Factory;
use Composer\Downloader\TransportException;
use Composer\Util\ConfigValidator;
use Composer\Util\RemoteFilesystem;
use Composer\Util\StreamContextFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;




class DiagnoseCommand extends Command
{
protected $rfs;
protected $failures = 0;

protected function configure()
{
$this
->setName('diagnose')
->setDescription('Diagnoses the system to identify common errors.')
->setHelp(<<<EOT
The <info>diagnose</info> command checks common errors to help debugging problems.

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$this->rfs = new RemoteFilesystem($this->getIO());

$output->write('Checking platform settings: ');
$this->outputResult($output, $this->checkPlatform());

$output->write('Checking http connectivity: ');
$this->outputResult($output, $this->checkHttp());

$opts = stream_context_get_options(StreamContextFactory::getContext());
if (!empty($opts['http']['proxy'])) {
$output->write('Checking HTTP proxy: ');
$this->outputResult($output, $this->checkHttpProxy());
$output->write('Checking HTTPS proxy support for request_fulluri: ');
$this->outputResult($output, $this->checkHttpsProxyFullUriRequestParam());
}

$composer = $this->getComposer(false);
if ($composer) {
$output->write('Checking composer.json: ');
$this->outputResult($output, $this->checkComposerSchema());
}

if ($composer) {
$config = $composer->getConfig();
} else {
$config = Factory::createConfig();
}

if ($oauth = $config->get('github-oauth')) {
foreach ($oauth as $domain => $token) {
$output->write('Checking '.$domain.' oauth access: ');
$this->outputResult($output, $this->checkGithubOauth($domain, $token));
}
}

$output->write('Checking composer version: ');
$this->outputResult($output, $this->checkVersion());

return $this->failures;
}

private function checkComposerSchema()
{
$validator = new ConfigValidator($this->getIO());
list($errors, $publishErrors, $warnings) = $validator->validate(Factory::getComposerFile());

if ($errors || $publishErrors || $warnings) {
$messages = array(
'error' => array_merge($errors, $publishErrors),
'warning' => $warnings,
);

$output = '';
foreach ($messages as $style => $msgs) {
foreach ($msgs as $msg) {
$output .= '<' . $style . '>' . $msg . '</' . $style . '>' . PHP_EOL;
}
}

return rtrim($output);
}

return true;
}

private function checkHttp()
{
$protocol = extension_loaded('openssl') ? 'https' : 'http';
try {
$json = $this->rfs->getContents('packagist.org', $protocol . '://packagist.org/packages.json', false);
} catch (\Exception $e) {
return $e;
}

return true;
}

private function checkHttpProxy()
{
$protocol = extension_loaded('openssl') ? 'https' : 'http';
try {
$json = json_decode($this->rfs->getContents('packagist.org', $protocol . '://packagist.org/packages.json', false), true);
$hash = reset($json['provider-includes']);
$hash = $hash['sha256'];
$path = str_replace('%hash%', $hash, key($json['provider-includes']));
$provider = $this->rfs->getContents('packagist.org', $protocol . '://packagist.org/'.$path, false);

if (hash('sha256', $provider) !== $hash) {
return 'It seems that your proxy is modifying http traffic on the fly';
}
} catch (\Exception $e) {
return $e;
}

return true;
}








private function checkHttpsProxyFullUriRequestParam()
{
$url = 'https://api.github.com/repos/Seldaek/jsonlint/zipball/1.0.0 ';
try {
$rfcResult = $this->rfs->getContents('api.github.com', $url, false);
} catch (TransportException $e) {
if (!extension_loaded('openssl')) {
return 'You need the openssl extension installed for this check';
}

try {
$this->rfs->getContents('api.github.com', $url, false, array('http' => array('request_fulluri' => false)));
} catch (TransportException $e) {
return 'Unable to assert the situation, maybe github is down ('.$e->getMessage().')';
}

return 'It seems there is a problem with your proxy server, try setting the "HTTP_PROXY_REQUEST_FULLURI" environment variable to "false"';
}

return true;
}

private function checkGithubOauth($domain, $token)
{
$this->getIO()->setAuthentication($domain, $token, 'x-oauth-basic');
try {
$url = $domain === 'github.com' ? 'https://api.'.$domain.'/user/repos' : 'https://'.$domain.'/api/v3/user/repos';

return $this->rfs->getContents($domain, $url, false) ? true : 'Unexpected error';
} catch (\Exception $e) {
if ($e instanceof TransportException && $e->getCode() === 401) {
return '<warning>The oauth token for '.$domain.' seems invalid, run "composer config --global --unset github-oauth.'.$domain.'" to remove it</warning>';
}

return $e;
}
}

private function checkVersion()
{
$protocol = extension_loaded('openssl') ? 'https' : 'http';
$latest = trim($this->rfs->getContents('getcomposer.org', $protocol . '://getcomposer.org/version', false));

if (Composer::VERSION !== $latest && Composer::VERSION !== '82fc3b3eb3ef89fb61f385b50bd029b1df7fab4e') {
return '<warning>Your are not running the latest version</warning>';
}

return true;
}

private function outputResult(OutputInterface $output, $result)
{
if (true === $result) {
$output->writeln('<info>OK</info>');
} else {
$this->failures++;
$output->writeln('<error>FAIL</error>');
if ($result instanceof \Exception) {
$output->writeln('['.get_class($result).'] '.$result->getMessage());
} elseif ($result) {
$output->writeln($result);
}
}
}

private function checkPlatform()
{
$output = '';
$out = function ($msg, $style) use (&$output) {
$output .= '<'.$style.'>'.$msg.'</'.$style.'>';
};


 $errors = array();
$warnings = array();

$iniPath = php_ini_loaded_file();
$displayIniMessage = false;
if ($iniPath) {
$iniMessage = PHP_EOL.PHP_EOL.'The php.ini used by your command-line PHP is: ' . $iniPath;
} else {
$iniMessage = PHP_EOL.PHP_EOL.'A php.ini file does not exist. You will have to create one.';
}
$iniMessage .= PHP_EOL.'If you can not modify the ini file, you can also run `php -d option=value` to modify ini values on the fly. You can use -d multiple times.';

if (!ini_get('allow_url_fopen')) {
$errors['allow_url_fopen'] = true;
}

if (version_compare(PHP_VERSION, '5.3.2', '<')) {
$errors['php'] = PHP_VERSION;
}

if (!isset($errors['php']) && version_compare(PHP_VERSION, '5.3.4', '<')) {
$warnings['php'] = PHP_VERSION;
}

if (!extension_loaded('openssl')) {
$warnings['openssl'] = true;
}

if (ini_get('apc.enable_cli')) {
$warnings['apc_cli'] = true;
}

ob_start();
phpinfo(INFO_GENERAL);
$phpinfo = ob_get_clean();
if (preg_match('{Configure Command(?: *</td><td class="v">| *=> *)(.*?)(?:</td>|$)}m', $phpinfo, $match)) {
$configure = $match[1];

if (false !== strpos($configure, '--enable-sigchild')) {
$warnings['sigchild'] = true;
}

if (false !== strpos($configure, '--with-curlwrappers')) {
$warnings['curlwrappers'] = true;
}
}

if (!empty($errors)) {
foreach ($errors as $error => $current) {
switch ($error) {
case 'php':
$text = PHP_EOL."Your PHP ({$current}) is too old, you must upgrade to PHP 5.3.2 or higher.";
break;

case 'allow_url_fopen':
$text = PHP_EOL."The allow_url_fopen setting is incorrect.".PHP_EOL;
$text .= "Add the following to the end of your `php.ini`:".PHP_EOL;
$text .= "    allow_url_fopen = On";
$displayIniMessage = true;
break;
}
$out($text, 'error');
}

$output .= PHP_EOL;
}

if (!empty($warnings)) {
foreach ($warnings as $warning => $current) {
switch ($warning) {
case 'apc_cli':
$text = PHP_EOL."The apc.enable_cli setting is incorrect.".PHP_EOL;
$text .= "Add the following to the end of your `php.ini`:".PHP_EOL;
$text .= "    apc.enable_cli = Off";
$displayIniMessage = true;
break;

case 'sigchild':
$text = PHP_EOL."PHP was compiled with --enable-sigchild which can cause issues on some platforms.".PHP_EOL;
$text .= "Recompile it without this flag if possible, see also:".PHP_EOL;
$text .= "    https://bugs.php.net/bug.php?id=22999";
break;

case 'curlwrappers':
$text = PHP_EOL."PHP was compiled with --with-curlwrappers which will cause issues with HTTP authentication and GitHub.".PHP_EOL;
$text .= "Recompile it without this flag if possible";
break;

case 'openssl':
$text = PHP_EOL."The openssl extension is missing, which will reduce the security and stability of Composer.".PHP_EOL;
$text .= "If possible you should enable it or recompile php with --with-openssl";
break;

case 'php':
$text = PHP_EOL."Your PHP ({$current}) is quite old, upgrading to PHP 5.3.4 or higher is recommended.".PHP_EOL;
$text .= "Composer works with 5.3.2+ for most people, but there might be edge case issues.";
break;
}
$out($text, 'warning');
}
}

if ($displayIniMessage) {
$out($iniMessage, 'warning');
}

return !$warnings && !$errors ? true : $output;
}
}
<?php











namespace Composer\Command;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\DependencyResolver\Pool;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Repository\CompositeRepository;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;






class ArchiveCommand extends Command
{
protected function configure()
{
$this
->setName('archive')
->setDescription('Create an archive of this composer package')
->setDefinition(array(
new InputArgument('package', InputArgument::OPTIONAL, 'The package to archive instead of the current project'),
new InputArgument('version', InputArgument::OPTIONAL, 'The package version to archive'),
new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'Format of the resulting archive: tar or zip', 'tar'),
new InputOption('dir', false, InputOption::VALUE_REQUIRED, 'Write the archive to this directory', '.'),
))
->setHelp(<<<EOT
The <info>archive</info> command creates an archive of the specified format
containing the files and directories of the Composer project or the specified
package in the specified version and writes it to the specified directory.

<info>php composer.phar archive [--format=zip] [--dir=/foo] [package [version]]</info>

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
return $this->archive(
$this->getIO(),
$input->getArgument('package'),
$input->getArgument('version'),
$input->getOption('format'),
$input->getOption('dir')
);
}

protected function archive(IOInterface $io, $packageName = null, $version = null, $format = 'tar', $dest = '.')
{
$config = Factory::createConfig();
$factory = new Factory;
$archiveManager = $factory->createArchiveManager($config);

if ($packageName) {
$package = $this->selectPackage($io, $packageName, $version);

if (!$package) {
return 1;
}
} else {
$package = $this->getComposer()->getPackage();
}

$io->write('<info>Creating the archive.</info>');
$archiveManager->archive($package, $format, $dest);

return 0;
}

protected function selectPackage(IOInterface $io, $packageName, $version = null)
{
$io->write('<info>Searching for the specified package.</info>');

if ($composer = $this->getComposer(false)) {
$localRepo = $composer->getRepositoryManager()->getLocalRepository();
$repos = new CompositeRepository(array_merge(array($localRepo), $composer->getRepositoryManager()->getRepositories()));
} else {
$defaultRepos = Factory::createDefaultRepositories($this->getIO());
$io->write('No composer.json found in the current directory, searching packages from ' . implode(', ', array_keys($defaultRepos)));
$repos = new CompositeRepository($defaultRepos);
}

$pool = new Pool();
$pool->addRepository($repos);

$constraint = ($version) ? new VersionConstraint('>=', $version) : null;
$packages = $pool->whatProvides($packageName, $constraint);

if (count($packages) > 1) {
$package = $packages[0];
$io->write('<info>Found multiple matches, selected '.$package->getPrettyString().'.</info>');
$io->write('Alternatives were '.implode(', ', array_map(function ($p) { return $p->getPrettyString(); }, $packages)).'.');
$io->write('<comment>Please use a more specific constraint to pick a different package.</comment>');
} elseif ($packages) {
$package = $packages[0];
$io->write('<info>Found an exact match '.$package->getPrettyString().'.</info>');
} else {
$io->write('<error>Could not find a package matching '.$packageName.'.</error>');
return false;
}

return $package;
}
}
<?php











namespace Composer\Command;

use Composer\Composer;
use Composer\Console\Application;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Symfony\Component\Console\Command\Command as BaseCommand;







abstract class Command extends BaseCommand
{



private $composer;




private $io;





public function getComposer($required = true)
{
if (null === $this->composer) {
$application = $this->getApplication();
if ($application instanceof Application) {

$this->composer = $application->getComposer($required);
} elseif ($required) {
throw new \RuntimeException(
'Could not create a Composer\Composer instance, you must inject '.
'one if this command is not used with a Composer\Console\Application instance'
);
}
}

return $this->composer;
}




public function setComposer(Composer $composer)
{
$this->composer = $composer;
}




public function getIO()
{
if (null === $this->io) {
$application = $this->getApplication();
if ($application instanceof Application) {

$this->io = $application->getIO();
} else {
$this->io = new NullIO();
}
}

return $this->io;
}




public function setIO(IOInterface $io)
{
$this->io = $io;
}
}
<?php











namespace Composer\Command;

use Composer\Config;
use Composer\Factory;
use Composer\Installer;
use Composer\Installer\ProjectInstaller;
use Composer\Installer\InstallationManager;
use Composer\IO\IOInterface;
use Composer\Package\BasePackage;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\Repository\ComposerRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\FilesystemRepository;
use Composer\Repository\InstalledFilesystemRepository;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Composer\Json\JsonFile;
use Composer\Util\Filesystem;
use Composer\Util\RemoteFilesystem;
use Composer\Package\Version\VersionParser;







class CreateProjectCommand extends Command
{
protected function configure()
{
$this
->setName('create-project')
->setDescription('Create new project from a package into given directory.')
->setDefinition(array(
new InputArgument('package', InputArgument::REQUIRED, 'Package name to be installed'),
new InputArgument('directory', InputArgument::OPTIONAL, 'Directory where the files should be created'),
new InputArgument('version', InputArgument::OPTIONAL, 'Version, will defaults to latest'),
new InputOption('stability', 's', InputOption::VALUE_REQUIRED, 'Minimum-stability allowed (unless a version is specified).', 'stable'),
new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist even for dev versions.'),
new InputOption('repository-url', null, InputOption::VALUE_REQUIRED, 'Pick a different repository url to look for the package.'),
new InputOption('dev', null, InputOption::VALUE_NONE, 'Whether to install dependencies for development.'),
new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'Whether to disable custom installers.'),
new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Whether to prevent execution of all defined scripts in the root package.'),
new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
new InputOption('keep-vcs', null, InputOption::VALUE_NONE, 'Whether to prevent deletion vcs folder.'),
))
->setHelp(<<<EOT
The <info>create-project</info> command creates a new project from a given
package into a new directory. You can use this command to bootstrap new
projects or setup a clean version-controlled installation
for developers of your project.

<info>php composer.phar create-project vendor/project target-directory [version]</info>

You can also specify the version with the package name using = or : as separator.

To install unstable packages, either specify the version you want, or use the
--stability=dev (where dev can be one of RC, beta, alpha or dev).

To setup a developer workable version you should create the project using the source
controlled code by appending the <info>'--prefer-source'</info> flag. Also, it is
advisable to install all dependencies required for development by appending the
<info>'--dev'</info> flag.

To install a package from another repository than the default one you
can pass the <info>'--repository-url=http://myrepository.org'</info> flag.

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$config = Factory::createConfig();

$preferSource = false;
$preferDist = false;
switch ($config->get('preferred-install')) {
case 'source':
$preferSource = true;
break;
case 'dist':
$preferDist = true;
break;
case 'auto':
default:

 break;
}
if ($input->getOption('prefer-source') || $input->getOption('prefer-dist')) {
$preferSource = $input->getOption('prefer-source');
$preferDist = $input->getOption('prefer-dist');
}

return $this->installProject(
$this->getIO(),
$config,
$input->getArgument('package'),
$input->getArgument('directory'),
$input->getArgument('version'),
$input->getOption('stability'),
$preferSource,
$preferDist,
$input->getOption('dev'),
$input->getOption('repository-url'),
$input->getOption('no-custom-installers'),
$input->getOption('no-scripts'),
$input->getOption('keep-vcs'),
$input->getOption('no-progress')
);
}

public function installProject(IOInterface $io, $config, $packageName, $directory = null, $packageVersion = null, $stability = 'stable', $preferSource = false, $preferDist = false, $installDevPackages = false, $repositoryUrl = null, $disableCustomInstallers = false, $noScripts = false, $keepVcs = false, $noProgress = false)
{
$stability = strtolower($stability);
if ($stability === 'rc') {
$stability = 'RC';
}
if (!isset(BasePackage::$stabilities[$stability])) {
throw new \InvalidArgumentException('Invalid stability provided ('.$stability.'), must be one of: '.implode(', ', array_keys(BasePackage::$stabilities)));
}

if (null === $repositoryUrl) {
$sourceRepo = new CompositeRepository(Factory::createDefaultRepositories($io, $config));
} elseif ("json" === pathinfo($repositoryUrl, PATHINFO_EXTENSION)) {
$sourceRepo = new FilesystemRepository(new JsonFile($repositoryUrl, new RemoteFilesystem($io)));
} elseif (0 === strpos($repositoryUrl, 'http')) {
$sourceRepo = new ComposerRepository(array('url' => $repositoryUrl), $io, $config);
} else {
throw new \InvalidArgumentException("Invalid repository url given. Has to be a .json file or an http url.");
}

$parser = new VersionParser();
$candidates = array();
$requirements = $parser->parseNameVersionPairs(array($packageName));
$name = strtolower($requirements[0]['name']);
if (!$packageVersion && isset($requirements[0]['version'])) {
$packageVersion = $requirements[0]['version'];
}

$pool = new Pool($packageVersion ? 'dev' : $stability);
$pool->addRepository($sourceRepo);

$constraint = $packageVersion ? new VersionConstraint('=', $parser->normalize($packageVersion)) : null;
$candidates = $pool->whatProvides($name, $constraint);
foreach ($candidates as $key => $candidate) {
if ($candidate->getName() !== $name) {
unset($candidates[$key]);
}
}

if (!$candidates) {
throw new \InvalidArgumentException("Could not find package $name" . ($packageVersion ? " with version $packageVersion." : " with stability $stability."));
}

if (null === $directory) {
$parts = explode("/", $name, 2);
$directory = getcwd() . DIRECTORY_SEPARATOR . array_pop($parts);
}


 $package = $candidates[0];
foreach ($candidates as $candidate) {
if (version_compare($package->getVersion(), $candidate->getVersion(), '<')) {
$package = $candidate;
}
}
unset($candidates);

$io->write('<info>Installing ' . $package->getName() . ' (' . VersionParser::formatVersion($package, false) . ')</info>');

if ($disableCustomInstallers) {
$io->write('<info>Custom installers have been disabled.</info>');
}

if (0 === strpos($package->getPrettyVersion(), 'dev-') && in_array($package->getSourceType(), array('git', 'hg'))) {
$package->setSourceReference(substr($package->getPrettyVersion(), 4));
}

$dm = $this->createDownloadManager($io, $config);
$dm->setPreferSource($preferSource)
->setPreferDist($preferDist)
->setOutputProgress(!$noProgress);

$projectInstaller = new ProjectInstaller($directory, $dm);
$im = $this->createInstallationManager();
$im->addInstaller($projectInstaller);
$im->install(new InstalledFilesystemRepository(new JsonFile('php://memory')), new InstallOperation($package));
$im->notifyInstalls();

$installedFromVcs = 'source' === $package->getInstallationSource();

$io->write('<info>Created project in ' . $directory . '</info>');
chdir($directory);

putenv('COMPOSER_ROOT_VERSION='.$package->getPrettyVersion());


 unset($dm, $im, $config, $projectInstaller, $sourceRepo, $package);


 $composer = Factory::create($io);
$installer = Installer::create($io, $composer);

$installer->setPreferSource($preferSource)
->setPreferDist($preferDist)
->setDevMode($installDevPackages)
->setRunScripts( ! $noScripts);

if ($disableCustomInstallers) {
$installer->disableCustomInstallers();
}

if (!$installer->run()) {
return 1;
}

if (!$keepVcs && $installedFromVcs
&& (
!$io->isInteractive()
|| $io->askConfirmation('<info>Do you want to remove the existing VCS (.git, .svn..) history?</info> [<comment>Y,n</comment>]? ', true)
)
) {
$finder = new Finder();
$finder->depth(0)->directories()->in(getcwd())->ignoreVCS(false)->ignoreDotFiles(false);
foreach (array('.svn', '_svn', 'CVS', '_darcs', '.arch-params', '.monotone', '.bzr', '.git', '.hg') as $vcsName) {
$finder->name($vcsName);
}

try {
$fs = new Filesystem();
$dirs = iterator_to_array($finder);
unset($finder);
foreach ($dirs as $dir) {
if (!$fs->removeDirectory($dir)) {
throw new \RuntimeException('Could not remove '.$dir);
}
}
} catch (\Exception $e) {
$io->write('<error>An error occurred while removing the VCS metadata: '.$e->getMessage().'</error>');
}
}

return 0;
}

protected function createDownloadManager(IOInterface $io, Config $config)
{
$factory = new Factory();

return $factory->createDownloadManager($io, $config);
}

protected function createInstallationManager()
{
return new InstallationManager();
}
}
<?php











namespace Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;




class AboutCommand extends Command
{
protected function configure()
{
$this
->setName('about')
->setDescription('Short information about Composer')
->setHelp(<<<EOT
<info>php composer.phar about</info>
EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$output->writeln(<<<EOT
<info>Composer - Package Management for PHP</info>
<comment>Composer is a dependency manager tracking local dependencies of your projects and libraries.
See http://getcomposer.org/ for more information.</comment>
EOT
);

}
}
<?php











namespace Composer\Command;

use Composer\Composer;
use Composer\DependencyResolver\Pool;
use Composer\DependencyResolver\DefaultPolicy;
use Composer\Factory;
use Composer\Package\CompletePackageInterface;
use Composer\Package\LinkConstraint\VersionConstraint;
use Composer\Package\Version\VersionParser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Repository\ArrayRepository;
use Composer\Repository\CompositeRepository;
use Composer\Repository\ComposerRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;





class ShowCommand extends Command
{
protected $versionParser;

protected function configure()
{
$this
->setName('show')
->setDescription('Show information about packages')
->setDefinition(array(
new InputArgument('package', InputArgument::OPTIONAL, 'Package to inspect'),
new InputArgument('version', InputArgument::OPTIONAL, 'Version or version constraint to inspect'),
new InputOption('installed', 'i', InputOption::VALUE_NONE, 'List installed packages only'),
new InputOption('platform', 'p', InputOption::VALUE_NONE, 'List platform packages only'),
new InputOption('available', 'a', InputOption::VALUE_NONE, 'List available packages only'),
new InputOption('self', 's', InputOption::VALUE_NONE, 'Show the root package information'),
new InputOption('name-only', 'N', InputOption::VALUE_NONE, 'List package names only'),
))
->setHelp(<<<EOT
The show command displays detailed information about a package, or
lists all packages available.

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$this->versionParser = new VersionParser;


 $platformRepo = new PlatformRepository;

if ($input->getOption('self')) {
$package = $this->getComposer(false)->getPackage();
$repos = $installedRepo = new ArrayRepository(array($package));
} elseif ($input->getOption('platform')) {
$repos = $installedRepo = $platformRepo;
} elseif ($input->getOption('installed')) {
$repos = $installedRepo = $this->getComposer()->getRepositoryManager()->getLocalRepository();
} elseif ($input->getOption('available')) {
$installedRepo = $platformRepo;
if ($composer = $this->getComposer(false)) {
$repos = new CompositeRepository($composer->getRepositoryManager()->getRepositories());
} else {
$defaultRepos = Factory::createDefaultRepositories($this->getIO());
$repos = new CompositeRepository($defaultRepos);
$output->writeln('No composer.json found in the current directory, showing available packages from ' . implode(', ', array_keys($defaultRepos)));
}
} elseif ($composer = $this->getComposer(false)) {
$composer = $this->getComposer();
$localRepo = $composer->getRepositoryManager()->getLocalRepository();
$installedRepo = new CompositeRepository(array($localRepo, $platformRepo));
$repos = new CompositeRepository(array_merge(array($installedRepo), $composer->getRepositoryManager()->getRepositories()));
} else {
$defaultRepos = Factory::createDefaultRepositories($this->getIO());
$output->writeln('No composer.json found in the current directory, showing available packages from ' . implode(', ', array_keys($defaultRepos)));
$installedRepo = $platformRepo;
$repos = new CompositeRepository(array_merge(array($installedRepo), $defaultRepos));
}


 if ($input->getArgument('package') || !empty($package)) {
$versions = array();
if (empty($package)) {
list($package, $versions) = $this->getPackage($installedRepo, $repos, $input->getArgument('package'), $input->getArgument('version'));

if (!$package) {
throw new \InvalidArgumentException('Package '.$input->getArgument('package').' not found');
}
} else {
$versions = array($package->getPrettyVersion() => $package->getVersion());
}

$this->printMeta($input, $output, $package, $versions, $installedRepo, $repos);
$this->printLinks($input, $output, $package, 'requires');
$this->printLinks($input, $output, $package, 'devRequires', 'requires (dev)');
if ($package->getSuggests()) {
$output->writeln("\n<info>suggests</info>");
foreach ($package->getSuggests() as $suggested => $reason) {
$output->writeln($suggested . ' <comment>' . $reason . '</comment>');
}
}
$this->printLinks($input, $output, $package, 'provides');
$this->printLinks($input, $output, $package, 'conflicts');
$this->printLinks($input, $output, $package, 'replaces');

return;
}


 $packages = array();

if ($repos instanceof CompositeRepository) {
$repos = $repos->getRepositories();
} elseif (!is_array($repos)) {
$repos = array($repos);
}

foreach ($repos as $repo) {
if ($repo === $platformRepo) {
$type = '<info>platform</info>:';
} elseif (
$repo === $installedRepo
|| ($installedRepo instanceof CompositeRepository && in_array($repo, $installedRepo->getRepositories(), true))
) {
$type = '<info>installed</info>:';
} else {
$type = '<comment>available</comment>:';
}
if ($repo instanceof ComposerRepository && $repo->hasProviders()) {
foreach ($repo->getProviderNames() as $name) {
$packages[$type][$name] = $name;
}
} else {
foreach ($repo->getPackages() as $package) {
if (!isset($packages[$type][$package->getName()])
|| !is_object($packages[$type][$package->getName()])
|| version_compare($packages[$type][$package->getName()]->getVersion(), $package->getVersion(), '<')
) {
$packages[$type][$package->getName()] = $package;
}
}
}
}

$tree = !$input->getOption('platform') && !$input->getOption('installed') && !$input->getOption('available');
$indent = $tree ? '  ' : '';
foreach (array('<info>platform</info>:' => true, '<comment>available</comment>:' => false, '<info>installed</info>:' => true) as $type => $showVersion) {
if (isset($packages[$type])) {
if ($tree) {
$output->writeln($type);
}
ksort($packages[$type]);

$nameLength = $versionLength = 0;
foreach ($packages[$type] as $package) {
if (is_object($package)) {
$nameLength = max($nameLength, strlen($package->getPrettyName()));
$versionLength = max($versionLength, strlen($this->versionParser->formatVersion($package)));
} else {
$nameLength = max($nameLength, $package);
}
}
list($width) = $this->getApplication()->getTerminalDimensions();
if (defined('PHP_WINDOWS_VERSION_BUILD')) {
$width--;
}

$writeVersion = !$input->getOption('name-only') && $showVersion && ($nameLength + $versionLength + 3 <= $width);
$writeDescription = !$input->getOption('name-only') && ($nameLength + ($showVersion ? $versionLength : 0) + 24 <= $width);
foreach ($packages[$type] as $package) {
if (is_object($package)) {
$output->write($indent . str_pad($package->getPrettyName(), $nameLength, ' '), false);

if ($writeVersion) {
$output->write(' ' . str_pad($this->versionParser->formatVersion($package), $versionLength, ' '), false);
}

if ($writeDescription) {
$description = strtok($package->getDescription(), "\r\n");
$remaining = $width - $nameLength - $versionLength - 4;
if (strlen($description) > $remaining) {
$description = substr($description, 0, $remaining - 3) . '...';
}
$output->write(' ' . $description);
}
} else {
$output->write($indent . $package);
}
$output->writeln('');
}
if ($tree) {
$output->writeln('');
}
}
}
}



