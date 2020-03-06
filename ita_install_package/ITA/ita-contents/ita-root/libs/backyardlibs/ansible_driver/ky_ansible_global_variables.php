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
    //////////////////////////////////////////////////////////////////////
    //
    //  【処理概要】
    //    共通変数のglobal宣言
    //
    //////////////////////////////////////////////////////////////////////
    // /libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php" );
    $vg_legacy_playbook_contents_dir  = $root_dir_path . "/uploadfiles/2100020104/PLAYBOOK_MATTER_FILE";
    $vg_pioneer_playbook_contents_dir = $root_dir_path . "/uploadfiles/2100020205/DIALOG_MATTER_FILE";
    $vg_copy_contents_dir = $root_dir_path . "/uploadfiles/2100040703/CONTENTS_FILE";
    $vg_template_contents_dir = $root_dir_path . "/uploadfiles/2100040704/ANS_TEMPLATE_FILE";

    // /libs/backyardlibs/ansible_driver/ky_ driver _setenv.php" );
    global $root_dir_path;
    global $vg_driver_id;

    global $vg_OrchestratorSubId;
    global $vg_OrchestratorSubId_dir;

    global $vg_info_table_name;
    global $vg_exe_ins_msg_table_name;
    global $vg_exe_ins_msg_table_jnl_name;

    global $vg_ansible_vars_masterDB;
    global $vg_ansible_vars_assignDB;
    global $vg_ansible_pattern_vars_linkDB;
    global $vg_ansible_master_fileDB;
    global $vg_ansible_master_file_pkeyITEM;
    global $vg_ansible_master_file_nameITEM;
    global $vg_ansible_pho_linkDB;
    global $vg_exe_ins_msg_table_jnl_seq;
    global $vg_ansible_pattern_linkDB;
    global $vg_ansible_role_packageDB;
    global $vg_ansible_roleDB;
    global $vg_ansible_role_varsDB;

    global $zip_temp_save_dir;
    global $vg_exe_ins_input_file_dir;
    global $vg_exe_ins_result_file_dir;

    global $vg_tower_driver_type;
    global $vg_tower_driver_id;
    global $vg_tower_driver_name;
    global $vg_parent_playbook_name;

    global $vg_log_driver_name;

?>
