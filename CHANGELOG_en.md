Exastro IT Automation 1.10.0 (2022-06-08)
==================================================

Installer
---------------
 * Updated to Version 1.10.0 #1761
 * Added a restart for php-fpm if the user is using RHEL8 type OS. #1818
 * Reworked some of the config default values when installing.(Apache) #1885
 * Reworked some of the sudoers settings when installing. #1886

General
---------------
 * Implemented measures for CSRF #1608
 * Added HttpOnly to Cookies. #1609
 * Added Samesite settings to Cookies. #1610
 * Fixed an issue that would occur when pressing a button when the session time ran out. #1631
 * Fixed an issue where "WARNING : PHP 7.2 have reached its "End of Life"" would be displayed. #1678
 * Fixed an issue where any uploaded files will be displayed when they are registered with the "Delete file" box checked. #1765
 * Made it so the log out activity will be displayed in the login output log. #1769
 * Fixed an issue that would occur when duplicating records with ssh secret key files attached to them. #1797
 * Reworked the Excel file names. #1892
 * Configured Permission settings for directory creation operations. #1907
 * Improved the Mail address validation check. #1910

RestAPI
---------------
 * Fixed an issue where user would not be able to register icons when registering Menu groups using RestAPI. #1861

Management console
---------------
 * Changed the Version number in the Version information menu to ".10.0". #1760
 * Added role/menu link list data for OASE 1.6. #1866

Export/Import
---------------
 * Fixed an issue that would occur where the menu import operation would end successfully but the records would not be registered. #1751
 * Fixed an issue where data would be output multiple times when exporting parameter reference menus using the Excel bulk export function. #1805

Conductor
---------------
 * Made it so operations that are paused have their status displayed in the operation list. #1120
 * Fixed an issue where the "Emergency stop" in the Conductor Confirmation menu would not be displayed. #1817
 * Adjusted some of the RESTAPI operation instance items. #1825
 * Fixed an issue where pressing the "Emergency stop" button on one of the nodes in a paralell-branch would cause the other node to be stuck "Executing". #1834

Ansible-Driver
---------------
 * Adjusted the installer settings in order to fit the Ansible Automation Controller better #1582
 * Fixed an issue where an validation error would occur when registering specific values (Strings and files) from pressing the "Duplicate" button. #1764
 * Fixed an issue where some of the ITA variables would not function as variables in the pioneer driver. #1766
 * Fixed an error that would occur when running ansible to the key authentication host after the execution user was changed. #1835
 * Improved the description in the variable translation table. #1840
 * Fixed an issue where the backyard log would output PHP notice messages.B #1849
 * Fixed an issue where if the file names of files uploaded to the file list or template list contained either a space or a paranthese would cause an error when executing them.#1854
 * Fixed an issue where connecting to a different tower after connecting to the Tower in the virtualenv settings would cause an "ID Conversion" error for any past virtualenv items. #1915
 * [Collect function]Fixed an issue where data set in parameter sheets in the Exastro collect function would not be displayed properly. #1767
 * [Collect function]Fixed an issue where parameter values collected from vertical menus would not be correct. #1780
 * [Collect function]Fixed an issue where execution results would output "null" even if the RESTAPI execution was executed successfully. #1845
 * [Collect function]Changed an issue where the REST method settings would not work and connect through IP Address. #1893
 * [Collect function]Changed an issue where the user could not collect if the user only wanted to collect array elements. #1897
 * [Collect function]Fixed an issue where blank (null) collection data would not be displayed correctly in parameter sheets. #1936

Terraform-Driver
---------------
 * Improved how variables with multiple input values are handled. #723
 * Improved the "HCL setting" items in the Substitute value auto-registration. #1777
 * Fixed an issue where records would not be displayed if the user moved to the Movement list by clicking a button in the Workspaces list menu. #1808


*******************************************************************************************************


Exastro IT Automation 1.9.1 (2022-2-2)
==================================================

Installer
---------------
 * Updated to Version 1.9.1 #1782

Export/Import
---------------
* Fixed an issue where a system error would occur when the "history" button in the Export/Import list" menu was pressed. #1768

Symphony
---------------
* Made it possible to use EDIT (RESTAPI) in the Symphony and Conductor list menus. #1781

Conductor
---------------
* Made it possible to use EDIT (RESTAPI) in the Symphony and Conductor list menus. #1781


*******************************************************************************************************


Exastro IT Automation 1.9.0 (2021-12-20)
==================================================

Installer
---------------
 * Changed some of the default config values when installing. #1096
 * Fixed an issue where HYML_AJAX could not be installed. #1104
 * Updated to Version 1.9.0. #1430
 * Fixed an issue where Exastro IT Automation could not be installed on CentOS 8.4.2105. #1577

General
---------------
 * Added a button that allows users to see what role they are linked to. #673
 * Applied a fix for the history display function. #816
 * Fixed a bug in the Progress log. #1014
 * Fixed a bug with the REST API FILTER search function. #1015
 * Fixed an issue where uploading UTF-8 files would freeze the upload process. #1149
 * Fixed some of the messages for the filter search function. #1184
 * Removed hidden rows and collumns from the Change history files. #1205
 * Implemented SonarQube to fix problems in the source code. #1355
 * Added "EPOCH" as a design type in the System settings. #1389
 * Fixed a bug with the URL and GET parameter that would occur when moving to the login screen. #1441
 * Improved the Display filter search function. #1442
 * Improved the Pulldown search function. #1443
 * Fixed a bug with ajax. #1491
 * Rewrote the text for the english version of ITA. #1524
 * Fixed an issue where the widget images would blend in with the background if the background was colored white. #1527
 * Fixed the Calendar's z-index. #1626
 * Fixed an issue where the login and logout button would disappear. #1670

Dashboard
---------------
 * Made it possible to import/export link collections. #1037
 * Fixed an issue where Menu groups would not be visible to the users. #1422
 * Fixed the "Add widget" menu text. #1648

RestAPI
---------------
 * Made it possible to collect resource ID through Rest API. #1128
 * Fixed an issue where the RestAPI response would not display correct information when updating a menu with a "File upload" item. #1545
 * Made it possible to download and upload excel files for all the menus by using RestAPI. #1550
 * Fixed an issue where an error would occur when registering a symphony through RestAPI. #1695

