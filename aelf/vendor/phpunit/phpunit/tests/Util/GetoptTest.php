ln('<info>descrip.</info> : ' . $package->getDescription());
$output->writeln('<info>keywords</info> : ' . join(', ', $package->getKeywords() ?: array()));
$this->printVersions($input, $output, $package, $versions, $installedRepo, $repos);
$output->writeln('<info>type</info>     : ' . $package->getType());
$output->writeln('<info>license</info>  : ' . implode(', ', $package->getLicense()));
$output->writeln('<info>source</info>   : ' . sprintf('[%s] <comment>%s</comment> %s', $package->getSourceType(), $package->getSourceUrl(), $package->getSourceReference()));
$output->writeln('<info>dist</info>     : ' . sprintf('[%s] <comment>%s</comment> %s', $package->getDistType(), $package->getDistUrl(), $package->getDistReference()));
$output->writeln('<info>names</info>    : ' . implode(', ', $package->getNames()));

if ($package->getSupport()) {
$output->writeln("\n<info>support</info>");
foreach ($package->getSupport() as $type => $value) {
$output->writeln('<comment>' . $type . '</comment> : '.$value);
}
}

if ($package->getAutoload()) {
$output->writeln("\n<info>autoload</info>");
foreach ($package->getAutoload() as $type => $autoloads) {
$output->writeln('<comment>' . $type . '</comment>');

if ($type === 'psr-0') {
foreach ($autoloads as $name => $path) {
$output->writeln(($name ?: '*') . ' => ' . ($path ?: '.'));
}
} elseif ($type === 'classmap') {
$output->writeln(implode(', ', $autoloads));
}
}
if ($package->getIncludePaths()) {
$output->writeln('<comment>include-path</comment>');
$output->writeln(implode(', ', $package->getIncludePaths()));
}
}
}




protected function printVersions(InputInterface $input, OutputInterface $output, CompletePackageInterface $package, array $versions, RepositoryInterface $installedRepo, RepositoryInterface $repos)
{
uasort($versions, 'version_compare');
$versions = array_keys(array_reverse($versions));


 if ($installedRepo->hasPackage($package)) {
$installedVersion = $package->getPrettyVersion();
$key = array_search($installedVersion, $versions);
if (false !== $key) {
$versions[$key] = '<info>* ' . $installedVersion . '</info>';
}
}

$versions = implode(', ', $versions);

$output->writeln('<info>versions</info> : ' . $versions);
}










protected function printLinks(InputInterface $input, OutputInterface $output, CompletePackageInterface $package, $linkType, $title = null)
{
$title = $title ?: $linkType;
if ($links = $package->{'get'.ucfirst($linkType)}()) {
$output->writeln("\n<info>" . $title . "</info>");

foreach ($links as $link) {
$output->writeln($link->getTarget() . ' <comment>' . $link->getPrettyConstraint() . '</comment>');
}
}
}
}
<?php











namespace Composer\Command;

use Composer\Installer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;




class UpdateCommand extends Command
{
protected function configure()
{
$this
->setName('update')
->setDescription('Updates your dependencies to the latest version according to composer.json, and updates the composer.lock file.')
->setDefinition(array(
new InputArgument('packages', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'Packages that should be updated, if not provided all packages are.'),
new InputOption('prefer-source', null, InputOption::VALUE_NONE, 'Forces installation from package sources when possible, including VCS information.'),
new InputOption('prefer-dist', null, InputOption::VALUE_NONE, 'Forces installation from package dist even for dev versions.'),
new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Outputs the operations but will not execute anything (implicitly enables --verbose).'),
new InputOption('dev', null, InputOption::VALUE_NONE, 'Enables installation of require-dev packages (enabled by default, only present for sanity).'),
new InputOption('no-dev', null, InputOption::VALUE_NONE, 'Disables installation of require-dev packages.'),
new InputOption('no-custom-installers', null, InputOption::VALUE_NONE, 'Disables all custom installers.'),
new InputOption('no-scripts', null, InputOption::VALUE_NONE, 'Skips the execution of all scripts defined in composer.json file.'),
new InputOption('no-progress', null, InputOption::VALUE_NONE, 'Do not output download progress.'),
new InputOption('verbose', 'v|vv|vvv', InputOption::VALUE_NONE, 'Shows more details including new commits pulled in when updating packages.'),
new InputOption('optimize-autoloader', 'o', InputOption::VALUE_NONE, 'Optimize autoloader during autoloader dump')
))
->setHelp(<<<EOT
The <info>update</info> command reads the composer.json file from the
current directory, processes it, and updates, removes or installs all the
dependencies.

<info>php composer.phar update</info>

To limit the update operation to a few packages, you can list the package(s)
you want to update as such:

<info>php composer.phar update vendor/package1 foo/mypackage [...]</info>
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