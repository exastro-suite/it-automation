#!/usr/bin/python
# -*- coding: utf-8 -*-
#   Copyright 2019 NEC Corporation
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#       http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
######################################################################
##
##  【概要】
##      pioneer用 対話ファイル実行スクリプト
##      python2とpython3に対応
##
##  【特記事項】
##      <<引数>>
##       username:           ユーザー名
##       protocol:           プロトコル
##       inventory_hostname: 接続先ホスト(IP/ホスト名)
##       exec_file:          対話ファイル名
##       grep_shell_dir:     文字列検索用デフォルトshell ルートディレトクリ
##       log_file_dir:       プライベートログ出力先ディレトクリ
##       ssh_key_file:       SSH秘密鍵ファイル
##       extra_args:         ssh/telnet接続時の追加パラメータ
##      <<返却値>>
##       なし
##
######################################################################
DOCUMENTATION = '''
---
module: pioneer_module
shotr_description: hoge
description:
  - hogehoge
option:
  username:
    required: false(default root)
    descriptio:
      - login user name
  protocol:
    required: false(default ssh)
    descriptio:
      - connection protocol(ssh or telnet)
  inventory_hostname:
    required: true
    description:
      - ssh(or telnet) target
  exec_file:
    required: true
    description:
      - expect/sendline list by yaml
  grep_shell_dir:
    required: true
    description:
      - default grep shell path
  extra_args:
    required: true
    description:
      - ssh/telnet extra args
  ssh_key_file:
    required: true
    description:
      - ssh key file
  log_file_dir:
    required: true
    description:
      - private log path
author: Hiroyuki Seike
'''

import yaml
import pexpect
import sys
import traceback
import datetime
import signal
import subprocess
import os
import re
from collections import defaultdict
from collections import OrderedDict
import binascii
import codecs

from ansible.module_utils.basic import *

import base64
password  = 'undefine'
output_password = '********'
log_file_name = ''
exit_dict = {}

# log format
when_log_str = "%s: [%s] %s"
prompt_log_str = "prompt: [%s]"
command_log_str = "command: [%s]"
execute_when_log_str = "%s: [%s]"

exec_log = [] 
host_name=''

register_used_flg = 0

class SignalReceive(Exception): pass

def signal_handle(signum,frame):
  raise SignalReceive('Urgency stop (signal=' + str(signum) + ')')

class AnsibleModule_exit(Exception): pass