Management console
---------------
 * Made it possible to acquire version information with REST. #758
 * Made it possible for users to remove the expiration date for user accounts. #784
 * Improved the readability for the version information. #1006
 * Added an option for no forced password resets for initial logins. #1081
 * Changed the smallest and biggest value for "Maximum line output for Excel files" in the Menu list. #1366
 * Fixed an issue where webaplogs would automatically be output when displaying the sequence list menu. #1427
 * Fixed an issue where files in the Symphony data relay storage path would not be deleted. #1525
 * Added "Conductor list" data to the Operation deletion list. #1526
 * Fixed an issue where the "oaseaction" role would delete itself. #1621
 * Changed the default Authentication period value. #1647

Basic console
---------------
 * Made it possible to encrypt the password for the CiscoIOS special mode in the Device list's Ansible user information field. #227
 * [Device list]Fixed an issue where users could register files even when no files had been uploaded. #1264
 * [Device list]Fixed a bug with the download function for encrypted SSH secret key files. #1315

Export/Import
---------------
 * Fixed an issue where upload files linked to deleted data would be exported. #1156
 * Fixed a bug with the import function that would occur when there are no menu to import. #1342
 * Made it so the Export/Import list's history displays the most recent history. #1349
 * Fixed an issue where a log would automatically be displayed. #1421
 * Fixed an issue where the "all menu" button would become active when there are no menus that can be imported when using the Excel bulk import function. #1510
 * Fixed an issue where the "Mode:Environment migration" text would be underlapped by the next item when enlargening the screen. #1519
 * Fixed an issue where the menu ID formats would be different from each other when running UPLOAD and EXECUTE Xcommand with RestAPI. #1661
 * Fixed Export/Import's RESTAPI's error handling. #1704
 * Fixed an issue where the "Excel Bulk export" menu would display Menus that didn't have excel output support. #1754

Symphony
---------------
 * Made it possible to gather input data and result data from the operation list through REST. #757

Conductor
---------------
 * Made it possible to gather input data and result data from the operation list through REST. #757
 * Added a notification function for the results. #312
 * Fixed an issue with the Conditional branch function. #467
 * Made it possible to branch succeeding processes from Movement return values. #587
 * Added more options when using RestAPI. #1134
 * Fixed an issue where response_code=200 would be returned. #1268
* Fixed an issue where registering a conditional branch with a blank case would output the text "Please select a class from the Conductor class list" without opening the list. #1746

Menu create function
---------------
 * Made it possible to reference values in parameter sheets without host from parameter sheets with host. #211
 * Made it possible to increase the maximum byte amount. #1095
 * Fixed an issue where the "OK" button that shows up when changing the menu to Vertical menu (REPEAT:1) would not function. #1263
 * Made it possible to register default values for menu items. #1303
 * Added validation for registering menus that exceeds the maximum number of repeated vertical menus. #1322
 * Made it so the the Menu/ table link list's menu name and excel file name matches. #1518
 * Fixed a bug where referenced password collumns in downloaded excel files would be encrypted. #1523
 * Fixed an issue where pressing a link from a pulldown menu created with 8192 bytes would cause an error to occur. #1566
 * Fixed an issue with the filter function for string items. #1569
 * Fixed an issue where refreshing created vertical menus collumn groups would not display correctly. #1628
* Fixed an issue where users could not download data in SCSV format from user-created menus. #1732

Compare function
---------------
 * Added RestAPI for the Compare function #946

Ansible-Driver
---------------
 * Linked file upload items and automatic registration settings. #427
 * Made it possible to specify Env when running AnsibleEngine. #1063
 * [Collect function]Fixed an issue where it would seem that the array elements' collection data was not acquireable.  #1160
 * Made it possible to have no values for global variables. #1300
 * Made it possible to load used variables from Playbooks when using the filter function. #1301
 * [Collect function]Fixed an issue where configuring collected interface information would output a log. #1386
 * Shortened the start cycle for automatic substitute value registration. #1444
 * [Collect function]Made it so only file upload collumns that has collected item values will be displayed in the list. #1506
 * [Collect function]Made it possible to specify files in addition to paths when using file upload items. #1532
 * Fixed a bug with the manual check for restoring records in Legacy-Role. #1537
 * Changed the process confirm cycle used when running Movements. #1541
 * Fixed a bug that would occur when using variables in the Pioneer mode Template list. #1593
 * Fixed a big that would occur when using Ansible tower as execute engine. #1713
 * Fixed an issue where the Sensitive settings for the "Substitute value list" would be invalid when updating ITA to 1.6.0. #1723

Terraform-Driver
---------------
 * Fixed a problem with conductor_workflowdir. #732
 * Made it possible to use the destroy command in Terraform menus. #1313
 * Shortened the start cycle for automatic substitute value registration. #1444
 * Put Terraform Variables in the InputData.zip file. #1581
 * Fixed an issue where the excel and web values for Terraform Substitute value auto-registration would be different from each other. #1605
 * Fixed an issue where "Substitute value auto-registration" value would be displayed even when the "Movement-Module link" record is deleted . #1630

CI/CD for IaC
---------------
 * Made it possible to clone git by using SSH. #1367
 * Fixed an issue where linking files would cause the "Remarks" field to go blank. #1382
 * Fixed an issue where pressing the "Resume" button in the "File link" menu would cause the "Status" item to go blank. #1408
 * Changed documents and policies regarding Github password authentication. #1511
 * Fixed an issue where linking a file with 0 bytes would cause an issue. #1586
 * Fixed an issue that would occur when branching a remote repository menu with a blank name. #1600
* Fixed an issue where the name of excel output remote repository files would have a different file name (Only in the English version) #1733


*******************************************************************************************************


Exastro IT Automation 1.8.2 (2021-11-12)
==================================================


Installer
---------------
 * Updated to Version v1.8.2 #1617
 * Fixed an issue with the Excel output by phpspreadhseet v1.19.0 #1687

Export/Import
---------------
 * The return value for the REST UPLOAD command for Excel bulk import is different from the one written in the manual. #1604

Ansible-Driver
---------------
 * Changed how the yaml.load modules were handled in Pioneer mode.#1589


*******************************************************************************************************


