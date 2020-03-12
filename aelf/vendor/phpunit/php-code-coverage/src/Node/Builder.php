it the
global config.json file.

    <comment>%command.full_name% --editor --global</comment>
EOT
)
;
}




protected function initialize(InputInterface $input, OutputInterface $output)
{
if ($input->getOption('global') && 'composer.json' !== $input->getOption('file')) {
throw new \RuntimeException('--file and --global can not be combined');
}

$this->config = Factory::createConfig();


 
 $configFile = $input->getOption('global')
? ($this->config->get('home') . '/config.json')
: $input->getOption('file');

$this->configFile = new JsonFile($configFile);
$this->configSource = new JsonConfigSource($this->configFile);


 if ($input->getOption('global') && !$this->configFile->exists()) {
touch($this->configFile->getPath());
$this->configFile->write(array('config' => new \ArrayObject));
chmod($this->configFile->getPath(), 0600);
}

if (!$this->configFile->exists()) {
throw new \RuntimeException('No composer.json found in the current directory');
}
}




protected function execute(InputInterface $input, OutputInterface $output)
{

 if ($input->getOption('editor')) {
$editor = getenv('EDITOR');
if (!$editor) {
if (defined('PHP_WINDOWS_VERSION_BUILD')) {
$editor = 'notepad';
} else {
foreach (array('vim', 'vi', 'nano', 'pico', 'ed') as $candidate) {
if (exec('which '.$candidate)) {
$editor = $candidate;
break;
}
}
}
}

system($editor . ' ' . $this->configFile->getPath() . (defined('PHP_WINDOWS_VERSION_BUILD') ? '': ' > `tty`'));

return 0;
}

if (!$input->getOption('global')) {
$this->config->merge($this->configFile->read());
}


 if ($input->getOption('list')) {
$this->listConfiguration($this->config->all(), $this->config->raw(), $output);

return 0;
}

$settingKey = $input->getArgument('setting-key');
if (!$settingKey) {
return 0;
}


 if (array() !== $input->getArgument('setting-value') && $input->getOption('unset')) {
throw new \RuntimeException('You can not combine a setting value with --unset');
}


 if (array() === $input->getArgument('setting-value') && !$input->getOption('unset')) {
$data = $this->config->all();
if (preg_match('/^repos?(?:itories)?(?:\.(.+))?/', $settingKey, $matches)) {
if (empty($matches[1])) {
$value = isset($data['repositories']) ? $data['repositories'] : array();
} else {
if (!isset($data['repositories'][$matches[1]])) {
throw new \InvalidArgumentException('There is no '.$matches[1].' repository defined');
}

$value = $data['repositories'][$matches[1]];
}
} elseif (strpos($settingKey, '.')) {
$bits = explode('.', $settingKey);
$data = $data['config'];
foreach ($bits as $bit) {
if (isset($data[$bit])) {
$data = $data[$bit];
} elseif (isset($data[implode('.', $bits)])) {

 $data = $data[implode('.', $bits)];
break;
} else {
throw new \RuntimeException($settingKey.' is not defined');
}
array_shift($bits);
}

$value = $data;
} elseif (isset($data['config'][$settingKey])) {
$value = $data['config'][$settingKey];
} else {
throw new \RuntimeException($settingKey.' is not defined');
}

if (is_array($value)) {
$value = json_encode($value);
}

$output->writeln($value);

return 0;
}

$values = $input->getArgument('setting-value'); 


 if (preg_match('/^repos?(?:itories)?\.(.+)/', $settingKey, $matches)) {
if ($input->getOption('unset')) {
return $this->configSource->removeRepository($matches[1]);
}

if (2 !== count($values)) {
throw new \RuntimeException('You must pass the type and a url. Example: php composer.phar config repositories.foo vcs http://bar.com');
}

return $this->configSource->addRepository($matches[1], array(
'type' => $values[0],
'url' => $values[1],
));
}


 if (preg_match('/^github-oauth\.(.+)/', $settingKey, $matches)) {
if ($input->getOption('unset')) {
return $this->configSource->removeConfigSetting('github-oauth.'.$matches[1]);
}

if (1 !== count($values)) {
throw new \RuntimeException('Too many arguments, expected only one token');
}

return $this->configSource->addConfigSetting('github-oauth.'.$matches[1], $values[0]);
}

$booleanValidator = function ($val) { return in_array($val, array('true', 'false', '1', '0'), true); };
$booleanNormalizer = function ($val) { return $val !== 'false' && (bool) $val; };


 $uniqueConfigValues = array(
'process-timeout' => array('is_numeric', 'intval'),
'use-include-path' => array(
$booleanValidator,
$booleanNormalizer
),
'preferred-install' => array(
function ($val) { return in_array($val, array('auto', 'source', 'dist'), true); },
function ($val) { return $val; }
),
'notify-on-install' => array(
$booleanValidator,
$booleanNormalizer
),
'vendor-dir' => array('is_string', function ($val) { return $val; }),
'bin-dir' => array('is_string', function ($val) { return $val; }),
'cache-dir' => array('is_string', function ($val) { return $val; }),
'cache-files-dir' => array('is_string', function ($val) { return $val; }),
'cache-repo-dir' => array('is_string', function ($val) { return $val; }),
'cache-vcs-dir' => array('is_string', function ($val) { return $val; }),
'cache-ttl' => array('is_numeric', 'intval'),
'cache-files-ttl' => array('is_numeric', 'intval'),
'cache-files-maxsize' => array(
function ($val) { return preg_match('/^\s*([0-9.]+)\s*(?:([kmg])(?:i?b)?)?\s*$/i', $val) > 0; },
function ($val) { return $val; }
),
'discard-changes' => array(
function ($val) { return in_array($val, array('stash', 'true', 'false', '1', '0'), true); },
function ($val) {
if ('stash' === $val) {
return 'stash';
}
return $val !== 'false' && (bool) $val;
}
),
);
$multiConfigValues = array(
'github-protocols' => array(
function ($vals) {
if (!is_array($vals)) {
return 'array expected';
}

foreach ($vals as $val) {
if (!in_array($val, array('git', 'https', 'http'))) {
return 'valid protocols include: git, https, http';
}
}

return true;
},
function ($vals) {
return $vals;
}
),
);

foreach ($uniqueConfigVa