Option Explicit
  Dim Args1, Args2
  Args1 = ""
  Args2 = ""

  On Error Resume Next
  Args1 = Wscript.Arguments.Item(0)
  Args2 = Wscript.Arguments.Item(1)
  
  Dim fso
  Set fso = CreateObject("Scripting.FileSystemObject")
  Dim fullpath
  fullpath = fso.GetAbsolutePathName(Args1)

  Dim objWshShell, objWshShortcut
  Dim  ShortcutName, ShortcutExt, ShortcutPath
  Set objWshShell =  WScript.CreateObject("WScript.Shell")
   
  ShortcutName    =  fso.GetBaseName(fullpath)
  ShortcutExt     =  fso.GetExtensionName(fullpath)
  ShortcutPath    =  fso.GetParentFolderName(fullpath)

  If Len(Args2)=0 Then Args2=ShortcutName

  Set objWshShortcut = objWshShell.CreateShortcut(ShortcutPath &"\"& Args2 &".lnk")
      With objWshShortcut
           .WorkingDirectory = ShortcutPath
           .TargetPath       = ShortcutPath &"\"& ShortcutName &"."& ShortcutExt
           .IconLocation     = fullpath &", 0"
           .Save
      End with