def main():

  # python2の場合にデフォルト文字コードをUTF-8に変更する。
  import sys
  if sys.version_info.major == 2:
    reload(sys)
    sys.setdefaultencoding('utf-8')

  global log_file_name
  global password
  global output_password
  module = AnsibleModule(
    argument_spec = dict(
      username=dict(required=True),
      protocol=dict(required=True),
      inventory_hostname=dict(required=True),
      host_vars_file=dict(required=True),
      exec_file=dict(required=False, default=''),
      grep_shell_dir=dict(required=True),
      log_file_dir=dict(required=True),
      ssh_key_file=dict(required=True),
      extra_args=dict(required=True),
    ),
#  ドライランモード許可設定
    supports_check_mode=True
  )
  signal.signal(signal.SIGTERM,signal_handle)
  protocol = module.params['protocol']
  user_name = module.params['username']
  host_name = module.params['inventory_hostname']
  host_vars_file = module.params['host_vars_file']
  shell_name = module.params['grep_shell_dir']
  shell_name = shell_name + '/ky_pionner_grep_side_Ansible.sh'
  log_file_name = module.params['log_file_dir'] + '/private.log'
  ssh_key_file   = module.params['ssh_key_file']
  extra_args = module.params['extra_args']
  if not module.params['exec_file']:
    #########################################################
    # normal exit
    #########################################################
    private_fail_json(obj=module,msg='exec_file no found fail exit')
  config = yaml.load(open(module.params['exec_file']).read())

  private_log_output(log_file_name,host_name,'python version:' + str(sys.version))
  private_log_output(log_file_name,host_name,'default encoding:' + str(sys.getdefaultencoding()))

  private_log_output(log_file_name,host_name,str(config))

  timeout = config['conf']['timeout']
  exec_cmd=''
  exec_name=''
  expect_cmd=''
  expect_name=''
  parameter_cmd=''
  shell_cmd=''
  stdout_file=''
  success_exit=''
  ignore_errors=''
  register_cmd=''
  register_name=''
  with_items_count=0

  # ファイルの順序通りに読み込みさせる
  yaml.add_constructor(yaml.resolver.BaseResolver.DEFAULT_MAPPING_TAG,lambda loader, node: OrderedDict(loader.construct_pairs(node)))

  try:
    # プロセスIDをファイルに出力
    pid = os.getpid()
    pid_file_name = module.params['log_file_dir'] + "/pioneer." + str(pid)
    fp = open(pid_file_name, "w")
    fp.write(str(pid))
    fp.close()

    # パスワードをデコードする
    host_vars = yaml.load(open(module.params['host_vars_file']).read())
    if '__loginpassword__' in host_vars:
      password = host_vars['__loginpassword__']
      password = base64.b64decode(codecs.encode(password, "rot-13"))
      password = password.decode('utf-8','replace')
      
    # ログに表示するパスワード
    output_password = '********'

    # telnet/sshに接続時の追加パラメータ適用

    # SSH接続でSSH秘密鍵ファイルが設定されているか判定
    if protocol == "ssh":
      append_param = " -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null "
    else:
      append_param = "" 

    if ssh_key_file != "__undefinesymbol__" and protocol == "ssh":
      append_param = append_param + " -o 'IdentityFile=\"" + ssh_key_file + "\"' "

    # EXTRA_ARGS(SSH/TELNET)が設定されているか判定
    if extra_args != "__undefinesymbol__":
      append_param = append_param  + " " + extra_args

    if user_name == "__undefinesymbol__":
      exec_cmd = protocol + " " + host_name + " " + append_param            
    else:
      exec_cmd = protocol + " " + host_name + " -l " + user_name  + " " + append_param
    exec_name = 'remote login:[' + exec_cmd + ']'

    private_log_output(log_file_name,host_name,exec_name)
    exec_log_output(exec_name)

    # python2ではencodingは機能しない
    #p = pexpect.spawn(exec_cmd,  encoding='utf-8',codec_errors='replace')
    p = pexpect.spawn(exec_cmd)

    # ドライランモードを退避しタイムアウト値を5秒にする。
    if module.check_mode:
      chk_mode = ':exit check mode'
      timeout  = 5;
    else:
      chk_mode = ''


    # exec_list read
    for input in config['exec_list']:

      exec_cmd=''
      exec_name=''
      expect_cmd=''
      expect_name=''
      parameter_cmd=''
      shell_cmd=''
      stdout_file=''
      success_exit=str(False)
      ignore_errors=str(False)
      when_cmd = {}
      with_cmd = {}
      tmp = {}
      tmp2 = {}
      temp3 = {}
      skip_flg = 0
      with_items_flg = 0
      exec_when_flg = 0
      with_file = {}
      with_def = {}
      def_cmd = {}
      failed_cmd = {}
      register_temp = ''
      max_count = 0
      exec_when_cmd = {}
      continue_flg = 0
      register_flg = 0
      global register_used_flg
      register_used_flg = 0
      register_tmp_name = ''
      count = 0
      timeout2 = config['conf']['timeout']
      prompt_count2 = 0
      prompt_count = 0
      timeout_count = 0
      prompt_num = 256
      timeout_num = 256

      # log output
      private_log_output(log_file_name,host_name,'=== execute command =================================================')
      input_cmd = password_replace(output_password,str(input))
      private_log_output(log_file_name,host_name,'execute command:' + input_cmd)
      private_log_output(log_file_name,host_name,'=====================================================================')
      exec_log_output('=== execute command: =================================================')
      exec_log_output('execute command: ' + input_cmd)
      exec_log_output('======================================================================')

      # expect command ?
      if 'expect' in input:
        for cmd in input:
          if 'expect' == cmd:
            expect_cmd = unicode2encode(input[cmd])
            expect_name = 'expect command:(' + expect_cmd + ')'

          elif 'exec' == cmd:
            exec_cmd = unicode2encode(input[cmd])
            exec_name = 'exec command:(' + exec_cmd + ')'
          else:
            # error log
            logstr = 'command(expect->' + cmd + ') not service'
            exec_log_output(logstr)
            private_log_output(log_file_name,host_name,logstr)
             
            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

        ####################################################
        # expect command execute
        ####################################################
        expect_prompt(p,log_file_name,host_name,expect_cmd,timeout)

        # ドライランモードの場合は接続確認したら終了
        if module.check_mode:
          private_exit_json(obj=module,msg=host_name + chk_mode,changed=False, exec_log=exec_log)

        ####################################################
        # exec command execute
        ####################################################
        exec_command(p,log_file_name,host_name,exec_cmd)

        ####################################################
        # read line
        ####################################################
        p.readline()

      # state command ?
      elif 'state' in input:
        # default shell set

        for cmd in input:
          if 'state' == cmd:
            exec_cmd = str(input[cmd])
            exec_name = 'state command:(' + exec_cmd + ')'
          elif 'prompt' == cmd:
            expect_cmd = str(input[cmd])
            expect_name = 'prompt:(' + expect_cmd + ')'
          elif 'parameter' == cmd:
            idx = 0
            max = len(input[cmd])
            while idx < max:
              parameter_cmd = parameter_cmd + input[cmd][idx] + ' '
              idx = idx + 1
          elif 'shell' == cmd:
            shell_cmd = str(input[cmd])
          elif 'stdout_file' == cmd:
            stdout_file = str(input[cmd])
          elif 'success_exit' == cmd:
            success_exit = str(input[cmd])
            if success_exit != str(False) and success_exit != str(True):
              logstr = 'success_exit=(' + str(input[cmd]) + '): only yes or no set'
              exec_log_output(logstr)
              private_log_output(log_file_name,host_name,logstr)

              #########################################################
              # fail exit
              #########################################################
              private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)

          elif 'ignore_errors' == cmd:
            ignore_errors = str(input[cmd])
            if ignore_errors != str(False) and ignore_errors != str(True):
              logstr = 'ignore_errors=(' + str(input[cmd]) + '): Only yes or no set'
              exec_log_output(logstr)
              private_log_output(log_file_name,host_name,logstr)
              #########################################################
              # fail exit
              #########################################################
              private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)
          else:
            # error log
            logstr = 'command(state->' + cmd + ') not service'
            exec_log_output(logstr)
            private_output(log_file_name,host_name,logstr)
             
            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)

        ####################################################
        # expect(prompt) command execute
        ####################################################
        expect_prompt(p,log_file_name,host_name,expect_cmd,timeout)

        # ドライランモードの場合は接続確認したら終了
        if module.check_mode:
          private_exit_json(obj=module,msg=host_name + chk_mode,changed=False, exec_log=exec_log)

        ####################################################
        # state command execute
        ####################################################
        exec_command(p,log_file_name,host_name,exec_cmd)

        p.readline() 

        ####################################################
        # expect(prompt) command execute
        ####################################################
        expect_prompt(p,log_file_name,host_name,expect_cmd,timeout)

        # 最後のESCコードから後ろを削除
        edit_stdout_data = last_escstr_cut(p.before)
        # stdout log file create
        if stdout_file:
          pass_rep_stdout_file = password_replace(password,stdout_file)

          craete_stdout_file(pass_rep_stdout_file,edit_stdout_data)
        else:
          stdout_file="/tmp/.ita_pioneer_module_stdout." + str(os.getpid())
          craete_stdout_file(stdout_file,edit_stdout_data)

        if shell_cmd:
          # user shell execute
          try:
            pass_rep_shell_cmd = password_replace(password,shell_cmd)
            output_shell_cmd  = password_replace(output_password,shell_cmd)
            pass_rep_parameter_cmd = password_replace(password,parameter_cmd)
            output_parameter_cmd  = password_replace(output_password,parameter_cmd)
            pass_rep_stdout_file = password_replace(password,stdout_file)

            logstr = 'user shell ' + output_shell_cmd + ' parameter(' + output_parameter_cmd + ') execute'
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)

            def_shell_cmd="sh " + pass_rep_shell_cmd + " " + pass_rep_stdout_file + " " + str(pass_rep_parameter_cmd)
            shell_ret = subprocess.call(def_shell_cmd, shell=True)
          except:
            import sys
            import traceback
            error_type, error_value, traceback = sys.exc_info()
            logstr='user shell execute error ' + str(error_type) + ' ' + str(error_value)
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)

            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)
        else:
          # default shell execute
          try:
            pass_rep_parameter_cmd = password_replace(password,parameter_cmd)
            pass_rep_stdout_file = password_replace(password,stdout_file)
            output_parameter_cmd  = password_replace(output_password,parameter_cmd)

            logstr = 'default shell parameter(' + output_parameter_cmd + ') execute'
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)

            def_shell_cmd="sh " + shell_name + " " + pass_rep_stdout_file + " " + str(pass_rep_parameter_cmd)
            shell_ret = subprocess.call(def_shell_cmd, shell=True)
          except:
            import sys
            import traceback
            error_type, error_value, traceback = sys.exc_info()
            logstr='default shell execute error ' + str(error_type) + ' ' + str(error_value)
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)

            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)

        # shell result check
        if shell_ret == 0:
          # shell result log output
          logstr = 'execute result OK'
          private_log_output(log_file_name,host_name,logstr)
          exec_log_output(logstr)

          # success_exit check
          if success_exit == str(True):
            logstr = 'dialog_file normal exit. (success_exit: yes)'
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)
          
            #########################################################
            # normal exit
            #########################################################
            private_exit_json(obj=module,msg=host_name + ':' + logstr,changed=True, exec_log=exec_log)

          else:
            private_log_output(log_file_name,host_name,'success_exit no')

        else:
          # shell result log output
          logstr='execute result NG exit code=(' + str(shell_ret) + ')'
          private_log_output(log_file_name,host_name,logstr)
          exec_log_output(logstr)

          # ignore_errors check
          if ignore_errors == str(False):
            logstr = 'dialog_file fail exit. (ignore_errors: no)'
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)
 
            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)

          else:
            logstr = 'dialog_file execut continue. (ignore_errors: yes)'
            private_log_output(log_file_name,host_name,logstr)
            exec_log_output(logstr)

        ####################################################
        # LF send
        ####################################################
        p.sendline('')

        p.readline() 

      # command?
      elif 'command' in input:
        for cmd in input:
          if 'command' == cmd:
            exec_cmd = str(input[cmd])
            exec_name = 'command command:(' + exec_cmd + ')'
          elif 'prompt' == cmd:
            expect_cmd = str(input[cmd])
            expect_name = 'prompt:(' + expect_cmd + ')'
          elif 'timeout' == cmd:
            timeout2 = input[cmd]
          elif 'register' == cmd:
            register_tmp_name = str(input[cmd])
            register_flg = 1
          elif 'when' == cmd:
            when_max = len(input[cmd])
            for i in range(0,when_max,1):
              when_tmp = str(input[cmd][i])
              when_cmd[i] = when_tmp
          elif 'with_items' == cmd:
            with_tmp = module.params['exec_file']
            with_tmp = with_tmp.replace("/in/", "/tmp/")
            with_tmp = with_tmp.replace("/dialog_files/", "/original_dialog_files/")

            with_file = yaml.load(open(with_tmp).read())

            with_def = yaml.load(open(module.params['host_vars_file']).read())

            # playbookから変数を取得
            idx = 0
            with_cmd = defaultdict(dict)
            for input2 in with_file['exec_list']:
              for cmd2 in input2:
                if 'with_items' == cmd2:
                  max = len(input2[cmd2])
                  for i in range(0,max,1):
                    with_tmp2 = str(input2[cmd2][i])
                    tmp1 = str(with_tmp2.find(' '))
                    tmp2 = str(with_tmp2.rfind(' '))
                    with_tmp2 = with_tmp2[int(tmp1)+1:int(tmp2)]
                    with_tmp2 = with_tmp2.lstrip()
                    with_tmp2 = with_tmp2.rstrip()
                    with_cmd[idx][i] = with_tmp2
                  idx = idx + 1

            # 変数定義から値を取得
            idx = 0
            idx2 = 0
            cnt = len(with_cmd[with_items_count])
            def_cmd = defaultdict(dict)
            for i in range(0,cnt,1):
              def_temp = with_cmd[with_items_count][i]
              if def_temp.find('VAR_prompt') != -1:
                prompt_num = i
              if def_temp.find('VAR_timeout') != -1:
                timeout_num = i
              max = len(with_def[def_temp])
              for j in range(0,max,1):
                def_tmp = str(with_def[def_temp][j])
                def_cmd[i][j] = def_tmp
            with_items_count = with_items_count + 1
          elif 'failed_when' == cmd:
            failed_max = len(input[cmd])
            for i in range(0,failed_max,1):
              failed_tmp = str(input[cmd][i])
              failed_cmd[i] = failed_tmp
          elif 'exec_when' == cmd:
            exec_max = len(input[cmd])
            for i in range(0,exec_max,1):
              exec_tmp = str(input[cmd][i])
              exec_when_cmd[i] = exec_tmp

          else:
            # error log
            logstr = 'command(command->' + cmd + ') not service'
            exec_log_output(logstr)
            private_log_output(log_file_name,host_name,logstr)

            #########################################################
            # fail exit
            #########################################################
            private_fail_json(obj=module,msg=host_name + ':' + logstr,exec_log=exec_log)

        ####################################################
        # expect(prompt) command execute
        ####################################################
        #ドライランモードの場合は接続確認したら終了
        if module.check_mode:
          private_exit_json(obj=module,msg=host_name + chk_mode,changed=False, exec_log=exec_log)

        ####################################################
        # command command execute
        ####################################################
        # whenパラメータがある場合
        if when_cmd:

          # ループ処理
          for i in range(0,when_max,1):

            temp_cmd2 = when_cmd[i]
            tmp1 = 0
            count = 0

            execute_when_log('when',log_file_name,host_name,temp_cmd2)

            # ORがある場合
            if com_re_search(" OR ", temp_cmd2 ):

              # OR実施数分ループ
              while 1:

                temp_cmd2 = temp_cmd2[int(tmp1):]
                temp_cmd2 = temp_cmd2.lstrip()

                if com_re_search(" OR ", temp_cmd2 ):
                  tmp2 = temp_cmd2.find(' OR ')
                  temp_cmd3 = temp_cmd2[:int(tmp2)]
                  temp_cmd3 = temp_cmd3.lstrip()
                  temp_cmd3 = temp_cmd3.rstrip()

                  # When結果ログ初期設定
                  when_command = temp_cmd3
                  when_name = 'when'

                  # When実施
                  temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                  # When結果ログ出力
                  when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                  tmp1 = int(tmp2)+3
                  count = count+1

                else:

                  # When結果ログ初期設定
                  temp_cmd2 = temp_cmd2.rstrip()

                  # When結果ログ初期設定
                  when_command = temp_cmd2
                  when_name = 'when'

                  # When実施
                  temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                  # When結果ログ出力
                  when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                  count = count+1
                  break

              tmp[i] = 1
              for j in range(0,count,1):
                if temp3[j] == 0:
                  tmp[i] = 0
                  break

            # ORがない場合
            else:

              # When結果ログ初期設定
              when_command = temp_cmd2
              when_name = 'when'

              # When実施
              tmp[i] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

              # When結果ログ出力
              when_check_result(when_name,tmp[i],log_file_name,host_name,when_command)

          # スキップフラグ確認
          for i in range(0,when_max,1):

            if tmp[i] == 1:

              skip_flg = 1
              break

        # skip_flgが0であった場合
        if skip_flg == 0:

          # with_itemsがある場合
          if with_cmd:

            with_items_flg = 1

            # with_itemsの変数の数を取得
            max = len(def_cmd)

            for i in range(0,max,1):

              # promptの場合、取得しない
              if prompt_num == i:
                continue

              # timeoutの場合、取得しない
              if timeout_num == i:
                continue

              # 変数に対して要素数を取得
              tmp_count = len(def_cmd[i])

              # 最大の要素数を取得
              if max_count < tmp_count:
                max_count = tmp_count

            for i in range(0,max,1):

              tmp_count = len(def_cmd[i])
              if max_count > tmp_count:
                for j in range(tmp_count,max_count,1):
                  def_cmd[i][j] = ''

            # 最大要素数分ループ
            for i in range(0,max_count,1):

              command_exec_flg = 0

              # コマンド文を退避
              temp_cmd = exec_cmd

              # コマンドにitem.Xの記述があるかチェック
              if com_re_search("{{ item.[0-9]|[1-9][0-9] }}", temp_cmd ):

                # with_itemsの変数分ループ
                for j in range(0,max,1):

                  # item.Xチェック
                  if com_re_search("{{ item.[0-9]|[1-9][0-9] }}", temp_cmd ):

                    temp = "{{ " + "item." + str(j) + " }}"

                    if com_re_search( temp, temp_cmd ):

                      # 空でない場合
                      if len(def_cmd[j][i]) != 0:

                        # 置換
                        temp_cmd = temp_cmd.replace( temp, def_cmd[j][i] )

                      # 空の場合
                      else:

                        command_exec_flg = 1
                        break

                  # item.Xがない場合ループから抜ける
                  else:
                    break

                if command_exec_flg == 1:

                  continue

                # exec_whenがある場合
                if exec_when_cmd:

                  exec_when_flg = 1

                  # exec_when数分ループ
                  for j in range(0,exec_max,1):

                    continue_flg = 0

                    # exec_whenにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", exec_when_cmd[j] ):

                      # exec_when文を退避
                      temp_cmd2 = exec_when_cmd[j]

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd2 ):

                            # 置換
                            temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                      tmp1 = 0
                      count = 0

                      # ORがある場合
                      if com_re_search( " OR ", temp_cmd2 ):

                        execute_when_log('exec_when',log_file_name,host_name,temp_cmd2)

                        # OR実施数分ループ
                        while 1:

                          temp_cmd2 = temp_cmd2[int(tmp1):]
                          temp_cmd2 = temp_cmd2.lstrip()

                          if com_re_search( " OR ", temp_cmd2 ):
                            tmp2 = temp_cmd2.find(' OR ')
                            temp_cmd3 = temp_cmd2[:int(tmp2)]
                            temp_cmd3 = temp_cmd3.lstrip()
                            temp_cmd3 = temp_cmd3.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd3
                            when_name = 'exec_when'

                            # exec_whenチェック
                            #temp3[count] = when_check(temp_cmd3,register_cmd,register_name,host_vars_file,log_file_name,host_name)
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            tmp1 = int(tmp2)+3
                            count = count+1

                          else:

                            temp_cmd2 = temp_cmd2.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)
                            
                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            count = count+1
                            break

                        tmp = 1
                        for k in range(0,count,1):
                          if temp3[k] == 0:
                            tmp = 0
                            break

                      # ORがない場合
                      else:

                        # When結果ログ初期設定
                        when_command = temp_cmd2
                        when_name = 'exec_when'

                        # exec_whenチェック
                        tmp = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                        # When結果ログ出力
                        when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                      # continue確認
                      if tmp == 1:

                        continue_flg = 1
                        logstr = 'exec_when not match continue'
                        private_log_output(log_file_name,host_name,logstr)
                        exec_log_output(logstr)
                        break

                    # item.Xの記述がない場合、そのままexec_whenチェック
                    else:

                      temp_cmd2 = exec_when_cmd[j]
                      tmp1 = 0
                      count = 0

                      # ORがある場合
                      if com_re_search( " OR ", temp_cmd2 ):

                        execute_when_log('exec_when',log_file_name,host_name,temp_cmd2)

                        # OR実施数分ループ
                        while 1:

                          temp_cmd2 = temp_cmd2[int(tmp1):]
                          temp_cmd2 = temp_cmd2.lstrip()

                          if com_re_search( " OR ", temp_cmd2 ):
                            tmp2 = temp_cmd2.find(' OR ')
                            temp_cmd3 = temp_cmd2[:int(tmp2)]
                            temp_cmd3 = temp_cmd3.lstrip()
                            temp_cmd3 = temp_cmd3.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd3
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            tmp1 = int(tmp2)+3
                            count = count+1

                          else:

                            temp_cmd2 = temp_cmd2.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)
                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            count = count+1
                            break

                        tmp = 1
                        for k in range(0,count,1):
                          if temp3[k] == 0:
                            tmp = 0
                            break

                      # ORがない場合
                      else:
                        # When結果ログ初期設定
                        when_command = temp_cmd2
                        when_name = 'exec_when'

                        # exec_whenチェック
                        tmp = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                        # When結果ログ出力
                        when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                      # continue確認
                      if tmp == 1:

                        continue_flg = 1
                        logstr = 'exec_when not match continue'
                        private_log_output(log_file_name,host_name,logstr)
                        exec_log_output(logstr)
                        break

                  if continue_flg == 0:

                    if prompt_count2 == 0:

                      # prompt文を退避
                      temp_cmd4 = expect_cmd

                      # promptにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd4 ):

                              if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                                logstr = 'The number of prompts is incorrect.'
                                private_log_output(log_file_name,host_name,logstr)
                                exec_log_output(logstr)

                                #########################################################
                                # fail exit
                                #########################################################
                                private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                              # 置換
                              temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                              prompt_count = prompt_count + 1

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                      # timeout値を退避
                      temp_cmd5 = str(timeout2)

                      # timeoutにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd5 ):

                              if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                                logstr = 'The number of timeouts is incorrect.'
                                private_log_output(log_file_name,host_name,logstr)
                                exec_log_output(logstr)

                                #########################################################
                                # fail exit
                                #########################################################
                                private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                              # 置換
                              temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                              timeout_count = timeout_count + 1

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                      # プロンプト待ち
                      expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                      prompt_count2 = prompt_count2 + 1

                    # コマンド実行
                    exec_command(p,log_file_name,host_name,temp_cmd)

                    # 出力結果読み込み
                    p.readline()

                    # prompt文を退避
                    temp_cmd4 = expect_cmd

                    # promptにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd4 ):

                            if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                              logstr = 'The number of prompts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                            prompt_count = prompt_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # timeout値を退避
                    temp_cmd5 = str(timeout2)

                    # timeoutにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd5 ):

                            if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                              logstr = 'The number of timeouts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                            timeout_count = timeout_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # プロンプト待ち
                    expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                    if failed_cmd:

                      # failed_when数分ループ
                      for j in range(0,failed_max,1):

                        # failed_whenにitem.Xの記述があるかチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", failed_cmd[j] ):

                          # failed_when文を退避
                          temp_cmd2 = failed_cmd[j]

                          # with_itemsの変数分ループ
                          for k in range(0,max,1):

                            # item.Xチェック
                            if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                              temp2 = "{{ " + "item." + str(k) + " }}"

                              if com_re_search( temp2, temp_cmd2 ):

                                # 置換
                                temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                            # item.Xがない場合ループから抜ける
                            else:
                              break

                          tmp1 = 0
                          count = 0

                          # ORがある場合
                          if com_re_search( " OR ", temp_cmd2 ):

                            execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                            # OR実施数分ループ
                            while 1:

                              temp_cmd2 = temp_cmd2[int(tmp1):]
                              temp_cmd2 = temp_cmd2.lstrip()

                              if com_re_search( " OR ", temp_cmd2 ):
                                tmp2 = temp_cmd2.find(' OR ')
                                temp_cmd3 = temp_cmd2[:int(tmp2)]
                                temp_cmd3 = temp_cmd3.lstrip()
                                temp_cmd3 = temp_cmd3.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd3
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)
                                
                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)
