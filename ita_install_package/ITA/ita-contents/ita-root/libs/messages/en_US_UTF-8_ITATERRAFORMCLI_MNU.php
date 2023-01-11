<?php
//   Copyright 2022 NEC Corporation
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
////en_US_UTF-8_ITATERRAFORMCLI_MNU
$ary["ITATERRAFORMCLI-MNU-101010"]     = "You can perform maintenance (View/Update) for connection interface information<br>This menu should be one record.";
$ary["ITATERRAFORMCLI-MNU-101020"]     = "No.";
$ary["ITATERRAFORMCLI-MNU-101030"]     = "Terraform_Interface information";
$ary["ITATERRAFORMCLI-MNU-101040"]     = "Terraform_Interface information";
$ary["ITATERRAFORMCLI-MNU-101050"]     = "Proxy";
$ary["ITATERRAFORMCLI-MNU-101060"]     = "Address";
$ary["ITATERRAFORMCLI-MNU-101070"]     = "Proxy server address";
$ary["ITATERRAFORMCLI-MNU-101080"]     = "Port";
$ary["ITATERRAFORMCLI-MNU-101090"]     = "Proxy server port";
$ary["ITATERRAFORMCLI-MNU-101100"]     = "Status monitoring cycle (milliseconds)";
$ary["ITATERRAFORMCLI-MNU-101110"]     = "Interval to refresh execution log when executing operation. Although tuning is required depending on the environment, normally the recommend value is 3000 milliseconds.";
$ary["ITATERRAFORMCLI-MNU-101120"]     = "Number of rows to display progress status";
$ary["ITATERRAFORMCLI-MNU-101130"]     = "The number of lines that will be output to the [Progress status] log in the [Check operation status] menu when executing an operation.\nThe log will only output the specified number of lines if the status displays [Not yet executed], [Preparing], [Executing] or [Executing (Delayed)].\nIf the status displays [Completed], [Completed(error)], [Unexpected error], [Emergency stop], [Not yet executed(reserved)] or [Reservation deleted], the log will ignore the set number and output the whole log.\nWhile this number should be tuned depending on the environment, the recommended value is 1000 lines.";
$ary["ITATERRAFORMCLI-MNU-101140"]     = "NULL link";
$ary["ITATERRAFORMCLI-MNU-101150"]     = "If specific value of parameter sheet is NULL in Substitut val-auto-reg, validates registration in Substitution value list or set default.\nvalid: Register NULL data.\nInvalid: Do not register null data.";
$ary["ITATERRAFORMCLI-MNU-101160"]     = "Number of parallel executions";
$ary["ITATERRAFORMCLI-MNU-101170"]     = "The maximum number of Movements (Terraform-CLI) that can be executed simultaneously.";

$ary["ITATERRAFORMCLI-MNU-102010"]     = "You can perform maintenance (View/Register/Update/Discard) for Workspaces information.";
$ary["ITATERRAFORMCLI-MNU-102020"]     = "Workspace ID";
$ary["ITATERRAFORMCLI-MNU-102030"]     = "Terraform_Workspaces list";
$ary["ITATERRAFORMCLI-MNU-102040"]     = "Terraform_Workspaces list";
$ary["ITATERRAFORMCLI-MNU-102050"]     = "Workspace Name";
$ary["ITATERRAFORMCLI-MNU-102060"]     = "Workspace Name. Alphanumeric characters and valid symbols(_-).[Maximum length] 90 bytes";
$ary["ITATERRAFORMCLI-MNU-102070"]     = "Alphanumeric characters and valid symbols(_-)";
$ary["ITATERRAFORMCLI-MNU-102080"]     = "Movement list";
$ary["ITATERRAFORMCLI-MNU-102090"]     = "Delete resource";
$ary["ITATERRAFORMCLI-MNU-102100"]     = "Execution";
$ary["ITATERRAFORMCLI-MNU-102110"]     = "Deletes resources constructed and managed by the Workspace.";