Exastro IT Automation 1.8.1 (2021-08-26)
==================================================


Installer
---------------
 *  Updated to Version v1.8.1 #1438
 *  Fixed an issue where the install log would display wrong numbers when only "ita_base" in the answer.txt file is set to "yes" in the answer.txt file. #1454

General
---------------
 * Added a menu that allows users to link with OASE roles #1380
 * [Aubstitution value auto-registration settings]Fixed an issue where the menu would not display the updated values when the pulldown menu's reference source was updated. #1437 

Management Console
---------------
 * Renamed the "SSO Basic information list" and "SSO Attribute preference" menu's excel file name to better fit it's function  #1460

Export/Import
---------------
 * Fixed an issue where "Export/Import"  would be included in the "Excel export" menu group when updating.#1449
 * Fixed an issue where the excel bundle import log would stop #1461
 * Fixed an issue where if the a file name with japanese characters with it are used when using Excel bundle import with CentOS8, an error would occur #1469

Compare
---------------
Fixed an issue where recreated menus could not be compared when selecting "Match all cases". #1489

Ansible-Driver
---------------
 * [Collect Function]Fixed an issue where the updating/registering a file would reset the target. #1433
 * [Substitution value auto-registration setting]Fixed an issue where exporting would display that the parameter item failed to convert ID #1464
 * Fixed an issue that would occur if the the template variable, "TPF" was set to the specific value in the substitute value list. #1481

Terraform-Driver
---------------
 * [Substitution value auto-registration setting]Fixed an issue where exporting would display that the parameter item failed to convert ID #1464


*******************************************************************************************************


Exastro IT Automation 1.8.0 (2021-07-16)
==================================================


Installer
---------------
 * Added support for CentOS Stream #528
 * Updated to Version v1.8.0 #999
 * Deleted the Construction file list function #1083
 * Fixed an issue where Warnings were output when restarting the httpd service when installing. #1105
 * Fixed an issue where the installer would fail because there were no MariaDB in the UBI8 Standard repository. #1146
 * Fixed an issue where repositories keeps getting added to /etc/yum.repos.d/ita.repo every time the install script is run. #1172

General
---------------
 * Added a clone button to the "List/Update" sub menu in all of the parameters. #174
 * Fixed an issue with the Excel output by phpspreadhseet v1.15.0 #329
 * Fixed an issue in the Filter selection (ID Column) where error 400 would occur when trying to search for an ID that doesn't exist in LIST with RestAPI. #437
 * Made it possible to download the Change history in an Excel file #482
 * Made it possible to check reserved Symphony/Conductors from the Main menu #488
 * Made adjustments to Exastro OASE user's initial Role/Menu link value #752
 * Created a function that allows users to delete Password items #753
 * Fixed an issue where the "Execution process type" headline cell is not combined when downloading the Excel file #831
 * Fixed an issue where menus users don't have permission to see would be displayed #987
 * Fixed an issue where the display filter's date items would not search properly when the "from" and "to" date are the same as the item the user is searching for #1027
 * Fixed an issue where the screen would not be displayed correctly when the display filter's "Pulldown search" button is pressed repeatedly #1125
 * Fixed an issue where the screen would not be displayed correctly when Darkmode is selected #1147
 * Fixed an issue where an system error would occur when filter searching #1165
 * Fixed an issue where some values would not display properly if some specific characters were included #1174
 * Fixed an issue where collumns after the "Access permission role" would not load when importiing with scsv #1225

RestAPI
---------------
 * Added options that does not include the title line when gathering menu data with FILTER #768
 * Made it so users can update required items without POST #771
 * Fixed an issue where a Notice would be output when updating from REST #1169

Management Console
---------------
 * Fixed an issue where deleting your own role/user link would cause an system error #897
 * Fixed an issue where some new users would not be moved to the "Change password" screen after their first login #1077

Basic Console
---------------
 * Made it possible to examine deleted Movements in Symphony class edit #910
 * Fixed an issue in the "Create ER Diagram" where if the source and destination reference were to be the same, the higlight function would not work properly #971
 * Fixed an issue where holding down the "Power ON" button in the Device list would cause a system error #1129
 * Fixed an issue with SonarQube identification in the ER Diagram creation function #1188

Export/Import
---------------
 * Made it possible to download/upload all the data for each menu in an excel file #674
 * Fixed an issue where the post-export screen would not have it's text color change regardless of the design theme #1001
 * Fixed an issue with SonarQube identification in the Export/Import menu function #1189
 * Fixed an issue where the post-export screen's "Export Menu" display would not show if "Gorgeous" design was selected #1281
 * Made it possible for users to have only tasks that they registered themelves displayed #1281

Symphony
---------------
 * Fixed an issue where users were able to press the "Emergency stop" button even after the Movement has been Emergency stopped #1013
 * Fixed an issue where users could not execute in Symphony class edit with RestAPI #1085
 * Made it possible to configure the time it takes for Movements to change status to "Not yet executed (Reserved)" in the "Regularly executions" menu #1163
 * Fixed an issue with SonarQube identification in Symphony Regular executions #1190

Conductor
---------------
 * Added RestAPI Function for the Class edit function #189
 * Fixed an error where error contents would not be output when an error occurred without a target host #749
 * Changed the update cycle for Conductor Operation Check #933
 * Added a legend to the Conductor edit screen #964
 * Changed the name of the  "Conductor class edit" Menu's "Merge" tab #965
 * Fixed an issue where some buttons would not display correctly in the Conductor class edit screen if the window aspect ratio is changed #986
 * Made it possible to configure the time it takes for Movements to change status to "Not yet executed (Reserved)" in the "Regularly executions" menu #1163
 * Fixed an issue in the Conductor list where changing a role/menu link from "View only" to "Can maintain" will cause a Javascript error #1167
 * Fixed an issue in Conductor Class edit hwere some abolished contents would remain #1186
 * Fixed an issue with SonarQube identification in the Conductor Regularly execution menu #1191

