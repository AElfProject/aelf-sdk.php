rmat: '.
'John Smith <john@example.com>'
);
}

protected function configure()
{
$this
->setName('init')
->setDescription('Creates a basic composer.json file in current directory.')
->setDefinition(array(
new InputOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the package'),
new InputOption('description', null, InputOption::VALUE_REQUIRED, 'Description of package'),
new InputOption('author', null, InputOption::VALUE_REQUIRED, 'Author name of package'),

 new InputOption('homepage', null, InputOption::VALUE_REQUIRED, 'Homepage of package'),
new InputOption('require', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Package to require with a version constraint, e.g. foo/bar:1.0.0 or foo/bar=1.0.0 or "foo/bar 1.0.0"'),
new InputOption('require-dev', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Package to require for development with a version constraint, e.g. foo/bar:1.0.0 or foo/bar=1.0.0 or "foo/bar 1.0.0"'),
new InputOption('stability', 's', InputOption::VALUE_REQUIRED, 'Minimum stability (empty or one of: '.implode(', ', array_keys(BasePackage::$stabilities)).')'),
new InputOption('license', 'l', InputOption::VALUE_REQUIRED, 'License of package'),
))
->setHelp(<<<EOT
The <info>init</info> command creates a basic composer.json file
in the current directory.

<info>php composer.phar init</info>

EOT
)
;
}

protected function execute(InputInterface $input, OutputInterface $output)
{
$dialog = $this->getHelperSet()->get('dialog');

$whitelist = array('name', 'description', 'author', 'homepage', 'require', 'require-dev', 'stability', 'license');

$options = array_filter(array_intersect_key($input->getOptions(), array_flip($whitelist)));

if (isset($options['author'])) {
$options['authors'] = $this->formatAuthors($options['author']);
unset($options['author']);
}

if (isset($options['stability'])) {
$options['minimum-stability'] = $options['stability'];
unset($options['stability']);
}

$options['require'] = isset($options['require']) ? $this->formatRequirements($options['require']) : new \stdClass;
if (array() === $options['require']) {
$options['require'] = new \stdClass;
}

if (isset($options['require-dev'])) {
$options['require-dev'] = $this->formatRequirements($options['require-dev']) ;
if (array() === $options['require-dev']) {
$options['require-dev'] = new \stdClass;
}
}

$file = new JsonFile('composer.json');

$json = $file->encode($options);

if ($input->isInteractive()) {
$output->writeln(array(
'',
$json,
''
));
if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
$output->writeln('<error>Command aborted</error>');

return 1;
}
}

$file->write($options);

if ($input->isInteractive() && is_dir('.git')) {
$ignoreFile = realpath('.gitignore');

if (false === $ignoreFile) {
$ignoreFile = realpath('.') . '/.gitignore';
}

if (!$this->hasVendorIgnore($ignoreFile)) {
$question = 'Would you like the <info>vendor</info> directory added to your <info>.gitignore</info> [<comment>yes</comment>]?';

if ($dialog->askConfirmation($output, $question, true)) {
$this->addVendorIgnore($ignoreFile);
}
}
}
}

protected function interact(InputInterface $input, OutputInterface $output)
{
$git = $this->getGitConfig();

$dialog = $this->getHelperSet()->get('dialog');
$formatter = $this->getHelperSet()->get('formatter');
$output->writeln(array(
'',
$formatter->formatBlock('Welcome to the Composer config generator', 'bg=blue;fg=white', true),
''
));


 $output->writeln(array(
'',
'This command will guide you through creating your composer.json config.',
'',
));

$cwd = realpath(".");

if (!$name = $input->getOption('name')) {
$name = basename($cwd);
$name = preg_replace('{(?:([a-z])([A-Z])|([A-Z])([A-Z][a-z]))}', '\\1\\3-\\2\\4', $name);
$name = strtolower($name);
if (isset($git['github.user'])) {
$name = $git['github.user'] . '/' . $name;
} elseif (!empty($_SERVER['USERNAME'])) {
$name = $_SERVER['USERNAME'] . '/' . $name;
} elseif (get_current_user()) {
$name = get_current_user() . '/' . $name;
} else {

 $name = $name . '/' . $name;
}
} else {
if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $name)) {
throw new \InvalidArgumentException(
'The package name '.$name.' is invalid, it should be lowercase and have a vendor name, a forward slash, and a package name, matching: [a-z0-9_.-]+/[a-z0-9_.-]+'
);
}
}

