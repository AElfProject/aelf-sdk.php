$this->authentications[$repositoryName] = array('username' => $username, 'password' => $password);
}
}
<?php











namespace Composer\IO;






class NullIO implements IOInterface
{



public function isInteractive()
{
return false;
}




public function isVerbose()
{
return false;
}




public function isVeryVerbose()
{
return false;
}




public function isDebug()
{
return false;
}




public function isDecorated()
{
return false;
}




public function write($messages, $newline = true)
{
}




public function overwrite($messages, $newline = true, $size = 80)
{
}




public function ask($question, $default = null)
{
return $default;
}




public function askConfirmation($question, $default = true)
{
return $default;
}




public function askAndValidate($question, $validator, $attempts = false, $default = null)
{
return $default;
}




public function askAndHideAnswer($question)
{
return null;
}




public function getAuthentications()
{
return array();
}




public function hasAuthentication($repositoryName)
{
return false;
}




public function getAuthentication($repositoryName)
{
return array('username' => null, 'password' => null);
}




public function setAuthentication($repositoryName, $username, $password = null)
{
}
}
<?php











namespace Composer\IO;






interface IOInterface
{





public function isInteractive();






public function isVerbose();






public function isVeryVerbose();






public function isDebug();






public function isDecorated();







public function write($messages, $newline = true);








public function overwrite($messages, $newline = true, $size = 80);











public function ask($question, $default = null);











public function askConfirmation($question, $default = true);

















public function askAndValidate($question, $validator, $attempts = false, $default = null);








public function askAndHideAnswer($question);






public function getAuthentications();








public function hasAuthentication($repositoryName);








public function getAuthentication($repositoryName);








public function set