#                                if temp3[count] == 0:
#                                  exec_log.append('failed_when: [' + temp_cmd3 + '] Match')

                                tmp1 = int(tmp2)+3
                                count = count+1

                              else:

                                temp_cmd2 = temp_cmd2.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd2
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                count = count+1
                                break

                            tmp = 1
                            for k in range(0,count,1):
                              if temp3[k] == 0:
                                tmp = 0
                                break

                          # ORがない場合
                          else:
                            # 最後のESCコードから後ろを削除
                            register_temp = last_escstr_cut(p.before)

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'failed_when'

                            # failed_whenチェック
                            tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                          # エラー確認
                          if tmp == 1:

                            logstr = 'failed_when not match fail exit'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                        # with_itemsあるがitem.Xがない場合、そのままfailed_whenチェック
                        else:

                          temp_cmd2 = failed_cmd[j]
                          tmp1 = 0
                          count = 0

                          # ORがある場合
                          if com_re_search( " OR ", temp_cmd2 ):

                            execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                            # OR実施数分ループ
                            while 1:

                              temp_cmd2 = temp_cmd2[int(tmp1):]
                              temp_cmd2 = temp_cmd2.lstrip()

                              if com_re_search( " OR ", temp_cmd2 ):
                                tmp2 = temp_cmd2.find(' OR ')
                                temp_cmd3 = temp_cmd2[:int(tmp2)]
                                temp_cmd3 = temp_cmd3.lstrip()
                                temp_cmd3 = temp_cmd3.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd3
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                tmp1 = int(tmp2)+3
                                count = count+1

                              else:

                                temp_cmd2 = temp_cmd2.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd2
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                count = count+1
                                break

                            tmp = 1
                            for k in range(0,count,1):
                              if temp3[k] == 0:
                                tmp = 0
                                break

                          # ORがない場合
                          else:
                            #register_temp = p.before
                            # 最後のESCコードから後ろを削除
                            register_temp = last_escstr_cut(p.before)

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'failed_when'

                            # failed_whenチェック
                            tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                          # エラー確認
                          if tmp == 1:

                            logstr = 'failed_when not match fail exit'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                # exec_whenがない場合
                else:

                  if prompt_count2 == 0:

                    # prompt文を退避
                    temp_cmd4 = expect_cmd

                    # promptにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd4 ):

                            if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                              logstr = 'The number of prompts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                            prompt_count = prompt_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # timeout値を退避
                    temp_cmd5 = str(timeout2)

                    # timeoutにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd5 ):

                            if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                              logstr = 'The number of timeouts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                            timeout_count = timeout_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # プロンプト待ち
                    expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                    prompt_count2 = prompt_count2 + 1

