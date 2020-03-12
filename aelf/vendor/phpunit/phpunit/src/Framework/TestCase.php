unction setStdin($stdin)
{
$this->stdin = $stdin;

return $this;
}






public function getOptions()
{
return $this->options;
}








public function setOptions(array $options)
{
$this->options = $options;

return $this;
}








public function getEnhanceWindowsCompatibility()
{
return $this->enhanceWindowsCompatibility;
}








public function setEnhanceWindowsCompatibility($enhance)
{
$this->enhanceWindowsCompatibility = (Boolean) $enhance;

return $this;
}






public function getEnhanceSigchildCompatibility()
{
return $this->enhanceSigchildCompatibility;
}












public function setEnhanceSigchildCompatibility($enhance)
{
$this->enhanceSigchildCompatibility = (Boolean) $enhance;

return $this;
}









public function checkTimeout()
{
if (0 < $this->timeout && $this->timeout < microtime(true) - $this->starttime) {
$this->stop(0);

throw new RuntimeException('The process timed-out.');
}
}






private function getDescriptors()
{

 
 
 if (defined('PHP_WINDOWS_VERSION_BUILD')) {
$this->fileHandles = array(
self::STDOUT => tmpfile(),
);
if (false === $this->fileHandles[self::STDOUT]) {
throw new RuntimeException('A temporary file could not be opened to write the process output to, verify that your TEMP environment variable is writable');
}
$this->readBytes = array(
self::STDOUT => 0,
);

return array(array('pipe', 'r'), $this->fileHandles[self::STDOUT], array('pipe', 'w'));
} 

if ($this->tty) {
$descriptors = array(
array('file', '/dev/tty', 'r'),
array('file', '/dev/tty', 'w'),
array('file', '/dev/tty', 'w'),
);
} else {
$descriptors = array(
array('pipe', 'r'), 
 array('pipe', 'w'), 
 array('pipe', 'w'), 
 );
}

if ($this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {

 $descriptors = array_merge($descriptors, array(array('pipe', 'w')));

$this->commandline = '('.$this->commandline.') 3>/dev/null; code=$?; echo $code >&3; exit $code';
}

return $descriptors;
}











protected function buildCallback($callback)
{
$that = $this;
$out = self::OUT;
$err = self::ERR;
$callback = function ($type, $data) use ($that, $callback, $out, $err) {
if ($out == $type) {
$that->addOutput($data);
} else {
$that->addErrorOutput($data);
}

if (null !== $callback) {
call_user_func($callback, $type, $data);
}
};

return $callback;
}




protected function updateStatus()
{
if (self::STATUS_STARTED !== $this->status) {
return;
}

$this->processInformation = proc_get_status($this->process);
if (!$this->processInformation['running']) {
$this->status = self::STATUS_TERMINATED;
if (-1 !== $this->processInformation['exitcode']) {
$this->exitcode = $this->processInformation['exitcode'];
}
}
}




protected function updateErrorOutput()
{
if (isset($this->pipes[self::STDERR]) && is_resource($this->pipes[self::STDERR])) {
$this->addErrorOutput(stream_get_contents($this->pipes[self::STDERR]));
}
}




protected function updateOutput()
{
if (defined('PHP_WINDOWS_VERSION_BUILD') && isset($this->fileHandles[self::STDOUT]) && is_resource($this->fileHandles[self::STDOUT])) {
fseek($this->fileHandles[self::STDOUT], $this->readBytes[self::STDOUT]);
$this->addOutput(stream_get_contents($this->fileHandles[self::STDOUT]));
} elseif (isset($this->pipes[self::STDOUT]) && is_resource($this->pipes[self::STDOUT])) {
$this->addOutput(stream_get_contents($this->pipes[self::STDOUT]));
}
}






protected function isSigchildEnabled()
{
if (null !== self::$sigchild) {
return self::$sigchild;
}

ob_start();
phpinfo(INFO_GENERAL);

return self::$sigchild = false !== strpos(ob_get_clean(), '--enable-sigchild');
}







private function processFileHandles($callback, $closeEmptyHandles = false)
{
$fh = $this->fileHandles;
foreach ($fh as $type => $fileHandle) {
fseek($fileHandle, $this->readBytes[$type]);
$data = fread($fileHandle, 8192);
if (strlen($data) > 0) {
$this->readBytes[$type] += strlen($data);
call_user_func($callback, $type == 1 ? self::OUT : self::ERR, $data);
}
if (false === $data || ($closeEmptyHandles && '' === $data && feof($fileHandle))) {
fclose($fileHandle);
unset($this->fileHandles[$type]);
}
}
}
}
<?php










namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;






class ProcessBuilder
{
private $arguments;
private $cwd;
private $env;
private $stdin;
private $timeout;
private $options;
private $inheritEnv;
private $prefix;

public function __construct(array $arguments = array())
{
$this->arguments = $arguments;

$this->timeout = 60;
$this->options = array();
$this->env = array();
$this->inheritEnv = true;
}

public static function create(array $arguments = array())
{
return new static($arguments);
}








public function add($argument)
{
$this->arguments[] = $argument;

return $this;
}










public function setPrefix($prefix)
{
$this->prefix = $prefix;

return $this;
}






public function setArguments(array $arguments)
{
$this->arguments = $arguments;

return $this;
}

public function setWorkingDirectory($cwd)
{
$this->cwd = $cwd;

return $this;
}

public function inheritEnvironmentVariables($inheritEnv = true)
{
$this->inheritEnv = $inheritEnv;

return $this;
}

public function setEnv($name, $value)
{
$this->env[$name] = $value;

return $this;
}

public function setInput($stdin)
{
$this->stdin = $stdin;

return $this;
}












public function setTimeout($timeout)
{
if (null === $timeout) {
$this->timeout = null;

return $this;
}

$timeout = (float) $timeout;

if ($timeout < 0) {
throw new InvalidArgumentException('The timeout value must be a valid positive integer or float number.');
}

$this->timeout = $timeout;

return $this;
}

public function setOption($name, $value)
{
$this->options[$name] = $value;

return $this;
}

public function getProcess()
{
if (!$this->prefix && !count($this->arguments)) {
throw new LogicException('You must add() command arguments before calling getProcess().');
}

$options = $this->options;

$arguments = $this->prefix ? array_merge(array($this->prefix), $this->arguments) : $this->arguments;
$script = implode(' ', array_map(array(__NAMESPACE__.'\\ProcessUtils', 'escapeArgument'), $arguments));

if ($this->inheritEnv) {
$env = $this->env ? $this->env + $_ENV : null;
} else {
$env = $this->env;
}

return new Process($script, $this->cwd, $env, $this->stdin, $this->timeout, $options);
}
}
<?php










namespace Symfony\Component\Process;








class ProcessUtils
{



private function __construct()
{
}








public static function escapeArgument($argument)
{

 
 
 
 if (defined('PHP_WINDOWS_VERSION_BUILD')) {
$escapedArgument = '';
foreach(preg_split('/([%"])/i', $argument, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE) as $part) {
if ('"' == $part) {
$escapedArgument .= '\\"';
} elseif ('%' == $part) {
$escapedArgument .= '^%';
} else {
$escapedArgument .= escapeshellarg($part);
}
}

return $escapedArgument;
}

return escapeshellarg($argument);
}
}
<?php










namespace Symfony\Component\Process\Exception;






interface ExceptionInterface
{
}
<?php










namespace Symfony\Component\Process\Exception;






class RuntimeException extends \RuntimeException implements ExceptionInterface
{
}
<?php










namespace Symfony\Component\Process\Exception;






class LogicException extends \LogicException implements ExceptionInterface
{
}
<?php










namespace Symfony\Component\Process\Exception;

use Symfony\Component\Process\Process;






