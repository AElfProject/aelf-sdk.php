nstaller;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;






class InstallCommand extends Command
{
protected function configure()
{
$this
->setName('install')
->setDescription('Installs the project dependencies from the composer.lock file if present, or falls back on the composer.json.')
->setDefinition(array(
new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist even for dev versions.'),
new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs the operations but will not execute anything (implicitly enables --verbose).'),
new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of require-dev packages.'),
new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages (enabled by default, only present for sanity).'),
new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'Disables all custom installers.'),
new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_NONE, 'Shows more details including new commits pulled in when updating packages.'),
new InputOption('optimize-autoloader', 'o', InputOption::VALUE_NONE, 'Optimize autoloader during autoloader dump')
))
->setHelp(<<<EOT
The <info>install</info> command reads the composer.lock file from
the current directory, processes it, and downloads and installs all the
libraries and dependencies outlined in that file. If the file does not
exist it will look for composer.json and do the same.

<info>php composer.phar install</info>

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$composer = $this->getComposer();
$composer->getDownloadManager()->setOutputProgress(!$input->getOption('no-progress'));
$io = $this->getIO();
$install = Installer::create($io, $composer);

$preferSource = false;
$preferDist = false;
switch ($composer->getConfig()->get('preferred-install')) {
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

$install
->setDryRun($input->getOption('dry-run'))
->setVerbose($input->getOption('verbose'))
->setPreferSource($preferSource)
->setPreferDist($preferDist)
->setDevMode($input->getOption('dev'))
->setRunScripts(!$input->getOption('no-scripts'))
->setOptimizeAutoloader($input->getOption('optimize-autoloader'))
;

if ($input->getOption('no-custom-installers')) {
$install->disableCustomInstallers();
}

return $install->run() ? 0 : 1;
}
}
<?php











namespace Composer\Command;

use Composer\Util\ConfigValidator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;







class ValidateCommand extends Command
{



protected function configure()
{
$this
->setName('validate')
->setDescription('Validates a composer.json')
->setDefinition(array(
new InputArgument('file', InputArgument::OPTIONAL, 'path to composer.json file', './composer.json')
))
->setHelp(<<<EOT
The validate command validates a given composer.json

EOT
);
}







protected function execute(InputInterface $input, OutputInterface $output)
{
$file = $input->getArgument('file');

if (!file_exists($file)) {
$output->writeln('<error>' . $file . ' not found.</error>');

return 1;
}
if (!is_readable($file)) {
$output->writeln('<error>' . $file . ' is not readable.</error>');

return 1;
}

$validator = new ConfigValidator($this->getIO());
list($errors, $publishErrors, $warnings) = $validator->validate($file);


 if (!$errors && !$publishErrors && !$warnings) {
$output->writeln('<info>' . $file . ' is valid</info>');
} elseif (!$errors && !$publishErrors) {
$output->writeln('<info>' . $file . ' is valid, but with a few warnings</info>');
$output->writeln('<warning>See http://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
} elseif (!$errors) {
$output->writeln('<info>' . $file . ' is valid for simple usage with composer but has</info>');
$output->writeln('<info>strict errors that make it unable to be published as a package:</info>');
$output->writeln('<warning>See http://getcomposer.org/doc/04-schema.md for details on the schema</warning>');
} else {
$output->writeln('<error>' . $file . ' is invalid, the following errors/warnings were found:</error>');
}

$messages = array(
'error' => array_merge($errors, $publishErrors),
'warning' => $warnings,
);

foreach ($messages as $style => $msgs) {
foreach ($msgs as $msg) {
$output->writeln('<' . $style . '>' . $msg . '</' . $style . '>');
}
}

return $errors || $publishErrors ? 1 : 0;
}
}
<?php











namespace Composer\Command;

use Composer\DependencyResolver\Pool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;





class DependsCommand extends Command
{
protected $linkTypes = array(
'require' => array('requires', 'requires'),
'require-dev' => array('devRequires', 'requires (dev)'),
);

protected function configure()
{
$this
->setName('depends')
->setDescription('Shows which packages depend on the given package')
->setDefinition(array(
new InputArgument('package', InputArgument::REQUIRED, 'Package to inspect'),
new InputOption('link-type', '', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Link types to show (require, require-dev)', array_keys($this->linkTypes)),
))
->setHelp(<<<EOT
Displays detailed information about where a package is referenced.

<info>php composer.phar depends composer/composer</info>

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$repo = $this->getComposer()->getRepositoryManager()->getLocalRepository();
$needle = $input->getArgument('package');

$pool = new Pool();
$pool->addRepository($repo);

$packages = $pool->whatProvides($needle);
if (empty($packages)) {
throw new \InvalidArgumentException('Could not find package "'.$needle.'" in your project.');
}

$linkTypes = $this->linkTypes;

$types = array_map(function ($type) use ($linkTypes) {
$type = rtrim($type, 's');
if (!isset($linkTypes[$type])) {
throw new \InvalidArgumentException('Unexpected link type: '.$type.', valid types: '.implode(', ', array_keys($linkTypes)));
}

return $type;
}, $input->getOption('link-type'));

$messages = array();
$outputPackages = array();
foreach ($repo->getPackages() as $package) {
foreach ($types as $type) {
foreach ($package->{'get'.$linkTypes[$type][0]}() as $link) {
if ($link->getTarget() === $needle) {
if (!isset($outputPackages[$package->getName()])) {
$messages[] = '<info>'.$package->getPrettyName() . '</info> ' . $linkTypes[$type][1] . ' ' . $needle .' (<info>' . $link->getPrettyConstraint() . '</info>)';
$outputPackages[$package->getName()] = true;
}
}
}
}
}

if ($messages) {
sort($messages);
$output->writeln($messages);
} else {
$output->writeln('<info>There is no installed package depending on "'.$needle.'".</info>');
}
}
}
<?php











namespace Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Repository\RepositoryInterface;
use Composer\Package\CompletePackageInterface;
use Composer\Package\AliasPackage;
use Composer\Factory;




class SearchCommand extends Command
{
protected $matches;
protected $lowMatches = array();
protected $tokens;
protected $output;
protected $onlyName;

protected function configure()
{
$this
->setName('search')
->setDescription('Search for packages')
->setDefinition(array(
new InputOption('only-name', 'N', InputOption::VALUE_NONE, 'Search only in name'),
new InputArgument('