Create Menu
---------------
 * Made it possible to choose "Yes/no" , "True/False" and "*/(blank)" from Pulldown sselections #178
 * Fixed an issue where it was possible to have the "/" character in item names when creating menus #540
 * Made it possible to configure multiunique settings from the Web screen #653
 * Fixed an issue where after changing the menu group of an earlier created menu, the pre-updated menu would remain in the old menu group #709
 * Removed the option to change the Menu name when editing #727
 * Fixed an issue where updating an regular expression would reset the data and it's history #887
 * Added Linksto Pulldown selections #950
 * Fixed an issue where an initialized data sheet would remain the the substitute value and reference menus #1010
 * Improved the VIEW for reference item information #1101
 * Fixed an issue where if the character "`" was written in an item name and it was referenced to a different menu, the page would not open #1222
 * Fixed an issue where a validation error would occur if there specified ID of Reference items had too many numbers #1236
 * Fixed an issue where the "Password" item in the "Menu item creation information" menu shows "Message ID is not found" #1347
 * Made the button names and Modal names in the  Menu define/create menu unique #1348
 * Fixed an issue where menus created in an ITA version before the RBAC function was added would not be editable. #1730

Compare
---------------
 * Added Options for the Compare function #942
 * Changed the text in the English version to better fit the function #1144

Host group
---------------
 * Fixed SonarQube identification for the host group function #1192
 * Fixed an issue where data registered to Menus for host groups would not be extracted to the "For host" menus #1231

Ansible-Driver
---------------
 * Made the Operation parameter settings active from the Movement side #911
 * Added support for Proxy when executing with API #810
 * Adjusted the Collect function log output items #821
 * Improved data registration to vertical menus when using the collect function #1050
 * Improved data registration to horizontal menus when using the collect function #1051
 * Made it possible to select different character codes for Pioneer mode #1052
 * Fixed issues that would occur when using full sized characters in Pioneer mode #1062
 * Fixed an issue where multiple Movements could not be executed at the same time #1068
 * Changed the default interface information value from -ww to -v #1069
 * Fixed an issue with Pioneer where the dialogue file would cause an unexpected error if the name contain these symbols "()" #1078
 * Fixed SonarQube identification for Ansible-Driver #1193
 * Fixed an issue where an unexpected error would occur if an operation parameter had "-verbosity" set to it #1214


CI/CD for IaC
---------------
 * Added support for CI/CD for IaC #815


*******************************************************************************************************


Exastro IT Automation 1.7.2 (2021-06-03)
==================================================


Installer
---------------
 * Updated to Version v1.7.2 #1117
 * Fixed an Issue where the installer would fail if MariaDB v10.6 and later were used #1369

Management Console
---------------
 * Fixed a mistake in the File delete manager function #1139

Conductor
---------------
 * Fixed an issue where Individual operations disappears when running Conductor through RestAPI #1122

Create Menu
---------------
 * Fixed an issues where items in the Menu item creation information with ID's over 1000 would not be referenced correctly #1100

Ansible-Driver
---------------
 * Fixed issues that would occur when using AnsibleRole convertion chart #1092


*******************************************************************************************************


Exastro IT Automation 1.7.1 (2021-05-14)
==================================================


Installer
---------------
 * Fixed an issue where an warning would appear in Python when executing Ansible-Driver with RHEL8 type OS #993
 * Updated to Version 1.7.1 #998
 * Fixed Issue #637 #1019
 * Fixed an issue where a space would be added when registering Master information login authentication type name #1032

General
---------------
 * Fixed an issue where File contents uploaded using API would not be encrypted #1036

Management Console
---------------
 * Changed the sample adddress in some files #1012

Basic Console
---------------
 * Fixed an issue where some information would not show up when updated using Excel files #983

Export/Import
---------------
 * Fixed REST Error #975
 * Fixed an issue where selecting "Time specification" would not import the history table's sequence number correctly #1025
 * Fixed an issue where the "Sequence list" menu would not display menu's imported to a different menu #1035
 
Symphony
---------------
 * Changed "Default skip" to "Pause" in the Symphony Class edit screen #989

Conductor
---------------
 * Fixed an issue where the Completed date/time would not be registered when a Conductor is stopped because of an abnormality #974
 * Fixed an issue in the Conductor class edit menu where registration in Symphony call->Conditional-Branch were impossible #994
 * Fixed an issue where the Conductor execution screen's operation buttons would not be displayed #1007

Create Menu
---------------
 * Fixed an issue where items created with the "Input method: Date" were not displayed in the Display filter #966
 * Fixed an issue where the "Define/Create Menu" page would not be displayed properly when collumn groups were in a nested structure #1047

Compare
---------------
 * Fixed an issuess where some excel/csv outputted files from Compare definition name would not open #1054

Ansible-Driver
---------------
 * Fixed an issue where an unneeded jump and narrow down button would be displayed when using Ansible Tower #976
 * Fixed issue that would occur when using Ansible Role Conversion tables #977
 * Fixed an issue where a warning message would display when the user is executing when the selected host name is just a string of numbers #1020
 * Fixed issue where multiple Movements could not be executed simultaneously #1068


*******************************************************************************************************


Exastro IT Automation 1.7.0 (2021-04-09)
==================================================


Installer
---------------
 * Updated to Version v1.7.0 #662
 * Changed the ita_answers.txt initial values #805
 * Fixed an issue where an error would occur when executing the offline installer because the pip was old #902
 * Installed boto library #957


General
---------------
 * Added History search button #55
 * Fixed an issue where operations with "Not yet executed" status could not be force stopped #83
 * Deleted the "Abolish" item name for the Header item #101
 * Abolished Excel Message management #191
 * Fixed an issue where asterisk marks would appear when history searching with History track process in IDCollumns #296
 * Fixed an issue where trying to upload a file while the session is timed out would freeze the process at "Upload processing" #331
 * Fixed an issue with data narrowing for Access permission roles for View data with Select count definitions #559
 * Fixed an issue where multibyte characters file names would corrupt when downloaded #588
 * Adjusted Menu names  and collumn names #596
 * Fixed an issue in the display filter where the Pulldown search name would be cut out if it was too long #618
 * Implemented Encrypted File upload collumns #637
 * Fixed an issue where some abolished roles could be restored when they shouldn't be able to #638
 * Fixed an error message that would appear when restoring an abolished record that contained an ID change fail #651
 * Adjusted the output items of the Web log #654
 * Fixed a bug with the Operation History widget on the Dashboard #698
 * Unified how Password items are displayed #725
 * Fixed issue where access control would not function properly when updating/abolishing/restoring records when using RESTAPI #774
 * Fixed issue where access control settings would not function properly when updating/abolishing/restoring records when using RESTAPI #775
 * Improved the URL Pass/Query gathering process #855
 * Fixed an issue where the Operation status check's Sub menus' initial settings doesn't function properly #876
 * Fixed an issue where the Excel file created cannot be opened if the IDCollumn's source collumn does not exist #892