class ProcessFailedException extends RuntimeException
{
private $process;

public function __construct(Process $process)
{
if ($process->isSuccessful()) {
throw new InvalidArgumentException('Expected a failed process, but the given process was successful.');
}

parent::__construct(
sprintf(
'The command "%s" failed.'."\nExit Code: %s(%s)\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
$process->getCommandLine(),
$process->getExitCode(),
$process->getExitCodeText(),
$process->getOutput(),
$process->getErrorOutput()
)
);

$this->process = $process;
}

public function getProcess()
{
return $this->process;
}
}
<?php










namespace Symfony\Component\Process\Exception;






class InvalidArgumentException extends \InvalidArgumentException implements ExceptionInterface
{
}
<?php










namespace Symfony\Component\Process;







class PhpExecutableFinder
{
private $executableFinder;

public function __construct()
{
$this->executableFinder = new ExecutableFinder();
}






public function find()
{

 if (defined('PHP_BINARY') && PHP_BINARY && ('cli' === PHP_SAPI)) {
return PHP_BINARY;
}

if ($php = getenv('PHP_PATH')) {
if (!is_executable($php)) {
return false;
}

return $php;
}

if ($php = getenv('PHP_PEAR_PHP_BIN')) {
if (is_executable($php)) {
return $php;
}
}

$dirs = array(PHP_BINDIR);
if (defined('PHP_WINDOWS_VERSION_BUILD')) {
$dirs[] = 'C:\xampp\php\\';
}

return $this->executableFinder->find('php', false, $dirs);
}
}
<?php










namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;






class HelpCommand extends Command
{
private $command;




protected function configure()
{
$this->ignoreValidationErrors();

$this
->setName('help')
->setDefinition(array(
new InputArgument('command_name', InputArgument::OPTIONAL, 'The command name', 'help'),
new InputOption('xml', null, InputOption::VALUE_NONE, 'To output help as XML'),
new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output help in other formats'),
new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command help'),
))
->setDescription('Displays help for a command')
->setHelp(<<<EOF
The <info>%command.name%</info> command displays help for a given command:

  <info>php %command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
)
;
}






public function setCommand(Command $command)
{
$this->command = $command;
}




protected function execute(InputInterface $input, OutputInterface $output)
{
if (null === $this->command) {
$this->command = $this->getApplication()->find($input->getArgument('command_name'));
}

if ($input->getOption('xml')) {
$input->setOption('format', 'xml');
}

$helper = new DescriptorHelper();
$helper->describe($output, $this->command, $input->getOption('format'), $input->getOption('raw'));
$this->command = null;
}
}
<?php










namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;








class Command
{
private $application;
private $name;
private $aliases;
private $definition;
private $help;
private $description;
private $ignoreValidationErrors;
private $applicationDefinitionMerged;
private $applicationDefinitionMergedWithArgs;
private $code;
private $synopsis;
private $helperSet;










public function __construct($name = null)
{
$this->definition = new InputDefinition();
$this->ignoreValidationErrors = false;
$this->applicationDefinitionMerged = false;
$this->applicationDefinitionMergedWithArgs = false;
$this->aliases = array();

if (null !== $name) {
$this->setName($name);
}

$this->configure();

if (!$this->name) {
throw new \LogicException('The command name cannot be empty.');
}
}






public function ignoreValidationErrors()
{
$this->ignoreValidationErrors = true;
}








public function setApplication(Application $application = null)
{
$this->application = $application;
if ($application) {
$this->setHelperSet($application->getHelperSet());
} else {
$this->helperSet = null;
}
}






public function setHelperSet(HelperSet $helperSet)
{
$this->helperSet = $helperSet;
}






public function getHelperSet()
{
return $this->helperSet;
}








public function getApplication()
{
return $this->application;
}









public function isEnabled()
{
return true;
}




protected function configure()
{
}

















protected function execute(InputInterface $input, OutputInterface $output)
{
throw new \LogicException('You must override the execute() method in the concrete command class.');
}







protected function interact(InputInterface $input, OutputInterface $output)
{
}










protected function initialize(InputInterface $input, OutputInterface $output)
{
}




















public function run(InputInterface $input, OutputInterface $output)
{

 $this->getSynopsis();


 $this->mergeApplicationDefinition();


 try {
$input->bind($this->definition);
} catch (\Exception $e) {
if (!$this->ignoreValidationErrors) {
throw $e;
}
}

$this->initialize($input, $output);

if ($input->isInteractive()) {
$this->interact($input, $output);
}

$input->validate();

if ($this->code) {
$statusCode = call_user_func($this->code, $input, $output);
} else {
$statusCode = $this->execute($input, $output);
}

return is_numeric($statusCode) ? $statusCode : 0;
}

















public function setCode($code)
{
if (!is_callable($code)) {
throw new \InvalidArgumentException('Invalid callable provided to Command::setCode.');
}

$this->code = $code;

return $this;
}








public function mergeApplicationDefinition($mergeArgs = true)
{
if (null === $this->application || (true === $this->applicationDefinitionMerged && ($this->applicationDefinitionMergedWithArgs || !$mergeArgs))) {
return;
}

if ($mergeArgs) {
$currentArguments = $this->definition->getArguments();
$this->definition->setArguments($this->application->getDefinition()->getArguments());
$this->definition->addArguments($currentArguments);
}

$this->definition->addOptions($this->application->getDefinition()->getOptions());

$this->applicationDefinitionMerged = true;
if ($mergeArgs) {
$this->applicationDefinitionMergedWithArgs = true;
}
}










public function setDefinition($definition)
{
if ($definition instanceof InputDefinition) {
$this->definition = $definition;
} else {
$this->definition->setDefinition($definition);
}

$this->applicationDefinitionMerged = false;

return $this;
}








public function getDefinition()
{
return $this->definition;
}











public function getNativeDefinition()
{
return $this->getDefinition();
}













public function addArgument($name, $mode = null, $description = '', $default = null)
{
$this->definition->addArgument(new InputArgument($name, $mode, $description, $default));

return $this;
}














public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
{
$this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));

return $this;
}

















public function setName($name)
{
$this->validateName($name);

$this->name = $name;

return $this;
}








public function getName()
{
return $this->name;
}










public function setDescription($description)
{
$this->description = $description;

return $this;
}








public function getDescription()
{
return $this->description;
}










public function setHelp($help)
{
$this->help = $help;

return $this;
}








public function getHelp()
{
return $this->help;
}







public function getProcessedHelp()
{
$name = $this->name;

$placeholders = array(
'%command.name%',
'%command.full_name%'
);
$replacements = array(
$name,
$_SERVER['PHP_SELF'].' '.$name
);

return str_replace($placeholders, $replacements, $this->getHelp());
}










public function setAliases($aliases)
{
foreach ($aliases as $alias) {
$this->validateName($alias);
}

$this->aliases = $aliases;

return $this;
}








public function getAliases()
{
return $this->aliases;
}






public function getSynopsis()
{
if (null === $this->synopsis) {
$this->synopsis = trim(sprintf('%s %s', $this->name, $this->definition->getSynopsis()));
}

return $this->synopsis;
}












public function getHelper($name)
{
return $this->helperSet->get($name);
}








public function asText()
{
$descriptor = new TextDescriptor();

return $descriptor->describe($this);
}










public function asXml($asDom = false)
{
$descriptor = new XmlDescriptor();

return $descriptor->describe($this, array('as_dom' => $asDom));
}

private function validateName($name)
{
if (!preg_match('/^[^\:]+(\:[^\:]+)*$/', $name)) {
throw new \InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
}
}
}
<?php










namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputDefinition;






