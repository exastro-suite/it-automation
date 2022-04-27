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
////en_US_UTF-8_ITABASEH_MNU
$ary["ITABASEH-MNU-101020"]         = "Maintenance (view/register/update/discard) can be performed on the target host.<BR>Please register the necessary information to the target host before executing each orchestrator.";
$ary["ITABASEH-MNU-101030"]         = "Managed system item number";
$ary["ITABASEH-MNU-101040"]         = "Device list";
$ary["ITABASEH-MNU-101050"]         = "Device list";
$ary["ITABASEH-MNU-101060"]         = "HW device type";
$ary["ITABASEH-MNU-101070"]         = "Select HW device type.\n・NW(network)\n・ST(storage)\n・SV(server)";
$ary["ITABASEH-MNU-101080"]         = "Host name";
$ary["ITABASEH-MNU-101081"]         = "Host name is invalid. Numerical host names cannot be set.";
$ary["ITABASEH-MNU-101090"]         = "[Maximum length] 128 bytes";
$ary["ITABASEH-MNU-102010"]         = "IP address";
$ary["ITABASEH-MNU-102020"]         = "Enter in [Maximum length]15byte\nxxx.xxx.xxx.xxx format.";
$ary["ITABASEH-MNU-102024"]         = "Ansible Dedicated information";
$ary["ITABASEH-MNU-102025"]         = "Pioneer Dedicated information";
$ary["ITABASEH-MNU-102026"]         = "Ansible Automation Controller Dedicated information";
$ary["ITABASEH-MNU-102030"]         = "Protocol";
$ary["ITABASEH-MNU-102040"]         = "Protocol for device login through Ansible-Pioneer.";
$ary["ITABASEH-MNU-102050"]         = "Login user ID";
$ary["ITABASEH-MNU-102060"]         = "[Maximum length] 30 bytes";
$ary["ITABASEH-MNU-102061"]         = "Login password";
$ary["ITABASEH-MNU-102062"]         = "Management";
$ary["ITABASEH-MNU-102063"]         = "Login password is required when ● is selected.";
$ary["ITABASEH-MNU-102064"]         = "Mark with '●' in order to deactivate login password expiration.";
$ary["ITABASEH-MNU-102065"]         = "Mark with '●' in order to deactivate forced password change upon first login.";
$ary["ITABASEH-MNU-102070"]         = "Login password";
$ary["ITABASEH-MNU-102071"]         = "Input for login password is mandatory when ● is set in login password management.";
$ary["ITABASEH-MNU-102072"]         = "Input for login password is not allowed when ● is not set in login password management.";
$ary["ITABASEH-MNU-102073"]         = "Input for login password is mandatory when authentication method is password authentication.";
$ary["ITABASEH-MNU-102074"]         = "Management of login password is mandatory when authentication method is password authentication.";
$ary["ITABASEH-MNU-102075"]         = "Input value for authentication method is not valid.";
$ary["ITABASEH-MNU-102080"]         = "[Maximum length] 128 bytes";
$ary["ITABASEH-MNU-102085"]         = "Dedicated information for Legacy/Role";
$ary["ITABASEH-MNU-102088"]         = "Authentication method";
$ary["ITABASEH-MNU-102089"]         = "Select the authentication method when connecting to the device from Ansible.
-Password authentication
 Select ● in the login password management and enter the login password.
-Key authentication (no passphrase)
 Uploading the ssh private key file is required.
-Key authentication (with passphrase)
 You must upload the ssh private key file and enter the passphrase.
-Key authentication (key exchanged)
 No upload of ssh private key file is required.
-Password authentication (winrm)
 Enter the WinRM connection information as required.
