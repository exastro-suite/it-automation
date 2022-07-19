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
////en_US_UTF-8_ITACICDFORIAC_MNU
$ary["ITACICDFORIAC-MNU-1200010000"] = "Since the Remote Repository clone is created in ITA, it is possible to maintain it's information (View/Register/Update/Abolish).<BR>
Set the \"Remote repository URL\" and \"Branch\" to the values of the arguments you want to pass to the Git clone command.<BR>
git clone 「Remote Repository」「Local Repository path」 -b「Branch」<BR>
 The「Local Repository path」is the following /「ITA installation directory」/ita-root/repositorys/0000000001(Item number: The last 10 digits).";
$ary["ITACICDFORIAC-MNU-1200010001"] = "Item";
$ary["ITACICDFORIAC-MNU-1200010002"] = "Remote repository";
$ary["ITACICDFORIAC-MNU-1200010003"] = "Remote repository";
$ary["ITACICDFORIAC-MNU-1200010100"] = "Repository Name";
$ary["ITACICDFORIAC-MNU-1200010101"] = "Please input the Remote Repository ITA Display name. [Maximum length] 256 bytes";
$ary["ITACICDFORIAC-MNU-1200010200"] = "Remote Repository (URL)";
$ary["ITACICDFORIAC-MNU-1200010201"] = "Enter the repository you want to clone from for the git clone command.[Maximum length] 256 Bytes";
$ary["ITACICDFORIAC-MNU-1200010300"] = "Branch";
$ary["ITACICDFORIAC-MNU-1200010301"] = "Please input the Branch name. [Maximum length] 256 bytes";
$ary["ITACICDFORIAC-MNU-1200010400"] = "Protocol";
$ary["ITACICDFORIAC-MNU-1200010401"] = "Please select the Protocol you want to connect to the Remote Repository.
If you're connecting to the Remote Repository through https, please select \"https\". 
If you're using Local Git, please select \"Local\".";
$ary["ITACICDFORIAC-MNU-1200010500"] = "Visibility type";
$ary["ITACICDFORIAC-MNU-1200010501"] = "Please select a Remote Repository Visibility type.
The Visibility type is required when \"private\" is selected for Protocol.";
$ary["ITACICDFORIAC-MNU-1200010600"] = "Git Account information";
$ary["ITACICDFORIAC-MNU-1200010700"] = "User";
$ary["ITACICDFORIAC-MNU-1200010701"] = "Input the Git user. 
The Uses is an required item when \"Private\" is selected for Visibility. 
[Maximum length] 128 Bytes";
$ary["ITACICDFORIAC-MNU-1200010800"] = "Password";
$ary["ITACICDFORIAC-MNU-1200010801"] = "Please input the password needed when running the Git clone command.
The password is required if the visibility type is set to \"Private\". 
[Max size] 128 bytes
Please note that password authentication was disabled by GitHub the 13th of August 2021.
https://github.blog/2020-12-15-token-authentication-requirements-for-git-operations/
If you are using GitHub with password authentication, you will need to create and enter your own personal access token for the Git account information password.
Please see the following URL for how to create a personal access token.
https://docs.github.com/ja/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token";
$ary["ITACICDFORIAC-MNU-1200010810"]   = "ssh connection information";
$ary["ITACICDFORIAC-MNU-1200010820"]   = "Password";
$ary["ITACICDFORIAC-MNU-1200010821"]   = "Please input the Linux user and password needed in order to run Git clone command.
The password is not required if you have ssh password authentication selected.
[Max length] 128 bytes";
$ary["ITACICDFORIAC-MNU-1200010830"]   = "Passphrase";
$ary["ITACICDFORIAC-MNU-1200010831"]   = "Please input the passphrase set to the secret keyfile used when running the Git clone command.
The passphrase is not required if you have ssh key certificate (with passphrase) selected.
[Max length] 128 bytes";
$ary["ITACICDFORIAC-MNU-1200010840"]   = "Connection parameters";
$ary["ITACICDFORIAC-MNU-1200010841"]   = "Configures the parameters that will be set to the \"GIT_SSH_COMMAND\" environment variables when running the Git clone command.
The \"GIT_SSH_COMMAND\" is an environment variable that can be configured in Git version 2.3 or later.If the ITA server has a Git version earlier than 2.3 installed, the configured parameter will be deactivated.
\"GIT_SSH_COMMAND\" is set to the following parameter by default.
UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no
The configured parameter is added on the end.
[Max length] 512 bytes";
$ary["ITACICDFORIAC-MNU-1200010900"] = "Proxy";
$ary["ITACICDFORIAC-MNU-1200011000"] = "Address";
$ary["ITACICDFORIAC-MNU-1200011001"] = "If you are using a Proxy server, please enter the Proxy server address.
[Maximum length] 128 bytes";
$ary["ITACICDFORIAC-MNU-1200011100"] = "Port";
$ary["ITACICDFORIAC-MNU-1200011101"] = "If you are using a Proxy server, please enter the Proxy Server port.";
$ary["ITACICDFORIAC-MNU-1200011200"] = "Remote Repository Synchronization information";
$ary["ITACICDFORIAC-MNU-1200011300"] = "Automatic Synchronization";
$ary["ITACICDFORIAC-MNU-1200011301"] = "Select if the Remote Repository should be synchronized automatically or not. 
Activated: Synchronizes with the remote repository with the input cycle.
Deactivated: Does not synchronize the remote repository automatically.";
$ary["ITACICDFORIAC-MNU-1200011400"] = "Cycle (Seconds)";
$ary["ITACICDFORIAC-MNU-1200011401"] = "Please input the cycle in which the Remote repository will be automatically synchronized with.
If nothing is input, the value will be set to the default 60 seconds.
Unit: Seconds.";
$ary["ITACICDFORIAC-MNU-1200011500"] = "Remote Repository Synchronization status.";
$ary["ITACICDFORIAC-MNU-1200011600"] = "Status";
$ary["ITACICDFORIAC-MNU-1200011601"] = "If the Automatic Synchronization is Active, the Remote repository Synchronization status will display \"Normal\" or \"Abmormal\".";
$ary["ITACICDFORIAC-MNU-1200011700"] = "Detailed Information";
$ary["ITACICDFORIAC-MNU-1200011701"] = "If the Automatic Synchronization is Active and an error occurs while synchronization, Error information will be displayed.";
$ary["ITACICDFORIAC-MNU-1200011800"] = "Last date/time";
$ary["ITACICDFORIAC-MNU-1200011801"] = "The date/time of the last Remote Repository synchronization will be displayed.";
$ary["ITACICDFORIAC-MNU-1200011900"] = "Resume";
$ary["ITACICDFORIAC-MNU-1200012000"] = "Connection retry information";
$ary["ITACICDFORIAC-MNU-1200012100"] = "Retry number";
$ary["ITACICDFORIAC-MNU-1200012101"] = "Enter the number of times to retries if communication fails.
If nothing is input, the value will be set to the default 3 times.";
$ary["ITACICDFORIAC-MNU-1200012200"] = "Cycle(ms)";
$ary["ITACICDFORIAC-MNU-1200012201"] = "Enter the period to retry if the communication fails.
If nothing is input, the value will be set to the default 1000ms.
Unit:ms";
$ary["ITACICDFORIAC-MNU-1200020000"] = "The file(s) registered to the Remote repository will be displayed.<BR>
The files displayed can be downloaded as a single file.";
$ary["ITACICDFORIAC-MNU-1200020001"] = "Item number";
$ary["ITACICDFORIAC-MNU-1200020002"] = "Remote repository file";
$ary["ITACICDFORIAC-MNU-1200020003"] = "Remote repository file";
$ary["ITACICDFORIAC-MNU-1200020100"] = "Remote Repository name";
$ary["ITACICDFORIAC-MNU-1200020101"] = "[Source data]Remote Repositoryt.";
$ary["ITACICDFORIAC-MNU-1200020200"] = "File path";
$ary["ITACICDFORIAC-MNU-1200020201"] = "File path within the Remote Repository
[Source data]Remote repository";
$ary["ITACICDFORIAC-MNU-1200020300"] = "File type";
$ary["ITACICDFORIAC-MNU-1200020301"] = "File type";
$ary["ITACICDFORIAC-MNU-1200030000"] = "It is possible to maintain(View/Register/Update/Abolish) the link between the files in the Remote Repository and the files in the other consoles.
The link files should be automatically update everytime the Remote Repository files are updated.";
$ary["ITACICDFORIAC-MNU-1200030001"] = "Item number";
$ary["ITACICDFORIAC-MNU-1200030002"] = "File link";
$ary["ITACICDFORIAC-MNU-1200030003"] = "File link";
$ary["ITACICDFORIAC-MNU-1200030100"] = "Link file name";
$ary["ITACICDFORIAC-MNU-1200030101"] = "Please input the name of the file that will be linked.
If a file with the input name and the selected type does not exist, a new one will be registered.
If the specified file is an abolished record, said record will be restored and the file will be updated.";
$ary["ITACICDFORIAC-MNU-1200030200"] = "Git Repository (From)";
$ary["ITACICDFORIAC-MNU-1200030300"] = "Remote Repository";
$ary["ITACICDFORIAC-MNU-1200030301"] = "[Source data]Remote Repository.";
$ary["ITACICDFORIAC-MNU-1200030400"] = "File path";
$ary["ITACICDFORIAC-MNU-1200030401"] = "[Source data]Remote repository file";
$ary["ITACICDFORIAC-MNU-1200030500"] = "Exastro IT automation(To)";
$ary["ITACICDFORIAC-MNU-1200030600"] = "Link file type";
$ary["ITACICDFORIAC-MNU-1200030601"] = "Select the type of the file that will be linked.";
$ary["ITACICDFORIAC-MNU-1200030700"] = "Ansible-Pioneer";
$ary["ITACICDFORIAC-MNU-1200030800"] = "Dialogue type";
$ary["ITACICDFORIAC-MNU-1200030801"] = "If \"Dialogue file collection\" is selected for the Link file type, the Dialogue type is a required item.
[Source data]ansible-pioneer Console/Dialogue type list.";
$ary["ITACICDFORIAC-MNU-1200030900"] = "OS Type";
$ary["ITACICDFORIAC-MNU-1200030901"] = "If \"Dialogue file collection\" is selected for the Link file type, the OS Type master is a required item.
[Source data]ansible-pioneer Console/OS Type master";
$ary["ITACICDFORIAC-MNU-1200031000"] = "File Synchronization information";
$ary["ITACICDFORIAC-MNU-1200031100"] = "Automatic Synchronization";
$ary["ITACICDFORIAC-MNU-1200031101"] = "Select if the Remote Repository should be synchronized automatically or not. 
Activated: Synchronizes with the remote repository with the input cycle.
Deactivated: Does not synchronize the remote repository automatically.";
$ary["ITACICDFORIAC-MNU-1200031200"] = "State";
$ary["ITACICDFORIAC-MNU-1200031201"] = "If Automatic Synchronization is active, The Remote Repository latest Synchronization status will display Normal/Abnormal/Resume";
$ary["ITACICDFORIAC-MNU-1200031300"] = "Detailed Information";
$ary["ITACICDFORIAC-MNU-1200031301"] = "If Automatic Synchronization is active and an error occurs while synchronization, Error information will be displayed.";
$ary["ITACICDFORIAC-MNU-1200031400"] = "Last date/time";
$ary["ITACICDFORIAC-MNU-1200031401"] = "The date/time of the last Remote Repository synchronization will be displayed.";
$ary["ITACICDFORIAC-MNU-1200031500"] = "Delivery Information";
$ary["ITACICDFORIAC-MNU-1200031600"] = "Operation";
$ary["ITACICDFORIAC-MNU-1200031601"] = "[Source data]Basic Console/Operation list";
$ary["ITACICDFORIAC-MNU-1200031700"] = "Movement";
$ary["ITACICDFORIAC-MNU-1200031701"] = "[Source data]XXX Console/Movement list";
$ary["ITACICDFORIAC-MNU-1200031800"] = "Dry run";
$ary["ITACICDFORIAC-MNU-1200031801"] = "If you want to dry run a Movement, please select ●.
The default is without dry run.";
$ary["ITACICDFORIAC-MNU-1200031900"] = "Operaiton instance number";
$ary["ITACICDFORIAC-MNU-1200031901"] = "The operation instance number that was taken when Movement was executed.";
$ary["ITACICDFORIAC-MNU-1200032000"] = "Execution Login ID";
$ary["ITACICDFORIAC-MNU-1200032001"] = "Select the User that is going to execute the file linking.
[Source data]Register Account";
$ary["ITACICDFORIAC-MNU-1200032100"] = "Detailed information";
$ary["ITACICDFORIAC-MNU-1200032101"] = "If an error occurs during Delivery, the error information will be displayed.";
$ary["ITACICDFORIAC-MNU-1200032200"] = "Assign Access permission role.";
$ary["ITACICDFORIAC-MNU-1200032201"] = "Select the settings for the permission roles to be granted to the records to be added or updated in each file collection.
Without: Blank (No access permission role)
With: Grants role linked the Rest user access by default.
If nothing is selected, the default \"Without\" will be selected.";
$ary["ITACICDFORIAC-MNU-1200032300"] = "Template Management";
$ary["ITACICDFORIAC-MNU-1200032400"] = "Variable Definition";
$ary["ITACICDFORIAC-MNU-1200032401"] = "The structure of the variables(VAR_) used in the Template files are defined in YAML format.
The following 3 structures can be defined.
・Variable that defines 1 specific value to the Variable name.
    E.g)
  　　VAR_sample:
・Variable that defines multiple specific values to the Variable name.
    E.g)
    　VAR_sample: []
・Hierarchical variables
    E.g)
      VAR_sample:
        name:
        value:";
$ary["ITACICDFORIAC-MNU-1200032500"] = "Operation Execution check Menu ID";
$ary["ITACICDFORIAC-MNU-1200032501"] = "Operation Execution check Menu ID";
$ary["ITACICDFORIAC-MNU-1200032600"] = "Operation status check";
$ary["ITACICDFORIAC-MNU-1200032601"] = "Operation status check";
$ary["ITACICDFORIAC-MNU-1200032800"] = "Last Executer's Login ID";
$ary["ITACICDFORIAC-MNU-1200032801"] = "Linked by:";
$ary["ITACICDFORIAC-MNU-1200035001"] = "Please select a Repository.";
$ary["ITACICDFORIAC-MNU-1200035002"] = "Select a link file type.";
$ary["ITACICDFORIAC-MNU-1200040000"] = "It is possible to maintain (View/Update) the Connection interface information needed to use RestAPI for ITA.";
$ary["ITACICDFORIAC-MNU-1200040001"] = "Item number";
$ary["ITACICDFORIAC-MNU-1200040002"] = "Interface information";
$ary["ITACICDFORIAC-MNU-1200040003"] = "Interface information";
$ary["ITACICDFORIAC-MNU-1200040100"] = "Host name";
$ary["ITACICDFORIAC-MNU-1200040101"] = "Input ITA Host name (or IP Address).
If the connecting through https, we recommend that you input the Host name/
[Maximum length]128 Bytes";
$ary["ITACICDFORIAC-MNU-1200040400"] = "Protocol";
$ary["ITACICDFORIAC-MNU-1200040401"] = "Input either http or https.
https is most common.";
$ary["ITACICDFORIAC-MNU-1200040500"] = "Port";
$ary["ITACICDFORIAC-MNU-1200040501"] = "Please input the Connection port.
Default is:
http:80
https:443.";
$ary["ITACICDFORIAC-MNU-1200050000"] = "It is possible to maintain (Viewv/Register/Update/Abolish) the information of the user that executes file linking. 
The user must be registered to the Management Console/User list.";
$ary["ITACICDFORIAC-MNU-1200050001"] = "Item number";
$ary["ITACICDFORIAC-MNU-1200050002"] = "Registered account";
$ary["ITACICDFORIAC-MNU-1200050003"] = "Registered account";
$ary["ITACICDFORIAC-MNU-1200050004"] = "Exastro IT Automation account";
$ary["ITACICDFORIAC-MNU-1200050100"] = "Login Id";
$ary["ITACICDFORIAC-MNU-1200050101"] = "Please select the Login ID of the user that is going to execute the File linking. 
 [Source Data]Management Console/User list";
$ary["ITACICDFORIAC-MNU-1200050200"] = "Login Password";
$ary["ITACICDFORIAC-MNU-1200050201"] = "Please Input the password for the Login ID.
The characters can be between 8-30 bytes and can contain alphanumeric characters and the following symbols(! #$%&'()*+. /;<=>? @[]^\\_`{|}~).";
$ary["ITACICDFORIAC-MNU-1200050202"] = "Alphanumeric characters and available symbols (! #$%&'()*+. /;<=>? @[]^\\_`{|}~).";
?>