class ListCommand extends Command
{



protected function configure()
{
$this
->setName('list')
->setDefinition($this->createDefinition())
->setDescription('Lists commands')
->setHelp(<<<EOF
The <info>%command.name%</info> command lists all commands:

  <info>php %command.full_name%</info>

You can also display the commands for a specific namespace:

  <info>php %command.full_name% test</info>

You can also output the information in other formats by using the <comment>--format</comment> option:

  <info>php %command.full_name% --format=xml</info>

It's also possible to get raw list of commands (useful for embedding command runner):

  <info>php %command.full_name% --raw</info>
EOF
)
;
}




public function getNativeDefinition()
{
return $this->createDefinition();
}




protected function execute(InputInterface $input, OutputInterface $output)
{
if ($input->getOption('xml')) {
$input->setOption('format', 'xml');
}

$helper = new DescriptorHelper();
$helper->describe($output, $this->getApplication(), $input->getOption('format'), $input->getOption('raw'));
}




private function createDefinition()
{
return new InputDefinition(array(
new InputArgument('namespace', InputArgument::OPTIONAL, 'The namespace name'),
new InputOption('xml', null, InputOption::VALUE_NONE, 'To output list as XML'),
new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw command list'),
new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output list in other formats'),
));
}
}
<?php










namespace Symfony\Component\Console\Tester;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;











class ApplicationTester
{
private $application;
private $input;
private $output;






public function __construct(Application $application)
{
$this->application = $application;
}















public function run(array $input, $options = array())
{
$this->input = new ArrayInput($input);
if (isset($options['interactive'])) {
$this->input->setInteractive($options['interactive']);
}

$this->output = new StreamOutput(fopen('php://memory', 'w', false));
if (isset($options['decorated'])) {
$this->output->setDecorated($options['decorated']);
}
if (isset($options['verbosity'])) {
$this->output->setVerbosity($options['verbosity']);
}

return $this->application->run($this->input, $this->output);
}








public function getDisplay($normalize = false)
{
rewind($this->output->getStream());

$display = stream_get_contents($this->output->getStream());

if ($normalize) {
$display = str_replace(PHP_EOL, "\n", $display);
}

return $display;
}






public function getInput()
{
return $this->input;
}






public function getOutput()
{
return $this->output;
}
}
<?php










namespace Symfony\Component\Console\Tester;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;






class CommandTester
{
private $command;
private $input;
private $output;






public function __construct(Command $command)
{
$this->command = $command;
}















public function execute(array $input, array $options = array())
{
$this->input = new ArrayInput($input);
if (isset($options['interactive'])) {
$this->input->setInteractive($options['interactive']);
}

$this->output = new StreamOutput(fopen('php://memory', 'w', false));
if (isset($options['decorated'])) {
$this->output->setDecorated($options['decorated']);
}
if (isset($options['verbosity'])) {
$this->output->setVerbosity($options['verbosity']);
}

return $this->command->run($this->input, $this->output);
}








public function getDisplay($normalize = false)
{
rewind($this->output->getStream());

$display = stream_get_contents($this->output->getStream());

if ($normalize) {
$display = str_replace(PHP_EOL, "\n", $display);
}

return $display;
}






public function getInput()
{
return $this->input;
}






public function getOutput()
{
return $this->output;
}
}
<?php










namespace Symfony\Component\Console\Formatter;




class OutputFormatterStyleStack
{



private $styles;




private $emptyStyle;






public function __construct(OutputFormatterStyleInterface $emptyStyle = null)
{
$this->emptyStyle = $emptyStyle ?: new OutputFormatterStyle();
$this->reset();
}




public function reset()
{
$this->styles = array();
}






public function push(OutputFormatterStyleInterface $style)
{
$this->styles[] = $style;
}










public function pop(OutputFormatterStyleInterface $style = null)
{
if (empty($this->styles)) {
return $this->emptyStyle;
}

if (null === $style) {
return array_pop($this->styles);
}

foreach (array_reverse($this->styles, true) as $index => $stackedStyle) {
if ($style->apply('') === $stackedStyle->apply('')) {
$this->styles = array_slice($this->styles, 0, $index);

return $stackedStyle;
}
}

throw new \InvalidArgumentException('Incorrectly nested style tag found.');
}






public function getCurrent()
{
if (empty($this->styles)) {
return $this->emptyStyle;
}

return $this->styles[count($this->styles)-1];
}






public function setEmptyStyle(OutputFormatterStyleInterface $emptyStyle)
{
$this->emptyStyle = $emptyStyle;

return $this;
}




public function getEmptyStyle()
{
return $this->emptyStyle;
}
}
<?php










namespace Symfony\Component\Console\Formatter;








class OutputFormatterStyle implements OutputFormatterStyleInterface
{
private static $availableForegroundColors = array(
'black' => 30,
'red' => 31,
'green' => 32,
'yellow' => 33,
'blue' => 34,
'magenta' => 35,
'cyan' => 36,
'white' => 37
);
private static $availableBackgroundColors = array(
'black' => 40,
'red' => 41,
'green' => 42,
'yellow' => 43,
'blue' => 44,
'magenta' => 45,
'cyan' => 46,
'white' => 47
);
private static $availableOptions = array(
'bold' => 1,
'underscore' => 4,
'blink' => 5,
'reverse' => 7,
'conceal' => 8
);

private $foreground;
private $background;
private $options = array();










public function __construct($foreground = null, $background = null, array $options = array())
{
if (null !== $foreground) {
$this->setForeground($foreground);
}
if (null !== $background) {
$this->setBackground($background);
}
if (count($options)) {
$this->setOptions($options);
}
}










public function setForeground($color = null)
{
if (null === $color) {
$this->foreground = null;

return;
}

if (!isset(static::$availableForegroundColors[$color])) {
throw new \InvalidArgumentException(sprintf(
'Invalid foreground color specified: "%s". Expected one of (%s)',
$color,
implode(', ', array_keys(static::$availableForegroundColors))
));
}

$this->foreground = static::$availableForegroundColors[$color];
}










public function setBackground($color = null)
{
if (null === $color) {
$this->background = null;

return;
}

if (!isset(static::$availableBackgroundColors[$color])) {
throw new \InvalidArgumentException(sprintf(
'Invalid background color specified: "%s". Expected one of (%s)',
$color,
implode(', ', array_keys(static::$availableBackgroundColors))
));
}

$this->background = static::$availableBackgroundColors[$color];
}










public function setOption($option)
{
if (!isset(static::$availableOptions[$option])) {
throw new \InvalidArgumentException(sprintf(
'Invalid option specified: "%s". Expected one of (%s)',
$option,
implode(', ', array_keys(static::$availableOptions))
));
}

if (false === array_search(static::$availableOptions[$option], $this->options)) {
$this->options[] = static::$availableOptions[$option];
}
}









public function unsetOption($option)
{
if (!isset(static::$availableOptions[$option])) {
throw new \InvalidArgumentException(sprintf(
'Invalid option specified: "%s". Expected one of (%s)',
$option,
implode(', ', array_keys(static::$availableOptions))
));
}

$pos = array_search(static::$availableOptions[$option], $this->options);
if (false !== $pos) {
unset($this->options[$pos]);
}
}






public function setOptions(array $options)
{
$this->options = array();

foreach ($options as $option) {
$this->setOption($option);
}
}








public function apply($text)
{
$codes = array();

if (null !== $this->foreground) {
$codes[] = $this->foreground;
}
if (null !== $this->background) {
$codes[] = $this->background;
}
if (count($this->options)) {
$codes = array_merge($codes, $this->options);
}

if (0 === count($codes)) {
return $text;
}

return sprintf("\033[%sm%s\033[0m", implode(';', $codes), $text);
}
}
<?php










namespace Symfony\Component\Console\Formatter;








interface OutputFormatterStyleInterface
{







public function setForeground($color = null);








public function setBackground($color = null);








public function setOption($option);






public function unsetOption($option);






public function setOptions(array $options);








public function apply($text);
}
<?php