$ary["ITATERRAFORMCLI-MNU-103010"]     = "You can perform maintenance (View/Register/Update/Discard) for Terraform-CLI Movement. ";
$ary["ITATERRAFORMCLI-MNU-103020"]     = "Movement ID";
$ary["ITATERRAFORMCLI-MNU-103030"]     = "Terraform-CLI_Movement list";
$ary["ITATERRAFORMCLI-MNU-103040"]     = "Terraform-CLI_Movement list";
$ary["ITATERRAFORMCLI-MNU-103050"]     = "Movement Name";
$ary["ITATERRAFORMCLI-MNU-103060"]     = "[Maximum length] 256 bytes";
$ary["ITATERRAFORMCLI-MNU-103070"]     = "Orchestrator";
$ary["ITATERRAFORMCLI-MNU-103080"]     = "The orchestrator used is displayed.";
$ary["ITATERRAFORMCLI-MNU-103090"]     = "Delay timer";
$ary["ITATERRAFORMCLI-MNU-103100"]     = "A delay status will appear if the movement is delayed more than the specified time (minutes)";
$ary["ITATERRAFORMCLI-MNU-103110"]     = "Terraform integration";
$ary["ITATERRAFORMCLI-MNU-103120"]     = "Workspace";
$ary["ITATERRAFORMCLI-MNU-103130"]     = "The target Workspace.";
$ary["ITATERRAFORMCLI-MNU-103140"]     = "Movement-Module link";
$ary["ITATERRAFORMCLI-MNU-103150"]     = "Select";

$ary["ITATERRAFORMCLI-MNU-104010"]     = "You can perform maintenance (View/Register/Update/Discard) for Terraform Module.";
$ary["ITATERRAFORMCLI-MNU-104020"]     = "Module file ID";
$ary["ITATERRAFORMCLI-MNU-104030"]     = "Terraform-CLI_Module files";
$ary["ITATERRAFORMCLI-MNU-104040"]     = "Terraform-CLI_Module files";
$ary["ITATERRAFORMCLI-MNU-104050"]     = "Module file name";
$ary["ITATERRAFORMCLI-MNU-104060"]     = "[Maximum length] 256 bytes";
$ary["ITATERRAFORMCLI-MNU-104070"]     = "Module file";
$ary["ITATERRAFORMCLI-MNU-104080"]     = "[Maximum size] 4GB";
$ary["ITATERRAFORMCLI-MNU-104090"]     = "Movement-Module link";

$ary["ITATERRAFORMCLI-MNU-105010"]     = "You can perform maintenance (View/Register/Update/Discard) for Module to be included for Movement.";
$ary["ITATERRAFORMCLI-MNU-105020"]     = "Associated item number";
$ary["ITATERRAFORMCLI-MNU-105030"]     = "Terraform_Movement module link";
$ary["ITATERRAFORMCLI-MNU-105040"]     = "Terraform_Movement module link";
$ary["ITATERRAFORMCLI-MNU-105050"]     = "Movement";
$ary["ITATERRAFORMCLI-MNU-105060"]     = "[Original data]Movement list";
$ary["ITATERRAFORMCLI-MNU-105070"]     = "Mocule file";
$ary["ITATERRAFORMCLI-MNU-105080"]     = "[Original data]Module files";

