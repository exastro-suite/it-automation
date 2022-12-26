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
////en_US_UTF-8_ITATERRAFORMCLI_ERR
$ary["ITATERRAFORMCLI-ERR-1"]          = "WARNING:ILLEGAL_ACCESS(Insufficient Privilege).";
$ary["ITATERRAFORMCLI-ERR-101"]        = "RESULT:UNEXPECTED_ERROR(QUERY_NOT_FOUND";
$ary["ITATERRAFORMCLI-ERR-401"]        = "WARNING:NO_QUERY_EXIST[{}]";
$ary["ITATERRAFORMCLI-ERR-402"]        = "ERROR:QUERY_IS_NOT_INTEGER[{}]";
$ary["ITATERRAFORMCLI-ERR-404"]        = "ERROR:UNEXPECTED_ERROR([FILE]{}[LINE]{}[ETC-Code]{})";
$ary["ITATERRAFORMCLI-ERR-501"]        = "{} Parse Error";
$ary["ITATERRAFORMCLI-ERR-502"]        = "{} Execute Error";
$ary["ITATERRAFORMCLI-ERR-503"]        = "{} Select Error";
$ary["ITATERRAFORMCLI-ERR-504"]        = "QUERY_NOT_FOUND(execution_No.)";
$ary["ITATERRAFORMCLI-ERR-505"]        = "QUERY_IS_NOT_INTEGER(execution_No.)";
$ary["ITATERRAFORMCLI-ERR-506"]        = "QUERY_NOT_FOUND(prg_recorder)";
$ary["ITATERRAFORMCLI-ERR-507"]        = "QUERY_IS_NOT_INTEGER(prg_recorder)";
$ary["ITATERRAFORMCLI-ERR-510"]        = "prg_recorder Select Error";
$ary["ITATERRAFORMCLI-ERR-511"]        = "";

$ary["ITATERRAFORMCLI-ERR-101010"]     = "Error occurred ([FILE]{}[LINE]{}[ETC-Code]{})";

$ary["ITATERRAFORMCLI-ERR-200010"]     = "Cannot find Target Execution No. \nRecord may have been discarded.";
$ary["ITATERRAFORMCLI-ERR-200020"]     = "The status of target operation in process cannot be emergency stop. ({})";
$ary["ITATERRAFORMCLI-ERR-200030"]     = "Failed to create emergency stop flag file.";

$ary["ITATERRAFORMCLI-ERR-201010"]     = "The file extension is invalid.";