namespace Symfony\Component\Console\Formatter;








class OutputFormatter implements OutputFormatterInterface
{



const FORMAT_PATTERN = '#(\\\\?)<(/?)([a-z][a-z0-9_=;-]+)?>((?: [^<\\\\]+ | (?!<(?:/?[a-z]|/>)). | .(?<=\\\\<) )*)#isx';

private $decorated;
private $styles = array();
private $styleStack;








public static function escape($text)
{
return preg_replace('/([^\\\\]?)</is', '$1\\<', $text);
}









public function __construct($decorated = null, array $styles = array())
{
$this->decorated = (Boolean) $decorated;

$this->setStyle('error', new OutputFormatterStyle('white', 'red'));
$this->setStyle('info', new OutputFormatterStyle('green'));
$this->setStyle('comment', new OutputFormatterStyle('yellow'));
$this->setStyle('question', new OutputFormatterStyle('black', 'cyan'));

foreach ($styles as $name => $style) {
$this->setStyle($name, $style);
}

$this->styleStack = new OutputFormatterStyleStack();
}








public function setDecorated($decorated)
{
$this->decorated = (Boolean) $decorated;
}








public function isDecorated()
{
return $this->decorated;
}









public function setStyle($name, OutputFormatterStyleInterface $style)
{
$this->styles[strtolower($name)] = $style;
}










public function hasStyle($name)
{
return isset($this->styles[strtolower($name)]);
}












public function getStyle($name)
{
if (!$this->hasStyle($name)) {
throw new \InvalidArgumentException(sprintf('Undefined style: %s', $name));
}

return $this->styles[strtolower($name)];
}










public function format($message)
{
$message = preg_replace_callback(self::FORMAT_PATTERN, array($this, 'replaceStyle'), $message);

return str_replace('\\<', '<', $message);
}




public function getStyleStack()
{
return $this->styleStack;
}








private function replaceStyle($match)
{

 if ('\\' === $match[1]) {
return $this->applyCurrentStyle($match[0]);
}

if ('' === $match[3]) {
if ('/' === $match[2]) {

 $this->styleStack->pop();

return $this->applyCurrentStyle($match[4]);
}


 return '<>'.$this->applyCurrentStyle($match[4]);
}

if (isset($this->styles[strtolower($match[3])])) {
$style = $this->styles[strtolower($match[3])];
} else {
$style = $this->createStyleFromString($match[3]);

if (false === $style) {
return $this->applyCurrentStyle($match[0]);
}
}

if ('/' === $match[2]) {
$this->styleStack->pop($style);
} else {
$this->styleStack->push($style);
}

return $this->applyCurrentStyle($match[4]);
}








private function createStyleFromString($string)
{
if (!preg_match_all('/([^=]+)=([^;]+)(;|$)/', strtolower($string), $matches, PREG_SET_ORDER)) {
return false;
}

$style = new OutputFormatterStyle();
foreach ($matches as $match) {
array_shift($match);

if ('fg' == $match[0]) {
$style->setForeground($match[1]);
} elseif ('bg' == $match[0]) {
$style->setBackground($match[1]);
} else {
$style->setOption($match[1]);
}
}

return $style;
}








private function applyCurrentStyle($text)
{
return $this->isDecorated() && strlen($text) > 0 ? $this->styleStack->getCurrent()->apply($text) : $text;
}
}
<?php










namespace Symfony\Component\Console\Formatter;








interface OutputFormatterInterface
{







public function setDecorated($decorated);








public function isDecorated();









public function setStyle($name, OutputFormatterStyleInterface $style);










public function hasStyle($name);










public function getStyle($name);










public function format($message);
}
<?php










namespace Symfony\Component\Console;

use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\ProgressHelper;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleForExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;


