Dashboard
---------------
 * Added a Jump button and made it easier to understand what menus they connect to #707

Management console
---------------
 * Made it so the last login date/time is displayed in the User list #595
 * Added a Jump button and made it easier to understand what menus they connect to #703
 * Added SOSO attribute information option #741
 * Adjusted text to better reflect the contents in the Menu manager Remarks field #785
 * Fixed an issue in the Role list where  the Menu group ID and the name will not display "ID Change failed" when it should #797

Basic Console
---------------
 * Added function that lets users output ER Diagrams #173

Export/Import
---------------
 * Adjusted the Export/Import function #162
 * Fixed an issue where if the were japanese characters in the kym file name, an error would occur when trying to download it #779

Symphony
---------------
 * Changed the Operation list's Access permission value to better fit individually specified Operations #524
 * Changed the Movement order on the left side of the screen to be ordered by ID. #556
 * Improved the Operation list display when data is returned using the menu import function #648
 * Fixed an issue where the access permission role in the Operation list's change history table would not be reflected correctly #679
 * Fixed an issue where a warning message log would be output into the /var/log/messages path when executing regularly executed operations #924

Conductor
---------------
 * Made it clearer on how many letters can be input when an validation error occurs.
 * Improved transitioning to a different screen from the Operation details screen.
 * Changed the Operation list's Access permission value to better fit individually specified Operations #524
 * Changed the Movement order on the left side of the screen to be ordered by ID.
 * Improved the Operation list display when data is returned using the menu import function.
 * Improved the "Conductor class edit" menu's Movement filter
 * Fixed an issue where the access permission role in the Operation list's change history table would not be reflected correctly.
 * Added a Jump button and made it easier to understand what menus they connect to.
 * Added a function for aligning objects #742
 * Fixed an issue where Symphony call status process would not progress from "Preparing" #745
 * Fixed an issue where individual operations are not function correctly in Conductor/Symphony sub callers. #750
 * Fixed an issue where an error would occur when the same sub conductor caller is lined up multiple times and executed #751
 * Made adjustment to Scram error displayed after an emergency stop when using conditional branch #777
 * Fixed an issue where if one movement in a parallel branch ends up in an error, the other one will not have it's status updated.
 * Fixed an issue where Nodes would not be cleared #875
 * Fixed an issue where a warning message log would be output into the /var/log/messages path when executing regularly executed operations #924

File link list
---------------
 * Fixed an issue where an error would occur when searching with "remote Repository URL" and "Clone Repository" in the "Interface Information" #860

Create Menu
---------------
 * Improved the Compare function #4
 * Made it possible to grab information linked to the master when using pulldown selection #47
 * Fixed an issue in the Create/Define Menu screen  where the selectable items are cut off when the names are too long #195
 * Made it clearer on how many letters can be input when an validation error occurs #206
 * Fixed issue where Data would be deleted when menus are updated #212
 * Fixed an issue where selecting either "Unique contraint" or "Required while the screen is scrolled to the right would scroll the screen all the way to the left #691
 * Added a Jump button and made it easier to understand what menus they connect to #704
 * Added more item types that can be selected by default in Pulldown selection #782
 * Fixed an issue where an error would occur when editing vertical menus with different collumn groups with items that has the same name #885

Host Group
---------------
 * Deleted Host group variable link function #693

Ansible-Driver
---------------
 * Added support for Secret key files with Passphrases #210
 * Fixed an issue where pressing the "Progress status (Error log)" title bar would make the log disappear #228
 * Fixed an issue where Pioneer would be unreachable if the Host name was set to "localhost" #245
 * Fixed an issue where some required items would not have a * mark next to it #459
 * Improved the Operation list display when data is returned using the menu import function #649
 * Added a Jump button and made it easier to understand what menus they connect to #705
 * Fixed an issue where Execution logs over 1mb could not be gathered when the operation is executed while linked to AnsibleTower #717
 * Fixed an issue with the Ansible Interface information's host pulldown list #728
 * Improved the Error message for invalid YAML files #842
 * Fixed the ky_ansible_execute-workflow description #912
 * Fixed an issue that would occur when Template list is used in Pioneer #915
 * Made it so the Collect function can collect files without file extensions #929
 * Fixed an issue where running Ansible will create a 0 byte log fine in /Exastro/ita-root/logs/backyardlogs #938
 * Fixed an issue that would occur when Command:with_items is used in Pioneer dialogue file #956

Cobbler-Driver
---------------
 * Fixed an issue where an unecesarry log would be output to the /var/log/messages #807

Terraform-Driver
---------------
 * Fixed issue where Movement list access permissions got set to the Operation history access permission #523
 * Improved the Operation list display when data is returned using the menu import function #650
 * Fixed an issue where the access permission roles wouldn't be displayed correctly in the Operation list's Change History table #675
 * Added a Jump button and made it easier to understand what menus they connect to #706
 * Fixed an issue where an error would occur when holding the Enter button over the "Register","Update","Delete" button in the "WorkSpace list" #781
 * Changed the "Terraform Registration list" to "Linked Terraform management" #806
 * Made it possible to connect with external Terraform interfaces from Proxy environments #808


*******************************************************************************************************


Exastro IT Automation 1.6.3 (2021-03-11)
==================================================


Conductor
---------------
 * Made it possible to transmit information between Movements when using Sub conductors #733
 * Fixed an issue where the Movement's process would continue even when the Movement stopped abnormally when using Parallel branch #823
 * Improved the Conductor screen UI #864
 * Fixed an issue where a validation error would occur when the OperationID contains more than 4 numbers #868


*******************************************************************************************************


Exastro IT Automation 1.6.2 (2021-02-22)
==================================================


Installer
---------------
 * Fixed an issue where the installation would fail because the pip3 version is too old #734
 * Fixed an issue where the installation would fail because one of the pip3 list options are no longer supported #735


