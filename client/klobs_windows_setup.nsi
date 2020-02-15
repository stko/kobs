SetCompress force
SetCompressor /SOLID lzma
Name "KLOBS"
OutFile "KLOBS_Windows_Setup.exe"
RequestExecutionLevel user
XPStyle on
InstallDir "$APPDATA\KLOBS"
#!include "ZipDLL.nsh"
Page license
Page directory
Page instfiles
UninstPage uninstConfirm
UninstPage instfiles

LicenseData gpl-2.0.txt
LicenseForceSelection checkbox

Section "KLOBS"
SetOutPath $INSTDIR
File /r  /x .svn "zbar"
#File /r  /x .svn "java/interface/qrcode/lib"
File /r  /x .svn "java/interface/qrcode/QRCodeReader.*"
File /r  /x .svn "java/interface/serial/lib"
File /r  /x .svn "java/interface/serial/klobsserial.*"
File /r  "java/interface/serial/ttyReader.jar"
File /r  /x .svn "java/application/Kobs/dist/*.*"
File java/application/Kobs/kobs.lang
File "klobs.url"
File "runQRCode.bat"
WriteUninstaller "$INSTDIR\uninstaller.exe"
CreateDirectory "$SMPROGRAMS\KLOBS"
CreateShortCut "$SMPROGRAMS\KLOBS\RF Card Reader.lnk" "javaw" "-jar ttyReader.jar"
CreateShortCut "$SMPROGRAMS\KLOBS\QRCode Reader.lnk" "$INSTDIR\runQRCode.bat"
CreateShortCut "$SMPROGRAMS\KLOBS\KLOBS.lnk" "javaw" "-jar klobs.jar"
CreateShortCut "$SMPROGRAMS\KLOBS\KLOBS Homepage.lnk" "$INSTDIR\klobs.url"
CreateShortCut "$SMPROGRAMS\KLOBS\Uninstall KLOBS.lnk" "$INSTDIR\uninstaller.exe"
WriteRegStr HKCU "SOFTWARE\Koehler_Programms\KLOBS" "InstDir" $INSTDIR


SectionEnd

Section "un.Uninstall"
RMDir /r $INSTDIR\lib
Delete "$INSTDIR\Klobs.jar"
Delete "$INSTDIR\klobs.url"
Delete "$INSTDIR\README.TXT"
Delete "$INSTDIR\kobs.lang"
Delete "$INSTDIR\klobs.props"
Delete "$INSTDIR\kobs.props"
Delete "$INSTDIR\sessiondata.xml"
Delete "$INSTDIR\userdata.xml"
Delete "$INSTDIR\uninstaller.exe"
RMDir /r $INSTDIR
Delete "$SMPROGRAMS\KLOBS\RF Card Reader.lnk"
Delete "$SMPROGRAMS\KLOBS\KLOBS.lnk"
Delete "$SMPROGRAMS\KLOBS\KLOBS Homepage.lnk"
Delete "$SMPROGRAMS\KLOBS\Uninstall KLOBS.lnk"
RMDir "$SMPROGRAMS\KLOBS"
DeleteRegValue HKCU "SOFTWARE\Koehler_Programms\KLOBS" "InstDir"
SectionEnd