class Application
{
private $commands;
private $wantHelps = false;
private $runningCommand;
private $name;
private $version;
private $catchExceptions;
private $autoExit;
private $definition;
private $helperSet;
private $dispatcher;









public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
{
$this->name = $name;
$this->version = $version;
$this->catchExceptions = true;
$this->autoExit = true;
$this->commands = array();
$this->helperSet = $this->getDefaultHelperSet();
$this->definition = $this->getDefaultInputDefinition();

foreach ($this->getDefaultCommands() as $command) {
$this->add($command);
}
}

public function setDispatcher(EventDispatcher $dispatcher)
{
$this->dispatcher = $dispatcher;
}













public function run(InputInterface $input = null, OutputInterface $output = null)
{
if (null === $input) {
$input = new ArgvInput();
}

if (null === $output) {
$output = new ConsoleOutput();
}

try {
$exitCode = $this->doRun($input, $output);
} catch (\Exception $e) {
if (!$this->catchExceptions) {
throw $e;
}

if ($output instanceof ConsoleOutputInterface) {
$this->renderException($e, $output->getErrorOutput());
} else {
$this->renderException($e, $output);
}
$exitCode = $e->getCode();

$exitCode = is_numeric($exitCode) && $exitCode ? $exitCode : 1;
}

if ($this->autoExit) {
if ($exitCode > 255) {
$exitCode = 255;
}

 exit($exitCode);

 }

return $exitCode;
}









public function doRun(InputInterface $input, OutputInterface $output)
{
$name = $this->getCommandName($input);

if (true === $input->hasParameterOption(array('--ansi'))) {
$output->setDecorated(true);
} elseif (true === $input->hasParameterOption(array('--no-ansi'))) {
$output->setDecorated(false);
}

if (true === $input->hasParameterOption(array('--help', '-h'))) {
if (!$name) {
$name = 'help';
$input = new ArrayInput(array('command' => 'help'));
} else {
$this->wantHelps = true;
}
}

if (true === $input->hasParameterOption(array('--no-interaction', '-n'))) {
$input->setInteractive(false);
}

if (function_exists('posix_isatty') && $this->getHelperSet()->has('dialog')) {
$inputStream = $this->getHelperSet()->get('dialog')->getInputStream();
if (!posix_isatty($inputStream)) {
$input->setInteractive(false);
}
}

if (true === $input->hasParameterOption(array('--quiet', '-q'))) {
$output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
} else {
if ($input->hasParameterOption('-vvv') || $input->hasParameterOption('--verbose=3') || $input->getParameterOption('--verbose') === 3) {
$output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
} elseif ($input->hasParameterOption('-vv') || $input->hasParameterOption('--verbose=2') || $input->getParameterOption('--verbose') === 2) {
$output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
} elseif ($input->hasParameterOption('-v') || $input->hasParameterOption('--verbose=1') || $input->hasParameterOption('--verbose') || $input->getParameterOption('--verbose')) {
$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
}
}

if (true === $input->hasParameterOption(array('--version', '-V'))) {
$output->writeln($this->getLongVersion());

return 0;
}

if (!$name) {
$name = 'list';
$input = new ArrayInput(array('command' => 'list'));
}


 $command = $this->find($name);

$this->runningCommand = $command;
$exitCode = $this->doRunCommand($command, $input, $output);
$this->runningCommand = null;

return is_numeric($exitCode) ? $exitCode : 0;
}








public function setHelperSet(HelperSet $helperSet)
{
$this->helperSet = $helperSet;
}








public function getHelperSet()
{
return $this->helperSet;
}








public function setDefinition(InputDefinition $definition)
{
$this->definition = $definition;
}






public function getDefinition()
{
return $this->definition;
}






public function getHelp()
{
$messages = array(
$this->getLongVersion(),
'',
'<comment>Usage:</comment>',
'  [options] command [arguments]',
'',
'<comment>Options:</comment>',
);

foreach ($this->getDefinition()->getOptions() as $option) {
$messages[] = sprintf('  %-29s %s %s',
'<info>--'.$option->getName().'</info>',
$option->getShortcut() ? '<info>-'.$option->getShortcut().'</info>' : '  ',
$option->getDescription()
);
}

return implode(PHP_EOL, $messages);
}








public function setCatchExceptions($boolean)
{
$this->catchExceptions = (Boolean) $boolean;
}








public function setAutoExit($boolean)
{
$this->autoExit = (Boolean) $boolean;
}








public function getName()
{
return $this->name;
}








public function setName($name)
{
$this->name = $name;
}








public function getVersion()
{
return $this->version;
}








public function setVersion($version)
{
$this->version = $version;
}








public function getLongVersion()
{
if ('UNKNOWN' !== $this->getName() && 'UNKNOWN' !== $this->getVersion()) {
return sprintf('<info>%s</info> version <comment>%s</comment>', $this->getName(), $this->getVersion());
}

return '<info>Console Tool</info>';
}










public function register($name)
{
return $this->add(new Command($name));
}








public function addCommands(array $commands)
{
foreach ($commands as $command) {
$this->add($command);
}
}












public function add(Command $command)
{
$command->setApplication($this);

if (!$command->isEnabled()) {
$command->setApplication(null);

return;
}

$this->commands[$command->getName()] = $command;

foreach ($command->getAliases() as $alias) {
$this->commands[$alias] = $command;
}

return $command;
}












public function get($name)
{
if (!isset($this->commands[$name])) {
throw new \InvalidArgumentException(sprintf('The command "%s" does not exist.', $name));
}

$command = $this->commands[$name];

if ($this->wantHelps) {
$this->wantHelps = false;

$helpCommand = $this->get('help');
$helpCommand->setCommand($command);

return $helpCommand;
}

return $command;
}










public function has($name)
{
return isset($this->commands[$name]);
}








public function getNamespaces()
{
$namespaces = array();
foreach ($this->commands as $command) {
$namespaces[] = $this->extractNamespace($command->getName());

foreach ($command->getAliases() as $alias) {
$namespaces[] = $this->extractNamespace($alias);
}
}

return array_values(array_unique(array_filter($namespaces)));
}










public function findNamespace($namespace)
{
$allNamespaces = $this->getNamespaces();
$found = '';
foreach (explode(':', $namespace) as $i => $part) {

 $namespaces = array();
foreach ($allNamespaces as $n) {
if ('' === $found || 0 === strpos($n, $found)) {
$namespaces[$n] = explode(':', $n);
}
}

$abbrevs = static::getAbbreviations(array_unique(array_values(array_filter(array_map(function ($p) use ($i) { return isset($p[$i]) ? $p[$i] : ''; }, $namespaces)))));

if (!isset($abbrevs[$part])) {
$message = sprintf('There are no commands defined in the "%s" namespace.', $namespace);

if (1 <= $i) {
$part = $found.':'.$part;
}

if ($alternatives = $this->findAlternativeNamespace($part, $abbrevs)) {
if (1 == count($alternatives)) {
$message .= "\n\nDid you mean this?\n    ";
} else {
$message .= "\n\nDid you mean one of these?\n    ";
}

$message .= implode("\n    ", $alternatives);
}

throw new \InvalidArgumentException($message);
}


 if (in_array($part, $abbrevs[$part])) {
$abbrevs[$part] = array($part);
}

if (count($abbrevs[$part]) > 1) {
throw new \InvalidArgumentException(sprintf('The namespace "%s" is ambiguous (%s).', $namespace, $this->getAbbreviationSuggestions($abbrevs[$part])));
}

$found .= $found ? ':' . $abbrevs[$part][0] : $abbrevs[$part][0];
}

return $found;
}















public function find($name)
{

 $namespace = '';
$searchName = $name;
if (false !== $pos = strrpos($name, ':')) {
$namespace = $this->findNamespace(substr($name, 0, $pos));
$searchName = $namespace.substr($name, $pos);
}


 $commands = array();
foreach ($this->commands as $command) {
$extractedNamespace = $this->extractNamespace($command->getName());
if ($extractedNamespace === $namespace
|| !empty($namespace) && 0 === strpos($extractedNamespace, $namespace)
) {
$commands[] = $command->getName();
}
}

$abbrevs = static::getAbbreviations(array_unique($commands));
if (isset($abbrevs[$searchName]) && 1 == count($abbrevs[$searchName])) {
return $this->get($abbrevs[$searchName][0]);
}

if (isset($abbrevs[$searchName]) && in_array($searchName, $abbrevs[$searchName])) {
return $this->get($searchName);
}

if (isset($abbrevs[$searchName]) && count($abbrevs[$searchName]) > 1) {
$suggestions = $this->getAbbreviationSuggestions($abbrevs[$searchName]);

throw new \InvalidArgumentException(sprintf('Command "%s" is ambiguous (%s).', $name, $suggestions));
}


 $aliases = array();
foreach ($this->commands as $command) {
foreach ($command->getAliases() as $alias) {
$extractedNamespace = $this->extractNamespace($alias);
if ($extractedNamespace === $namespace
|| !empty($namespace) && 0 === strpos($extractedNamespace, $namespace)
) {
$aliases[] = $alias;
}
}
}

$aliases = static::getAbbreviations(array_unique($aliases));
if (!isset($aliases[$searchName])) {
$message = sprintf('Command "%s" is not defined.', $name);

if ($alternatives = $this->findAlternativeCommands($searchName, $abbrevs)) {
if (1 == count($alternatives)) {
$message .= "\n\nDid you mean this?\n    ";
} else {
$message .= "\n\nDid you mean one of these?\n    ";
}
$message .= implode("\n    ", $alternatives);
}

throw new \InvalidArgumentException($message);
}

if (count($aliases[$searchName]) > 1) {
throw new \InvalidArgumentException(sprintf('Command "%s" is ambiguous (%s).', $name, $this->getAbbreviationSuggestions($aliases[$searchName])));
}

return $this->get($aliases[$searchName][0]);
}












public function all($namespace = null)
{
if (null === $namespace) {
return $this->commands;
}

$commands = array();
foreach ($this->commands as $name => $command) {
if ($namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1)) {
$commands[$name] = $command;
}
}

return $commands;
}








public static function getAbbreviations($names)
{
$abbrevs = array();
foreach ($names as $name) {
for ($len = strlen($name); $len > 0; --$len) {
$abbrev = substr($name, 0, $len);
$abbrevs[$abbrev][] = $name;
}
}

return $abbrevs;
}











public function asText($namespace = null, $raw = false)
{
$descriptor = new TextDescriptor();

return $descriptor->describe($this, array('namespace' => $namespace, 'raw_text' => $raw));
}











public function asXml($namespace = null, $asDom = false)
{
$descriptor = new XmlDescriptor();

return $descriptor->describe($this, array('namespace' => $namespace, 'as_dom' => $asDom));
}