# pass   
                  # コマンド実行
                  exec_command(p,log_file_name,host_name,temp_cmd)

                  # 出力結果読み込み
                  p.readline()

                  # prompt文を退避
                  temp_cmd4 = expect_cmd

                  # promptにitem.Xの記述があるかチェック
                  if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                    # with_itemsの変数分ループ
                    for k in range(0,max,1):

                      # item.Xチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                        temp2 = "{{ " + "item." + str(k) + " }}"

                        if com_re_search( temp2, temp_cmd4 ):

                          if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                            logstr = 'The number of prompts is incorrect.'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                          # 置換
                          temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                          prompt_count = prompt_count + 1

                      # item.Xがない場合ループから抜ける
                      else:
                        break

                  # timeout値を退避
                  temp_cmd5 = str(timeout2)

                  # timeoutにitem.Xの記述があるかチェック
                  if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                    # with_itemsの変数分ループ
                    for k in range(0,max,1):

                      # item.Xチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                        temp2 = "{{ " + "item." + str(k) + " }}"

                        if com_re_search( temp2, temp_cmd5 ):

                          if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                            logstr = 'The number of timeouts is incorrect.'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                          # 置換
                          temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                          timeout_count = timeout_count + 1

                      # item.Xがない場合ループから抜ける
                      else:
                        break

                  # プロンプト待ち
                  expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                  if failed_cmd:

                    # failed_when数分ループ
                    for j in range(0,failed_max,1):

                      # failed_whenにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", failed_cmd[j] ):

                        # コマンド文を退避
                        temp_cmd2 = failed_cmd[j]

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd2 ):

                              # 置換
                              temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                        tmp1 = 0
                        count = 0

                        # ORがある場合
                        if com_re_search( " OR ", temp_cmd2 ):

                          execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                          # OR実施数分ループ
                          while 1:

                            temp_cmd2 = temp_cmd2[int(tmp1):]
                            temp_cmd2 = temp_cmd2.lstrip()

                            if com_re_search( " OR ", temp_cmd2 ):
                              tmp2 = temp_cmd2.find(' OR ')
                              temp_cmd3 = temp_cmd2[:int(tmp2)]
                              temp_cmd3 = temp_cmd3.lstrip()
                              temp_cmd3 = temp_cmd3.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd3
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              tmp1 = int(tmp2)+3
                              count = count+1

                            else:
                              temp_cmd2 = temp_cmd2.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd2
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              count = count+1
                              break

                          tmp = 1
                          for k in range(0,count,1):
                            if temp3[k] == 0:
                              tmp = 0
                              break

                        # ORがない場合
                        else:
                          #register_temp = p.before
                          # 最後のESCコードから後ろを削除
                          register_temp = last_escstr_cut(p.before)

                          # When結果ログ初期設定
                          when_command = temp_cmd2
                          when_name = 'failed_when'

                          # failed_whenチェック
                          tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                          # When結果ログ出力
                          when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                        # エラー確認
                        if tmp == 1:

                          logstr = 'failed_when not match fail exit'
                          private_log_output(log_file_name,host_name,logstr)
                          exec_log_output(logstr)

                          #########################################################
                          # fail exit
                          #########################################################
                          private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                      # with_itemsあるがitem.Xがない場合、そのままfailed_whenチェック
                      else:

                        temp_cmd2 = failed_cmd[j]
                        tmp1 = 0
                        count = 0

                        # ORがある場合
                        if com_re_search( " OR ", temp_cmd2 ):

                          execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                          # OR実施数分ループ
                          while 1:

                            temp_cmd2 = temp_cmd2[int(tmp1):]
                            temp_cmd2 = temp_cmd2.lstrip()

                            if com_re_search( " OR ", temp_cmd2 ):
                              tmp2 = temp_cmd2.find(' OR ')
                              temp_cmd3 = temp_cmd2[:int(tmp2)]
                              temp_cmd3 = temp_cmd3.lstrip()
                              temp_cmd3 = temp_cmd3.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd3
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              tmp1 = int(tmp2)+3
                              count = count+1

                            else:

                              temp_cmd2 = temp_cmd2.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)
                              
                              # When結果ログ初期設定
                              when_command = temp_cmd2
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              count = count+1
                              break

                          tmp = 1
                          for k in range(0,count,1):
                            if temp3[k] == 0:
                              tmp = 0
                              break

                        # ORがない場合
                        else:
                          #register_temp = p.before
                          # 最後のESCコードから後ろを削除
                          register_temp = last_escstr_cut(p.before)

                          # When結果ログ初期設定
                          when_command = temp_cmd2
                          when_name = 'failed_when'

                          # failed_whenチェック
                          tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                          # When結果ログ出力
                          when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                        # エラー確認
                        if tmp == 1:

                          logstr = 'failed_when not match fail exit'
                          private_log_output(log_file_name,host_name,logstr)
                          exec_log_output(logstr)

                          #########################################################
                          # fail exit
                          #########################################################
                          private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

              # コマンドにitem.Xの記述がない場合そのまま実行
              else:

                # exec_whenがある場合
                if exec_when_cmd:

                  exec_when_flg = 1

                  # exec_when数分ループ
                  for j in range(0,exec_max,1):

                    continue_flg = 0

                    # exec_whenにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", exec_when_cmd[j] ):

                      # exec_when文を退避
                      temp_cmd2 = exec_when_cmd[j]

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd2 ):

                            # 置換
                            temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                      tmp1 = 0
                      count = 0

                      # ORがある場合
                      if com_re_search( " OR ", temp_cmd2 ):

                        execute_when_log('exec_when',log_file_name,host_name,temp_cmd2)

                        # OR実施数分ループ
                        while 1:

                          temp_cmd2 = temp_cmd2[int(tmp1):]
                          temp_cmd2 = temp_cmd2.lstrip()

                          if com_re_search( " OR ", temp_cmd2 ):
                            tmp2 = temp_cmd2.find(' OR ')
                            temp_cmd3 = temp_cmd2[:int(tmp2)]
                            temp_cmd3 = temp_cmd3.lstrip()
                            temp_cmd3 = temp_cmd3.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd3
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            tmp1 = int(tmp2)+3
                            count = count+1

                          else:

                            temp_cmd2 = temp_cmd2.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            count = count+1
                            break

                        tmp = 1
                        for k in range(0,count,1):
                          if temp3[k] == 0:
                            tmp = 0
                            break

                      # ORがない場合
                      else:

                        # When結果ログ初期設定
                        when_command = temp_cmd2
                        when_name = 'exec_when'

                        # exec_whenチェック
                        #tmp = when_check(temp_cmd2,register_cmd,register_name,host_vars_file,log_file_name,host_name)
                        tmp = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                        # When結果ログ出力
                        when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                      # continue確認
                      if tmp == 1:

                        continue_flg = 1
                        logstr = 'exec_when not match continue'
                        private_log_output(log_file_name,host_name,logstr)
                        exec_log_output(logstr)
                        break

                    # item.Xの記述がない場合、そのままexec_whenチェック
                    else:

                      temp_cmd2 = exec_when_cmd[j]
                      tmp1 = 0
                      count = 0

                      # ORがある場合
                      if com_re_search( " OR ", temp_cmd2 ):

                        execute_when_log('exec_when',log_file_name,host_name,temp_cmd2)

                        # OR実施数分ループ
                        while 1:

                          temp_cmd2 = temp_cmd2[int(tmp1):]
                          temp_cmd2 = temp_cmd2.lstrip()

                          if com_re_search( " OR ", temp_cmd2 ):
                            tmp2 = temp_cmd2.find(' OR ')
                            temp_cmd3 = temp_cmd2[:int(tmp2)]
                            temp_cmd3 = temp_cmd3.lstrip()
                            temp_cmd3 = temp_cmd3.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd3
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            tmp1 = int(tmp2)+3
                            count = count+1

                          else:

                            temp_cmd2 = temp_cmd2.rstrip()

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'exec_when'

                            # exec_whenチェック
                            temp3[count] = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                            count = count+1
                            break

                        tmp = 1
                        for k in range(0,count,1):
                          if temp3[k] == 0:
                            tmp = 0
                            break

                      # ORがない場合
                      else:

                        # When結果ログ初期設定
                        when_command = temp_cmd2
                        when_name = 'exec_when'

                        # exec_whenチェック
                        tmp = when_check(when_command,register_cmd,register_name,host_vars_file,log_file_name,host_name)

                        # When結果ログ出力
                        when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                      # continue確認
                      if tmp == 1:

                        continue_flg = 1
                        logstr = 'exec_when not match continue'
                        private_log_output(log_file_name,host_name,logstr)
                        exec_log_output(logstr)
                        break

                  if continue_flg == 0:

                    if prompt_count2 == 0:

                      # prompt文を退避
                      temp_cmd4 = expect_cmd

                      # promptにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd4 ):

                              if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                                logstr = 'The number of prompts is incorrect.'
                                private_log_output(log_file_name,host_name,logstr)
                                exec_log_output(logstr)

                                #########################################################
                                # fail exit
                                #########################################################
                                private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                              # 置換
                              temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                              prompt_count = prompt_count + 1

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                      # timeout値を退避
                      temp_cmd5 = str(timeout2)

                      # timeoutにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd5 ):

                              if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                                logstr = 'The number of timeouts is incorrect.'
                                private_log_output(log_file_name,host_name,logstr)
                                exec_log_output(logstr)

                                #########################################################
                                # fail exit
                                #########################################################
                                private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                              # 置換
                              temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                              timeout_count = timeout_count + 1

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                      # プロンプト待ち
                      expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                      prompt_count2 = prompt_count2 + 1