$ary["ITATERRAFORMCLI-ERR-202010"]     = "Item is not registered in the associated menu.";
$ary["ITATERRAFORMCLI-ERR-202020"]     = "Menu is not registered in the associated menu.";
$ary["ITATERRAFORMCLI-ERR-202030"]     = "Registration method is not selected.";
$ary["ITATERRAFORMCLI-ERR-202040"]     = "Registration method is out of range";
$ary["ITATERRAFORMCLI-ERR-202050"]     = "variable is non-registration in Module File registered with the Movement module link.";
$ary["ITATERRAFORMCLI-ERR-202060"]     = "The Movement of the Key variable and Val variable do not match.";
$ary["ITATERRAFORMCLI-ERR-202070"]     = "Menu group:Menu:Item is not selected.";
$ary["ITATERRAFORMCLI-ERR-202080"]     = "Movement is not selected.";
$ary["ITATERRAFORMCLI-ERR-202090"]     = "Registration method: \"String {} type\" can not be set when \"{} variable information\" is set.";
$ary["ITATERRAFORMCLI-ERR-202100"]     = "Movement is not registered.";
$ary["ITATERRAFORMCLI-ERR-202110"]     = "Variable (Key) is not selected.";
$ary["ITATERRAFORMCLI-ERR-202120"]     = "Variable (Key) is not registered.";
$ary["ITATERRAFORMCLI-ERR-202130"]     = "Variable (Value) is not selected. ";
$ary["ITATERRAFORMCLI-ERR-202140"]     = "Variable (Value) is not registered.";
$ary["ITATERRAFORMCLI-ERR-202150"]     = "Following items are duplicated with the records of [Item No.]:({}). n [(Movement),(Variable),(Member variable), (Substitution order)]";
$ary["ITATERRAFORMCLI-ERR-202160"]     = "You cannot enter same variables in the Key variable and Value variable.";
$ary["ITATERRAFORMCLI-ERR-202170"]     = "The combination of [Movement, variable name] is invalid.";
$ary["ITATERRAFORMCLI-ERR-202180"]     = "Registration method:When (Key type) is set, the HCL setting of the Value variable cannot be changed to ON.";
$ary["ITATERRAFORMCLI-ERR-202190"]     = "When the HCL setting is ON, [member variable, substitution order] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-202200"]     = "If [movement, variable name] matches, make sure that the HCL settings are the same as the others (ON or OFF).";
$ary["ITATERRAFORMCLI-ERR-202210"]     = "Following items are duplicated with the records of [Item No.]:({}). n [(Movement),(Variable),(HCL settings)]";
$ary["ITATERRAFORMCLI-ERR-202220"]     = "[member variable, substitution order] cannot be entered for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202230"]     = "If the variable type is map type, it cannot be registered in the Key variable.";
$ary["ITATERRAFORMCLI-ERR-202240"]     = "Member Variables are required for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202250"]     = "You cannot enter the substitution order for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202260"]     = "You cannot enter member variables in the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202270"]     = "Substitution order is required for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202280"]     = "Member Variables are required for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202290"]     = "Substitution order is required for the member variables of the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202300"]     = "[member variable, substitution order] is required for the selected Key variable.";
$ary["ITATERRAFORMCLI-ERR-202310"]     = "[member variable, substitution order] cannot be entered for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202320"]     = "If the variable type is map type, set the HCL setting to ON.";
$ary["ITATERRAFORMCLI-ERR-202330"]     = "Member Variable are required for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202340"]     = "You cannot enter the substitution order for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202350"]     = "You cannot enter a member variable for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202360"]     = "Substitution order is required for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202370"]     = "Member  are required for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202380"]     = "Substitution order is required for the member variables of the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202390"]     = "[member variables, substitution order] is required for the selected Value variable.";
$ary["ITATERRAFORMCLI-ERR-202400"]     = "The combination of [variable name,Member variable name] is invalid.";
$ary["ITATERRAFORMCLI-ERR-202410"]     = "The combination of [variable name,Member variable name] is invalid.";

$ary["ITATERRAFORMCLI-ERR-203010"]     = "Variable is not registered in Module registered in Movement module link.";
$ary["ITATERRAFORMCLI-ERR-203020"]     = "Movement is not selected.";
$ary["ITATERRAFORMCLI-ERR-203030"]     = "No variables have been selected.";
$ary["ITATERRAFORMCLI-ERR-203040"]     = "Movement is not registered.";
$ary["ITATERRAFORMCLI-ERR-203050"]     = "The combination of [operation, Movement, variable name] is invalid.";
$ary["ITATERRAFORMCLI-ERR-203060"]     = "The combination of [variable name,Member variable name] is invalid.";
$ary["ITATERRAFORMCLI-ERR-203070"]     = "If the HCL setting is ON, you do not need to enter [member variable, substitution order].";
$ary["ITATERRAFORMCLI-ERR-203080"]     = "If [operation, movement, variable name] match, make sure that the HCL settings are the same as the others (ON or OFF).";
$ary["ITATERRAFORMCLI-ERR-203090"]     = "If [operation, movement, variable name] match, make sure that the HCL settings are the same as the others (ON or OFF).";
$ary["ITATERRAFORMCLI-ERR-203100"]     = "If [operation, movement, variable name] match, make sure that the Sensitive settings are the same as the others (ON or OFF).";
$ary["ITATERRAFORMCLI-ERR-203110"]     = "If [operation, movement, variable name] match, make sure that the Sensitive settings are the same as the others (ON or OFF).";
$ary["ITATERRAFORMCLI-ERR-203120"]     = "[member variable, substitution order] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-203130"]     = "[substitution order] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-203140"]     = "[member Variable] is required.";
$ary["ITATERRAFORMCLI-ERR-203150"]     = "[substitution order] is required.";
$ary["ITATERRAFORMCLI-ERR-203160"]     = "[member variable] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-203170"]     = "[member variable, substitution order] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-203180"]     = "[substitution order] is required.";
$ary["ITATERRAFORMCLI-ERR-203190"]     = "[substitution order] cannot be entered.";
$ary["ITATERRAFORMCLI-ERR-203200"]     = "[substitution order] is required.";
$ary["ITATERRAFORMCLI-ERR-203210"]     = "[member Variable] is required.";
$ary["ITATERRAFORMCLI-ERR-203220"]     = "If the variable type is map type, set the HCL setting to ON.";