public function renderException($e, $output)
{
$strlen = function ($string) {
if (!function_exists('mb_strlen')) {
return strlen($string);
}

if (false === $encoding = mb_detect_encoding($string)) {
return strlen($string);
}

return mb_strlen($string, $encoding);
};

do {
$title = sprintf('  [%s]  ', get_class($e));
$len = $strlen($title);
$width = $this->getTerminalWidth() ? $this->getTerminalWidth() - 1 : PHP_INT_MAX;
$lines = array();
foreach (preg_split('/\r?\n/', $e->getMessage()) as $line) {
foreach (str_split($line, $width - 4) as $line) {
$lines[] = sprintf('  %s  ', $line);
$len = max($strlen($line) + 4, $len);
}
}

$messages = array(str_repeat(' ', $len), $title.str_repeat(' ', max(0, $len - $strlen($title))));

foreach ($lines as $line) {
$messages[] = $line.str_repeat(' ', $len - $strlen($line));
}

$messages[] = str_repeat(' ', $len);

$output->writeln("");
$output->writeln("");
foreach ($messages as $message) {
$output->writeln('<error>'.$message.'</error>');
}
$output->writeln("");
$output->writeln("");

if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
$output->writeln('<comment>Exception trace:</comment>');


 $trace = $e->getTrace();
array_unshift($trace, array(
'function' => '',
'file' => $e->getFile() != null ? $e->getFile() : 'n/a',
'line' => $e->getLine() != null ? $e->getLine() : 'n/a',
'args' => array(),
));

for ($i = 0, $count = count($trace); $i < $count; $i++) {
$class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
$type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
$function = $trace[$i]['function'];
$file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
$line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

$output->writeln(sprintf(' %s%s%s() at <info>%s:%s</info>', $class, $type, $function, $file, $line));
}

$output->writeln("");
$output->writeln("");
}
} while ($e = $e->getPrevious());

if (null !== $this->runningCommand) {
$output->writeln(sprintf('<info>%s</info>', sprintf($this->runningCommand->getSynopsis(), $this->getName())));
$output->writeln("");
$output->writeln("");
}
}






protected function getTerminalWidth()
{
$dimensions = $this->getTerminalDimensions();

return $dimensions[0];
}






protected function getTerminalHeight()
{
$dimensions = $this->getTerminalDimensions();

return $dimensions[1];
}






public function getTerminalDimensions()
{
if (defined('PHP_WINDOWS_VERSION_BUILD')) {

 if (preg_match('/^(\d+)x\d+ \(\d+x(\d+)\)$/', trim(getenv('ANSICON')), $matches)) {
return array((int) $matches[1], (int) $matches[2]);
}

 if (preg_match('/^(\d+)x(\d+)$/', $this->getConsoleMode(), $matches)) {
return array((int) $matches[1], (int) $matches[2]);
}
}

if ($sttyString = $this->getSttyColumns()) {

 if (preg_match('/rows.(\d+);.columns.(\d+);/i', $sttyString, $matches)) {
return array((int) $matches[2], (int) $matches[1]);
}

 if (preg_match('/;.(\d+).rows;.(\d+).columns/i', $sttyString, $matches)) {
return array((int) $matches[2], (int) $matches[1]);
}
}

return array(null, null);
}













protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
{
if (null === $this->dispatcher) {
return $command->run($input, $output);
}

$event = new ConsoleCommandEvent($command, $input, $output);
$this->dispatcher->dispatch(ConsoleEvents::COMMAND, $event);

try {
$exitCode = $command->run($input, $output);
} catch (\Exception $e) {
$event = new ConsoleTerminateEvent($command, $input, $output, $e->getCode());
$this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $event);

$event = new ConsoleForExceptionEvent($command, $input, $output, $e, $event->getExitCode());
$this->dispatcher->dispatch(ConsoleEvents::EXCEPTION, $event);

throw $event->getException();
}

$event = new ConsoleTerminateEvent($command, $input, $output, $exitCode);
$this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $event);

return $event->getExitCode();
}








protected function getCommandName(InputInterface $input)
{
return $input->getFirstArgument();
}






protected function getDefaultInputDefinition()
{
return new InputDefinition(array(
new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message.'),
new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message.'),
new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version.'),
new InputOption('--ansi', '', InputOption::VALUE_NONE, 'Force ANSI output.'),
new InputOption('--no-ansi', '', InputOption::VALUE_NONE, 'Disable ANSI output.'),
new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question.'),
));
}






protected function getDefaultCommands()
{
return array(new HelpCommand(), new ListCommand());
}






protected function getDefaultHelperSet()
{
return new HelperSet(array(
new FormatterHelper(),
new DialogHelper(),
new ProgressHelper(),
new TableHelper(),
));
}






private function getSttyColumns()
{
if (!function_exists('proc_open')) {
return;
}

$descriptorspec = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
$process = proc_open('stty -a | grep columns', $descriptorspec, $pipes, null, null, array('suppress_errors' => true));
if (is_resource($process)) {
$info = stream_get_contents($pipes[1]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);

return $info;
}
}






private function getConsoleMode()
{
if (!function_exists('proc_open')) {
return;
}

$descriptorspec = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
$process = proc_open('mode CON', $descriptorspec, $pipes, null, null, array('suppress_errors' => true));
if (is_resource($process)) {
$info = stream_get_contents($pipes[1]);
fclose($pipes[1]);
fclose($pipes[2]);
proc_close($process);

if (preg_match('/--------+\r?\n.+?(\d+)\r?\n.+?(\d+)\r?\n/', $info, $matches)) {
return $matches[2].'x'.$matches[1];
}
}
}








private function getAbbreviationSuggestions($abbrevs)
{
return sprintf('%s, %s%s', $abbrevs[0], $abbrevs[1], count($abbrevs) > 2 ? sprintf(' and %d more', count($abbrevs) - 2) : '');
}











public function extractNamespace($name, $limit = null)
{
$parts = explode(':', $name);
array_pop($parts);

return implode(':', null === $limit ? $parts : array_slice($parts, 0, $limit));
}









private function findAlternativeCommands($name, $abbrevs)
{
$callback = function($item) {
return $item->getName();
};

return $this->findAlternatives($name, $this->commands, $abbrevs, $callback);
}









private function findAlternativeNamespace($name, $abbrevs)
{
return $this->findAlternatives($name, $this->getNamespaces(), $abbrevs);
}












private function findAlternatives($name, $collection, $abbrevs, $callback = null)
{
$alternatives = array();

foreach ($collection as $item) {
if (null !== $callback) {
$item = call_user_func($callback, $item);
}

$lev = levenshtein($name, $item);
if ($lev <= strlen($name) / 3 || false !== strpos($item, $name)) {
$alternatives[$item] = $lev;
}
}

if (!$alternatives) {
foreach ($abbrevs as $key => $values) {
$lev = levenshtein($name, $key);
if ($lev <= strlen($name) / 3 || false !== strpos($key, $name)) {
foreach ($values as $value) {
$alternatives[$value] = $lev;
}
}
}
}

asort($alternatives);

return array_keys($alternatives);
}
}
<?php










namespace Symfony\Component\Console\Input;








class InputArgument
{
const REQUIRED = 1;
const OPTIONAL = 2;
const IS_ARRAY = 4;

private $name;
private $mode;
private $default;
private $description;













public function __construct($name, $mode = null, $description = '', $default = null)
{
if (null === $mode) {
$mode = self::OPTIONAL;
} elseif (!is_int($mode) || $mode > 7 || $mode < 1) {
throw new \InvalidArgumentException(sprintf('Argument mode "%s" is not valid.', $mode));
}

$this->name = $name;
$this->mode = $mode;
$this->description = $description;

$this->setDefault($default);
}






public function getName()
{
return $this->name;
}






public function isRequired()
{
return self::REQUIRED === (self::REQUIRED & $this->mode);
}






public function isArray()
{
return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
}








public function setDefault($default = null)
{
if (self::REQUIRED === $this->mode && null !== $default) {
throw new \LogicException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
}

if ($this->isArray()) {
if (null === $default) {
$default = array();
} elseif (!is_array($default)) {
throw new \LogicException('A default value for an array argument must be an array.');
}
}

$this->default = $default;
}






public function getDefault()
{
return $this->default;
}






public function getDescription()
{
return $this->description;
}
}
<?php










namespace Symfony\Component\Console\Input;