$name = $dialog->askAndValidate(
$output,
$dialog->getQuestion('Package name (<vendor>/<name>)', $name),
function ($value) use ($name) {
if (null === $value) {
return $name;
}

if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $value)) {
throw new \InvalidArgumentException(
'The package name '.$value.' is invalid, it should be lowercase and have a vendor name, a forward slash, and a package name, matching: [a-z0-9_.-]+/[a-z0-9_.-]+'
);
}

return $value;
}
);
$input->setOption('name', $name);

$description = $input->getOption('description') ?: false;
$description = $dialog->ask(
$output,
$dialog->getQuestion('Description', $description)
);
$input->setOption('description', $description);

if (null === $author = $input->getOption('author')) {
if (isset($git['user.name']) && isset($git['user.email'])) {
$author = sprintf('%s <%s>', $git['user.name'], $git['user.email']);
}
}

$self = $this;
$author = $dialog->askAndValidate(
$output,
$dialog->getQuestion('Author', $author),
function ($value) use ($self, $author) {
if (null === $value) {
return $author;
}

$author = $self->parseAuthorString($value);

return sprintf('%s <%s>', $author['name'], $author['email']);
}
);
$input->setOption('author', $author);

$minimumStability = $input->getOption('stability') ?: '';
$minimumStability = $dialog->askAndValidate(
$output,
$dialog->getQuestion('Minimum Stability', $minimumStability),
function ($value) use ($self, $minimumStability) {
if (null === $value) {
return $minimumStability;
}

if (!isset(BasePackage::$stabilities[$value])) {
throw new \InvalidArgumentException(
'Invalid minimum stability "'.$value.'". Must be empty or one of: '.
implode(', ', array_keys(BasePackage::$stabilities))
);
}

return $value;
}
);
$input->setOption('stability', $minimumStability);

$license = $input->getOption('license') ?: false;
$license = $dialog->ask(
$output,
$dialog->getQuestion('License', $license)
);
$input->setOption('license', $license);

$output->writeln(array(
'',
'Define your dependencies.',
''
));

$requirements = array();
if ($dialog->askConfirmation($output, $dialog->getQuestion('Would you like to define your dependencies (require) interactively', 'yes', '?'), true)) {
$requirements = $this->determineRequirements($input, $output, $input->getOption('require'));
}
$input->setOption('require', $requirements);
$devRequirements = array();
if ($dialog->askConfirmation($output, $dialog->getQuestion('Would you like to define your dev dependencies (require-dev) interactively', 'yes', '?'), true)) {
$devRequirements = $this->determineRequirements($input, $output, $input->getOption('require-dev'));
}
$input->setOption('require-dev', $devRequirements);
}

protected function findPackages($name)
{
$packages = array();


 if (!$this->repos) {
$this->repos = new CompositeRepository(array_merge(
array(new PlatformRepository),
Factory::createDefaultRepositories($this->getIO())
));
}

return $this->repos->search($name);
}

protected function determineRequirements(InputInterface $input, OutputInterface $output, $requires = array())
{
$dialog = $this->getHelperSet()->get('dialog');
$prompt = $dialog->getQuestion('Search for a package', false, ':');

if ($requires) {
$requires = $this->normalizeRequirements($requires);
$result = array();

foreach ($requires as $key => $requirement) {
if (!isset($requirement['version']) && $input->isInteractive()) {
$question = $dialog->getQuestion('Please provide a version constraint for the '.$requirement['name'].' requirement');
if ($constraint = $dialog->ask($output, $question)) {
$requirement['version'] = $constraint;
}
}
if (!isset($requirement['version'])) {
throw new \InvalidArgumentException('The requirement '.$requirement['name'].' must contain a version constraint');
}

$result[] = $requirement['name'] . ' ' . $requirement['version'];
}

return $result;
}

while (null !== $package = $dialog->ask($output, $prompt)) {
$matches = $this->findPackages($package);

if (count($matches)) {
$output->writeln(array(
'',
sprintf('Found <info>%s</info> packages matching <info>%s</info>', count($matches), $package),
''
));

$exactMatch = null;
$choices = array();
foreach ($matches as $position => $package) {
$choices[] = sprintf(' <info>%5s</info> %s', "[$position]", $package['name']);
if ($package['name'] === $package) {
$exactMatch = true;
break;
}
}


 if (!$exactMatch) {
$output->writeln($choices);
$output->writeln('');

$validator = function ($selection) use ($matches