*******************************************************************************************************


Exastro IT Automation 1.6.1 (2021-01-13)
==================================================


Installer
---------------
 * Updated to Version v1.6.1 #558

General
---------------
 * Improved the "Remarks" item #107
 * Seperated the error pages per HTTP Status #165
 * Made it possible to configure Access Permission roles with Excel/REST-API #451
 * Fixed an issue where the ID Change failed wont display when using VIEW and TextColumn with JOIN #464
 * Added a percentage display to the graphs in the Dashboard #492
 * Improved the Access Permission role Filter #494
 * Fixed an issue where roles without permission displayed "ID Change failed" #514
 * Changed paths accessed with URL from Absolute paths to Relative Paths #529
 * Made it possible for user to jump to the Operation list from the DashBoard Operation Status/Result/History #533
 * Fixed an issue where the ID Change failed wont display when using VIEW and TextColumn with JOIN #568
 * Fixed an issue where the History search ID Change failed wont display with VIEW using JOIN #569
 * Made it so the Update/Abolish rows in the "Operation list" Menu are hidden #583
 * Fixed an issue where an error would occur when the "Last updated by:" value exceeded 65 bytes #624
 * Fixed an issue when restoring abolished records where abolished roles set to access permission roles are restorable #638
 * Adjusted how the User Password expiration period is handled with Rest-API #710

Management Console
---------------
 * Fixed an issue where the user list display would collapse if the user name were too long #516

Basic Console
---------------
 * Fixed an issue where some combinations of login password and management could not be restored #493

Export/Import
---------------
 * Made it so multiple kym files can exist on the same ITA Environment #270

Symphony
---------------
 * Fixed an issue where the pop-up window that would be displayed in the Regular Execution menu's "Schedule settings" would move downwards when the user is dragging it with the mouse #382
 * Fixed an issue where users were able to executes Movements they didn't have access to #458
 * Fixed an issue where users could not check the Access access permission to the device's Movement when executing Symphonies/Conductors from the regular Execution menu #515

Conductor
---------------
 * ixed an issue where the pop-up window that would be displayed in the Regular Execution menu's "Schedule settings" would move downwards when the user is dragging it with the mouse #382
 * Fixed an issue where users were able to executes Movements they didn't have access to #458
 * Fixed an issue where users could not check the Access access permission to the device's Movement when executing Symphonies/Conductors from the regular Execution menu #515
 * Made it so the role of the user who executed the operation gets access to the Conductor Class List #519


Create Menu
---------------
 * Deleted unneeded Menu text in the "Menu creation information" menu #539
 * Fixed an issue where files deleted in vertical menu'S File upload items would not be reflected correctly in the Substitute Value menu #553
 * Made it so Backyard for Automatic Substitute value registration settings are started when updating datasheets #619

Ansible-Driver
---------------
 * Added File Upload Collumn to the Collect function #449
 * Created directory for the Collect function and added Reservation variables #512
 * Made it possible to configure access permission roles for the Collect function #517
 * Fixed an issue where data that should not be created gets created when the user is using Access permission role #525
 * Improved the Collection function status text #527
 * Fixed an issue where the Null link wouldn't work against Template list Variable definitions #528
 * Added OperationID to Reservation variables #585
 * Fixed an issue where the Movement list access permission would be configured to the Operation history access permission #522


Openstack-Driver
---------------
 * Deleted Openstack-driver #450

Terraform-Driver
---------------
 * Fixed an issue where data that should not be created gets created when the user is using Access permission role #526


*******************************************************************************************************


Exastro IT Automation 1.6.0 (2020-11-25)
==================================================


Installer
---------------
 * Fixed issue with the ITA issued Self Certificate #7
 * Fixed an issue where including symbols in the MariaDB password would cause an error #179
 * Changed the Install Script construction #181
 * Fixed an issue where the process would stop if the user doesn't have permission to execute in the parent directory #204
 * Fixed an issue where the return value would be 0 if sh ita_builder_online.sh fails #217
 * Fixed an issue where using Composer v2.0 or later would stop the installer #347
 * Fixed an issue with the RHUI3 Repository #360
 * Added Version patch v1.6.0 #364
 * Fixed an issue where the installation process would halt when installing MariaDB ver 10.4 or later #428
 * Fixed an issue where the MariaDB Cache would remain even after ITA is uninstalled #447

General
---------------
 * Added RBAC to Record units #28
 * Added Operation history graph to the main menu #72
 * Added Menu open/close settings #105
 * Fixed an issue where user who does not have access to the see the post-login screen logging in would cause an error #146
 * Fixed an issue where the "Eye" mark in the Password item would be displayed outside the item frame if the password item name is too long #246
 * Fixed an issue where filtered item names would be displayed in the Table settings #281
 * Fixed an issue where an Error would be output when executing Registration process (EDIT) with REST #309
 * Deleted Score from all of the Operation list menus #353

Management Console
---------------
 * Added menu that allows users to adjust Sequences #180
 
Export/Import
---------------
 * Improved the stop service process when importing #399

Symphony
---------------
 * Added function that allows users to gather Input/Result data from the Symphony list #12

Conductor
---------------
 * Added Symphony call function #199
 * Fixed issue where editing an executing Conductor class would cause it to be different in the Operation Details screen #201
 * Improved how Links are displayed in the Operation Details screen #208
 * Added function that allows users to gather Input/Result data from the Conductor list #252
 * Fixed an issue where the Conductor class name items and items in other menus would have different names #255
 * Fixed an issue where abolished conductors could be executed #287
 * Fixed an issue where the Conductor Operation Check screen would say that the operation is still running even when the operation has ended #332
 * Added RESTAPI(INFO) items for the Operation Check menu #478 

File link list
---------------
 * Added Terraform Driver to File link list #190
 * Fixed an issue where an error would occur when initializing the Interface information with a blank repository #269
 * Fixed an issue where registering without a value set in the "File master" menu>"Directory" would freeze the system #341

