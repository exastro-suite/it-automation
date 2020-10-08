<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
////en_US_UTF-8_ITACREPAR_MNU
$ary[102001] = "Maintenance (view/register/update/discard) can be performed on the menu name of the parameter sheet menu or data sheet menu to be created.<br>
You cannot use \"Main menu\" as the menu name.";
$ary[102002] = "Id";
$ary[102003] = "Menu definition information";
$ary[102004] = "Menu definition information";
$ary[102005] = "Menu name";
$ary[102006] = "Enter the name of the menu to be created.";
$ary[102007] = "Display order";
$ary[102008] = "Enter the display order of the menu group. It is displayed in ascending order.";
$ary[102009] = "Use";
$ary[102010] = "Enter the use of the menu to be created.";
$ary[102011] = "Menu group for Input";
$ary[102012] = "Enter the menu group on which to create the input menu.";
$ary[102013] = "Menu group for Substitution value auto-registration";
$ary[102014] = "Enter the menu group on which to create the substitution value auto-registration menu.";
$ary[102015] = "Menu group for View";
$ary[102016] = "Enter the menu group on which to create the view menu.";
$ary[102017] = "Description";
$ary[102018] = "Enter the details to display in the description column of the menu screen.";
$ary[102019] = "Vertical";
$ary[102020] = "Enter when creating a vertical menu";
$ary[102023] = "Creation target";
$ary[102026] = "If you select \"Parameter Sheet\", ITA will create a parameter sheet menu that is set in the substitution value automatic registration setting.
If you select \"Data Sheet\", ITA will create a data sheet menu that is not set in the substitution value automatic registration setting.";
$ary[102101] = "Maintenance (view/register/update/discard) can be performed on items managed in the menu.";
$ary[102102] = "Id";
$ary[102103] = "Menu item creation information";
$ary[102104] = "Menu item creation information";
$ary[102105] = "Menu name";
$ary[102106] = "Select the menu to link the parameter from the pulldown menu.";
$ary[102107] = "Item name";
$ary[102108] = "Enter the item name you want display on the menu.";
$ary[102109] = "Maximum number of bytes";
$ary[102110] = "Enter the maximum number of bytes.
Total number of bytes (8192).
For single byte alphanumeric characters, the required bytes will be equal to the number of characters.
For double byte characters, the number of characters multiplied by 3, plus 2 bytes will be required.";
$ary[102111] = "Display order";
$ary[102112] = "Enter the order of the columns to be displayed in the menu. Columns will be displayed in ascending order from left to right.";
$ary[102113] = "Required";
$ary[102114] = "For required items, set \"●\".";
$ary[102115] = "Regular expression";
$ary[102116] = "Enter when you want to restrict the input using regular expressions.\nExample:\nHalf-width numbers only (value not required): / ^ [0-9] * $ /\nHalf size alphanumeric characters only (value required): / ^ [a - z A - Z 0 - 9] + $ /\"";
$ary[102117] = "Description";
$ary[102118] = "Enter a description to be displayed when mousing over the item name.";
$ary[102119] = "Unique";
$ary[102120] = "For unique items, set \"●\".";
$ary[102121] = "Input method";
$ary[102122] = "Select between \"String\",\"Multi string\",\"Interger\",\"Decimal number\",\"Date/time\", \"Date\", or \"Pulldown selection\"";
$ary[102123] = "Menu group: Menu: Item";
$ary[102124] = "Select the item to be referenced.";
$ary[102125] = "String";
$ary[102126] = "Pulldown selection";
$ary[102127] = "Column group";
$ary[102128] = "Select the column group to belong to.";
$ary[102129] = "Integer";
$ary[102130] = "Maximum value";
$ary[102131] = "Enter the maximum value of Integer column. 2147483647 will be set if not entered.";
$ary[102132] = "Minimum value";
$ary[102133] = "Enter the minimum value of Integer column. -2147483648 will be set if not entered.";
$ary[102134] = "Decimal number";
$ary[102135] = "Maximum value";
$ary[102136] = "Enter the maximum value of Decimal number column. 99999999999999 will be set if not entered.";
$ary[102137] = "Minimum value";
$ary[102138] = "Enter the minimum value of Decimal number column. -99999999999999 will be set if not entered.";
$ary[102139] = "Digits";
$ary[102140] = "Enter the maximum total digit of whole number part and fraction part for Decimal number column. 14 will be set if not entered.";
$ary[102141] = "Multi string";
$ary[102201] = "Display the link between the created menu and the DB table.";
$ary[102202] = "Id";
$ary[102203] = "Menu /Table link list";
$ary[102204] = "Menu /Table link list";
$ary[102205] = "Menu group:Menu";
$ary[102206] = "The name of the created menu.";
$ary[102207] = "Table name";
$ary[102208] = "The name of the created table.";
$ary[102209] = "Primary key";
$ary[102210] = "The primary key of the created table.";
$ary[102211] = "Table name （history)";
$ary[102212] = "The name of the created history table.";
$ary[102401] = "The status of Menu creation can be viewed.";
$ary[102402] = "Id";
$ary[102403] = "Menu creation history";
$ary[102404] = "Menu creation history";
$ary[102405] = "Menu name";
$ary[102406] = "Name of the menu to be created.";
$ary[102407] = "Status";
$ary[102408] = "Menu creation status:
Not executed: The menu has not yet been created
Executing: BackYard is in the process of creating the menu
Completed: The menu was created successfully
Completed (error): Menu creation ended in error";
$ary[102409] = "Menu file";
$ary[102410] = "PHP file and SQL file used in the menu.";
$ary[102411] = "Created menu";
$ary[102412] = "Created menu";
$ary[102501] = "A menu can be created in the menu group specified in \"Menu creation information\".<br/>* If a menu with the same menu name or menu with the same \"Menu definition information\" ID already exists, the existing data will be deleted and overwritten by the new menu.<br/>If you need to retain the existing data, select \"Cancel\" and perform a backup.";
$ary[102502] = "Create Menu";
$ary[102503] = "Start Menu creation";
$ary[102504] = "There is no target to create.";
$ary[102505] = "Check all";
$ary[102506] = "Menu creation was accepted.";
$ary[102507] = "Id";
$ary[102508] = "Menu creation history";
$ary[102509] = "Select the Menu that you want to create.";
$ary[102510] = "Do you want to create the  Menu ?\\n\\n*If a menu with the same menu name or a menu the same 'Menu definition information' ID already exists, the existing data will be deleted and overwritten by the new data.\\nIf you need to retain the existing data, select 'Cancel' and perform a backup.";
$ary[102601] = "Host name";
$ary[102602] = "Host group name";
$ary[102603] = "Operation";
$ary[102604] = "Scheduled date for execution";
$ary[102605] = "[Original data] Basic console/Input operation list";
$ary[102606] = "ID";
$ary[102607] = "[Original data] Basic console/Input operation list";
$ary[102608] = "Operation name";
$ary[102609] = "[Original data] Basic console/Input operation list";
$ary[102610] = "Operation";
$ary[102611] = "The combined value of \"Scheduled date for execution\", \"Operation ID\" and \"Operation name\" in the \"Basic console/input operation list\"";
$ary[102612] = "Parameter";
$ary[102613] = "Input order";
$ary[102614] = "When converting from the vertical menu to the horizontal menu, items will be arranged in ascending order from left to right.";
$ary[102615] = "Reference date";
$ary[102616] = "If \"Last execute date\" of \"Basic console / input operation list\" has a value, \"Last execution date\" is displayed, otherwise \"Scheduled date for execution\" is displayed.";
$ary[102617] = "Last execution date";
$ary[102618] = "[Original data] Basic console/Input operation list";
$ary[102701] = "Menu for creating items to be selected for \"Reference other menu\" in the \"Menu  item creation information\" menu.<br/>
Automatically created.";
$ary[102702] = "Id";
$ary[102703] = "Reference other menu";
$ary[102704] = "Reference other menu";
$ary[102705] = "Menu group";
$ary[102706] = "";
$ary[102707] = "Menu";
$ary[102708] = "";
$ary[102709] = "Item";
$ary[102710] = "";
$ary[102711] = "Menu group: Menu: Item";
$ary[102712] = "";
$ary[102713] = "Table name";
$ary[102714] = "";
$ary[102715] = "Primary key";
$ary[102716] = "";
$ary[102717] = "Column name";
$ary[102718] = "";
$ary[102719] = "Menu group: Menu";
$ary[102720] = "";
$ary[103601] = "Maintenance (view/register/update/discard) can be performed on the column group of the menu to be created.";
$ary[103602] = "Id";
$ary[103603] = "Column group list";
$ary[103604] = "Column group list";
$ary[103605] = "Parent column group";
$ary[103606] = "Select the parent column group from the pulldown menu.";
$ary[103607] = "Column group Name";
$ary[103608] = "Enter the column group name.";
$ary[103609] = "Column group";
$ary[103610] = "The combined value of the parent column group and column group name is displayed.";
$ary[104001] = "Maintenance (view/register/update/discard) can be performed on the menu information that manages the repeated items of the menu vertically.";
$ary[104002] = "Id";
$ary[104003] = "Vertical Menu creation information";
$ary[104004] = "Vertical Menu creation information";
$ary[104005] = "Menu name: Start item name";
$ary[104006] = "Specify the menu that will be the basis of the vertical menu to be created, and the item name of the start of the repeat item.";
$ary[104007] = "Number of items";
$ary[104008] = "Enter the number of items.";
$ary[104009] = "Repetition count";
$ary[104010] = "Enter the repetition count.";
$ary[104101] = "Information from the conversion from vertical menu to horizontal menu can be viewed.";
$ary[104102] = "Id";
$ary[104103] = "Menu horizontal and vertical conversion management";
$ary[104104] = "Menu horizontal and vertical conversion management";
$ary[104105] = "Conversion source menu name";
$ary[104106] = "Display the conversion source Menu group: menu.";
$ary[104107] = "Conversion destination menu name";
$ary[104108] = "Display the conversion destination Menu group: menu.";
$ary[104109] = "Purpose";
$ary[104110] = "Enter the purpose of the menu to be created.";
$ary[104111] = "Repeat starting column name";
$ary[104112] = "Display the horizontal menu repeat starting column name.";
$ary[104113] = "Number of items";
$ary[104114] = "Specify the number of items.";
$ary[104115] = "Repetition count";
$ary[104116] = "Specify the repetition count.";
$ary[104117] = "Column/row conversion completed flag";
$ary[104118] = "Specify the column/row conversion completed flag";
$ary[104201] = "Item";
$ary[104202] = "Group";
$ary[104203] = "Repeat";
$ary[104204] = "Cancel";
$ary[104205] = "Redo";
$ary[104206] = "Preview";
$ary[104207] = "Log";
$ary[104208] = "List(Preview)";
$ary[104209] = "Menu creation information";
$ary[104210] = "Basic information";
$ary[104211] = "Id：";
$ary[104212] = "Menu name";
$ary[104213] = "Creation target";
$ary[104214] = "Display order";
$ary[104215] = "Use";
$ary[104216] = "Last modified：";
$ary[104217] = "Last updated by：";
$ary[104218] = "Target menu group";
$ary[104219] = "Host";
$ary[104220] = "Host group";
$ary[104221] = "Reference";
$ary[104222] = "Vertical：";
$ary[104223] = "Data sheet";
$ary[104224] = "Explanation";
$ary[104225] = "Remarks";
$ary[104226] = "Create";
$ary[104227] = "Edit";
$ary[104228] = "Diversion new";
$ary[104229] = "Menu creation history";
$ary[104230] = "Reload";
$ary[104231] = "Cancel";
$ary[104232] = "Menu definition/creation";
$ary[104233] = "Auto-input";
$ary[104234] = "Select menu group";
$ary[104235] = "Required item";
$ary[104236] = "Password";
$ary[104237] = "Create as hostgroup menu";
$ary[104238] = "Create as vertical menu";
$ary[104239] = "Input";
$ary[104240] = "Substitution value";
$ary[104241] = "Reference";
$ary[104242] = "Yes";
?>