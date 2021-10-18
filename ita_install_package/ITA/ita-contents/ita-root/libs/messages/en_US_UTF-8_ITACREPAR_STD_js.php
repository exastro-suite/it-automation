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
////en_US_UTF-8_ITACREPAR_STD
$ary["ITACREPAR_1001"]       = "Menu is not created";
$ary["ITACREPAR_1101"]       = "Menu is not created";
$ary["ITACREPAR_1201"]       = "Do you want to create the  Menu ?\n*If a menu with the same menu name or a menu the same 'Menu definition information' ID already exists, the existing data will be deleted and overwritten by the new data.\\nIf you need to retain the existing data, select 'Cancel' and perform a backup.";
$ary["ITACREPAR_1202"]       = "Item";
$ary["ITACREPAR_1203"]       = "Group";
$ary["ITACREPAR_1204"]       = "String";
$ary["ITACREPAR_1205"]       = "Multi string";
$ary["ITACREPAR_1206"]       = "Integer";
$ary["ITACREPAR_1207"]       = "Decimal number";
$ary["ITACREPAR_1208"]       = "Date and time";
$ary["ITACREPAR_1209"]       = "Date";
$ary["ITACREPAR_1210"]       = "Select pulldown";
$ary["ITACREPAR_1211"]       = "Id";
$ary["ITACREPAR_1212"]       = "Auto-input";
$ary["ITACREPAR_1213"]       = "Maximum number of bytes";
$ary["ITACREPAR_1214"]       = "Regular expression";
$ary["ITACREPAR_1215"]       = "Minimum value";
$ary["ITACREPAR_1216"]       = "Maximum value";
$ary["ITACREPAR_1217"]       = "Digit number";
$ary["ITACREPAR_1218"]       = "Selection item";
$ary["ITACREPAR_1219"]       = "Required";
$ary["ITACREPAR_1220"]       = "Unique constraint";
$ary["ITACREPAR_1221"]       = "Explanation";
$ary["ITACREPAR_1222"]       = "Remark";
$ary["ITACREPAR_1223"]       = "Host name";
$ary["ITACREPAR_1224"]       = "Operation";
$ary["ITACREPAR_1225"]       = "Parameter";
$ary["ITACREPAR_1226"]       = "Operation name";
$ary["ITACREPAR_1227"]       = "Reference date and time";
$ary["ITACREPAR_1228"]       = "Scheduled date";
$ary["ITACREPAR_1229"]       = "Last run date";
$ary["ITACREPAR_1230"]       = "Remark";
$ary["ITACREPAR_1231"]       = "Last Modified";
$ary["ITACREPAR_1232"]       = "Last updated by";
$ary["ITACREPAR_1233"]       = "Operation";
$ary["ITACREPAR_1234"]       = "System Administrator";
$ary["ITACREPAR_1235"]       = "Target menu group";
$ary["ITACREPAR_1236"]       = "Menu creation was accepted.\nPlease click the Menu creation history button and check the creation status.\nId:";
$ary["ITACREPAR_1237"]       = "Password";
$ary["ITACREPAR_1238"]       = "Repeat item will be canceled.";
$ary["ITACREPAR_1239"]       = "Items containing repeats cannot be copied.";
$ary["ITACREPAR_1240"]       = "Input";
$ary["ITACREPAR_1241"]       = "Substitution<br>value";
$ary["ITACREPAR_1242"]       = "Reference";
$ary["ITACREPAR_1243"]       = "Menu group name";
$ary["ITACREPAR_1244"]       = "About vertical menu";
$ary["ITACREPAR_1245"]       = "Yes";
$ary["ITACREPAR_1246"]       = "Maximum number of<br>bytes in the file";
$ary["ITACREPAR_1247"]       = "File";
$ary["ITACREPAR_1248"]       = "Link";
$ary["ITACREPAR_1249"]       = "Do you want to perform an edit of the menu? \n*The data entered in the existing item will remain, but if you had deleted the existing item, the data entered in that item will be deleted. \nIf you change the \"Regular expression\" in an existing item, it may cause inconsistency with the existing data. \nAlso, if the newly added item was set as \"Required\" and \"Unique constraint\", empty data will be entered in the required field, which may cause data inconsistency. \nIf you need to modify the data, please select \"Cancel\".";
$ary["ITACREPAR_1250"]       = "Do you want to perform menu initialization? \n*The data you have already entered in this menu will be deleted. \nIf you need the entered data, please select \"Cancel\" to back up the data.";
$ary["ITACREPAR_1251"]       = "There are no items available for reference.";
$ary["ITACREPAR_1252"]       = "Reference item";
$ary["ITACREPAR_1253"]       = "[Referenced value]";
$ary["ITACREPAR_1254"]       = "Select reference item";
$ary["ITACREPAR_1255"]       = "Exchange ID has failed({})";
$ary["ITACREPAR_1256"]       = "Enter the item name to be displayed on the menu. \nThe maximum size is 256 bytes.\nDo not use \"/\" in the item names.\n\"Names [numbers] used in the repeat frame\" cannot be used for item names outside the repeat frame.";
$ary["ITACREPAR_1257"]       = "Move item.";
$ary["ITACREPAR_1258"]       = "Delete the item.";
$ary["ITACREPAR_1259"]       = "Copy the item.";
$ary["ITACREPAR_1260"]       = "Enter the number of repeats.\nInteger value from 2 to 99 can be entered.";
$ary["ITACREPAR_1261"]       = "Selecte \"String\", \"Multi string\", \"Integer\", \"Decimal number\", \"Date\", \"Date/time\",\n\"Pull down selection\", \"Password\", \"File upload\", \"Link\", \"Parameter Sheet Reference\" from the pulldown menu.";
$ary["ITACREPAR_1262"]       = "Enter the maximum number of bytes.\nThe maximum size is 8192 bytes.\nFor editing, it is possible to increase it from the original value.\nThe byte count of half-width alphanumeric characters are equivalent to the number of characters.\nFor full-width characters, the number of characters x 3 + 2 bytes is required.";
$ary["ITACREPAR_1263"]       = "If you want to check input values with regular expression, enter the regular expression.\nThe maximum size is 8192 bytes.\nExample: For half-width numeric items of 0 bytes or more: /^[0-9]*$/\n For half-width alphanumeric characters of 1 byte or more:/^[a-zA-Z0-9]+$/";
$ary["ITACREPAR_1264"]       = "Enter the minimum value of the column.\nFor editing, it is possible to reduce it from the original value.\nInteger value from -2147483648 to 2147483647 can be entered.\nThe value will be -2147483648 if not entered.\nPlease enter value smaller than the maximum value.";
$ary["ITACREPAR_1265"]       = "Enter the maximum value of the column.\nFor editing, it is possible to increase it from the original value.\nInteger value from -2147483648 to 2147483647 can be entered.\nThe value will be 2147483647 if not entered.\nPlease enter value larger than the minimum value.";
$ary["ITACREPAR_1266"]       = "Enter the minimum value of the column.\nFor editing, it is possible to reduce it from the original value.\nInteger value from -99999999999999 to 99999999999999 with total digit for whole\nnumber + fraction part less than 14 digits can be entered.\nThe value will be -99999999999999 if not entered.\nPlease enter value smaller than the maximum value.";
$ary["ITACREPAR_1267"]       = "Enter the maximum value of the column.\nFor editing, it is possible to increase it from the original value.\nInteger value from -99999999999999 to 99999999999999 with total digit for whole\nnumber + fraction part less than 14 digits can be entered.\nThe value will be 99999999999999 if not entered.\nPlease enter value larger than the minimum value.";
$ary["ITACREPAR_1268"]       = "Enter the upper limit of the total digit for whole number + fraction part.\nFor editing, it is possible to increase it from the original valu.\n\nExample: 0.123 has 4 digits (whole number 1 digit, fraction part 3 digits)\n 11.1111 has 6 digits (whole number 2 digit2, fraction part 3 digits)\nInteger value from 1 to 14 can be entered.";
$ary["ITACREPAR_1269"]       = "Select the item to be referenced in the pulldown menu from the pull-down menu.\n※Items that satisfy the following conditions are displayed in the pull-down menu .\nMenu: 「Basic Console: Device List」 and menu created with this function\nItem: String, required and unique constraint item.";
$ary["ITACREPAR_1270"]       = "Enter the maximum number of bytes for the file to upload.\nFor editing, it is possible to increase it from the original value.\nThe maximum size is 4294967296 bytes.";
$ary["ITACREPAR_1271"]       = "To make it a required item, check the check box.";
$ary["ITACREPAR_1272"]       = "To make it a unique item, check the check box.";
$ary["ITACREPAR_1273"]       = "Enter the description that will be displayed when users hover mouse cursor over the item name.\nThe maximum size is 1024 bytes.";
$ary["ITACREPAR_1274"]       = "Enter the remarks column.\nThe maximum size is 8192 bytes.";
$ary["ITACREPAR_1275"]       = "You can refer to other items based on the menu item you selected in \"Pulldown Selection\".";
$ary["ITACREPAR_1276"]       = "Create";
$ary["ITACREPAR_1277"]       = "Not created";
$ary["ITACREPAR_1278"]       = "Created";
$ary["ITACREPAR_1279"]       = "Failed to get the reference item.";
$ary["ITACREPAR_1280"]       = "Unique constraint(Multiple items)";
$ary["ITACREPAR_1281"]       = "No items.";
$ary["ITACREPAR_1282"]       = "Delete";
$ary["ITACREPAR_1283"]       = "Add a pattern";
$ary["ITACREPAR_1284"]       = "No patterns.";
$ary["ITACREPAR_1285"]       = "Permission role";
$ary["ITACREPAR_1286"]       = "Reference Item";
$ary["ITACREPAR_1287"]       = "Default value";
$ary["ITACREPAR_1288"]       = "Failed to get the Default value";
$ary["ITACREPAR_1289"]       = "When registering from the created menu, set the value to be entered in the input field by default.\nYou cannot set a value that exceeds the \"Maximum number of bytes\" or a value that does not match the \"Regular expression\".";
$ary["ITACREPAR_1290"]       = "When registering from the created menu, set the value to be entered in the input field by default.\nYou cannot set a Values outside the range of \"Maximum value\" and \"Minimum value\".";
$ary["ITACREPAR_1291"]       = "When registering from the created menu, set the value to be entered in the input field by default.\nYou cannot set a Values outside the range of \"Maximum value\" and \"Minimum value\" and \"Digit number\".";
$ary["ITACREPAR_1292"]       = "When registering from the created menu, set the value to be entered in the input field by default.";
$ary["ITACREPAR_1293"]       = "When registering from the created menu, set the value to be entered in the input field by default.\nYou cannot set a value that exceeds the \"Maximum number of bytes\".";
$ary["ITACREPAR_1294"]       = "When registering from the created menu, Set the value selected by default.";
$ary["ITACREPAR_1295"]       = "Please Wait... Loading";
$ary["ITACREPAR_1296"]       = "Menu";
$ary["ITACREPAR_1297"]       = "Item";
$ary["ITACREPAR_1298"]       = "[Referenced value]";
$ary["ITACREPAR_1299"]       = "Select the item to be referenced from the menu items created in the Creation target \"Parameter Sheet(Operation)\".\nRefers to the value of the same operation from the selected items.";
$ary["ITACREPAR_1300"]       = "Failed to get the Item";
?>