$ary["ITATERRAFORMCLI-ERR-204010"]     = "Parameter check error";
$ary["ITATERRAFORMCLI-ERR-204020"]     = "Cannot find Target Execution No. \nRecord may have been discarded.";
$ary["ITATERRAFORMCLI-ERR-204030"]     = "Schedule cancellation is not possible for the status of target operation in process. ({})";

$ary["ITATERRAFORMCLI-ERR-205010"]     = "Failed to register the task for deleting the resource.(FILE:{} LINE:{} StatusCode:{})";
$ary["ITATERRAFORMCLI-ERR-205020"]     = "The value of the item is invalid.(FILE:{} LINE:{} Target item:{})";

$ary["ITATERRAFORMCLI-ERR-206010"]     = "An error occurred in ending the transaction";
$ary["ITATERRAFORMCLI-ERR-206020"]     = "Rollback has failed.";
$ary["ITATERRAFORMCLI-ERR-206030"]     = "End procedure (error)";
$ary["ITATERRAFORMCLI-ERR-206040"]     = "End procedure (warning)";
$ary["ITATERRAFORMCLI-ERR-206050"]     = "Module file is not registered. Processing will be skipped. (Module:{})";
$ary["ITATERRAFORMCLI-ERR-206060"]     = "Exception occurred.";
$ary["ITATERRAFORMCLI-ERR-206070"]     = "The Module file managed by the system does not exist. (ModuleID:{} File name:{})";
$ary["ITATERRAFORMCLI-ERR-206080"]     = "DB access error occurred. (file:{}line:{})";
$ary["ITATERRAFORMCLI-ERR-206090"]     = "Failed to check whether related database changes.";
$ary["ITATERRAFORMCLI-ERR-206100"]     = "Failed to register completion of reflection of update of related database.";
$ary["ITATERRAFORMCLI-ERR-206110"]     = "Registration for backyard processing (valautostup-workflow) activation failed.";
$ary["ITATERRAFORMCLI-ERR-206120"]     = "Failed to register the repetition count. (FILE:{} LINE:{})";
$ary["ITATERRAFORMCLI-ERR-206130"]     = "Failed to update the repetition count. (FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206140"]     = "Failed to discard the repetition count. (FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206150"]     = "Failed to register the member variable.(FILE:{} LINE:{})";
$ary["ITATERRAFORMCLI-ERR-206160"]     = "Failed to update the member variable.(FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206170"]     = "Failed to discard  the member variable.(FILE:{} LINE:{} ID:{})";