# pass      
                    # コマンド実行
                    exec_command(p,log_file_name,host_name,exec_cmd)

                    # 出力結果読み込み
                    p.readline()

                    # prompt文を退避
                    temp_cmd4 = expect_cmd

                    # promptにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd4 ):

                            if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                              logstr = 'The number of prompts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                            prompt_count = prompt_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # timeout値を退避
                    temp_cmd5 = str(timeout2)

                    # timeoutにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd5 ):

                            if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                              logstr = 'The number of timeouts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                            timeout_count = timeout_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # プロンプト待ち
                    expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                    if failed_cmd:

                      # failed_when数分ループ
                      for j in range(0,failed_max,1):

                        # failed_whenにitem.Xの記述があるかチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", failed_cmd[j] ):

                          # failed_when文を退避
                          temp_cmd2 = failed_cmd[j]

                          # with_itemsの変数分ループ
                          for k in range(0,max,1):

                            # item.Xチェック
                            if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                              temp2 = "{{ " + "item." + str(k) + " }}"

                              if com_re_search( temp2, temp_cmd2 ):

                                # 置換
                                temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                            # item.Xがない場合ループから抜ける
                            else:
                              break

                          tmp1 = 0
                          count = 0

                          # ORがある場合
                          if com_re_search( " OR ", temp_cmd2 ):

                            execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                            # OR実施数分ループ
                            while 1:

                              temp_cmd2 = temp_cmd2[int(tmp1):]
                              temp_cmd2 = temp_cmd2.lstrip()

                              if com_re_search( " OR ", temp_cmd2 ):
                                tmp2 = temp_cmd2.find(' OR ')
                                temp_cmd3 = temp_cmd2[:int(tmp2)]
                                temp_cmd3 = temp_cmd3.lstrip()
                                temp_cmd3 = temp_cmd3.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd3
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                tmp1 = int(tmp2)+3
                                count = count+1

                              else:

                                temp_cmd2 = temp_cmd2.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd2
                                when_name = 'failed_when'

                                # failed_whenチェック
                                #temp3[count] = failed_when_check(temp_cmd2,register_temp,log_file_name,host_name)
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                count = count+1
                                break

                            tmp = 1
                            for k in range(0,count,1):
                              if temp3[k] == 0:
                                tmp = 0
                                break

                          # ORがない場合
                          else:

                            #register_temp = p.before
                            # 最後のESCコードから後ろを削除
                            register_temp = last_escstr_cut(p.before)

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'failed_when'

                            # failed_whenチェック
                            tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                          # エラー確認
                          if tmp == 1:

                            logstr = 'failed_when not match fail exit'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                        # with_itemsあるがitem.Xがない場合、そのままfailed_whenチェック
                        else:

                          temp_cmd2 = failed_cmd[j]
                          tmp1 = 0
                          count = 0

                          # ORがある場合
                          if com_re_search( " OR ", temp_cmd2 ):

                            execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                            # OR実施数分ループ
                            while 1:

                              temp_cmd2 = temp_cmd2[int(tmp1):]
                              temp_cmd2 = temp_cmd2.lstrip()

                              if com_re_search( " OR ", temp_cmd2 ):
                                tmp2 = temp_cmd2.find(' OR ')
                                temp_cmd3 = temp_cmd2[:int(tmp2)]
                                temp_cmd3 = temp_cmd3.lstrip()
                                temp_cmd3 = temp_cmd3.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd3
                                when_name = 'failed_when'

                                # failed_whenチェック
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                                tmp1 = int(tmp2)+3
                                count = count+1

                              else:

                                temp_cmd2 = temp_cmd2.rstrip()

                                #register_temp = p.before
                                # 最後のESCコードから後ろを削除
                                register_temp = last_escstr_cut(p.before)

                                # When結果ログ初期設定
                                when_command = temp_cmd2
                                when_name = 'failed_when'

                                # failed_whenチェック
                                #temp3[count] = failed_when_check(temp_cmd2,register_temp,log_file_name,host_name)
                                temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                                # When結果ログ出力
                                when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)