Create Menu
---------------
 * Created Password collumn for the Menu item creation tool #176
 * Created File upload collumn for the Menu item creation tool #177
 * Created parametersheet that does not contain hosts #203
 * Fixed an isssue where changing the parameter sheet after a data sheet was created and not inputing a menu group would cause the screen to freeze #216
 * Fixed an issue where recreating a vertical menu after creating it would cause the screen to freeze #229
 * Fixed issues where users could created vertical menus without collumn groups when they set it to "Collumn groups" #230
 * Fixed an issue where collumn gorups could not be configured while in repeat #231
 * Fixed an issue where digits would disappear for "Input method: Decimal numbers" when the "Create" button is pressed #243
 * Configured Default values for selecting menu groups #263
 * Created Link items #422


Ansible-Driver
---------------
 * Changed the storage place of the log displayed in the Operation State check page #163
 * Made Ansible-vault ecryption faster #202
 * Added Collect function #207
 * Fixed an issue where multiple role packages could be registered to the same Movement #226
 * Fixed an issue where the added role name would not be automatically reflected in the Ansible-Legacy role after the role package was registered in the File link list #272
 * Fixed an problem where blank lines would be output in the Backyardlog #273
 * Fixed an issue where the host vars would not be output even when GBL_XXX is written in the Template list's Variable Definition #280
 * Fixed an issue where there were some characters that could not be used in the Device list when encrypting the password with Ansible-vault #289
 * Fixed an issue where an error would occur when the user would try to register or update in the template list when the variable definition was blank #295
 * Fixed an issue where a validation error would occur if the user tried to filter with alphabetic characters in the global variables field #302
 * Fixed an issue where the pulldown selection would not display properly if it was edited with both the "register" and the "update" button #383



Terraform-Driver
---------------
 * Implemented Manual execution function for "Apply Method" #182
 * Created page where users can manage items registered to the TFE side #183
 * Improved the Sensitive settings for the Substitute value page #184
 * Improved the Sensitive settings for the Substitute value page #185
 * Created page for Automatic substitute value registration settings #186
 * Fixed a problem where the PolicySet and Workspace link would be displayed on the TFE side even if it were deleted from the ITA side #284
 * Made it so the Inputdata that gets created after an operation is executed is included in the policy file #285
 * Changed the storage place of the log displayed in the Operation State check page #342
 * Fixed an issue where the pulldown selection would not display properly if it was edited with both the "register" and the "update" button #383


*******************************************************************************************************


Exastro IT Automation 1.5.0 (2020-07-31)
==================================================


Installer
---------------
 * Added "Manual Install guide" #21
 * Added Version patch #22
 * Fixed some errors in the Online install #58
 * Made it clearer in the manual that the hosts does not need to be changed #113
 * Changed the YAML analysis library #136
 * Fixed the "for terraform-driver" in the Install script #140
 * Made the hostgroup in the ita_answers.txt initial value to "yes" #141
 * Deleted DSC-Driver #143
 * Added Netcat commant when installing AnsibleDriver #150
 * Changed Apache vhost setting value #151
 * Fixed an issue where an error would occur when the ITA Install directory was something other than "/" #152
 * Fixed an issue where an error would occur because the sudo command was not installed in the Centos7 image #157
 * Fixed an issue where session cookies would be duplicated if the ITA Container was started multiple times or if it were accessed at the same time #160
 * Improved the /etc/sudoers configuration method #161
 * Added paramiko Module #166
 * Added Backyard service registration and Data Relay Storage directory #170

General
---------------
 * Made it so a button is deactivated when the button collumn record is deleted #54
 * Deleted L7protocol.txt #116
 * Changed the naming style for Excel outpu file name and sheet names when they are long #153
 * Fixed an issue where a broken file would be output when using phpspreadhseet 1.13.0 #156

Export/Import
---------------
 * Fixed some messages #97
 * Reduced the Export file size #137
 * Added Terraform driver to Symphony/Operation Export/Import #171

Basic Console
---------------
 * Made it so Symphony class names has to be unique #98
 * Fixed an issue where an error would occur in Symphony Execution when the user would specify an Operation ID and execute it #145

Management Console
---------------
 * Implemented SSO Authentication #80
 * Changed the size of the names of Menus and Menu groups #111
 * Fixed an issue where an error would occur when searching in the Change history in Menu lists #172


Symphony
---------------
 * Added new Symphony functions #34

Create Menu
---------------
 * Made it possible to link the role of the creator when creating menus #96
 * Made it possible to select File embeded variabled and template embeded variable names from pulldown selections #123
 * Made it possible to register and update items related to Create Menu by GUI #135
 * Made Multicollumns target for Automatic Substitute value registration settings #138
 * Rewrote some messages #148
 * Fixed an issue where Vertical menus would not have their sized check if it was not "String" #154

Host Group
---------------
 * Fixed an issue where unnecessary variable names would be displayed in the pulldown menu in the Automatic Substitute value registration settings even when the Host group name was deleted in the Host link list #133

Ansible-Driver
---------------
 * Fixed an isssue where uploads in advance would lead to an error #11
 * Fixed a problem where the Legacy/LegacyRole file does not get reflected correctly in the Device list's Pioneer user information #26
 * Made it possible to connect with other methods than SSH via AnsibleTower #27
 * Improved the Jobslice Log display #30
 * Made it possible change the Substitute value list's specific values into Multi line text #76
 * Made it so the files are sent instead of unpacked to the AnsibleTower Project base path #93
 * Fixed an issue in the Automatic registration settings screen where the user could register both Key and value variables when "Value type" is selected #134
 * Changed the "OS Type master" Menu's Menu group #142
 * Made it so registering dialogue files with character codes other than UTF-8 will lead to an error #144
 * Fixed an issue where the log would not display everything in the Ansible operation status check screen #164
 * Fixed an issue where an unexpected error would occur when the jobslice has no host linked to it #167
 * Fixed an issue where some role names not be selectable in the Movement details role name list when they were registered to the Role package list #200

DSC-driver
---------------
 * Fixed an issue in the Automatic registration settings screen where the user could register both Key and value variables when "Value type" is selected #134
 
Terraform-Driver
---------------
 * Added Terraform-Driver support #82


*******************************************************************************************************


Exastro IT Automation 1.4.1 (2020-04-28)
==================================================

Installer
---------------
 * Made HTTP access enabled by standard #117
 * Installed boto3 library #129