For authentication methods other than password authentication (winrm), the following settings are required on the device side.
The login user sudo privileges must be set to / etc / sudoers with his NOPASSWD.";
$ary["ITABASEH-MNU-102090"]         = "OS type";
$ary["ITABASEH-MNU-102100"]         = "LANG";
$ary["ITABASEH-MNU-102101"]         = "Select the character encoding (LANG) when executing the Pioneer dialog file. If it is blank, it will be treated as utf-8.";
$ary["ITABASEH-MNU-102110"]         = "EtherWakeOnLan";
$ary["ITABASEH-MNU-102120"]         = "Power ON";
$ary["ITABASEH-MNU-102130"]         = "Power ON";
$ary["ITABASEH-MNU-102140"]         = "MAC address";
$ary["ITABASEH-MNU-102150"]         = "[Maximum length] 17 bytes";
$ary["ITABASEH-MNU-102160"]         = "Network device name";
$ary["ITABASEH-MNU-102170"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-103010"]         = "Used for different dialog files based on OS type of device in Ansible-Pioneer.";
$ary["ITABASEH-MNU-103015"]         = "Dedicated information for Cobbler";
$ary["ITABASEH-MNU-103020"]         = "Profile";
$ary["ITABASEH-MNU-103030"]         = "[Original data] Cobbler console/ profile list";
$ary["ITABASEH-MNU-103040"]         = "Interface";
$ary["ITABASEH-MNU-103050"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-103051"]         = "Connection type";
$ary["ITABASEH-MNU-103052"]         = "Sets the connection type for Ansible Automation Controller credentials. Basically, select machine. Select Network for network devices that require ansible_connection set to locla.";
$ary["ITABASEH-MNU-103060"]         = "MAC address";
$ary["ITABASEH-MNU-103070"]         = "[Maximum length] 17 bytes";
$ary["ITABASEH-MNU-103080"]         = "Netmask";
$ary["ITABASEH-MNU-103090"]         = "[Maximum length] 15 bytes";
$ary["ITABASEH-MNU-104010"]         = "Gateway";
$ary["ITABASEH-MNU-104020"]         = "[Maximum length] 15 bytes";
$ary["ITABASEH-MNU-104030"]         = "Static";
$ary["ITABASEH-MNU-104040"]         = "[Maximum length] 32 bytes";
$ary["ITABASEH-MNU-104050"]         = "Display order";
$ary["ITABASEH-MNU-104060"]         = "To control display order";
$ary["ITABASEH-MNU-104070"]         = "Maintenance (view/register/update/discard) can be performed on the operation list.";
$ary["ITABASEH-MNU-104080"]         = "No.";
$ary["ITABASEH-MNU-104090"]         = "Operation list";
$ary["ITABASEH-MNU-104101"]         = "Dedicated information for SCRAB ";
$ary["ITABASEH-MNU-104111"]         = "Port number";
$ary["ITABASEH-MNU-104112"]         = "For Linux OS type systems, set the port number of ssh.
For Windows OS type systems, set a port number of WinRM.
Generally port 22 is used for ssh and port 5985 is used for WinRM.";
$ary["ITABASEH-MNU-104121"]         = "OS type";
$ary["ITABASEH-MNU-104122"]         = "OS type of the node targeted for construction";
$ary["ITABASEH-MNU-104131"]         = "Data link";
$ary["ITABASEH-MNU-104132"]         = "When performing server information and data synchronization of SCARB, please choose \"●\".";
$ary["ITABASEH-MNU-104141"]         = "Specified host format";
$ary["ITABASEH-MNU-104142"]         = "Method to specify build node.";
$ary["ITABASEH-MNU-104151"]         = "Authentication method";
$ary["ITABASEH-MNU-104152"]         = "Set a method for authentication with SCARB.
For Windows OS type systems, choose a version of powershell. 
PowerShell version 4 or earlier
PowerShell version 5 or later
For Linux OS type systems, choose an authentication method from the following.
Password authentication
ssh key authentication
ssh config file";
$ary["ITABASEH-MNU-104161"]         = "ssh private key file";
$ary["ITABASEH-MNU-104162"]         = "When ssh key authentication is specified as the authentication method, input the path of the authentication key file.
The authentication key file must be located on a SCRAB server";
$ary["ITABASEH-MNU-104171"]         = "ssh config file";
$ary["ITABASEH-MNU-104172"]         = "When ssh config file is specified as the authentication method, input the path of the ssh config file.
The ssh config file must be located on a SCRAB server";
$ary["ITABASEH-MNU-104201"]         = "Dedicated information for OpenAudIT";
$ary["ITABASEH-MNU-104211"]         = "Connection type";
$ary["ITABASEH-MNU-104212"]         = "";
$ary["ITABASEH-MNU-104213"]         = "Community";
$ary["ITABASEH-MNU-104214"]         = "";
$ary["ITABASEH-MNU-104215"]         = "User name";
$ary["ITABASEH-MNU-104216"]         = "";
$ary["ITABASEH-MNU-104217"]         = "Password";
$ary["ITABASEH-MNU-104218"]         = "";
$ary["ITABASEH-MNU-104219"]         = "KEY File";
$ary["ITABASEH-MNU-104220"]         = "";
$ary["ITABASEH-MNU-104221"]         = "Security name";
$ary["ITABASEH-MNU-104222"]         = "";
$ary["ITABASEH-MNU-104223"]         = "Security level";
$ary["ITABASEH-MNU-104224"]         = "";
$ary["ITABASEH-MNU-104225"]         = "Authentication protocol";
$ary["ITABASEH-MNU-104226"]         = "";
$ary["ITABASEH-MNU-104227"]         = "Authentication passphrase";
$ary["ITABASEH-MNU-104228"]         = "";
$ary["ITABASEH-MNU-104229"]         = "Privacy protocol";
$ary["ITABASEH-MNU-104230"]         = "";
$ary["ITABASEH-MNU-104231"]         = "Privacy passphrase";
$ary["ITABASEH-MNU-104232"]         = "";
$ary["ITABASEH-MNU-104501"]         = "DSC Dedicated information";
$ary["ITABASEH-MNU-104502"]         = "Certificate File";
$ary["ITABASEH-MNU-104503"]         = "To encrypt credentials, enter a certificate file.";
$ary["ITABASEH-MNU-104504"]         = "Thumbprint";
$ary["ITABASEH-MNU-104505"]         = "To encrypt credentials, enter a thumbprint.";
$ary["ITABASEH-MNU-104600"]         = "WinRM connection information";
$ary["ITABASEH-MNU-104605"]         = "Port no";
$ary["ITABASEH-MNU-104606"]         = "Specify the port number to use for WinRM connections to Windows Server. \nIf no port number is specified, the default port number (http:5985) will be used.";
$ary["ITABASEH-MNU-104610"]         = "Server certificate";
$ary["ITABASEH-MNU-104611"]         = "Enter the server certificate to use for WinRM connections to Windows Server over https.
If the Python version is 2.7 or later and does not verify the https server certificate.
    ansible_winrm_server_cert_validation=ignore";
$ary["ITABASEH-MNU-104615"]         = "Connection options";
$ary["ITABASEH-MNU-104616"]         = "When the protocol is ssh\nTo set options other than the ssh option set in ssh_args in /etc/ansible/ansible.cfg, specify the desired options.\n(Example)\n    To specify the ssh config file.\n      -F /root/.ssh/ssh_config\n\nWhen the protocol is telnet\nTo set options for telnet connections, specify the desired options.\n(Example)\n    To specify 11123 as the port number.\n      11123";
$ary["ITABASEH-MNU-104620"]         = "Inventory file\nAdditional option";
$ary["ITABASEH-MNU-104621"]         = "Enter additional options in YAML format to set inventory file options that ITA does not set.
(Example)
    ansible_connection: network_cli
    ansible_network_os: nxos";
$ary["ITABASEH-MNU-104630"]         = "Instance group name";
$ary["ITABASEH-MNU-104631"]         = "Specify the instance group to be set as the inventory of Ansible Automation Controller.";
$ary["ITABASEH-MNU-105010"]         = "Operation list";
$ary["ITABASEH-MNU-105020"]         = "Operation name";
$ary["ITABASEH-MNU-105030"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-105040"]         = "Scheduled date for execution";
$ary["ITABASEH-MNU-105050"]         = "Not used in the system";
$ary["ITABASEH-MNU-105060"]         = "Operation ID";
$ary["ITABASEH-MNU-105070"]         = "Operation ID (auto numbering)";
$ary["ITABASEH-MNU-105075"]         = "Last execution date";
$ary["ITABASEH-MNU-105076"]         = "Date/time when the operation was actually performed.";
$ary["ITABASEH-MNU-105080"]         = "Display order";
$ary["ITABASEH-MNU-105090"]         = "To control display order";
$ary["ITABASEH-MNU-106010"]         = "Select";
$ary["ITABASEH-MNU-106020"]         = "Allows users to perform maintenance(view/register/update/discard) for the OS type.";
$ary["ITABASEH-MNU-106030"]         = "OS type ID";
$ary["ITABASEH-MNU-106040"]         = "Ansible Common OS type master";
$ary["ITABASEH-MNU-106050"]         = "Ansible Common OS type master";
$ary["ITABASEH-MNU-106060"]         = "OS type name";
$ary["ITABASEH-MNU-106070"]         = "Include till version.\n(Example)RHEL7.2";
$ary["ITABASEH-MNU-106075"]         = "Device type";
$ary["ITABASEH-MNU-106080"]         = "SV";
$ary["ITABASEH-MNU-106090"]         = "";
$ary["ITABASEH-MNU-107010"]         = "NW";
$ary["ITABASEH-MNU-107020"]         = "";
$ary["ITABASEH-MNU-107030"]         = "ST";
$ary["ITABASEH-MNU-107040"]         = "";
$ary["ITABASEH-MNU-107050"]         = "Display order";
$ary["ITABASEH-MNU-107060"]         = "To control display order";
$ary["ITABASEH-MNU-107070"]         = "The association between Movement and Orchestrator can be viewed.";
$ary["ITABASEH-MNU-107080"]         = "Movement ID";
$ary["ITABASEH-MNU-107090"]         = "Movement list";
$ary["ITABASEH-MNU-108010"]         = "Movement list";
$ary["ITABASEH-MNU-108020"]         = "Movement Name";
$ary["ITABASEH-MNU-108030"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-108040"]         = "Orchestrator";
$ary["ITABASEH-MNU-108050"]         = "The orcestrator used is displayed.";
$ary["ITABASEH-MNU-108060"]         = "Delay timer";
$ary["ITABASEH-MNU-108070"]         = "If there is a delay in the Movement as per the specified period (minutes), delayed status appears.";
$ary["ITABASEH-MNU-108075"]         = "Dedicated information for Ansible";
$ary["ITABASEH-MNU-108080"]         = "Host specific format";
$ary["ITABASEH-MNU-108090"]         = "Method that specifies build node. ";
$ary["ITABASEH-MNU-108091"]         = "Number of parallel executions";
$ary["ITABASEH-MNU-108092"]         = "NULL or positive integer";
$ary["ITABASEH-MNU-108100"]         = "WinRM connection";
$ary["ITABASEH-MNU-108110"]         = "Select when the build node connects to a WinRM through a WindowsServer.";
$ary["ITABASEH-MNU-108120"]         = "gather_facts";
$ary["ITABASEH-MNU-108130"]         = "Select if you want to get the build node information (gather_facts) when executing Playbook.";
$ary["ITABASEH-MNU-108200"]         = "OpenStack Dedicated information";
$ary["ITABASEH-MNU-108210"]         = "HEAT template";
$ary["ITABASEH-MNU-108220"]         = "Upload the HEAT template to execute. [Maximum size] 4GB";
$ary["ITABASEH-MNU-108230"]         = "Environment configuration file";
$ary["ITABASEH-MNU-108240"]         = "Upload the script file to be executed after executing the HEAT template. [Maximum size] 4GB";
$ary["ITABASEH-MNU-108241"]         = "Tower Dedicated information";
$ary["ITABASEH-MNU-108242"]         = "virtualenv";
$ary["ITABASEH-MNU-108243"]         = "Ansible execution environment directory built with virtualenv is displayed.\nChoose the ansible execution environment you want to run.\nIf it is not choose, the ansible execution environment installed at the time of Ansible Automation Controller installation will be used.";
$ary["ITABASEH-MNU-108300"]         = "DSC Dedicated information";
$ary["ITABASEH-MNU-108310"]         = "Error retry timeout";
$ary["ITABASEH-MNU-108320"]         = "If the error persists beyond the specified time (seconds), the status display error.";
$ary["ITABASEH-MNU-109006"]         = "ssh private key file";
$ary["ITABASEH-MNU-109007"]         = "ssh private key file for key authentication.
The uploaded file is encrypted and saved. 
If you download after registration, the encrypted file will be downloaded.";
$ary["ITABASEH-MNU-109008"]         = "Passphrase";
$ary["ITABASEH-MNU-109009"]         = "Enter the passphrase set in the ssh private key file.";
$ary["ITABASEH-MNU-109010"]         = "Ansible-vault encrypted ssh private key file.";
$ary["ITABASEH-MNU-109011"]         = "ssh key credentials";
$ary["ITABASEH-MNU-109030"]         = "Allows users to view Symphony class. <br>By clicking “Details”, transit to Symphony class edit menu.";
$ary["ITABASEH-MNU-109040"]         = "Symphony class ID";
$ary["ITABASEH-MNU-109050"]         = "Symphony class List";
$ary["ITABASEH-MNU-109060"]         = "Symphony class List";
$ary["ITABASEH-MNU-109070"]         = "Symphony name";
$ary["ITABASEH-MNU-109080"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-109090"]         = "Description";
$ary["ITABASEH-MNU-111010"]         = "Terraform Dedicated information";
$ary["ITABASEH-MNU-111020"]         = "Organization:Workspace";
$ary["ITABASEH-MNU-111030"]         = "Target Organization:Workspace.";
$ary["ITABASEH-MNU-120001"]         = "Item";
$ary["ITABASEH-MNU-120002"]         = "Item type";
$ary["ITABASEH-MNU-120003"]         = "Parent Item";
$ary["ITABASEH-MNU-120004"]         = "Physical name";
$ary["ITABASEH-MNU-120005"]         = "Logical name";
$ary["ITABASEH-MNU-120006"]         = "Related Table name";
$ary["ITABASEH-MNU-120007"]         = "Related Item";
$ary["ITABASEH-MNU-120008"]         = "Table name";
$ary["ITABASEH-MNU-120009"]         = "View name";
$ary["ITABASEH-MNU-120010"]         = "ER Diagram Menu List";
$ary["ITABASEH-MNU-120011"]         = "Item, Menu ID and Multi-unique";
$ary["ITABASEH-MNU-120012"]         = "Item types can have the following states.\n・Group\n・Item";
$ary["ITABASEH-MNU-120013"]         = "If the Item is under a group, the group's item will be set.";
$ary["ITABASEH-MNU-120014"]         = "Pysical Item name";
$ary["ITABASEH-MNU-120015"]         = "Logical Item name";
$ary["ITABASEH-MNU-120016"]         = "Table name with Relation";
$ary["ITABASEH-MNU-120017"]         = "Item with Relation";
$ary["ITABASEH-MNU-120018"]         = "Required Item. Table name that gets linked to Menu.";
$ary["ITABASEH-MNU-120019"]         = "Table view linked to Menu.";
$ary["ITABASEH-MNU-120020"]         = "Configures settings related to ER Diagrams. Configures Displayed Menu Information.";
$ary["ITABASEH-MNU-120021"]         = "Configures Settings related to ER Diagrams. Configures Items within displayed menus.";
$ary["ITABASEH-MNU-120022"]         = "ER Diagram Item List";
$ary["ITABASEH-MNU-201010"]         = "Detailed display";
$ary["ITABASEH-MNU-201020"]         = "Details";
$ary["ITABASEH-MNU-201030"]         = "Display order";
$ary["ITABASEH-MNU-201040"]         = "To control display order";
$ary["ITABASEH-MNU-201050"]         = "Select";
$ary["ITABASEH-MNU-201060"]         = "Allows users to view Symphony execution list (execution history). <br>By clicking “Details”, transit to Symphony execution check menu.";
$ary["ITABASEH-MNU-201070"]         = "Symphony instance ID";
$ary["ITABASEH-MNU-201080"]         = "Symphony execution list";
$ary["ITABASEH-MNU-201090"]         = "Symphony execution list";
$ary["ITABASEH-MNU-201110"]         = "Executing user";
$ary["ITABASEH-MNU-201120"]         = "[Original data] User list";
$ary["ITABASEH-MNU-202010"]         = "Detailed display";
$ary["ITABASEH-MNU-202020"]         = "Details";
$ary["ITABASEH-MNU-202030"]         = "Symphony name";
$ary["ITABASEH-MNU-202040"]         = "[Original data] Symphony class list";
$ary["ITABASEH-MNU-202050"]         = "Operation";
$ary["ITABASEH-MNU-202060"]         = "[Original data] Operation list";
$ary["ITABASEH-MNU-202070"]         = "Operation Name";
$ary["ITABASEH-MNU-202080"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-202090"]         = "Status";
$ary["ITABASEH-MNU-202100"]         = "Pause status";
$ary["ITABASEH-MNU-203010"]         = "The following status states exist.\n
・Unexecuted
・Unexecuted(schedule)
・Executing
・Executing(delayed)
・Normal end
・Emergency stop
・Abend
・Unexpected error
・Schedule canceled";
$ary["ITABASEH-MNU-203020"]         = "Emergency stop flag";
$ary["ITABASEH-MNU-203030"]         = "[Original data] Check Symphony execution";
$ary["ITABASEH-MNU-203040"]         = "Scheduled date/time";
$ary["ITABASEH-MNU-203050"]         = "[Format]YYYY/MM/DD HH:MM";
$ary["ITABASEH-MNU-203060"]         = "Start";
$ary["ITABASEH-MNU-203070"]         = "[Format]YYYY/MM/DD HH:MM";
$ary["ITABASEH-MNU-203080"]         = "End";
$ary["ITABASEH-MNU-203090"]         = "[Format]YYYY/MM/DD HH:MM";
$ary["ITABASEH-MNU-203091"]         = "Notification log";
$ary["ITABASEH-MNU-203092"]         = "Output log file.";
$ary["ITABASEH-MNU-203093"]         = "The following status states exist。\n
・Paused";
$ary["ITABASEH-MNU-203094"]         = "Paused";
$ary["ITABASEH-MNU-203095"]         = "Not issued";
$ary["ITABASEH-MNU-203096"]         = "Issued";
$ary["ITABASEH-MNU-204010"]         = "Display order";
$ary["ITABASEH-MNU-204020"]         = "To control display order";
$ary["ITABASEH-MNU-204030"]         = "Select";
$ary["ITABASEH-MNU-204040"]         = "Description";
$ary["ITABASEH-MNU-204050"]         = "Edit Symphony";
$ary["ITABASEH-MNU-204060"]         = "Symphony class ID";
$ary["ITABASEH-MNU-204070"]         = "Symphony name";
$ary["ITABASEH-MNU-204071"]         = "Symphony role";
$ary["ITABASEH-MNU-204080"]         = "Note";
$ary["ITABASEH-MNU-204090"]         = "Start";
$ary["ITABASEH-MNU-205010"]         = "Display filter";
$ary["ITABASEH-MNU-205020"]         = "Contents";
$ary["ITABASEH-MNU-205030"]         = "Auto filter";
$ary["ITABASEH-MNU-205040"]         = "Filter";
$ary["ITABASEH-MNU-205050"]         = "Clear filter.";
$ary["ITABASEH-MNU-205060"]         = "Scheduling";
$ary["ITABASEH-MNU-205065"]         = "The following Symphony executions are possible. <br>・Immediate execution<br>・Scheduled execution <br>Select the Symphony class ID and Operation ID to execute.
";
$ary["ITABASEH-MNU-205070"]         = "Specify the scheduled date/time in (YYYY/MM/DD HH:MM) 
Immediately execute when blank.";
$ary["ITABASEH-MNU-205080"]         = "Scheduled date/time.";
$ary["ITABASEH-MNU-205090"]         = "Symphony [Filter]";
$ary["ITABASEH-MNU-206010"]         = "Symphony [List]";
$ary["ITABASEH-MNU-206020"]         = "Operation [Filter]";
$ary["ITABASEH-MNU-206030"]         = "Operation [List]";
$ary["ITABASEH-MNU-206040"]         = "Execute Symphony";
$ary["ITABASEH-MNU-206050"]         = "Symphony class ID";
$ary["ITABASEH-MNU-206060"]         = "Symphony name";
$ary["ITABASEH-MNU-206070"]         = "Description";
$ary["ITABASEH-MNU-206080"]         = "Start";
$ary["ITABASEH-MNU-206090"]         = "Operation ID";
$ary["ITABASEH-MNU-207010"]         = "Operation Name";
$ary["ITABASEH-MNU-207020"]         = "Check Symphony execution";
$ary["ITABASEH-MNU-207030"]         = "Symphony instance ID";
$ary["ITABASEH-MNU-207040"]         = "Symphony name";
$ary["ITABASEH-MNU-207050"]         = "Description";
$ary["ITABASEH-MNU-207060"]         = "Start";
$ary["ITABASEH-MNU-207070"]         = "Operation ID";
$ary["ITABASEH-MNU-207080"]         = "Operation Name";
$ary["ITABASEH-MNU-207090"]         = "Status";
$ary["ITABASEH-MNU-208010"]         = "Scheduled date/time";
$ary["ITABASEH-MNU-208020"]         = "Emergency stop command";
$ary["ITABASEH-MNU-209000"]         = "Emergency stop command";
$ary["ITABASEH-MNU-209001"]         = "Movement class ID";
$ary["ITABASEH-MNU-209002"]         = "movement associated with Symphony list";
$ary["ITABASEH-MNU-209003"]         = "Orchestrator ID";
$ary["ITABASEH-MNU-209004"]         = "Movement ID";
$ary["ITABASEH-MNU-209005"]         = "Sequence no";
$ary["ITABASEH-MNU-209006"]         = "Pause";
$ary["ITABASEH-MNU-209007"]         = "Description";
$ary["ITABASEH-MNU-209008"]         = "Symphony class no";
$ary["ITABASEH-MNU-209100"]         = "View of movement instance associated with symphony instance";
$ary["ITABASEH-MNU-209101"]         = "Symphony instance id";
$ary["ITABASEH-MNU-209102"]         = "Movement instance list";
$ary["ITABASEH-MNU-209103"]         = "Movement class no";
$ary["ITABASEH-MNU-209104"]         = "Orchestrator id";
$ary["ITABASEH-MNU-209105"]         = "Pattern id";
$ary["ITABASEH-MNU-209106"]         = "Pattern name";
$ary["ITABASEH-MNU-209107"]         = "Time limit";
$ary["ITABASEH-MNU-209108"]         = "Ansible host designate type id";
$ary["ITABASEH-MNU-209109"]         = "Ansible winrm id";
$ary["ITABASEH-MNU-209110"]         = "DSC retry timeout";
$ary["ITABASEH-MNU-209111"]         = "Movement sequence number";
$ary["ITABASEH-MNU-209112"]         = "Flag of next Pending";
$ary["ITABASEH-MNU-209113"]         = "Description";
$ary["ITABASEH-MNU-209114"]         = "Symphony instance no";
$ary["ITABASEH-MNU-209115"]         = "Execution no";
$ary["ITABASEH-MNU-209116"]         = "Status id";
$ary["ITABASEH-MNU-209117"]         = "Flag of abort received";
$ary["ITABASEH-MNU-209118"]         = "Start time";
$ary["ITABASEH-MNU-209119"]         = "End time";
$ary["ITABASEH-MNU-209120"]         = "Flag to hold release";
$ary["ITABASEH-MNU-209121"]         = "Flag to skip execution";
$ary["ITABASEH-MNU-209122"]         = "Overwrite operation no";
$ary["ITABASEH-MNU-209123"]         = "Overwrite operation name";
$ary["ITABASEH-MNU-209124"]         = "Overwrite operation id";
$ary["ITABASEH-MNU-211000"]         = "Maintenance (view/register/update/discard) can be performed on the menu associated with the substitution value auto-registration setting.";
$ary["ITABASEH-MNU-211001"]         = "Item No.";
$ary["ITABASEH-MNU-211002"]         = "Associated menu";
$ary["ITABASEH-MNU-211003"]         = "Associated menu";
$ary["ITABASEH-MNU-211004"]         = "Menu group";
$ary["ITABASEH-MNU-211005"]         = "ID";
$ary["ITABASEH-MNU-211006"]         = "This item is not subject to updates when registering/updating. (Update menu ID)";
$ary["ITABASEH-MNU-211007"]         = "Name";
$ary["ITABASEH-MNU-211008"]         = "This item is not subject to updates when registering/updating. (Update menu ID)";
$ary["ITABASEH-MNU-211009"]         = "Menu";
$ary["ITABASEH-MNU-211010"]         = "ID";
$ary["ITABASEH-MNU-211011"]         = "This item is not subject to updates when registering/updating. (Menu group: update menu)";
$ary["ITABASEH-MNU-211012"]         = "Name";
$ary["ITABASEH-MNU-211013"]         = "This item is not subject to updates when registering/updating. (Menu group: update menu)";
$ary["ITABASEH-MNU-211014"]         = "Menu group:Menu";
$ary["ITABASEH-MNU-211015"]         = "Sheet type";
$ary["ITABASEH-MNU-211016"]         = "Permission role flg";
$ary["ITABASEH-MNU-212000"]         = "Associated menu table list";
$ary["ITABASEH-MNU-212001"]         = "Item No.";
$ary["ITABASEH-MNU-212002"]         = "Associated menu table list";
$ary["ITABASEH-MNU-212003"]         = "Associated menu table list";
$ary["ITABASEH-MNU-212004"]         = "Menu";
$ary["ITABASEH-MNU-212005"]         = "Table name";
$ary["ITABASEH-MNU-212006"]         = "Primary key";
$ary["ITABASEH-MNU-213000"]         = "Associated menu column list";
$ary["ITABASEH-MNU-213001"]         = "Item No.";
$ary["ITABASEH-MNU-213002"]         = "Associated menu column list";
$ary["ITABASEH-MNU-213003"]         = "Associated menu column list";
$ary["ITABASEH-MNU-213004"]         = "Menu";
$ary["ITABASEH-MNU-213005"]         = "Column";
$ary["ITABASEH-MNU-213006"]         = "Item name";
$ary["ITABASEH-MNU-213007"]         = "Reference table name";
$ary["ITABASEH-MNU-213008"]         = "Reference primary key";
$ary["ITABASEH-MNU-213009"]         = "Reference column name";
$ary["ITABASEH-MNU-213010"]         = "Display order";
$ary["ITABASEH-MNU-213011"]         = "Class";
$ary["ITABASEH-MNU-214001"]         = "Maintenance (view/register/update/discard) can be performed on data deletion information with an operation that has an expired retention period.";
$ary["ITABASEH-MNU-214002"]         = "No";
$ary["ITABASEH-MNU-214003"]         = "Operation delete list";
$ary["ITABASEH-MNU-214004"]         = "Operation delete list";
$ary["ITABASEH-MNU-214005"]         = "Logical deletion days";
$ary["ITABASEH-MNU-214006"]         = "";
$ary["ITABASEH-MNU-214007"]         = "Physical deletion days";
$ary["ITABASEH-MNU-214008"]         = "";
$ary["ITABASEH-MNU-214009"]         = "Table name";
$ary["ITABASEH-MNU-214010"]         = "";
$ary["ITABASEH-MNU-214011"]         = "Primary key column name";
$ary["ITABASEH-MNU-214012"]         = "";
$ary["ITABASEH-MNU-214013"]         = "Operation ID column name";
$ary["ITABASEH-MNU-214014"]         = "";
$ary["ITABASEH-MNU-214015"]         = "Data storage path acquisition SQL";
$ary["ITABASEH-MNU-214016"]         = "";
$ary["ITABASEH-MNU-214017"]         = "History data path 1";
$ary["ITABASEH-MNU-214018"]         = "";
$ary["ITABASEH-MNU-214019"]         = "History data path 2";
$ary["ITABASEH-MNU-214020"]         = "";
$ary["ITABASEH-MNU-214021"]         = "History data path 3";
$ary["ITABASEH-MNU-214022"]         = "";
$ary["ITABASEH-MNU-214023"]         = "History data path 4";
$ary["ITABASEH-MNU-214024"]         = "";
$ary["ITABASEH-MNU-215001"]         = "Maintenance (view/register/update/discard) can be performed on data deletion information with a file that has an expired retention period.";
$ary["ITABASEH-MNU-215002"]         = "No";
$ary["ITABASEH-MNU-215003"]         = "File delete list";
$ary["ITABASEH-MNU-215004"]         = "File delete list";
$ary["ITABASEH-MNU-215005"]         = "Deletion days";
$ary["ITABASEH-MNU-215006"]         = "";
$ary["ITABASEH-MNU-215007"]         = "Directories to delete";
$ary["ITABASEH-MNU-215008"]         = "";
$ary["ITABASEH-MNU-215009"]         = "Files to delete";
$ary["ITABASEH-MNU-215010"]         = "";
$ary["ITABASEH-MNU-215011"]         = "Delete subdirectories";
$ary["ITABASEH-MNU-215012"]         = "";
$ary["ITABASEH-MNU-301010"]         = "AD group judgement";
$ary["ITABASEH-MNU-301020"]         = "Item No.";
$ary["ITABASEH-MNU-301030"]         = "AD group judgement";
$ary["ITABASEH-MNU-301040"]         = "AD group judgement";
$ary["ITABASEH-MNU-301050"]         = "AD group identifier";
$ary["ITABASEH-MNU-301060"]         = "AD group identifier";
$ary["ITABASEH-MNU-301070"]         = "ITA role";
$ary["ITABASEH-MNU-301080"]         = "ITA role";
$ary["ITABASEH-MNU-302010"]         = "AD user judgement";
$ary["ITABASEH-MNU-302020"]         = "Item No.";
$ary["ITABASEH-MNU-302030"]         = "AD user judgement";
$ary["ITABASEH-MNU-302040"]         = "AD user judgement";
$ary["ITABASEH-MNU-302050"]         = "AD user identifier";
$ary["ITABASEH-MNU-302060"]         = "AD user identifier";
$ary["ITABASEH-MNU-302070"]         = "ITA user";
$ary["ITABASEH-MNU-302080"]         = "ITA user";
$ary["ITABASEH-MNU-303000"]         = "Allows users to perform maintenance (view/update) of the interface information on Symphony. <br>This menu should be one record.";
$ary["ITABASEH-MNU-303010"]         = "No";
$ary["ITABASEH-MNU-303020"]         = "Symphony Interface information";
$ary["ITABASEH-MNU-303030"]         = "Symphony Interface information";
$ary["ITABASEH-MNU-303040"]         = "Data relay storage path";
$ary["ITABASEH-MNU-303050"]         = "ITA shared directory for each Symphony instance.";
$ary["ITABASEH-MNU-303060"]         = "Status monitoring cycle (milliseconds)";
$ary["ITABASEH-MNU-303070"]         = "The execution status refresh interval while Symphony is executing. \n Although tuning is required depending on the environment, the recommended value is 3000 milliseconds.";
$ary["ITABASEH-MNU-304000"]         = "Conductor interface information can be maintained (view/update). <br>This menu must be 1 record.";
$ary["ITABASEH-MNU-304010"]         = "No";
$ary["ITABASEH-MNU-304020"]         = "Conductor Interface information";
$ary["ITABASEH-MNU-304030"]         = "Conductor Interface information";
$ary["ITABASEH-MNU-304040"]         = "Data relay storage path";
$ary["ITABASEH-MNU-304050"]         = "A shared directory for each Conductor instance on the ITA side.";
$ary["ITABASEH-MNU-304060"]         = "Condition monitoring cycle (unit: millisecond)";
$ary["ITABASEH-MNU-304070"]         = "Interval to refresh the work status when Conductor is executed. \nTuning is required for each environment, but normally 3000 milliseconds is the recommended value.";
$ary["ITABASEH-MNU-305030"]         = "Allows users to browse the Conductor class. <br> Click \"Details\" to go to the Conductor class edit menu.";
$ary["ITABASEH-MNU-305040"]         = "Conductor class ID";
$ary["ITABASEH-MNU-305050"]         = "Conductor class list";
$ary["ITABASEH-MNU-305060"]         = "Conductor class list";
$ary["ITABASEH-MNU-305070"]         = "Conductor name";
$ary["ITABASEH-MNU-305080"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-305090"]         = "Explanation";
$ary["ITABASEH-MNU-305100"]         = "Editor version";
$ary["ITABASEH-MNU-305110"]         = "Node number";
$ary["ITABASEH-MNU-305120"]         = "Terminal number";
$ary["ITABASEH-MNU-305130"]         = "Edge number";
$ary["ITABASEH-MNU-305140"]         = "Height";
$ary["ITABASEH-MNU-305150"]         = "Type";
$ary["ITABASEH-MNU-305160"]         = "Width";
$ary["ITABASEH-MNU-305170"]         = "Horizontal axis";
$ary["ITABASEH-MNU-305180"]         = "Vertical axis";
$ary["ITABASEH-MNU-305190"]         = "Link line";
$ary["ITABASEH-MNU-305200"]         = "Link node";
$ary["ITABASEH-MNU-305210"]         = "Condition";
$ary["ITABASEH-MNU-305220"]         = "Line id";
$ary["ITABASEH-MNU-305230"]         = "In connection node";
$ary["ITABASEH-MNU-305240"]         = "Out connection node";
$ary["ITABASEH-MNU-305250"]         = "In connection terminal";
$ary["ITABASEH-MNU-305260"]         = "Out connection terminal";
$ary["ITABASEH-MNU-305270"]         = "End Type";
$ary["ITABASEH-MNU-305280"]         = "Notification settings";
$ary["ITABASEH-MNU-306010"]         = "Conductor work list (execution history) can be viewed. <br> Click \"Details\" to go to the Conductor work confirmation menu.";
$ary["ITABASEH-MNU-306020"]         = "Conductor instance ID";
$ary["ITABASEH-MNU-306030"]         = "Conductor list";
$ary["ITABASEH-MNU-306040"]         = "Conductor list";
$ary["ITABASEH-MNU-306050"]         = "Conductor name";
$ary["ITABASEH-MNU-306060"]         = "[Original data] Conductor class list";
$ary["ITABASEH-MNU-306070"]         = "Emergency stop flag";
$ary["ITABASEH-MNU-306080"]         = "[Original data] Conductor work confirmation";
$ary["ITABASEH-MNU-307001"]         = "Allows users to run Conductor regularly according to a schedule. <br>Select the target Conductor, operation, and enter the detailed settings from \"Schedule Settings\".";
$ary["ITABASEH-MNU-307002"]         = "Periodic work execution ID";
$ary["ITABASEH-MNU-307003"]         = "Conductor Regularly execution";
$ary["ITABASEH-MNU-307004"]         = "Conductor Regularly execution";
$ary["ITABASEH-MNU-307005"]         = "Check the work list";
$ary["ITABASEH-MNU-307006"]         = "status";
$ary["ITABASEH-MNU-307007"]         = "The following status states exist.\n
・Preparing\n
・In operation\n
・Done\n
・Inconsistency error\n
・Linking error\n
・Unexpected error\n
・Abolished Conductor\n
・Operation abolition";
$ary["ITABASEH-MNU-307008"]         = "Schedule settings";
$ary["ITABASEH-MNU-307009"]         = "Schedule";
$ary["ITABASEH-MNU-307010"]         = "Next execution date";
$ary["ITABASEH-MNU-307011"]         = "Start date";
$ary["ITABASEH-MNU-307012"]         = "End date";
$ary["ITABASEH-MNU-307013"]         = "period";
$ary["ITABASEH-MNU-307014"]         = "interval";
$ary["ITABASEH-MNU-307015"]         = "Week number";
$ary["ITABASEH-MNU-307016"]         = "Day of the week";
$ary["ITABASEH-MNU-307017"]         = "Day";
$ary["ITABASEH-MNU-307018"]         = "time";
$ary["ITABASEH-MNU-307019"]         = "Work suspension period";
$ary["ITABASEH-MNU-307020"]         = "start";
$ary["ITABASEH-MNU-307021"]         = "End";
$ary["ITABASEH-MNU-307022"]         = "Conductor name";
$ary["ITABASEH-MNU-307023"]         = "[Original data]-Conductor list";
$ary["ITABASEH-MNU-307024"]         = "Operation name";
$ary["ITABASEH-MNU-307025"]         = "[Original data] Operation list";
$ary["ITABASEH-MNU-307026"]         = "Auto-input";
$ary["ITABASEH-MNU-307027"]         = "Execution user";
$ary["ITABASEH-MNU-307028"]         = "User that executed the Conductor (The registered/updated user will be automatically filled in)";
$ary["ITABASEH-MNU-308000"]         = "Allows users to browse the Node associated with the Conductor class.";
$ary["ITABASEH-MNU-308001"]         = "Node class id";
$ary["ITABASEH-MNU-308002"]         = "List of Conductor pegged nodes";
$ary["ITABASEH-MNU-308003"]         = "Node name";
$ary["ITABASEH-MNU-308004"]         = "Node type id";
$ary["ITABASEH-MNU-308005"]         = "Orchestrator id";
$ary["ITABASEH-MNU-308006"]         = "Pattern id";
$ary["ITABASEH-MNU-308007"]         = "Conductor class no";
$ary["ITABASEH-MNU-308008"]         = "Conductor call class no";
$ary["ITABASEH-MNU-308009"]         = "Operation no idbh";
$ary["ITABASEH-MNU-308010"]         = "Skip flag";
$ary["ITABASEH-MNU-308100"]         = "Allows users to browse the Terminal associated with the Node class.";
$ary["ITABASEH-MNU-308101"]         = "Terminal class id";
$ary["ITABASEH-MNU-308102"]         = "Node pegging terminal list";
$ary["ITABASEH-MNU-308104"]         = "Terminal type id";
$ary["ITABASEH-MNU-308105"]         = "Terminal name";
$ary["ITABASEH-MNU-308106"]         = "Node class no";
$ary["ITABASEH-MNU-308107"]         = "Conductor class no";
$ary["ITABASEH-MNU-308108"]         = "Conductor node name";
$ary["ITABASEH-MNU-308109"]         = "Conditional id";
$ary["ITABASEH-MNU-308110"]         = "Case no";
$ary["ITABASEH-MNU-308200"]         = "Allows users to browse the Conductor instance.";
$ary["ITABASEH-MNU-308201"]         = "Conductor instance id";
$ary["ITABASEH-MNU-308202"]         = "Conductor instance list";
$ary["ITABASEH-MNU-308203"]         = "I Conductor class no";
$ary["ITABASEH-MNU-308204"]         = "Operation no uapk";
$ary["ITABASEH-MNU-308205"]         = "Status id";
$ary["ITABASEH-MNU-308206"]         = "Execution user";
$ary["ITABASEH-MNU-308207"]         = "Abort execution flg";
$ary["ITABASEH-MNU-308208"]         = "Conductor ncall flg";
$ary["ITABASEH-MNU-308209"]         = "Conductor caller no";
$ary["ITABASEH-MNU-308210"]         = "Time book";
$ary["ITABASEH-MNU-308211"]         = "Time start";
$ary["ITABASEH-MNU-308212"]         = "Time end";
$ary["ITABASEH-MNU-308300"]         = "Allows users to browse Node instances.";
$ary["ITABASEH-MNU-308301"]         = "Node instance id";
$ary["ITABASEH-MNU-308302"]         = "List of Node instances";
$ary["ITABASEH-MNU-308303"]         = "I node class no";
$ary["ITABASEH-MNU-309001"]         = "Allows users to run the Conductor. <BR>-Immediate execution <BR>-Reserved execution <BR> is possible. <BR>Please select Conductor class ID and operation ID when executing.";
$ary["ITABASEH-MNU-309002"]         = "Conductor [filter]";
$ary["ITABASEH-MNU-309003"]         = "Conductor [List]";
$ary["ITABASEH-MNU-309004"]         = "Conductor execution";
$ary["ITABASEH-MNU-309005"]         = "Conductor class ID";
$ary["ITABASEH-MNU-309006"]         = "Conductor name";
$ary["ITABASEH-MNU-309007"]         = "New";
$ary["ITABASEH-MNU-309008"]         = "Save";
$ary["ITABASEH-MNU-309009"]         = "Read";
$ary["ITABASEH-MNU-309010"]         = "Cancel";
$ary["ITABASEH-MNU-309011"]         = "Redo";
$ary["ITABASEH-MNU-309012"]         = "Delete node";
$ary["ITABASEH-MNU-309013"]         = "The entire display";
$ary["ITABASEH-MNU-309014"]         = "Display reset";
$ary["ITABASEH-MNU-309015"]         = "full screen";
$ary["ITABASEH-MNU-309016"]         = "Full screen release";
$ary["ITABASEH-MNU-309017"]         = "log";
$ary["ITABASEH-MNU-309018"]         = "Registration";
$ary["ITABASEH-MNU-309019"]         = "To edit";
$ary["ITABASEH-MNU-309020"]         = "Diversion";
$ary["ITABASEH-MNU-309021"]         = "update";
$ary["ITABASEH-MNU-309022"]         = "Reload";
$ary["ITABASEH-MNU-309023"]         = "Cancel";
$ary["ITABASEH-MNU-309024"]         = "Execution";
$ary["ITABASEH-MNU-309025"]         = "Cancel reservation";
$ary["ITABASEH-MNU-309026"]         = "Emergency stop";
$ary["ITABASEH-MNU-309027"]         = "Conductor name";
$ary["ITABASEH-MNU-309028"]         = "Input data set (zip)";
$ary["ITABASEH-MNU-309029"]         = "Result data set (zip)";
$ary["ITABASEH-MNU-309030"]         = "download(.zip)";
$ary["ITABASEH-MNU-309031"]         = "Input data set (zip)";
$ary["ITABASEH-MNU-309032"]         = "Result data set (zip)";
$ary["ITABASEH-MNU-309033"]         = "download(.zip)";
$ary["ITABASEH-MNU-309034"]         = "It is populated data set (zip).";
$ary["ITABASEH-MNU-309035"]         = "It is result data set (zip).";
$ary["ITABASEH-MNU-309036"]         = "OK";
$ary["ITABASEH-MNU-309037"]         = "Cancel";
$ary["ITABASEH-MNU-309038"]         = "Align";
$ary["ITABASEH-MNU-309039"]         = "Equal space";
$ary["ITABASEH-MNU-309040"]         = "Align horizontally to the left";
$ary["ITABASEH-MNU-309041"]         = "Align horizontally to the center";
$ary["ITABASEH-MNU-309042"]         = "Align horizontally to the right";
$ary["ITABASEH-MNU-309043"]         = "Align vertically to the top";
$ary["ITABASEH-MNU-309044"]         = "Align vertically to the center";
$ary["ITABASEH-MNU-309045"]         = "Align vertically to the bottom";
$ary["ITABASEH-MNU-309046"]         = "Distribute evenly vertically";
$ary["ITABASEH-MNU-309047"]         = "Distribute evenly horizontally";
$ary["ITABASEH-MNU-309048"]         = "Enter Conductor name.(Maximum length) 256 bytes";
$ary["ITABASEH-MNU-309049"]         = "Enter description (Maximum length) 8192 bytes";
$ary["ITABASEH-MNU-309050"]         = "Print";
$ary["ITABASEH-MNU-309051"]         = "Menu group select";
$ary["ITABASEH-MNU-309052"]         = "Relation";
$ary["ITABASEH-MNU-309053"]         = "Mouse wheel";
$ary["ITABASEH-MNU-309054"]         = "Enlargement / Reduction the screen";
$ary["ITABASEH-MNU-309055"]         = "Mouse right drag";
$ary["ITABASEH-MNU-309056"]         = "Screen movement";
$ary["ITABASEH-MNU-309057"]         = "Mouse left click";
$ary["ITABASEH-MNU-309058"]         = "Node select / Delete connection line";
$ary["ITABASEH-MNU-309059"]         = "Mouse left drag";
$ary["ITABASEH-MNU-309060"]         = "Node move / Node multiple select";
$ary["ITABASEH-MNU-309061"]         = "Node select";
$ary["ITABASEH-MNU-309062"]         = "Node select / Check execution status";
$ary["ITABASEH-MNU-310000"]         = "Compare list can be viewed. <br> The following can be used as a Compare target menu. <br> -Parameter Sheet(Host/Operation) ";
$ary["ITABASEH-MNU-310001"]         = "Compare list";
$ary["ITABASEH-MNU-310002"]         = "Compare name";
$ary["ITABASEH-MNU-310003"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-310004"]         = "Compare target menu 1";
$ary["ITABASEH-MNU-310005"]         = "Compare target menu 2";
$ary["ITABASEH-MNU-310006"]         = "[Original data] Linked menu";
$ary["ITABASEH-MNU-310007"]         = "Match all cases";
$ary["ITABASEH-MNU-310008"]         = "If the item names in the Compare target menu match exactly, select ●.
If they do not match, you need to set the Compare definition details. ";
$ary["ITABASEH-MNU-310100"]         = "Compare details can be viewed . <br> Set the Compare target item (display item name, link of Compare target item).";
$ary["ITABASEH-MNU-310101"]         = "Compare details";
$ary["ITABASEH-MNU-310102"]         = "Compare name";
$ary["ITABASEH-MNU-310103"]         = "[Original data] Compare list";
$ary["ITABASEH-MNU-310104"]         = "Display item name";
$ary["ITABASEH-MNU-310105"]         = "[Maximum length] 256 bytes";
$ary["ITABASEH-MNU-310106"]         = "Target column 1";
$ary["ITABASEH-MNU-310107"]         = "Target column 2";
$ary["ITABASEH-MNU-310108"]         = "[Original data] Linked menu column management";
$ary["ITABASEH-MNU-310109"]         = "Please select a menu";
$ary["ITABASEH-MNU-310110"]         = "Display order";
$ary["ITABASEH-MNU-310111"]         = "Display order when Compare executed";
$ary["ITABASEH-MNU-310200"]         = "Compare execution the parameter sheets based on the information set in the Compare list. <br> The parameters of compare execution are as follows. * If you do not specify the base date, it will be the latest base date data. <br> -Compare list <br> -Base date 1 (specify the base date of the Compare target menu 1) <br> -Base date 2 (specify the base date of the Compare target menu 2) <br> -Target host";
$ary["ITABASEH-MNU-310201"]         = "Compare execution";
$ary["ITABASEH-MNU-310202"]         = "Compare list:";
$ary["ITABASEH-MNU-310203"]         = "Base date 1:";
$ary["ITABASEH-MNU-310204"]         = "Base date 2:";
$ary["ITABASEH-MNU-310205"]         = "Target host:";
$ary["ITABASEH-MNU-310206"]         = "Choice";
$ary["ITABASEH-MNU-310207"]         = "Compare";
$ary["ITABASEH-MNU-310208"]         = "Compare result";
$ary["ITABASEH-MNU-310209"]         = "* The Compare execution result of the parameter sheet is output here.";
$ary["ITABASEH-MNU-310210"]         = "Compare item number";
$ary["ITABASEH-MNU-310211"]         = "Result";
$ary["ITABASEH-MNU-310212"]         = "Hostname";
$ary["ITABASEH-MNU-310213"]         = "Menu name";
$ary["ITABASEH-MNU-310214"]         = "No";
$ary["ITABASEH-MNU-310215"]         = "Operation name";
$ary["ITABASEH-MNU-310216"]         = "Base date";
$ary["ITABASEH-MNU-310217"]         = "Difference";
$ary["ITABASEH-MNU-310218"]         = "Failed to exchange ID. ({})";
$ary["ITABASEH-MNU-310219"]         = "Excel output";
$ary["ITABASEH-MNU-310220"]         = "CSV output";
$ary["ITABASEH-MNU-310221"]         = "There is no data to compare under the specified conditions";
$ary["ITABASEH-MNU-310222"]         = "Arial";
$ary["ITABASEH-MNU-310223"]         = "No Difference";
$ary["ITABASEH-MNU-310224"]         = "Failed to exchange ID. (";
$ary["ITABASEH-MNU-310225"]         = "Output:";
$ary["ITABASEH-MNU-310226"]         = "ALL";
$ary["ITABASEH-MNU-310227"]         = "Difference Only";
$ary["ITABASEH-MNU-311000"]         = 'PUse PHPs cURL function to handle notifications.<br>
■Notification setting  using Webhook<br>
▼Example: Teams / Slack　 <br>
&nbsp -Notification destination (CURLOPT_URL) Example: <br>
&nbsp&nbsp&nbsp&nbspEnter the service webhook URL<br>
&nbsp -Header (CURLOPT_HTTPHEADER)) Example: <br>
&nbsp&nbsp&nbsp&nbsp[ "Content-Type: application/json" ]<br>
&nbsp -Message (CURLOPT_POSTFIELDS)) Example:: <br>
&nbsp&nbsp&nbsp&nbsp{"text": "Notification name：__NOTICE_NAME__,  &lt;br&gt;Conductor name: __CONDUCTOR_NAME__,  &lt;br&gt; Conductor instance ID:__CONDUCTOR_INSTANCE_ID__,&lt;br&gt; status: __STATUS_NAME__, &lt;br&gt; Work confirmation URL : __JUMP_URL__, &lt;br&gt; "}<br><br>

※For the input format of Message (CURLOPT_POSTFIELDS) and the notation method of line breaks, refer to Sending a message by Webhook of each service.<br>
<br>
■About each setting item<br>
<table>
    <thead>
        <tr><td>&nbsp</td><td>Input items</td><td>:</td><td>discription</td></tr>
    </thead>
    <tbody>
    <tr><td>&nbsp</td><td>Notification destination (CURLOPT_URL) </td><td>:</td><td>Please enter the URL of the notification destination.</td></tr>
    <tr><td>&nbsp</td><td>Header (CURLOPT_HTTPHEADER) </td><td>:</td><td>Please enter the header</td></tr>
    <tr><td>&nbsp</td><td>Message (CURLOPT_POSTFIELDS) </td><td>:</td><td>Please enter the content of the notification</td></tr>
    <tr><td>&nbsp</td><td>PROXY / URL(CURLOPT_PROXY) </td><td>:</td><td>If you need to set PROXY, please enter the URL.</td></tr>
    <tr><td>&nbsp</td><td>PROXY / PORT(CURLOPT_PROXYPORT) </td><td>:</td><td>If you need to set PROXY, enter PORT.</td></tr>
    <tr><td>&nbsp</td><td>Work confirmation URL (FQDN)</td><td>:</td><td>Enter the FQDN to be used in the reserved variable of the work confirmation URL.</td></tr>
    <tr><td>&nbsp</td><td>Other </td><td>:</td><td>Please enter in JSON format.<br>（Only the options corresponding to curl_setopt () are available.<br>For more information, see PHP cURL function.</td></tr>
    </tbody>
</table>
<br>
■The following reserved variables are available in Message (CURLOPT_POSTFIELDS).<br>
<table>
    <thead>
        <tr><td>&nbsp</td><td>Reserved variables</td><td>:</td><td>Item name</td></tr>
    </thead>
    <tbody>
        <tr><td>&nbsp</td><td>__CONDUCTOR_INSTANCE_ID__ </td><td>:</td><td>Conductor instance ID </td></tr>
        <tr><td>&nbsp</td><td>__CONDUCTOR_NAME__ </td><td>:</td><td>Conductor name </td></tr>
        <tr><td>&nbsp</td><td>__OPERATION_ID__ </td><td>:</td><td>Operation ID </td></tr>
        <tr><td>&nbsp</td><td>__OPERATION_NAME__ </td><td>:</td><td>Operation name</td></tr>
        <tr><td>&nbsp</td><td>__STATUS_ID__ </td><td>:</td><td>Status ID </td></tr>
        <tr><td>&nbsp</td><td>__STATUS_NAME__ </td><td>:</td><td>Status name </td></tr>
        <tr><td>&nbsp</td><td>__EXECUTION_USER__ </td><td>:</td><td>Execution user </td></tr>
        <tr><td>&nbsp</td><td>__TIME_BOOK__ </td><td>:</td><td>Scheduled date/time </td></tr>
        <tr><td>&nbsp</td><td>__TIME_START__ </td><td>:</td><td>Start </td></tr>
        <tr><td>&nbsp</td><td>__TIME_END__ </td><td>:</td><td>End </td></tr>
        <tr><td>&nbsp</td><td>__JUMP_URL__ </td><td>:</td><td>Work confirmation URL </td></tr>
        <tr><td>&nbsp</td><td>__NOTICE_NAME__ </td><td>:</td><td>Notification name </td></tr>
    </tbody>
</table>
';
$ary["ITABASEH-MNU-311001"]         = "Conductor notification definition";
$ary["ITABASEH-MNU-311002"]         = "Notification name";
$ary["ITABASEH-MNU-311003"]         = "[Maximum length] 128 bytes";
$ary["ITABASEH-MNU-311004"]         = "HTTP Request Options";
$ary["ITABASEH-MNU-311005"]         = "Notification destination (CURLOPT_URL)";
$ary["ITABASEH-MNU-311006"]         = "The URL of the notification destination.";
$ary["ITABASEH-MNU-311007"]         = "Header (CURLOPT_HTTPHEADER)";
$ary["ITABASEH-MNU-311008"]         = "Enter HTTP header fields in JSON format.";
$ary["ITABASEH-MNU-311009"]         = "Message (CURLOPT_POSTFIELDS)";
$ary["ITABASEH-MNU-311010"]         = "Enter according to the specifications of the service to be notified.";
$ary["ITABASEH-MNU-311011"]         = "PROXY";
$ary["ITABASEH-MNU-311012"]         = "URL (CURLOPT_PROXY)";
$ary["ITABASEH-MNU-311013"]         = "If you need to set PROXY, please enter the URL.";
$ary["ITABASEH-MNU-311014"]         = "PORT (CURLOPT_PROXYPORT)";
$ary["ITABASEH-MNU-311015"]         = "If you need to set PROXY, enter PORT.";
$ary["ITABASEH-MNU-311016"]         = "Work confirmation URL (FQDN)";
$ary["ITABASEH-MNU-311017"]         = "Use in the reserved variable of the work confirmation URL, enter the FQDN. \nExample:\nhttp://<FQDN>\nhttps://<FQDN>";
$ary["ITABASEH-MNU-311018"]         = "Other";
$ary["ITABASEH-MNU-311019"]         = 'Please enter in JSON format. \nAs for the available options, the one corresponding to the curl_setopt () option is available. See PHPs cURL function. Example: {"CURLOPT_CONNECTTIMEOUT": 10}';
$ary["ITABASEH-MNU-311020"]         =  "Deterrence period";
$ary["ITABASEH-MNU-311021"]         =  "Start date and time";
$ary["ITABASEH-MNU-311022"]         =  "When the Conductor work is executed, the notification is suppressed if it is after the start date and time.";
$ary["ITABASEH-MNU-311023"]         =  "End date and time";
$ary["ITABASEH-MNU-311024"]         =  "When the Conductor work is executed, the notification is suppressed if it is before the end date and time.";

$ary["ITABASEH-MNU-900001"]         = "Export";
$ary["ITABASEH-MNU-900002"]         = "Upload";
$ary["ITABASEH-MNU-900003"]         = "Import";
$ary["ITABASEH-MNU-900004"]         = "Upload a file you want to import";
$ary["ITABASEH-MNU-900005"]         = "Upload file";
$ary["ITABASEH-MNU-900006"]         = "Export menu";
$ary["ITABASEH-MNU-900007"]         = "Import menu";
$ary["ITABASEH-MNU-900008"]         = "Export・Import data list";
$ary["ITABASEH-MNU-900009"]         = "Import process has registred.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900010"]         = "The following functions are provided.<br>・View of exported or imported data list";
$ary["ITABASEH-MNU-900011"]         = "The following functions are provided.<br>・Upload import data<br>&nbsp;&nbsp;&nbsp;&nbsp;Upload the kym file containing the compressed data to import<br><br>・Import data<br>&nbsp;&nbsp;&nbsp;&nbsp;A list of importable menus is displayed.<br>&nbsp;&nbsp;&nbsp;&nbsp;Select the menus to import and click the import button.<br>&nbsp;&nbsp;&nbsp;&nbsp;The status of imported data can be checked from the \"Export/Import data list\".";
$ary["ITABASEH-MNU-900012"]         = "The following functions are provided.<br>&nbsp;&nbsp;&nbsp;・Menu export<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select the menu that you want to export data from and click the \"Export\" button.<br><br>Mode<br>&nbsp;&nbsp;&nbsp;・Environment migration<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports all the data of the selected menu and replaces the data in the import destination.<br><br>&nbsp;&nbsp;&nbsp;・Time specification<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports the data at the specified time.<br>If the ID is the same as the data of the important destination, the exported data has priority over the imported data.<br><br>Abolition data<br>&nbsp;&nbsp;&nbsp;・All records<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports all records<br>&nbsp;&nbsp;&nbsp;・Exclude discarded records<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports without discarded records";
$ary["ITABASEH-MNU-900013"]         = "Execution No.";
$ary["ITABASEH-MNU-900014"]         = "Status";
$ary["ITABASEH-MNU-900015"]         = "File name";
$ary["ITABASEH-MNU-900016"]         = "The following states exist for status.\n・Unexecuted\n・Executing\n・Completed\n・Completed (error)";
$ary["ITABASEH-MNU-900017"]         = "Cannot be edited (auto-registration)";
$ary["ITABASEH-MNU-900018"]         = "All menus";
$ary["ITABASEH-MNU-900019"]         = "Import (Exclude discarded records)";
$ary["ITABASEH-MNU-900020"]         = "Import type";
$ary["ITABASEH-MNU-900021"]         = "The following states exist for Import type.\n・All records\n・Exclude discarded records";
$ary["ITABASEH-MNU-900022"]         = "Execution type";
$ary["ITABASEH-MNU-900023"]         = "The following states exist for Execution type.\n・Export\n・Import";
$ary["ITABASEH-MNU-900024"]         = "The data export process has been registred.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900025"]         = "Mode";
$ary["ITABASEH-MNU-900026"]         = "Environment migration";
$ary["ITABASEH-MNU-900027"]         = "Time specification";
$ary["ITABASEH-MNU-900028"]         = "Abolition data";
$ary["ITABASEH-MNU-900029"]         = "All records";
$ary["ITABASEH-MNU-900030"]         = "Exclude discarded records";
$ary["ITABASEH-MNU-900031"]         = "The following modes exist.\n・Override\n・Add";
$ary["ITABASEH-MNU-900032"]         = "The following abolition data exists.\n・All records\n・Exclude discarded records";
$ary["ITABASEH-MNU-900033"]         = "Specified time";
$ary["ITABASEH-MNU-900034"]         = "If the mode is \"Time specification\", records after the specified time will be exported/imported.";
$ary["ITABASEH-MNU-900035"]         = "Execution user";
$ary["ITABASEH-MNU-900036"]         = "The user who executed the Export/Import process will be displayed.";
$ary["ITABASEH-MNU-900051"]         = "The following functions are provided.<br>&nbsp;&nbsp;・Export Symphony/Operation data<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select Symphony/Operation you want to export and click the export button.";
$ary["ITABASEH-MNU-900052"]         = "The following functions are provided.<br>・Upload import Symphony/Operation data<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Upload the kym2 file containing the compressed data to import<br><br>・Import Symphony/Operation data<br>&nbsp;&nbsp;&nbsp;&nbsp;A list of importable Symphony/Operation is displayed.<br>&nbsp;&nbsp;&nbsp;&nbsp;Select the Symphony/Operation to import and click the import button.<br>&nbsp;&nbsp;&nbsp;&nbsp;The status of imported data can be checked from the \"Export・Import Symphony/Operation list\".";
$ary["ITABASEH-MNU-900053"]         = "All Operations";
$ary["ITABASEH-MNU-900054"]         = "All Symphonies";
$ary["ITABASEH-MNU-900055"]         = "The following functions are provided.<br>・View of exported or imported Symphony/Operation list";
$ary["ITABASEH-MNU-900056"]         = "Export・Import Symphony/Operation list";
$ary["ITABASEH-MNU-900057"]         = "Export Symphony/Operation";
$ary["ITABASEH-MNU-900058"]         = "The Symphony/Operation export process has been registred.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900059"]         = "Import Symphony/Operation";
$ary["ITABASEH-MNU-900060"]         = "The Symphony/Operation import process has registred.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900100"]         = "Orchestrator ID";
$ary["ITABASEH-MNU-900101"]         = "Movement ID";
$ary["ITABASEH-MNU-900102"]         = "pause(OFF:/ON:checkedValue)";
$ary["ITABASEH-MNU-900103"]         = "Description";
$ary["ITABASEH-MNU-900104"]         = "Operation ID (Specified individually)";
$ary["ITABASEH-MNU-910001"]         = "version";
$ary["ITABASEH-MNU-910002"]         = "driver";
$ary["ITABASEH-MNU-910003"]         = "version";
$ary["ITABASEH-MNU-910004"]         = "Exastro IT Automation Version";
$ary["ITABASEH-MNU-910005"]         = "Installed Driver";
$ary["ITABASEH-MNU-920001"]         = "Symphony can be run periodically according to a schedule.
<br>Select the target symphony, operation, and enter detailed settings from \"Schedule Setting\".";
$ary["ITABASEH-MNU-920002"]         = "RegularlyID";
$ary["ITABASEH-MNU-920003"]         = "Symphony Regularly execution";
$ary["ITABASEH-MNU-920004"]         = "Symphony Regularly execution";
$ary["ITABASEH-MNU-920005"]         = "RegularlyWorkList";
$ary["ITABASEH-MNU-920006"]         = "Status";
$ary["ITABASEH-MNU-920007"]         = "The following status states exist.\n
・In preparation\n
・In operation\n
・Completed\n
・Mismatch error\n
・Linking error\n
・Unexpected error\n
・Symphony discard\n
・Operation discard";
$ary["ITABASEH-MNU-920008"]         = "Schedule setting";
$ary["ITABASEH-MNU-920009"]         = "Schedule";
$ary["ITABASEH-MNU-920010"]         = "Next execution date";
$ary["ITABASEH-MNU-920011"]         = "Start date";
$ary["ITABASEH-MNU-920012"]         = "End date";
$ary["ITABASEH-MNU-920013"]         = "Period";
$ary["ITABASEH-MNU-920014"]         = "Interval";
$ary["ITABASEH-MNU-920015"]         = "Week number";
$ary["ITABASEH-MNU-920016"]         = "Day of weeek";
$ary["ITABASEH-MNU-920017"]         = "Day";
$ary["ITABASEH-MNU-920018"]         = "Time";
$ary["ITABASEH-MNU-920019"]         = "Work suspension period";
$ary["ITABASEH-MNU-920020"]         = "Start";
$ary["ITABASEH-MNU-920021"]         = "End";
$ary["ITABASEH-MNU-920022"]         = "Symphony name";
$ary["ITABASEH-MNU-920023"]         = "[Original data] Symphony class list";
$ary["ITABASEH-MNU-920024"]         = "Operation";
$ary["ITABASEH-MNU-920025"]         = "[Original data] Operation list";
$ary["ITABASEH-MNU-920026"]         = "Auto-input";
$ary["ITABASEH-MNU-920027"]         = "Execution user";
$ary["ITABASEH-MNU-920028"]         = "User to run Symphony (The registered/updated user will be automatically filled in)";
$ary["ITABASEH-MNU-2100000329_1"]   = "The following functions are provided.<br>&nbsp;&nbsp;&nbsp;・Excel Bulk Export<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Select the menu that you want to export data from and click the \"Export\" button.<br>Abolition data<br>&nbsp;&nbsp;&nbsp;・All records<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports all records<br>&nbsp;&nbsp;&nbsp;・Exclude discarded records<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports without discarded records<br>&nbsp;&nbsp;&nbsp;・Only discarded records<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Exports only discarded records";
$ary["ITABASEH-MNU-2100000329_2"]   = "Excel Bulk Export";
$ary["ITABASEH-MNU-2100000329_3"]   = "Excel batch export process accepted.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-2100000329_4"]   = "All records";
$ary["ITABASEH-MNU-2100000329_5"]   = "Exclude discarded records";
$ary["ITABASEH-MNU-2100000329_6"]   = "Only discarded records";
$ary["ITABASEH-MNU-2100000330_1"]   = "Import target";
$ary["ITABASEH-MNU-2100000330_2"]   = "Menu group";
$ary["ITABASEH-MNU-2100000330_3"]   = "Menu";
$ary["ITABASEH-MNU-2100000330_4"]   = "Menu Id";
$ary["ITABASEH-MNU-2100000330_5"]   = "File name";
$ary["ITABASEH-MNU-2100000330_6"]   = "Error";
$ary["ITABASEH-MNU-2100000330_7"]   = "The following functions are provided.<br>・Upload import data<br>&nbsp;&nbsp;&nbsp;&nbsp;Upload the zip file containing the compressed data to import<br><br>・Excel Bulk Import<br>&nbsp;&nbsp;&nbsp;&nbsp;A list of importable menus is displayed.<br>&nbsp;&nbsp;&nbsp;&nbsp;Select the menus to import and click the import button.<br>&nbsp;&nbsp;&nbsp;&nbsp;The status of imported data can be checked from the \"Excel Bulk Export/Import list\".";
$ary["ITABASEH-MNU-2100000330_8"]   = "Are you sure you want to import?\n※The import operation will follow the order listed in MENU_LIST.txt\nIf you are importing multiple menu data, make sure that you are aware about the data's consistency before executing.";
$ary["ITABASEH-MNU-2100000330_9"]   = "Excel Bulk Import";
$ary["ITABASEH-MNU-2100000330_10"]  = "Excel batch import process accepted.<br>Execution No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-2100000331_1"]   = "The following functions are provided.<br>・View of Excel Bulk Export・Import list";
$ary["ITABASEH-MNU-2100000331_2"]   = "Excel Bulk Export・Import list";
$ary["ITABASEH-MNU-2100000331_3"]   = "The following abolition data exists.\n・All records\n・Exclude discarded records\n・Only discarded records";
$ary["ITABASEH-MNU-2100000331_4"]   = "Execution user";
$ary["ITABASEH-MNU-2100000331_5"]   = "The user who executed the Export/Import process will be displayed.";
?>