#                                if temp3[count] == 0:
#                                  exec_log.append('failed_when: [' + temp_cmd2 + '] Match')

                                count = count+1
                                break

                            tmp = 1
                            for k in range(0,count,1):
                              if temp3[k] == 0:
                                tmp = 0
                                break

                          # ORがない場合
                          else:

                            #register_temp = p.before
                            # 最後のESCコードから後ろを削除
                            register_temp = last_escstr_cut(p.before)

                            # When結果ログ初期設定
                            when_command = temp_cmd2
                            when_name = 'failed_when'

                            # failed_whenチェック
                            #tmp = failed_when_check(temp_cmd2,register_temp,log_file_name,host_name)
                            tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                            # When結果ログ出力
                            when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                          # エラー確認
                          if tmp == 1:

                            logstr = 'failed_when not match fail exit'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                # exec_whenがない場合
                else:

                  if prompt_count2 == 0:

                    # prompt文を退避
                    temp_cmd4 = expect_cmd

                    # promptにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd4 ):

                            if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                              logstr = 'The number of prompts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                            prompt_count = prompt_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # timeout値を退避
                    temp_cmd5 = str(timeout2)

                    # timeoutにitem.Xの記述があるかチェック
                    if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                      # with_itemsの変数分ループ
                      for k in range(0,max,1):

                        # item.Xチェック
                        if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                          temp2 = "{{ " + "item." + str(k) + " }}"

                          if com_re_search( temp2, temp_cmd5 ):

                            if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                              logstr = 'The number of timeouts is incorrect.'
                              private_log_output(log_file_name,host_name,logstr)
                              exec_log_output(logstr)

                              #########################################################
                              # fail exit
                              #########################################################
                              private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                            # 置換
                            temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                            timeout_count = timeout_count + 1

                        # item.Xがない場合ループから抜ける
                        else:
                          break

                    # プロンプト待ち
                    expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                    prompt_count2 = prompt_count2 + 1

                  # コマンド実行
                  exec_command(p,log_file_name,host_name,exec_cmd)

                  # 出力結果読み込み
                  p.readline()

                  # prompt文を退避
                  temp_cmd4 = expect_cmd

                  # promptにitem.Xの記述があるかチェック
                  if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                    # with_itemsの変数分ループ
                    for k in range(0,max,1):

                      # item.Xチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd4 ):

                        temp2 = "{{ " + "item." + str(k) + " }}"

                        if com_re_search( temp2, temp_cmd4 ):

                          if len(def_cmd[k]) == prompt_count or def_cmd[k][prompt_count] == '':

                            logstr = 'The number of prompts is incorrect.'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                          # 置換
                          temp_cmd4 = temp_cmd4.replace( temp2, def_cmd[k][prompt_count] )
                          prompt_count = prompt_count + 1

                      # item.Xがない場合ループから抜ける
                      else:
                        break

                  # timeout値を退避
                  temp_cmd5 = str(timeout2)

                  # timeoutにitem.Xの記述があるかチェック
                  if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                    # with_itemsの変数分ループ
                    for k in range(0,max,1):

                      # item.Xチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd5 ):

                        temp2 = "{{ " + "item." + str(k) + " }}"

                        if com_re_search( temp2, temp_cmd5 ):

                          if len(def_cmd[k]) == timeout_count or def_cmd[k][timeout_count] == '':

                            logstr = 'The number of timeouts is incorrect.'
                            private_log_output(log_file_name,host_name,logstr)
                            exec_log_output(logstr)

                            #########################################################
                            # fail exit
                            #########################################################
                            private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                          # 置換
                          temp_cmd5 = temp_cmd5.replace( temp2, def_cmd[k][timeout_count] )
                          timeout_count = timeout_count + 1

                      # item.Xがない場合ループから抜ける
                      else:
                        break

                  # プロンプト待ち
                  expect_prompt(p,log_file_name,host_name,temp_cmd4,temp_cmd5)

                  if failed_cmd:

                    # failed_when 数分ループ
                    for j in range(0,failed_max,1):

                      # failed_whenにitem.Xの記述があるかチェック
                      if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", failed_cmd[j] ):

                        # コマンド文を退避
                        temp_cmd2 = failed_cmd[j]

                        # with_itemsの変数分ループ
                        for k in range(0,max,1):

                          # item.Xチェック
                          if com_re_search( "{{ item.[0-9]|[1-9][0-9] }}", temp_cmd2 ):

                            temp2 = "{{ " + "item." + str(k) + " }}"

                            if com_re_search( temp2, temp_cmd2 ):

                              # 置換
                              temp_cmd2 = temp_cmd2.replace( temp2, def_cmd[k][i] )

                          # item.Xがない場合ループから抜ける
                          else:
                            break

                        tmp1 = 0
                        count = 0

                        # ORがある場合
                        if com_re_search( " OR ", temp_cmd2 ):

                          execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                          # OR実施数分ループ
                          while 1:

                            temp_cmd2 = temp_cmd2[int(tmp1):]
                            temp_cmd2 = temp_cmd2.lstrip()

                            if com_re_search( " OR ", temp_cmd2 ):
                              tmp2 = temp_cmd2.find(' OR ')
                              temp_cmd3 = temp_cmd2[:int(tmp2)]
                              temp_cmd3 = temp_cmd3.lstrip()
                              temp_cmd3 = temp_cmd3.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd3
                              when_name = 'failed_when'

                              # failed_whenチェック
                              #temp3[count] = failed_when_check(temp_cmd3,register_temp,log_file_name,host_name)
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)
#                              if temp3[count] == 0:
#                                exec_log.append('failed_when: [' + temp_cmd3 + '] Match')

                              tmp1 = int(tmp2)+3
                              count = count+1

                            else:

                              temp_cmd2 = temp_cmd2.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd2
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              count = count+1
                              break

                          tmp = 1
                          for k in range(0,count,1):
                            if temp3[k] == 0:
                              tmp = 0
                              break

                        # ORがない場合
                        else:

                          #register_temp = p.before
                          # 最後のESCコードから後ろを削除
                          register_temp = last_escstr_cut(p.before)

                          # When結果ログ初期設定
                          when_command = temp_cmd2
                          when_name = 'failed_when'

                          # failed_whenチェック
                          tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                          # When結果ログ出力
                          when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                        # エラー確認
                        if tmp == 1:

                          logstr = 'failed_when not match fail exit'
                          private_log_output(log_file_name,host_name,logstr)
                          exec_log_output(logstr)

                          #########################################################
                          # fail exit
                          #########################################################
                          private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

                      # with_itemsあるがitem.Xがない場合、そのままwhenチェック
                      else:

                        temp_cmd2 = failed_cmd[j]
                        tmp1 = 0
                        count = 0

                        # ORがある場合
                        if com_re_search( " OR ", temp_cmd2 ):

                          execute_when_log('failed_when',log_file_name,host_name,temp_cmd2)

                          # OR実施数分ループ
                          while 1:

                            temp_cmd2 = temp_cmd2[int(tmp1):]
                            temp_cmd2 = temp_cmd2.lstrip()

                            if com_re_search( " OR ", temp_cmd2 ):
                              tmp2 = temp_cmd2.find(' OR ')
                              temp_cmd3 = temp_cmd2[:int(tmp2)]
                              temp_cmd3 = temp_cmd3.lstrip()
                              temp_cmd3 = temp_cmd3.rstrip()

                              register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd3
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              tmp1 = int(tmp2)+3
                              count = count+1

                            else:

                              temp_cmd2 = temp_cmd2.rstrip()

                              #register_temp = p.before
                              # 最後のESCコードから後ろを削除
                              register_temp = last_escstr_cut(p.before)

                              # When結果ログ初期設定
                              when_command = temp_cmd2
                              when_name = 'failed_when'

                              # failed_whenチェック
                              temp3[count] = failed_when_check(when_command,register_temp,log_file_name,host_name)

                              # When結果ログ出力
                              when_check_result(when_name,temp3[count],log_file_name,host_name,when_command)

                              count = count+1
                              break

                          tmp = 1
                          for k in range(0,count,1):
                            if temp3[k] == 0:
                              tmp = 0
                              break

                        # ORがない場合
                        else:

                          #register_temp = p.before
                          # 最後のESCコードから後ろを削除
                          register_temp = last_escstr_cut(p.before)

                          # When結果ログ初期設定
                          when_command = temp_cmd2
                          when_name = 'failed_when'

                          # failed_whenチェック
                          tmp = failed_when_check(when_command,register_temp,log_file_name,host_name)

                          # When結果ログ出力
                          when_check_result(when_name,tmp,log_file_name,host_name,when_command)

                        # エラー確認
                        if tmp == 1:

                          logstr = 'failed_when not match fail exit'
                          private_log_output(log_file_name,host_name,logstr)
                          exec_log_output(logstr)

                          #########################################################
                          # fail exit
                          #########################################################
                          private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

          # with_itemsを使用していないので、そのまま実行
          else:
            expect_prompt(p,log_file_name,host_name,expect_cmd,timeout2)

            # コマンド実行
            exec_command(p,log_file_name,host_name,exec_cmd)

        # スキップフラグが1であった場合
        else:
          # スキップする。
          logstr = 'Skip ...'
          exec_log_output(logstr)
          private_log_output(log_file_name,host_name,logstr)

        if register_used_flg == 1:
          register_cmd == ''
          register_name == ''

        ####################################################
        # read line
        ####################################################
        if skip_flg != 1 and with_items_flg != 1 and exec_when_flg != 1:
          p.readline()

        ####################################################
        # expect(prompt) command execute
        ####################################################
        if skip_flg != 1 and with_items_flg != 1 and exec_when_flg != 1:
          expect_prompt(p,log_file_name,host_name,expect_cmd,timeout2)

        if register_flg == 1:
          register_cmd = unicode2encode(p.before)
          register_name = register_tmp_name
          private_log_output(log_file_name,host_name,register_cmd)
          private_log_output(log_file_name,host_name,register_name)

        ####################################################
        # LF send
        ####################################################
        p.sendline('')

        ####################################################
        # dummy read line
        ####################################################
        p.readline()       

      else:
        # error log
        logstr = 'command not service fail exit'
        private_log_output(log_file_name,host_name,logstr)
        exec_log_output(logstr)

        #########################################################
        # fail exit
        #########################################################
        private_fail_json(obj=module,msg=host_name + ":" + logstr,exec_log=exec_log)

    #########################################################
    # normal exit
    #########################################################
    private_exit_json(obj=module,msg=host_name + ':nomal exit',changed=True, exec_log=exec_log)

  except pexpect.TIMEOUT:
    exec_log_output('except command timeout')
    private_log_output(log_file_name,host_name,'except command timeout')
    #########################################################
    # fail exit
    #########################################################
    module.fail_json(msg=host_name + ': ' + 'except command timeout',exec_log=exec_log)
  except SignalReceive as e:
    exec_log_output(str(e))
    private_log_output(log_file_name,host_name,str(e))
    #########################################################
    # fail exit
    #########################################################
    module.fail_json(msg=host_name + ": " + str(e),exec_log=exec_log)
  # try chuu no module.fail_json de exceptions.SystemExit 
  except SystemExit:
    private_log_output(log_file_name,host_name,"except exceptions.SystemExit")
    #########################################################
    # fail exit
    #########################################################
    module,fail_json(msg=host_name + ": " + "except exceptions.SystemExit",exec_log=exec_log)
  except AnsibleModule_exit as e:
    ## try内でexit_json/fail_jsonをcallするとpythonでexceptが発生
    ## try外でexit_json/fail_jsonをcall
    pass 
  except:
    import sys
    import traceback
    error_type, error_value, tb = sys.exc_info()

    stack_trace = traceback.format_exception(error_type, error_value, tb)
    edit_trace = ''
    for line in stack_trace:
        edit_trace = edit_trace  + line
    private_log_output(log_file_name,host_name,"Exception-------------------------------------------")
    private_log_output(log_file_name,host_name, edit_trace)
    private_log_output(log_file_name,host_name,"----------------------------------------------------")
    exec_log_output('EXCEPTION-------------------------------------------')
    exec_log_output(edit_trace)
    exec_log_output('----------------------------------------------------')

    #########################################################
    # fail exit
    #########################################################
    module.fail_json(msg=host_name + ':exception',exec_log=exec_log)

  #########################################################
  # AnsibleModule exit
  #########################################################
  if exit_dict['code'] == 'exit_json':
    module.exit_json(msg=exit_dict['msg'],changed=exit_dict['changed'], exec_log=exit_dict['exec_log'])
  else:
    module.fail_json(msg=exit_dict['msg'],exec_log=exit_dict['exec_log'])

def private_fail_json(**args) :
  global exit_dict 

  obj          = args.get('obj', 'error')
  ret_msg      = args.get('msg', '')
  ret_exec_log = args.get('exec_log', 'None')
  ret_exp      = args.get('exp', False)

  if type(ret_exec_log) is str:
    ret_exec_log = []
  output_log = []
  for recode in ret_exec_log:
    ##############output_log.append(recode.decode('utf-8','replace'))
    output_log.append(recode)

  exit_dict = {}
  exit_dict['code']      = 'fail_json'
  exit_dict['msg']       = ret_msg
  exit_dict['exec_log']  = output_log

  if ret_exp == False:
    raise AnsibleModule_exit()
  else:
    ubj.fail_json(msg=ret_msg,exec_log=ret_exec_log)

def private_exit_json(**args) :
  global exit_dict 

  obj          = args.get('obj', 'error')
  ret_msg      = args.get('msg', '')
  ret_changed  = args.get('changed', False)
  ret_exec_log = args.get('exec_log', 'None')

  if type(ret_exec_log) is str:
    ret_exec_log = []
  output_log = []
  for recode in ret_exec_log:
    ####output_log.append(recode.decode('utf-8','replace'))
    output_log.append(recode)

  exit_dict = {}
  exit_dict['code']      = 'exit_json'
  exit_dict['msg']       = ret_msg
  exit_dict['changed']   = ret_changed
  exit_dict['exec_log']  = output_log
  raise AnsibleModule_exit()
  ###obj.exit_json(msg=ret_msg,changed=ret_changed, exec_log=output_log)

def private_log(log_file_name,host,var):
  now = datetime.datetime.now()
  f = open(log_file_name,'a')
  f.writelines(now.strftime("%Y%m%d %H:%M:%S") + '[' + host + ']' + var + "\n")
  f.close()

def craete_stdout_file(file,data):
  now = datetime.datetime.now()
  f = open(file,'w')
  f.writelines(data)
  f.close()