General
---------------
 * Fixed issue where the characters would not become bold when double quotation would be put in the search filter in the "Operation status check" menu #94

Export/Import
---------------
 * Fixed issue where the Menus with long menu names would not be displayed correctly in the "Menu export" menu #112
 * Fixed issue where the Menu group's image panel would not be exported when exported menus #114

Construction File Management
---------------
 * Fixed the English environment's "Remarks" field #120

Create Menu
---------------
 * Fixed messages for the English environment's "Menu item creation information" menu #110
 * Fixed issue where the "Substitute value" item name would not display properly #115
 * Fixed an issue where an error would not occur even when the input numeric value would exceed the set digit in the decimal collumn #118
 * Rewrote the error message that would display when the maximum value for decimal numbers were exceeded #119
 * Fixed parameter values for menus created with "Pulldown selection (Date/time)" #124
 * Made it so Automatic Sbustitute value registration settings are linked when using Pulldown selection (Date/Time) #125

Ansible-Driver
---------------
 * Fixed issue where the Ansible driver could not executed if ITA was installed in a directory other than /exastro #121
 * Fixed an issue where Validation error would not occur if the "register" button was pressed when the Role package file was selected but not uploaded in advance #122
 * Changed the "Ansible.cfg" ssh_args controlMaster's value to Auto #130




*******************************************************************************************************



Exastro IT Automation 1.4.0 (2020-04-07)
==================================================

General
---------------
 * Created collumn class for masks #13
 * Changed the maximum file size for upload files (20MB to 4GB) #15
 * Removed Auth for Pear #18
 * Fixed the WebUI of the English version of ITA #35
 * Added a "release password" button #48
 * Fixed an issue where excess files would be left when RestAPI Registration to menus with upload files failed #49
 * Put http access settings as a comment in vhost #51
 * Fixed issue where the Unique constraint wouldn't work properly when using RestAPI and the unique constrainted character string is "0" #69
 * Raised the set value for DB and PHP Resources when installing ITA #71
 * Changed the HTML_AJAX Time out value #81


Management console
---------------
 * Added design themes for the ITA Screen #8
 * Added Panel image creation tool #40
 * Fixed issue where an error would occur when outputting excel file in the English version environments' "User/Menu link list" and "Role/User link list" #87
 * Renamed the "User/Menu link list" to "Role/Menu link list" #89

Basic Console
---------------
 * Made it so operations can be executed regularly #20
 * Fixed issue when using RestAPI for Symphony in English versions of the Environment #61
 * Fixed issue where the search filter function would not work properly when searching for Operation IDs from "Input Operation list" #70

Export/Import
---------------
 * Fixed issue where the result file would be the same file if the export was executed multiple times #50
 * Moved all menus related to the "Export/Import" function to a new menu group #88


Create Menu
---------------
 * Made "VIEW" items also target in the Parameter sheets linked to the "Automatic substitute value registration settings" #42
 * Configured Maximum and Minimum value (text/number/date) for Menu item creation information #43
 * Configured amount of digits for small numbers in the Menu item creation information #44
 * Configured amount of digits for integers in the Menu item creation information #45
 * Made it so users can choose between Calendar collumn, Integer collumn, Small number collumn and Time collumn when selecting a collumn #46
 * Changed storage path for created PHP source #62
 * Removed Validation check for Menu item creation information #73
 * Fixed issue where data would display information too slowly #77

Host Group
---------------
 * Fixed issue in the "Change History" field in the "Host group split target" menu #64

Ansible-Driver
---------------
 * Fixed issue where users could not connect via AnsibleTower with SSH Key authentification #9
 * Added target host name directory for Pioneer Execution result file #14
 * Added DBIndex #33
 * Made it so operations can be executed in paralell #36
 * Improved the text replacement process in the Ansible log #38
 * Improved the Directory creation process in Pioneer #39
 * Fixed issue where deleted menus would be displayed in the pulldown menu in the Automatic Substitute registration settings menu #57
 * Fixed issue where Variable names in the Automatic Substitute value list would display an error after the menu was imported #59
 * Fixed problem with Ansible-vault when using HA Configuration #65
 * Fixed problem with the legacy-Role Rolepackage "Register/Update" button #66
 * Fixed issue when the Automatic substitute valu registration setting's Backyard process exceeds the memory_limit(PHP) #67
 * Fixed issue where the Automatic Substitute value registration settings wouldn't be displayed when the "Defaults" directory doesn't exist and the user is using Ansible-role's ITA_Readme file #68
 * Increased the Maximum amount of bytes that can be used for Specific values in the Global Variable list from 1024 to 8192 bytes #74
 * Fixed issue when template variables are set as the Specific values in the Ansible Role Substitute Value list #75
 * Fixed issue where Ansible Operations executed with Ansible Tower 3.6 through ITA would have it's status display "Complete (Abnormal)" #78



*******************************************************************************************************



Exastro IT Automation 1.3.0 (2020-01-31)
==================================================

Installer
---------------
 * Removed support for RHEL6/CentOS6
 * Added support for RHEL8/CentOS8
 * Changed PHP5.6 to PHP7.
 * Changed the Ansible version from Python2 to Python 3

General
---------------
 * Added function that allows users to hide/display items.

Management Console
---------------
 * Added RestAPI for Menu Export and Menu Import

Basic Console
---------------
 * Added function that exports/Imports information linked to Symphony/Operation
 * Changed the design of the Movements in Symphony Class Editor
 * Fixed problem where Movement with long names would not display correctly in Symphony Class Editor
 * Added RestAPI for Symphony Class Editor

Create Menu
---------------
 * Combined Parameter sheet creation and Master creation into Menu creation.
 * Added function that allows users to create Data sheets (Menus that are not used in Substitution value auto-registration settings)

Ansible-Driver 
---------------
 * Added function that allows the users to encrypt password items with Ansible-Vault
 * Changed the hosts file extension to .yaml
 * Made it possible to use ansible.cfg within the roles in Ansible-LegacyRole
 * Added function that allows users to specify a virtualenv for each movement when connected with AnsibleTower
 * Added RestAPI for executing and checking operations


DSC-Driver
---------------
 * Added RestAPI for executing and checking operations.

OpenStack-Driver
---------------
 * Added RestAPI for executing and checking operations.