$ary["ITATERRAFORMCLI-MNU-106010"]     = "In the variable nesting of Terraform, if the type of the variable defined in the tf file registered in the Module material collection is list, set and list, set, tuple, object is defined in that variable, the member variable You can maintain (view / update) the maximum number of iterations.";
$ary["ITATERRAFORMCLI-MNU-106020"]     = "Item No.";
$ary["ITATERRAFORMCLI-MNU-106030"]     = "Terraform_Nested variable list";
$ary["ITATERRAFORMCLI-MNU-106040"]     = "Terraform_Nested variable list";
$ary["ITATERRAFORMCLI-MNU-106050"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-106060"]     = "The name defined in the variable block";
$ary["ITATERRAFORMCLI-MNU-106070"]     = "Member variable name (iteration)";
$ary["ITATERRAFORMCLI-MNU-106080"]     = "A character string that represents the nesting of the target key by connecting the key indexes of the array type or Key-Value type with '.'.";
$ary["ITATERRAFORMCLI-MNU-106090"]     = "Maximum iteration count";
$ary["ITATERRAFORMCLI-MNU-106100"]     = "The number of times the target variable or the member variable under the member variable is repeated.";

$ary["ITATERRAFORMCLI-MNU-107010"]     = "You can perform maintenance (view/register/update/discard) for operations registered in the associated menu, and Movement and variables associated with the setting value of item. <br><br> There are three methods to register the setting value of item. <br> Value type: Setting value of item is registered in the substitution value list as a specific value of associated variable. <br>Key-Value type: Name (Key) and setting value (Value) of item are registered in the substitution value list as a specific value of associated variable. <br> Key type: Item name is registered in the substitution value list as a specific value of associated variable. When the setting value of the item is blank, it is not registered in the substitution value list";
$ary["ITATERRAFORMCLI-MNU-107020"]     = "No.";
$ary["ITATERRAFORMCLI-MNU-107030"]     = "Terraform_Substitution val auto-reg setting";
$ary["ITATERRAFORMCLI-MNU-107040"]     = "Terraform_Substitution val auto-reg setting";
$ary["ITATERRAFORMCLI-MNU-107050"]     = "Parameter sheet(From)";
$ary["ITATERRAFORMCLI-MNU-107060"]     = "Menu group";
$ary["ITATERRAFORMCLI-MNU-107070"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-107080"]     = "This item is not subject to updates when registering/updating.";
$ary["ITATERRAFORMCLI-MNU-107090"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-107100"]     = "This item is not subject to updates when registering/updating.";
$ary["ITATERRAFORMCLI-MNU-107110"]     = "Menu";
$ary["ITATERRAFORMCLI-MNU-107120"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-107130"]     = "This item is not subject to updates when registering/updating.";
$ary["ITATERRAFORMCLI-MNU-107140"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-107150"]     = "This item is not subject to updates when registering/updating.";
$ary["ITATERRAFORMCLI-MNU-107160"]     = "Menu group:Menu";
$ary["ITATERRAFORMCLI-MNU-107170"]     = "[Original data] Associated menu";
$ary["ITATERRAFORMCLI-MNU-107180"]     = "Item";
$ary["ITATERRAFORMCLI-MNU-107190"]     = "[Original data] Associated menu items";
$ary["ITATERRAFORMCLI-MNU-107200"]     = "Select menu";
$ary["ITATERRAFORMCLI-MNU-107210"]     = "Menu Group:Menu:Item";
$ary["ITATERRAFORMCLI-MNU-107220"]     = "[Original data] Associated menu items";
$ary["ITATERRAFORMCLI-MNU-107230"]     = "Registration method";
$ary["ITATERRAFORMCLI-MNU-107240"]     = "Value type: Setting value of item of associated menu is registered in the substitution value list as a specific value of associated Value type variable. \nKey-Value: Name (Key) and setting value (Value) of associated menu are registered in the substitution value list as a specific value of associated variable name. \nKey type: Item name of associated menu is registered in the substitution value list as a specific value of associated Key variable. When the setting value of the item is blank, it is not registered in the substitution value list";
$ary["ITATERRAFORMCLI-MNU-107250"]     = "IaC variable(To)";
$ary["ITATERRAFORMCLI-MNU-107260"]     = "Movement";
$ary["ITATERRAFORMCLI-MNU-107270"]     = "[Original data] Movement list";
$ary["ITATERRAFORMCLI-MNU-107280"]     = "Key variable";
$ary["ITATERRAFORMCLI-MNU-107290"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-107300"]     = "Auto acquire from ModuleFile \n Setup variable associated with item. \n The name of item becomes the specific value of the variable.";
$ary["ITATERRAFORMCLI-MNU-107310"]     = "Select Movement";
$ary["ITATERRAFORMCLI-MNU-107320"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-107330"]     = "Movement:variable";
$ary["ITATERRAFORMCLI-MNU-107340"]     = "Auto acquire from Module File";
$ary["ITATERRAFORMCLI-MNU-107350"]     = "Value variable";
$ary["ITATERRAFORMCLI-MNU-107360"]     = "Auto acquire from ModuleFile \n Setup variable associated with item. \n The setting value of item becomes the specific value of the variable.";
$ary["ITATERRAFORMCLI-MNU-107370"]     = "Select Movement";
$ary["ITATERRAFORMCLI-MNU-107380"]     = "Movement:variable";
$ary["ITATERRAFORMCLI-MNU-107390"]     = "Auto acquire from Module File";
$ary["ITATERRAFORMCLI-MNU-107400"]     = "HCL setting";
$ary["ITATERRAFORMCLI-MNU-107410"]     = "If \"ON\", parse this value as HashiCorp Configuration Language (HCL). This allows you to interpolate values at runtime.";
$ary["ITATERRAFORMCLI-MNU-107420"]     = "NULL link";
$ary["ITATERRAFORMCLI-MNU-107430"]     = "If specific value of parameter sheet is NULL, set whether to enable Substitution value list registration.\nBlank: Follow Null linkage of Ansible common interface information.\nvalid: Register NULL data.\nInvalid: Do not register null data.";
$ary["ITATERRAFORMCLI-MNU-107440"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107450"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107460"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107470"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107480"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107490"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107500"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107510"]     = "Member variable";
$ary["ITATERRAFORMCLI-MNU-107520"]     = "If the variable type is object, use <KEY> of <KEY> = <TYPE> as a member variable.\nWhen the variable type is tuple, the variables defined in tuple are numbered as [0], [1], [2] ... from the beginning and used as member variables.\nWhen the variable type is the registration target of the variable nest management menu, it is numbered as [0], [1], [2] ... based on the maximum number of repetitions and used as a member variable.\nNo input is required for variable types other than the above.";
$ary["ITATERRAFORMCLI-MNU-107530"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107540"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107550"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107560"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107570"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107580"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107590"]     = "Substitution order";
$ary["ITATERRAFORMCLI-MNU-107600"]     = "If the variable type is list, set, enter the assignment order.\nRequired if the type of the variable at the bottom of the variable or hierarchical variable is list, set.\nNo input is required for variable types other than the above.";
$ary["ITATERRAFORMCLI-MNU-107610"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107620"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107630"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107640"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107650"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107660"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107670"]     = "Member variable";
$ary["ITATERRAFORMCLI-MNU-107680"]     = "If the variable type is object, use <KEY> of <KEY> = <TYPE> as a member variable.\nWhen the variable type is tuple, the variables defined in tuple are numbered as [0], [1], [2] ... from the beginning and used as member variables.\nWhen the variable type is the registration target of the variable nest management menu, it is numbered as [0], [1], [2] ... based on the maximum number of repetitions and used as a member variable.\nNo input is required for variable types other than the above.";
$ary["ITATERRAFORMCLI-MNU-107690"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107700"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107710"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107720"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107730"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107740"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-107750"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-107760"]     = "Substitution order";
$ary["ITATERRAFORMCLI-MNU-107770"]     = "If the variable type is list, set, enter the assignment order.\nRequired if the type of the variable at the bottom of the variable or hierarchical variable is list, set.\nNo input is required for variable types other than the above.";

$ary["ITATERRAFORMCLI-MNU-108010"]     = "Maintenance (view/register/update/discard) can be performed on specific values that are substituted for the variable in Module files that are used by the target Movement for each operation.";
$ary["ITATERRAFORMCLI-MNU-108020"]     = "Item number";
$ary["ITATERRAFORMCLI-MNU-108030"]     = "Terraform_Substitution value list";
$ary["ITATERRAFORMCLI-MNU-108040"]     = "Terraform_Substitution value list";
$ary["ITATERRAFORMCLI-MNU-108050"]     = "Operation";
$ary["ITATERRAFORMCLI-MNU-108060"]     = "[Original data] Basic console/Operation list";
$ary["ITATERRAFORMCLI-MNU-108070"]     = "Movement：variable";
$ary["ITATERRAFORMCLI-MNU-108080"]     = "Obtained automatically from Module";
$ary["ITATERRAFORMCLI-MNU-108090"]     = "Movement";
$ary["ITATERRAFORMCLI-MNU-108100"]     = "[Original data] Movement list";
$ary["ITATERRAFORMCLI-MNU-108110"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-108120"]     = "Obtained automatically from Module";
$ary["ITATERRAFORMCLI-MNU-108130"]     = "Please select a Movement";
$ary["ITATERRAFORMCLI-MNU-108140"]     = "Specific value";
$ary["ITATERRAFORMCLI-MNU-108150"]     = "[Maximum length] 8192 bytes";
$ary["ITATERRAFORMCLI-MNU-108160"]     = "Secure setting";
$ary["ITATERRAFORMCLI-MNU-108170"]     = "When \"ON\", hides the display of concrete values on the \"Substitution value list\" menu. \nAlso, it will not be stored in \"Populated data\" that can be obtained from the \"Confirm execution status\" and \"Execution list\" menus. \nIf you want to hide the value on the Plan and Apply logs, you need to specify \"sensitive = true\" in the variable block of the registered Module material.";
$ary["ITATERRAFORMCLI-MNU-108180"]     = "HCL setting";
$ary["ITATERRAFORMCLI-MNU-108190"]     = "If \"ON\", parse this value as HashiCorp Configuration Language (HCL). This allows you to interpolate values at runtime.";
$ary["ITATERRAFORMCLI-MNU-108200"]     = "Member variable";
$ary["ITATERRAFORMCLI-MNU-108210"]     = "If the variable type is object, use <KEY> of <KEY> = <TYPE> as a member variable.\nWhen the variable type is tuple, the variables defined in tuple are numbered as [0], [1], [2] ... from the beginning and used as member variables.\nWhen the variable type is the registration target of the variable nest management menu, it is numbered as [0], [1], [2] ... based on the maximum number of repetitions and used as a member variable.\nNo input is required for variable types other than the above.";
$ary["ITATERRAFORMCLI-MNU-108220"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108230"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-108240"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108250"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108260"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108270"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-108280"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108290"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-108300"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108310"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108320"]     = "Select variable name";
$ary["ITATERRAFORMCLI-MNU-108330"]     = "Not required";
$ary["ITATERRAFORMCLI-MNU-108340"]     = "Substitution order";
$ary["ITATERRAFORMCLI-MNU-108350"]     = "If the variable type is list, set, enter the assignment order.\nRequired if the type of the variable at the bottom of the variable or hierarchical variable is list, set.\nNo input is required for variable types other than the above.";
$ary["ITATERRAFORMCLI-MNU-108360"]     = "Default value";
$ary["ITATERRAFORMCLI-MNU-108370"]     = "Specific value associated with variable is displayed in default.";

$ary["ITATERRAFORMCLI-MNU-109010"]     = "The execution list (execution history) can be viewed. <br>Click \"Confirm execution status\" to ｔransition to the execution checking menu.";
$ary["ITATERRAFORMCLI-MNU-109020"]     = "Execution No.";
$ary["ITATERRAFORMCLI-MNU-109030"]     = "Terraform_Execution list";
$ary["ITATERRAFORMCLI-MNU-109040"]     = "Terraform_Execution list";
$ary["ITATERRAFORMCLI-MNU-109050"]     = "Confirm execution status";
$ary["ITATERRAFORMCLI-MNU-109060"]     = "Execution type";
$ary["ITATERRAFORMCLI-MNU-109070"]     = "The following states exist for execution type.
・Normal
・Plan check";
$ary["ITATERRAFORMCLI-MNU-109080"]     = "Status";
$ary["ITATERRAFORMCLI-MNU-109090"]     = "The following status states exost:
・Not executed
・Preparing
・Executing
・Executing (delayed)
・Completed
・Completed (error)
・Unexpected error
・Emergency stop
・Not executed (delayed)
・Schedule cancelled";
$ary["ITATERRAFORMCLI-MNU-109100"]     = "Caller Symphony";
$ary["ITATERRAFORMCLI-MNU-109110"]     = "[Original data]Symphony class List";
$ary["ITATERRAFORMCLI-MNU-109120"]     = "Executing user";
$ary["ITATERRAFORMCLI-MNU-109130"]     = "[Original data]User list";
$ary["ITATERRAFORMCLI-MNU-109140"]     = "Movement";
$ary["ITATERRAFORMCLI-MNU-109150"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-109160"]     = "[Original data]Movement list";
$ary["ITATERRAFORMCLI-MNU-109170"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-109180"]     = "[Original data]Movement list";
$ary["ITATERRAFORMCLI-MNU-109190"]     = "Delay timer";
$ary["ITATERRAFORMCLI-MNU-109200"]     = "[Original data]Movement list";
$ary["ITATERRAFORMCLI-MNU-109210"]     = "Terraform Dedicated information";
$ary["ITATERRAFORMCLI-MNU-109220"]     = "Workspace";
$ary["ITATERRAFORMCLI-MNU-109230"]     = "[Original data]Workspace list";
$ary["ITATERRAFORMCLI-MNU-109240"]     = "RUN-ID";
$ary["ITATERRAFORMCLI-MNU-109250"]     = "RUN ID managed by Terraform";
$ary["ITATERRAFORMCLI-MNU-109260"]     = "Operation";
$ary["ITATERRAFORMCLI-MNU-109270"]     = "No.";
$ary["ITATERRAFORMCLI-MNU-109280"]     = "[Original data]Operation list";
$ary["ITATERRAFORMCLI-MNU-109290"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-109300"]     = "[Original data]Operation list";
$ary["ITATERRAFORMCLI-MNU-109310"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-109320"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-109330"]     = "Input data";
$ary["ITATERRAFORMCLI-MNU-109340"]     = "Populated data";
$ary["ITATERRAFORMCLI-MNU-109350"]     = "Populated data set (zip).";
$ary["ITATERRAFORMCLI-MNU-109360"]     = "Output data";
$ary["ITATERRAFORMCLI-MNU-109370"]     = "Result data";
$ary["ITATERRAFORMCLI-MNU-109380"]     = "Result data set (zip).";
$ary["ITATERRAFORMCLI-MNU-109390"]     = "Execution status";
$ary["ITATERRAFORMCLI-MNU-109400"]     = "Scheduled date/time";
$ary["ITATERRAFORMCLI-MNU-109410"]     = "[Format] YYYY/MM/DD HH:MM";
$ary["ITATERRAFORMCLI-MNU-109420"]     = "Start date/time";
$ary["ITATERRAFORMCLI-MNU-109430"]     = "[Format] YYYY/MM/DD HH:MM";
$ary["ITATERRAFORMCLI-MNU-109440"]     = "End date/time";
$ary["ITATERRAFORMCLI-MNU-109450"]     = "[Format] YYYY/MM/DD HH:MM";
$ary["ITATERRAFORMCLI-MNU-109460"]     = "Caller Conductor";
$ary["ITATERRAFORMCLI-MNU-109470"]     = "[Original data]Conductor class List";

$ary["ITATERRAFORMCLI-MNU-110010"]     = "Terraform substitution variable name management";
$ary["ITATERRAFORMCLI-MNU-110020"]     = "Item No.";
$ary["ITATERRAFORMCLI-MNU-110030"]     = "Terraform_Module variable association list";
$ary["ITATERRAFORMCLI-MNU-110040"]     = "Terraform_Module variable association list";
$ary["ITATERRAFORMCLI-MNU-110050"]     = "Module file";
$ary["ITATERRAFORMCLI-MNU-110060"]     = "[Original data]Module files";
$ary["ITATERRAFORMCLI-MNU-110070"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-110080"]     = "[Maximum length] 256 bytes";
$ary["ITATERRAFORMCLI-MNU-110090"]     = "Variable name description";
$ary["ITATERRAFORMCLI-MNU-110100"]     = "Single line text input";
$ary["ITATERRAFORMCLI-MNU-110110"]     = "Type";
$ary["ITATERRAFORMCLI-MNU-110120"]     = "Variable type";
$ary["ITATERRAFORMCLI-MNU-110130"]     = "Default value";
$ary["ITATERRAFORMCLI-MNU-110140"]     = "Default value defined in the variable block";

$ary["ITATERRAFORMCLI-MNU-120010"]     = "You can perform maintenance (view/register/update/discard) for member variables of Terraform.";
$ary["ITATERRAFORMCLI-MNU-120020"]     = "Item No.";
$ary["ITATERRAFORMCLI-MNU-120030"]     = "Terraform_Member variable list";
$ary["ITATERRAFORMCLI-MNU-120040"]     = "Terraform_Member variable list";
$ary["ITATERRAFORMCLI-MNU-120050"]     = "Original variable";
$ary["ITATERRAFORMCLI-MNU-120060"]     = "Parent variable name";
$ary["ITATERRAFORMCLI-MNU-120070"]     = "Id of parent member variable";
$ary["ITATERRAFORMCLI-MNU-120080"]     = "The member variable ID in the hierarchy one level above the member variable";
$ary["ITATERRAFORMCLI-MNU-120090"]     = "Key of child member variable";
$ary["ITATERRAFORMCLI-MNU-120100"]     = "KEY name of member variable";
$ary["ITATERRAFORMCLI-MNU-120110"]     = "Member variable name";
$ary["ITATERRAFORMCLI-MNU-120120"]     = "Nesting information for member variables";
$ary["ITATERRAFORMCLI-MNU-120130"]     = "Member variable type";
$ary["ITATERRAFORMCLI-MNU-120140"]     = "Member variable type";
$ary["ITATERRAFORMCLI-MNU-120150"]     = "Hierarchy of child member variable";
$ary["ITATERRAFORMCLI-MNU-120160"]     = "Hierarchy of member variables";
$ary["ITATERRAFORMCLI-MNU-120170"]     = "VALUE of child member variable";
$ary["ITATERRAFORMCLI-MNU-120180"]     = "Default value of member variable";
$ary["ITATERRAFORMCLI-MNU-120190"]     = "Column order";
$ary["ITATERRAFORMCLI-MNU-120200"]     = "Numerical index of member variables";
$ary["ITATERRAFORMCLI-MNU-120210"]     = "Display availability of substitution value list system";
$ary["ITATERRAFORMCLI-MNU-120220"]     = "Yes (1)/No (0)";

$ary["ITATERRAFORMCLI-MNU-130010"]     = "The association between Movement and Variable can be viewed.";
$ary["ITATERRAFORMCLI-MNU-130020"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-130030"]     = "Terraform_Movement variable association list";
$ary["ITATERRAFORMCLI-MNU-130040"]     = "Terraform_Movement variable association list";
$ary["ITATERRAFORMCLI-MNU-130050"]     = "Movement name";
$ary["ITATERRAFORMCLI-MNU-130060"]     = "Movement name";
$ary["ITATERRAFORMCLI-MNU-130070"]     = "Variable name";
$ary["ITATERRAFORMCLI-MNU-130080"]     = "Variable name";

$ary["ITATERRAFORMCLI-MNU-140010"]     = "Item";
$ary["ITATERRAFORMCLI-MNU-140020"]     = "Value";
$ary["ITATERRAFORMCLI-MNU-140030"]     = "Execution No.";
$ary["ITATERRAFORMCLI-MNU-140040"]     = "Execution type";
$ary["ITATERRAFORMCLI-MNU-140050"]     = "Status";
$ary["ITATERRAFORMCLI-MNU-140060"]     = "Caller Symphony";
$ary["ITATERRAFORMCLI-MNU-140070"]     = "Caller Conductor";
$ary["ITATERRAFORMCLI-MNU-140080"]     = "Executing user";
$ary["ITATERRAFORMCLI-MNU-140090"]     = "Movement";
$ary["ITATERRAFORMCLI-MNU-140100"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-140110"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-140120"]     = "Delay timer (minutes)";
$ary["ITATERRAFORMCLI-MNU-140130"]     = "Terraform Dedicated information";
$ary["ITATERRAFORMCLI-MNU-140140"]     = "Workspace";
$ary["ITATERRAFORMCLI-MNU-140150"]     = "RUN-ID";
$ary["ITATERRAFORMCLI-MNU-140160"]     = "Operation";
$ary["ITATERRAFORMCLI-MNU-140170"]     = "No.";
$ary["ITATERRAFORMCLI-MNU-140180"]     = "Name";
$ary["ITATERRAFORMCLI-MNU-140190"]     = "ID";
$ary["ITATERRAFORMCLI-MNU-140200"]     = "Variable";
$ary["ITATERRAFORMCLI-MNU-140210"]     = "Input data";
$ary["ITATERRAFORMCLI-MNU-140220"]     = "Populated data";
$ary["ITATERRAFORMCLI-MNU-140230"]     = "Output data";
$ary["ITATERRAFORMCLI-MNU-140240"]     = "Result data";
$ary["ITATERRAFORMCLI-MNU-140250"]     = "Execution status";
$ary["ITATERRAFORMCLI-MNU-140260"]     = "Scheduled date/time";
$ary["ITATERRAFORMCLI-MNU-140270"]     = "Start date/time";
$ary["ITATERRAFORMCLI-MNU-140280"]     = "End date/time";
$ary["ITATERRAFORMCLI-MNU-140290"]     = "Confirm";

$ary["ITATERRAFORMCLI-MNU-150010"]     = "Execution log";
$ary["ITATERRAFORMCLI-MNU-150020"]     = "Error log";
$ary["ITATERRAFORMCLI-MNU-150030"]     = "Plan log";
$ary["ITATERRAFORMCLI-MNU-150040"]     = "Init log";
$ary["ITATERRAFORMCLI-MNU-150050"]     = "Apply log";
$ary["ITATERRAFORMCLI-MNU-150060"]     = "Menu for monitoring the operations for setting the registered Terraform. <br>You can check the progress of an operation in real time and initiate emergency stops.";
$ary["ITATERRAFORMCLI-MNU-150070"]     = "Description";
$ary["ITATERRAFORMCLI-MNU-150080"]     = "Target operation";
$ary["ITATERRAFORMCLI-MNU-150090"]     = "Schedule cancellation";
$ary["ITATERRAFORMCLI-MNU-150100"]     = "Schedule cancellation";
$ary["ITATERRAFORMCLI-MNU-150110"]     = "Progress status";
$ary["ITATERRAFORMCLI-MNU-150120"]     = "Emergency stop";
$ary["ITATERRAFORMCLI-MNU-150130"]     = "Emergency stop";

$ary["ITATERRAFORMCLI-MNU-160010"]     = "Filter";
$ary["ITATERRAFORMCLI-MNU-160020"]     = "Display only corresponding lines";

?>