$ary["ITATERRAFORMCLI-ERR-207010"]     = "DB access error occurred. (file:{}line:{})";
$ary["ITATERRAFORMCLI-ERR-207020"]     = "Failed to check whether related database changes.";
$ary["ITATERRAFORMCLI-ERR-207030"]     = "Failed to register completion of reflection of update of related database.";
$ary["ITATERRAFORMCLI-ERR-207040"]     = "Registration for backyard processing (varsautolistup-workflow) activation failed.";
$ary["ITATERRAFORMCLI-ERR-207050"]     = "Get the variable information for each column from the Substitution value auto-registration setting has failed.";
$ary["ITATERRAFORMCLI-ERR-207060"]     = "Reading of substitution value list failed.";
$ary["ITATERRAFORMCLI-ERR-207070"]     = "Register the specific value of variable for substitution value list has failed.";
$ary["ITATERRAFORMCLI-ERR-207080"]     = "Delete the unnecessary data from substitution value list has failed.";
$ary["ITATERRAFORMCLI-ERR-207090"]     = "Associated menu registered in the Substitution value auto-registration setting is discarded. This record will be out of scope of processing. (Substitution value auto-registration setting Item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207100"]     = "Item information of associated menu registered in the Substitution value auto-registration setting is discarded. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207110"]     = "Movement registered in the substitution value auto-registration setting is not registered in the Movement module link. This record will be out of scope of processing. (Substitution value auto-registration setting Item No:{})";
$ary["ITATERRAFORMCLI-ERR-207120"]     = "Could not get the associated menu registered in the Substitution value auto-registration setting. This record will be out of scope of processing. (Substitution value auto-registration setting Item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207130"]     = "Could not get the primary key name of the associated menu with the Substitution value auto-registration setting. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207140"]     = "Could not get the item information of associated menu registered in the Substitution value auto-registration setting. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.o:{})";
$ary["ITATERRAFORMCLI-ERR-207150"]     = "Could not get the item name of associated menu registered in the Substitution value auto-registration setting. This record will be out of scope of processing. (Substitution value auto-registration setting Item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207160"]     = "Registration method, which is registered in the Substitution value auto-registration setting, is set with an out of range value. This record will be out of scope of processing. (Substitution value auto-registration setting Item No:{} Registration method :{})";
$ary["ITATERRAFORMCLI-ERR-207170"]     = "Variables are not set in the Substitution value auto-registration setting. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.:{} Variable classification:{})";
$ary["ITATERRAFORMCLI-ERR-207180"]     = "The combination of Movement and variables registered in the Substitution value auto-registration setting is not used in the Playbook or Template file associated with Movement module link, because Movement is not associated with Movement module link. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.:{} Variable classification:{})";
$ary["ITATERRAFORMCLI-ERR-207190"]     = "Variables registered in the Substitution value auto-registration setting are not used in the playbook or template registered in playbook files or template list. This record will be out of scope of processing. (Substitution value auto-registration setting: Item No.:{} Variable classification:{})";
$ary["ITATERRAFORMCLI-ERR-207200"]     = "This associated menu has no column information. Associated menu is out of scope of processing. (MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207210"]     = "Get information of associated menu. Associated menu is out of scope of processing has failed. (MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207220"]     = "Data is not registered in the associated menu. (MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207230"]     = "Operation ID column is not set in the associated menu. This record will be out of scope of processing. (MENU_ID:{} Associated menu item No.:{})";
$ary["ITATERRAFORMCLI-ERR-207240"]     = "Operation and host of Item No:{} and Item No:{} of the Substitution value auto-registration setting duplicate.Item No:{} of Substitution value auto-registration setting will be out of scope of processing. (Operation ID:{} Host ID:{} Variable classification: {})";
$ary["ITATERRAFORMCLI-ERR-207250"]     = "Specific value of associated menu is not set. This record will be out of scope of processing. (MENU_ID:{} Associated menu item No.:{} Item name:{})";
$ary["ITATERRAFORMCLI-ERR-207260"]     = "Specific value of associated menu is blank. (MENU_ID:{} Associated menu item No.:{} Item name:{})";
$ary["ITATERRAFORMCLI-ERR-207270"]     = "There are no records for Terraform interface information";
$ary["ITATERRAFORMCLI-ERR-207280"]     = "Start transaction has failed.";
$ary["ITATERRAFORMCLI-ERR-207290"]     = "Lock sequence has failed.";
$ary["ITATERRAFORMCLI-ERR-207300"]     = "Commit transaction has failed.";
$ary["ITATERRAFORMCLI-ERR-207310"]     = "An exception occurred.";
$ary["ITATERRAFORMCLI-ERR-207320"]     = "Rollback has failed.";
$ary["ITATERRAFORMCLI-ERR-207330"]     = "An error occurred at the time of ending the transaction.";
$ary["ITATERRAFORMCLI-ERR-207340"]     = "Failed to get the permission role of master data.";
$ary["ITATERRAFORMCLI-ERR-207350"]     = "Since the access permission roles of the operation list and movement list do not match, Skip processing this data.  (menu:{} column: {} operation: {}({}) movement: {}({}))";

?>