abstract class Input implements InputInterface
{
protected $definition;
protected $options;
protected $arguments;
protected $interactive = true;






public function __construct(InputDefinition $definition = null)
{
if (null === $definition) {
$this->arguments = array();
$this->options = array();
$this->definition = new InputDefinition();
} else {
$this->bind($definition);
$this->validate();
}
}






public function bind(InputDefinition $definition)
{
$this->arguments = array();
$this->options = array();
$this->definition = $definition;

$this->parse();
}




abstract protected function parse();






public function validate()
{
if (count($this->arguments) < $this->definition->getArgumentRequiredCount()) {
throw new \RuntimeException('Not enough arguments.');
}
}






public function isInteractive()
{
return $this->interactive;
}






public function setInteractive($interactive)
{
$this->interactive = (Boolean) $interactive;
}






public function getArguments()
{
return array_merge($this->definition->getArgumentDefaults(), $this->arguments);
}










public function getArgument($name)
{
if (!$this->definition->hasArgument($name)) {
throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
}

return isset($this->arguments[$name]) ? $this->arguments[$name] : $this->definition->getArgument($name)->getDefault();
}









public function setArgument($name, $value)
{
if (!$this->definition->hasArgument($name)) {
throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
}

$this->arguments[$name] = $value;
}








public function hasArgument($name)
{
return $this->definition->hasArgument($name);
}






public function getOptions()
{
return array_merge($this->definition->getOptionDefaults(), $this->options);
}










public function getOption($name)
{
if (!$this->definition->hasOption($name)) {
throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
}

return isset($this->options[$name]) ? $this->options[$name] : $this->definition->getOption($name)->getDefault();
}









public function setOption($name, $value)
{
if (!$this->definition->hasOption($name)) {
throw new \InvalidArgumentException(sprintf('The "%s" option does not exist.', $name));
}

$this->options[$name] = $value;
}








public function hasOption($name)
{
return $this->definition->hasOption($name);
}








public function escapeToken($token)
{
return preg_match('{^[\w-]+$}', $token) ? $token : escapeshellarg($token);
}
}
<?php










namespace Symfony\Component\Console\Input;












class StringInput extends ArgvInput
{
const REGEX_STRING = '([^\s]+?)(?:\s|(?<!\\\\)"|(?<!\\\\)\'|$)';
const REGEX_QUOTED_STRING = '(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')';











public function __construct($input, InputDefinition $definition = null)
{
parent::__construct(array(), null);

$this->setTokens($this->tokenize($input));

if (null !== $definition) {
$this->bind($definition);
}
}










private function tokenize($input)
{
$tokens = array();
$length = strlen($input);
$cursor = 0;
while ($cursor < $length) {
if (preg_match('/\s+/A', $input, $match, null, $cursor)) {
} elseif (preg_match('/([^="\'\s]+?)(=?)('.self::REGEX_QUOTED_STRING.'+)/A', $input, $match, null, $cursor)) {
$tokens[] = $match[1].$match[2].stripcslashes(str_replace(array('"\'', '\'"', '\'\'', '""'), '', substr($match[3], 1, strlen($match[3]) - 2)));
} elseif (preg_match('/'.self::REGEX_QUOTED_STRING.'/A', $input, $match, null, $cursor)) {
$tokens[] = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
} elseif (preg_match('/'.self::REGEX_STRING.'/A', $input, $match, null, $cursor)) {
$tokens[] = stripcslashes($match[1]);
} else {

 
 throw new \InvalidArgumentException(sprintf('Unable to parse input near "... %s ..."', substr($input, $cursor, 10)));

 }

$cursor += strlen($match[0]);
}

return $tokens;
}
}
<?php










namespace Symfony\Component\Console\Input;








class InputOption
{
const VALUE_NONE = 1;
const VALUE_REQUIRED = 2;
const VALUE_OPTIONAL = 4;
const VALUE_IS_ARRAY = 8;

private $name;
private $shortcut;
private $mode;
private $default;
private $description;














public function __construct($name, $shortcut = null, $mode = null, $description = '', $default = null)
{
if (0 === strpos($name, '--')) {
$name = substr($name, 2);
}

if (empty($name)) {
throw new \InvalidArgumentException('An option name cannot be empty.');
}

if (empty($shortcut)) {
$shortcut = null;
}

if (null !== $shortcut) {
if (is_array($shortcut)) {
$shortcut = implode('|', $shortcut);
}
$shortcuts = preg_split('{(\|)-?}', ltrim($shortcut, '-'));
$shortcuts = array_filter($shortcuts);
$shortcut = implode('|', $shortcuts);

if (empty($shortcut)) {
throw new \InvalidArgumentException('An option shortcut cannot be empty.');
}
}

if (null === $mode) {
$mode = self::VALUE_NONE;
} elseif (!is_int($mode) || $mode > 15 || $mode < 1) {
throw new \InvalidArgumentException(sprintf('Option mode "%s" is not valid.', $mode));
}

$this->name = $name;
$this->shortcut = $shortcut;
$this->mode = $mode;
$this->description = $description;

if ($this->isArray() && !$this->acceptValue()) {
throw new \InvalidArgumentException('Impossible to have an option mode VALUE_IS_ARRAY if the option does not accept a value.');
}

$this->setDefault($default);
}






public function getShortcut()
{
return $this->shortcut;
}






public function getName()
{
return $this->name;
}






public function acceptValue()
{
return $this->isValueRequired() || $this->isValueOptional();
}






public function isValueRequired()
{
return self::VALUE_REQUIRED === (self::VALUE_REQUIRED & $this->mode);
}






public function isValueOptional()
{
return self::VALUE_OPTIONAL === (self::VALUE_OPTIONAL & $this->mode);
}






public function isArray()
{
return self::VALUE_IS_ARRAY === (self::VALUE_IS_ARRAY & $this->mode);
}








public function setDefault($default = null)
{
if (self::VALUE_NONE === (self::VALUE_NONE & $this->mode) && null !== $default) {
throw new \LogicException('Cannot set a default value when using InputOption::VALUE_NONE mode.');
}

if ($this->isArray()) {
if (null === $default) {
$default = array();
} elseif (!is_array($default)) {
throw new \LogicException('A default value for an array option must be an array.');
}
}

$this->default = $this->acceptValue() ? $default : false;
}






public function getDefault()
{
return $this->default;
}






public function getDescription()
{
return $this->description;
}







public function equals(InputOption $option)
{
return $option->getName() === $this->getName()
&& $option->getShortcut() === $this->getShortcut()
&& $option->getDefault() === $this->getDefault()
&& $option->isArray() === $this->isArray()
&& $option->isValueRequired() === $this->isValueRequired()
&& $option->isValueOptional() === $this->isValueOptional()
;
}
}
<?php










namespace Symfony\Component\Console\Input;




























