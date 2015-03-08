@ECHO OFF
wscript.exe  "RegisterSendTo/mkLink.vbs" "searchload.exe" "NYTDL"
@move NYTDL.lnk %appdata%/microsoft/windows/sendTo
echo "OK"
pause