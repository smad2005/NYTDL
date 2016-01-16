$packageName = 'nytdl' 
$toolsDir = "$(Split-Path -parent $MyInvocation.MyCommand.Definition)"
$url = "https://github.com/smad2005/NYTDL/releases/download/1.0.87/NYTDL_build.zip"
Install-ChocolateyZipPackage $packageName $url $toolsDir
cd $toolsDir
.\AddToSendTo.bat