class ArgvInput extends Input
{
private $tokens;
private $parsed;









public function __construct(array $argv = null, InputDefinition $definition = null)
{
if (null === $argv) {
$argv = $_SERVER['argv'];
}


 array_shift($argv);

$this->tokens = $argv;

parent::__construct($definition);
}

protected function setTokens(array $tokens)
{
$this->tokens = $tokens;
}




protected function parse()
{
$parseOptions = true;
$this->parsed = $this->tokens;
while (null !== $token = array_shift($this->parsed)) {
if ($parseOptions && '' == $token) {
$this->parseArgument($token);
} elseif ($parseOptions && '--' == $token) {
$parseOptions = false;
} elseif ($parseOptions && 0 === strpos($token, '--')) {
$this->parseLongOption($token);
} elseif ($parseOptions && '-' === $token[0]) {
$this->parseShortOption($token);
} else {
$this->parseArgument($token);
}
}
}






private function parseShortOption($token)
{
$name = substr($token, 1);

if (strlen($name) > 1) {
if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {

 $this->addShortOption($name[0], substr($name, 1));
} else {
$this->parseShortOptionSet($name);
}
} else {
$this->addShortOption($name, null);
}
}








private function parseShortOptionSet($name)
{
$len = strlen($name);
for ($i = 0; $i < $len; $i++) {
if (!$this->definition->hasShortcut($name[$i])) {
throw new \RuntimeException(sprintf('The "-%s" option does not exist.', $name[$i]));
}

$option = $this->definition->getOptionForShortcut($name[$i]);
if ($option->acceptValue()) {
$this->addLongOption($option->getName(), $i === $len - 1 ? null : substr($name, $i + 1));

break;
} else {
$this->addLongOption($option->getName(), true);
}
}
}






private function parseLongOption($token)
{
$name = substr($token, 2);

if (false !== $pos = strpos($name, '=')) {
$this->addLongOption(substr($name, 0, $pos), substr($name, $pos + 1));
} else {
$this->addLongOption($name, null);
}
}








private function parseArgument($token)
{
$c = count($this->arguments);


 if ($this->definition->hasArgument($c)) {
$arg = $this->definition->getArgument($c);
$this->arguments[$arg->getName()] = $arg->isArray()? array($token) : $token;


 } elseif ($this->definition->hasArgument($c - 1) && $this->definition->getArgument($c - 1)->isArray()) {
$arg = $this->definition->getArgument($c - 1);
$this->arguments[$arg->getName()][] = $token;


 } else {
throw new \RuntimeException('Too many arguments.');
}
}









private function addShortOption($shortcut, $value)
{
if (!$this->definition->hasShortcut($shortcut)) {
throw new \RuntimeException(sprintf('The "-%s" option does not exist.', $shortcut));
}

$this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
}









private function addLongOption($name, $value)
{
if (!$this->definition->hasOption($name)) {
throw new \RuntimeException(sprintf('The "--%s" option does not exist.', $name));
}

$option = $this->definition->getOption($name);


 if (false === $value) {
$value = null;
}

if (null === $value && $option->acceptValue() && count($this->parsed)) {

 
 $next = array_shift($this->parsed);
if (isset($next[0]) && '-' !== $next[0]) {
$value = $next;
} elseif (empty($next)) {
$value = '';
} else {
array_unshift($this->parsed, $next);
}
}

if (null === $value) {
if ($option->isValueRequired()) {
throw new \RuntimeException(sprintf('The "--%s" option requires a value.', $name));
}

if (!$option->isArray()) {
$value = $option->isValueOptional() ? $option->getDefault() : true;
}
}

if ($option->isArray()) {
$this->options[$name][] = $value;
} else {
$this->options[$name] = $value;
}
}






public function getFirstArgument()
{
foreach ($this->tokens as $token) {
if ($token && '-' === $token[0]) {
continue;
}

return $token;
}
}











public function hasParameterOption($values)
{
$values = (array) $values;

foreach ($this->tokens as $v) {
if (in_array($v, $values)) {
return true;
}
}

return false;
}












public function getParameterOption($values, $default = false)
{
$values = (array) $values;

$tokens = $this->tokens;
while ($token = array_shift($tokens)) {
foreach ($values as $value) {
if (0 === strpos($token, $value)) {
if (false !== $pos = strpos($token, '=')) {
return substr($token, $pos + 1);
}

return array_shift($tokens);
}
}
}

return $default;
}






public function __toString()
{
$self = $this;
$tokens = array_map(function ($token) use ($self) {
if (preg_match('{^(-[^=]+=)(.+)}', $token, $match)) {
return $match[1] . $self->escapeToken($match[2]);
}

if ($token && $token[0] !== '-') {
return $self->escapeToken($token);
}

return $token;
}, $this->tokens);

return implode(' ', $tokens);
}
}
<?php










namespace Symfony\Component\Console\Input;






interface InputInterface
{





public function getFirstArgument();











public function hasParameterOption($values);












public function getParameterOption($values, $default = false);






public function bind(InputDefinition $definition);








public function validate();






public function getArguments();








public function getArgument($name);









public function setArgument($name, $value);








public function hasArgument($name);






public function getOptions();








public function getOption($name);









public function setOption($name, $value);








public function hasOption($name);






public function isInteractive();






public function setInteractive($interactive);
}
<?php










namespace Symfony\Component\Console\Input;












class ArrayInput extends Input
{
private $parameters;









public function __construct(array $parameters, InputDefinition $definition = null)
{
$this->parameters = $parameters;

parent::__construct($definition);
}






public function getFirstArgument()
{
foreach ($this->parameters as $key => $value) {
if ($key && '-' === $key[0]) {
continue;
}

return $value;
}
}











public function hasParameterOption($values)
{
$values = (array) $values;

foreach ($this->parameters as $k => $v) {
if (!is_int($k)) {
$v = $k;
}

if (in_array($v, $values)) {
return true;
}
}

return false;
}












public function getParameterOption($values, $default = false)
{
$values = (array) $values;

foreach ($this->parameters as $k => $v) {
if (is_int($k) && in_array($v, $values)) {
return true;
} elseif (in_array($k, $values)) {
return $v;
}
}

return $default;
}






public function __toString()
{
$params = array();
foreach ($this->parameters as $param => $val) {
if ($param && '-' === $param[0]) {
$params[] = $param . ('' != $val ? '='.$this->escapeToken($val) : '');
} else {
$params[] = $this->escapeToken($val);
}
}

return implode(' ', $params);
}




protected function parse()
{
foreach ($this->parameters as $key => $value) {
if (0 === strpos($key, '--')) {
$this->addLongOption(substr($key, 2), $value);
} elseif ('-' === $key[0]) {
$this->addShortOption(substr($key, 1), $value);
} else {
$this->addArgument($key, $value);
}
}
}









private function addShortOption($shortcut, $value)
{
if (!$this->definition->hasShortcut($shortcut)) {
throw new \InvalidArgumentException(sprintf('The "-%s" option does not exist.', $shortcut));
}

$this->addLongOption($this->definition->getOptionForShortcut($shortcut)->getName(), $value);
}










private function addLongOption($name, $value)
{
if (!$this->definition->hasOption($name)) {
throw new \InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
}

$option = $this->definition->getOption($name);

if (null === $value) {
if ($option->isValueRequired()) {
throw new \InvalidArgumentException(sprintf('The "--%s" option requires a value.', $name));
}

$value = $option->isValueOptional() ? $option->getDefault() : true;
}

$this->options[$name] = $value;
}









private function addArgument($name, $value)
{
if (!$this->definition->hasArgument($name)) {
throw new \InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
}

$this->arguments[$name] = $value;
}
}
<?php










namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Descriptor\TextDescriptor;
use Symfony\Component\Console\Descriptor\XmlDescriptor;















class InputDefinition
{
private $arguments;
private $requiredCount;
private $hasAnArrayArgument = false;
private $hasOptional;
private $options;
private $shortcuts;








public function __construct(array $definition = array())
{
$this->setDefinition($definition);
}








public function setDefinition(array $definition)
{
$arguments = array();
$options = array();
foreach ($definition as $item) {
if ($item instanceof InputOption) {
$options[] = $item;
} else {
$arguments[] = $item;
}
}

$this->setArguments($arguments);
$this->setOptions($options);
}








public function setArguments($arguments = array())
{
$this->arguments = array();
$this->requiredCount = 0;
$this->hasOptional = false;
$this->hasAnArrayArgument = false;
$this->addArguments($arguments);
}








public function addArguments($arguments = array())
{
if (null !== $arguments) {
foreach ($arguments as $argument) {
$this->addArgument($argument);
}
}
}










public function addArgument(InputArgument $argument)
{
if (isset($this->arguments[$argument->getName()])) {
throw new \LogicException(sprintf('An argument with name "%s" already exists.', $argument->getName()));
}

if ($this->hasAnArrayArgument) {
throw new \LogicException('Cannot add an argumen