def when_check(when_cmd,register_cmd,register_name,host_vars_file,log_file_name,host_name):

 # pass
  global password
  global register_used_flg
  r = re.compile("(.*)(\n)(.*)")

  when_cmd = password_replace(password,when_cmd)

  # whenが"no match"である場合
  if com_re_search( "no match", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # '('が何文字目か検索
        tmp1 = str(when_cmd.find('('))

        # ')'が何文字目か検索
        tmp2 = str(when_cmd.rfind(')'))

        # ( から )までの文字列を抽出
        tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # registerに条件分が一致するか検索
        if com_re_search( tmp3, register_cmd ):

          # 一致した場合、1を返却する
          return 1

        else:

          # 一致しない場合、0を返却する
          return 0

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # '('が何文字目か検索
        tmp1 = str(when_cmd.find('('))

        # ')'が何文字目か検索
        tmp2 = str(when_cmd.rfind(')'))

        # ( から )までの文字列を抽出
        tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # ' no match 'が何文字目か検索
        tmp4 = str(when_cmd.find('no match'))

        # ' no match '前の文字列を抽出
        tmp5 = when_cmd[:int(tmp4)]
        tmp5 = tmp5.lstrip()
        tmp5 = tmp5.rstrip()

        if len(tmp5) == 0:

          # 空の場合、1を返却する
          return 1

        # 一致するか検索
        if com_re_search( tmp3, tmp5 ):

          # 一致した場合、1を返却する
          return 1

        else:

          # 一致しない場合、0を返却する
          return 0

    # register変数が空の場合
    else:

      # '('が何文字目か検索
      tmp1 = str(when_cmd.find('('))

      # ')'が何文字目か検索
      tmp2 = str(when_cmd.rfind(')'))

      # ( から )までの文字列を抽出
      tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # ' no match 'が何文字目か検索
      tmp4 = str(when_cmd.find('no match'))

      # ' no match '前の文字列を抽出
      tmp5 = when_cmd[:int(tmp4)]
      tmp5 = tmp5.lstrip()
      tmp5 = tmp5.rstrip()

      if len(tmp5) == 0:

          # 空の場合、1を返却する
          return 1

      # 一致するか検索
      if com_re_search( tmp3, tmp5 ):

        # 一致した場合、1を返却する
        return 1

      else:
        # 一致しない場合、0を返却する
        return 0

  # whenが"match"である場合
  elif com_re_search( "match", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # '('が何文字目か検索
        tmp1 = str(when_cmd.find('('))

        # ')'が何文字目か検索
        tmp2 = str(when_cmd.rfind(')'))

        # ( から )までの文字列を抽出
        tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # registerに条件分が一致するか検索
        if com_re_search( tmp3, register_cmd ):

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # '('が何文字目か検索
        tmp1 = str(when_cmd.find('('))

        # ')'が何文字目か検索
        tmp2 = str(when_cmd.rfind(')'))

        # ( から )までの文字列を抽出
        tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # ' no match 'が何文字目か検索
        tmp4 = str(when_cmd.find('match'))

        # ' match '前の文字列を抽出
        tmp5 = when_cmd[:int(tmp4)]
        tmp5 = tmp5.lstrip()
        tmp5 = tmp5.rstrip()

        if len(tmp5) == 0:

          # 空の場合、1を返却する
          return 1

        # 一致するか検索
        if com_re_search( tmp3, tmp5 ):

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # '('が何文字目か検索
      tmp1 = str(when_cmd.find('('))

      # ')'が何文字目か検索
      tmp2 = str(when_cmd.rfind(')'))

      # ( から )までの文字列を抽出
      tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # ' no match 'が何文字目か検索
      tmp4 = str(when_cmd.find('match'))

      # ' match '前の文字列を抽出
      tmp5 = when_cmd[:int(tmp4)]
      tmp5 = tmp5.lstrip()
      tmp5 = tmp5.rstrip()

      if len(tmp5) == 0:

        # 空の場合、1を返却する
        return 1

      # 一致するか検索
      if com_re_search( tmp3, tmp5 ):

        # 一致した場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # 比較演算子("==")の場合
  elif com_re_search( "==", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # register取得
        tmp1 = register_cmd

        if len(tmp1) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp1)
        tmp1 = m.group(1)
        tmp1 = tmp1.lstrip()
        tmp1 = tmp1.rstrip()

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp2)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if tmp1 == tmp3:

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # 前の'='の位置を取得
        tmp1 = str(when_cmd.find('='))

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp3 = when_cmd[:int(tmp1)-1]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp4 = when_cmd[int(tmp2)+1:]
        tmp4 = tmp4.lstrip()
        tmp4 = tmp4.rstrip()

        if len(tmp4) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if tmp3 == tmp4:

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # 前の'='の位置を取得
      tmp1 = str(when_cmd.find('='))

      # 後ろの'='の位置を取得
      tmp2 = str(when_cmd.rfind('='))

      # 左辺の文字列を取得
      tmp3 = when_cmd[:int(tmp1)-1]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp4 = when_cmd[int(tmp2)+1:]
      tmp4 = tmp4.lstrip()
      tmp4 = tmp4.rstrip()

      if len(tmp4) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if tmp3 == tmp4:

        # 一致した場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # 比較演算子("!=")の場合
  elif com_re_search( "!=", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # register取得
        tmp1 = register_cmd

        if len(tmp1) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp1)
        tmp1 = m.group(1)
        tmp1 = tmp1.lstrip()
        tmp1 = tmp1.rstrip()

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp2)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if tmp1 != tmp3:

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # 前の'!'の位置を取得
        tmp1 = str(when_cmd.find('!'))

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp3 = when_cmd[:int(tmp1)-1]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp4 = when_cmd[int(tmp2)+1:]
        tmp4 = tmp4.lstrip()
        tmp4 = tmp4.rstrip()

        if len(tmp4) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if tmp3 != tmp4:

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # 前の'!'の位置を取得
      tmp1 = str(when_cmd.find('!'))

      # 後ろの'='の位置を取得
      tmp2 = str(when_cmd.rfind('='))

      # 左辺の文字列を取得
      tmp3 = when_cmd[:int(tmp1)-1]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp4 = when_cmd[int(tmp2)+1:]
      tmp4 = tmp4.lstrip()
      tmp4 = tmp4.rstrip()

      if len(tmp4) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if tmp3 != tmp4:
        # 一致する場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # 比較演算子(">=")の場合
  elif com_re_search( ">=", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # 後ろの'='の位置を取得
        tmp1 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp2 = register_cmd

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp2)
        tmp2 = m.group(1)
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) >= int(tmp3):

          # 一致した場合、0を返却する
          return 0

        else:
          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # 前の'>'の位置を取得
        tmp1 = str(when_cmd.find('>'))

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp3 = when_cmd[:int(tmp1)-1]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp4 = when_cmd[int(tmp2)+1:]
        tmp4 = tmp4.lstrip()
        tmp4 = tmp4.rstrip()

        if len(tmp4) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp3) >= int(tmp4):

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # 前の'>'の位置を取得
      tmp1 = str(when_cmd.find('>'))

      # 後ろの'='の位置を取得
      tmp2 = str(when_cmd.rfind('='))

      # 左辺の文字列を取得
      tmp3 = when_cmd[:int(tmp1)-1]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp4 = when_cmd[int(tmp2)+1:]
      tmp4 = tmp4.lstrip()
      tmp4 = tmp4.rstrip()

      if len(tmp4) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if int(tmp3) >= int(tmp4):

        # 一致した場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # 比較演算子(">")の場合
  elif com_re_search( ">", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # '>'の位置を取得
        tmp1 = str(when_cmd.find('>'))

        # 左辺の文字列を取得
        tmp2 = register_cmd

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp2)
        tmp2 = m.group(1)
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) > int(tmp3):

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # '>'の位置を取得
        tmp1 = str(when_cmd.find('>'))

        # 左辺の文字列を取得
        tmp2 = when_cmd[:int(tmp1)-1]
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) > int(tmp3):

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # '>'の位置を取得
      tmp1 = str(when_cmd.find('>'))

      # 左辺の文字列を取得
      tmp2 = when_cmd[:int(tmp1)-1]
      tmp2 = tmp2.lstrip()
      tmp2 = tmp2.rstrip()

      if len(tmp2) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp3 = when_cmd[int(tmp1)+1:]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if int(tmp2) > int(tmp3):

        # 一致する場合、0を返却する
        return 0

      else:

        # 一致しない場合、1を返却する
        return 1

  # 比較演算子("<=")の場合
  elif com_re_search( "<=", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # 後ろの'='の位置を取得
        tmp1 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp2 = register_cmd

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp2)
        tmp2 = m.group(1)
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) <= int(tmp3):

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # 前の'<'の位置を取得
        tmp1 = str(when_cmd.find('<'))

        # 後ろの'='の位置を取得
        tmp2 = str(when_cmd.rfind('='))

        # 左辺の文字列を取得
        tmp3 = when_cmd[:int(tmp1)-1]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp4 = when_cmd[int(tmp2)+1:]
        tmp4 = tmp4.lstrip()
        tmp4 = tmp4.rstrip()

        if len(tmp4) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp3) <= int(tmp4):

          # 一致した場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # 前の'<'の位置を取得
      tmp1 = str(when_cmd.find('<'))

      # 後ろの'='の位置を取得
      tmp2 = str(when_cmd.rfind('='))

      # 左辺の文字列を取得
      tmp3 = when_cmd[:int(tmp1)-1]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp4 = when_cmd[int(tmp2)+1:]
      tmp4 = tmp4.lstrip()
      tmp4 = tmp4.rstrip()

      if len(tmp4) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if int(tmp3) <= int(tmp4):

        # 一致した場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # 比較演算子("<")の場合
  elif com_re_search( "<", when_cmd ):

    if len(register_name) != 0:

      # whenとregister変数が一致する場合
      if com_re_search( register_name, when_cmd ):

        register_used_flg = 1

        # '<'の位置を取得
        tmp1 = str(when_cmd.find('<'))

        # 左辺の文字列を取得
        tmp2 = register_cmd

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        m = r.match(tmp2)
        tmp2 = m.group(1)
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) < int(tmp3):

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

      # whenとregister変数が一致しない場合、VAR_xxと判断
      else:

        # '<'の位置を取得
        tmp1 = str(when_cmd.find('<'))

        # 左辺の文字列を取得
        tmp2 = when_cmd[:int(tmp1)-1]
        tmp2 = tmp2.lstrip()
        tmp2 = tmp2.rstrip()

        if len(tmp2) == 0:

          # 空の場合、1を返却する
          return 1

        # 右辺の文字列を取得
        tmp3 = when_cmd[int(tmp1)+1:]
        tmp3 = tmp3.lstrip()
        tmp3 = tmp3.rstrip()

        if len(tmp3) == 0:

          # 空の場合、1を返却する
          return 1

        # playbookの記述通りにif文
        if int(tmp2) < int(tmp3):

          # 一致する場合、0を返却する
          return 0

        else:

          # 一致しない場合、1を返却する
          return 1

    # register変数が空の場合
    else:

      # '<'の位置を取得
      tmp1 = str(when_cmd.find('<'))

      # 左辺の文字列を取得
      tmp2 = when_cmd[:int(tmp1)-1]
      tmp2 = tmp2.lstrip()
      tmp2 = tmp2.rstrip()

      if len(tmp2) == 0:

        # 空の場合、1を返却する
        return 1

      # 右辺の文字列を取得
      tmp3 = when_cmd[int(tmp1)+1:]
      tmp3 = tmp3.lstrip()
      tmp3 = tmp3.rstrip()

      if len(tmp3) == 0:

        # 空の場合、1を返却する
        return 1

      # playbookの記述通りにif文
      if int(tmp2) < int(tmp3):
        # 一致する場合、0を返却する
        return 0

      else:
        # 一致しない場合、1を返却する
        return 1

  # is defineの場合
  elif com_re_search( "is define", when_cmd ):

    # 後ろから'is'の位置を取得
    tmp1 = str(when_cmd.rfind('is'))

    # 変数の文字列を取得
    tmp2 = when_cmd[:int(tmp1)-1]
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    with open(host_vars_file, "r") as f:
      tmp3 = f.read()

    if com_re_search( tmp2, tmp3 ):

      # 一致した場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # is undefineの場合
  elif com_re_search( "is undefine", when_cmd ):

    # 後ろから'is'の位置を取得
    tmp1 = str(when_cmd.rfind('is'))

    # 変数の文字列を取得
    tmp2 = when_cmd[:int(tmp1)-1]
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    with open(host_vars_file, "r") as f:
      tmp3 = f.read()

    if com_re_search( tmp2, tmp3 ):

      # 一致した場合、1を返却する
      return 1

    else:
      # 一致した場合、0を返却する
      return 0

def failed_when_check(when_cmd,register_cmd,log_file_name,host_name):

 # pass
  global password
  r = re.compile("(.*)(\n)(.*)")

  when_cmd = password_replace(password,when_cmd)

  # whenが"no match"である場合
  if com_re_search( "no match", when_cmd ):

    # '('が何文字目か検索
    tmp1 = str(when_cmd.find('('))

    # ')'が何文字目か検索
    tmp2 = str(when_cmd.rfind(')'))

    # ( から )までの文字列を抽出
    tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # registerに条件分が一致するか検索
    if com_re_search( tmp3, register_cmd ):

      # 一致した場合、1を返却する
      return 1

    else:
      # 一致しない場合、0を返却する
      return 0

  # whenが"match"である場合
  elif com_re_search( "match", when_cmd ):

    # '('が何文字目か検索
    tmp1 = str(when_cmd.find('('))

    # ')'が何文字目か検索
    tmp2 = str(when_cmd.rfind(')'))

    # ( から )までの文字列を抽出
    tmp3 = when_cmd[int(tmp1)+1:int(tmp2)]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # registerに条件分が一致するか検索
    if com_re_search( tmp3, register_cmd ):

      # 一致した場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子("==")の場合
  elif com_re_search( "==", when_cmd ):

    # 後ろの'='の位置を取得
    tmp1 = str(when_cmd.rfind('='))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if tmp2 == tmp3:

      # 一致した場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子("!=")の場合
  elif com_re_search( "!=", when_cmd ):

    # 後ろの'='の位置を取得
    tmp1 = str(when_cmd.rfind('='))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if tmp2 != tmp3:
      # 一致する場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子(">=")の場合
  elif com_re_search( ">=", when_cmd ):

    # 後ろの'='の位置を取得
    tmp1 = str(when_cmd.rfind('='))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if int(tmp2) >= int(tmp3):

      # 一致した場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子(">")の場合
  elif com_re_search( ">", when_cmd ):

    # '>'の位置を取得
    tmp1 = str(when_cmd.find('>'))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if int(tmp2) > int(tmp3):
      # 一致する場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子("<=")の場合
  elif com_re_search( "<=", when_cmd ):

    # 後ろの'='の位置を取得
    tmp1 = str(when_cmd.rfind('='))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if int(tmp2) <= int(tmp3):

      # 一致した場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

  # 比較演算子("<")の場合
  elif com_re_search( "<", when_cmd ):

    # '<'の位置を取得
    tmp1 = str(when_cmd.find('<'))

    # 左辺の文字列を取得
    tmp2 = register_cmd
    m = r.match(tmp2)
    tmp2 = m.group(1)
    tmp2 = tmp2.lstrip()
    tmp2 = tmp2.rstrip()

    # 右辺の文字列を取得
    tmp3 = when_cmd[int(tmp1)+1:]
    tmp3 = tmp3.lstrip()
    tmp3 = tmp3.rstrip()

    if len(tmp3) == 0:

      # 空の場合、1を返却する
      return 1

    # playbookの記述通りにif文
    if int(tmp2) < int(tmp3):
      # 一致する場合、0を返却する
      return 0

    else:
      # 一致しない場合、1を返却する
      return 1

def last_escstr_cut(sometext):
  m_split = re.split('\x1b',unicode2encode(sometext))
  stdout_data = ""
  idx = 1
  max = len(m_split)
  for data in m_split:
    stdout_data += data
    idx  += 1
    if idx == max:
      break;
    newline = binascii.a2b_hex(b'1b')
    newline = newline.decode('utf-8','replace')
    stdout_data += newline
  return stdout_data

def password_replace(password,data):
  rep_str = data.replace('<<__loginpassword__>>', password)
  return rep_str

def exec_log_output(log,replace_flg = True):
  global exec_log
  global output_password
  if replace_flg == True:
    output_log = password_replace(output_password,str(log))
  exec_log.append(output_log)

def debug_exec_log_output(log,replace_flg = True):
  global exec_log
  global output_password
  if replace_flg == True:
    output_log = password_replace(output_password,str(log))
  exec_log.append(output_log)

def private_log_output(log_file_name,host_name,log,replace_flg = True):
  global output_password
  if replace_flg == True:
    output_log = password_replace(output_password,str(log))
  private_log(log_file_name,host_name,output_log)

# python2/3で文字型に差異があり、re.searchに渡す文字型を調整
def com_re_search(in_find_str,in_str):
  return re.search( str2unicode(in_find_str), str2unicode(in_str) )

def str2unicode(in_str,code='utf-8'):
  out_unicode = in_str
  if sys.version_info.major == 2:
    if type(in_str) is str:
      # unicodeを指定文字コードにエンコードする。
      out_unicode = in_str.decode('utf-8')
  return out_unicode

def unicode2encode(in_str,code='utf-8',errors='replace'):

  # python version確認
  if sys.version_info.major == 2:
    if type(in_str) is unicode:
      # unicodeを指定文字コードにエンコードする。
      return in_str.encode(code,errors)
  else:
    if type(in_str) is bytes:
      # バイト文字列を指定文字コードにデコードする。
      return in_str.decode(code,errors)
  return in_str

def expect_prompt(p,log_file_name,host_name,cmd,timeout):
  global password
  global prompt_log_str

  # 隠蔽文字デコード
  pass_rep_cmd = password_replace(password,cmd)

  # promptログ生成
  # prompt: [%s]
  logstr =  prompt_log_str % (cmd)

  # promptログ出力
  exec_log_output(logstr)
  private_log_output(log_file_name,host_name,logstr)

  # prompt待ち
  p.expect(pass_rep_cmd, timeout=int(timeout))

  # 標準出力ログ
  exec_log_output('prompt match')
  exec_log_output('before:[' + unicode2encode(p.before) + ']')
  exec_log_output('match:[' + unicode2encode(p.after) + ']')
  exec_log_output('after:[' + unicode2encode(p.buffer) + ']')
  private_log_output(log_file_name,host_name,"prompt match")
  private_log_output(log_file_name,host_name,"before:[" + unicode2encode(p.before) + "]")
  private_log_output(log_file_name,host_name,"match:[" + unicode2encode(p.after) + "]")
  private_log_output(log_file_name,host_name,"after:[" + unicode2encode(p.buffer) + "]")

  private_log_output(log_file_name,host_name,"Ok")

def exec_command(p,log_file_name,host_name,cmd):
  global password
  global command_log_str

  # 隠蔽文字デコード
  pass_rep_cmd = password_replace(password,cmd)

  # execコマンドログ生成
  # "command: [%s]"
  logstr =  command_log_str % (cmd)

  # execコマンドログ出力
  exec_log_output(logstr)
  private_log_output(log_file_name,host_name,logstr)

  # コマンド実行
  p.sendline(pass_rep_cmd)

  private_log_output(log_file_name,host_name,"Ok")

def when_check_result(when_name,ret,log_file_name,host_name,cmd):
  global when_log_str
  if ret == 0:
    # "%s: [%s] %s"
    logstr = when_log_str % (when_name, cmd, 'Match')
  else:
    # "%s: [%s] %s"
    logstr = when_log_str % (when_name, cmd, 'No Match')
  exec_log_output(logstr)
  private_log_output(log_file_name,host_name,logstr)

def execute_when_log(when_name,log_file_name,host_name,logstr):
  global execute_when_log_str
  # "%s: [%s]"
  logstr = execute_when_log_str % (when_name,logstr)
  # exec_log_output(logstr)
  # private_log_output(log_file_name,host_name,logstr)

main()
