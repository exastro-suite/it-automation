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

// ----contants
    // 無操作状態でセッションタイムアウト時にstatusにセットされる
    define('AUTH_IDLED', -1);

    // ログイン有効期限切れてセッションタイムアウト時にstatusにセットされる
    define('AUTH_EXPIRED', -2);

    // 認証時にID/PASSが正しくない場合にstatusにセットされる
    define('AUTH_WRONG_LOGIN', -3);

    // ログイン認証時のREQUEST METHODが指定されていない場合にセットされる
    define('AUTH_METHOD_NOT_SUPPORTED', -4);

    // ログイン関数の実行ができなかった場合にstatusにセットされる
    define('AUTH_LOGIN_FUNCTION_ABORT', -5);

    // ログインフォーム表示関数の実行ができなかった場合にstatusにセットされる
    define('AUTH_LOGIN_FORM_FUNCTION_ABORT', -6);

    // セッション情報の正当性が確認できなかった場合にstatusにセットされる
    define('AUTH_SESSION_NOT_AVALABLE', -7);
// contants----

class Auth
{
  // propeties
    /**
      *  ログインの有効期限をセットする(単位:秒)
      *   0をセットすると有効期限のチェックをしない
      *
      *  @type   integer
      *  @see    checkAuth(), setExpire()
      **/
    protected $expire = 0;

    /**
      *  ログイン有効期限超過でタイムアウトしたときにtrueがセットされる
      *
      *  @type   bool
      *  @see    checkAuth()
      **/
    protected $expired = false;

    /**
      *  未操作タイムアウトの期限(単位:秒)
      *  0をセットすると未操作期限のチェックをしない
      *
      *  expireとidleの違い
      *  expire: ログインからタイムアウトまでの期限
      *  idle: 前回の操作から次の操作までの未操作のタイムアウト期限
      *  idleの場合は操作するとリセットされるがexpireはリセットされない
      *  idleのリセット処理はcheckAuth()で実行される
      *
      *  @type   integer
      *  @see    checkAuth(), setIdle()
      **/
    protected $idle = 0;

    /**
      *  未操作タイムアウトしたときにtrueにセットされる
      *
      *  @type bool
      *  @see  checkAuth()
      **/
    protected $idled = false;

    /**
      *  formからpostされたid/passの認証を行うコールバック関数をセットする
      *  コールバック関数への引数: なし
      *  期待するコールバックの返却値
      *    true:  認証成功
      *    false; 認証失敗
      *  認証失敗の場合statusにAUTH_WRONG_LOGINをセットする
      *
      *  @type string
      *  @see  login(), setLoginFunction()
      **/
    protected $loginFunction = '';

    /**
      *  ログインフォームを表示するコールバック関数をセットする
      *  コールバック関数への引数: $this->status
      *  コールバック関数の返却値: void
      *
      *  @type string
      *  @see  login(), setLoginFormFunction()
      **/
    protected $loginFormFunction = '';

    /**
      *  ログインを許可するフラグ
      *  ログインフォームの表示とid/passの認証の許可/不許可を設定する
      *    true:  許可する
      *    false: 許可しない
      *
      *  @type  bool
      *  @see   start(), login(), setAllowLogin()
      **/
    protected $allowLogin = true;

    /**
      *  id/pass認証を許可するREQUEST METHODを定義する
      *
      *  @type string
      *  @see  login()
      **/
    protected $allowLoginMethod = 'POST';

    /**
      *  現在のstatusを保持する
      *
      *  @type integer
      *  @see  checkAuth(), setAuth(), start(), login(), getStatus()
      **/
    protected $status = 0;

    /**
      *  ユーザ名
      *
      *  @type string
      **/
    protected $username = '';

    /**
      *  session name
      *  セッション名
      *  cookieにつけるセッション名
      *
      *  @type string
      **/
    protected $sessionName = '';

    /**
      *  $_SESSIONの中に作成するこのclassで使うディレクトリ名
      *
      *  @type string
      **/
    protected $sessionAuthName = 'Auth';

    /**
      *  id/pass認証での$_POSTでのsernameのkey name
      *  setLoginFunctionに設定した関数の返却値がtrueだった場合にsetAuth()にusernameとして設定する値はここで設定したpost値から取得する
      *
      *  @type string
      **/
    protected $postUsername = 'username';

    /**
      *  セッションを保持する
      *
      *  @type array
      **/
    protected $session;

    /**
      *  $_SERVERを保持する
      *
      *  @type array
      **/
    protected $server;

    /**
      *  $_POSTを保持する
      *
      *  @type array
      **/
    protected $post;

    /**
      *  ログアウト時のユーザ名を保持する
      *  外部から直接参照されているためpublicにしている
      *
      *  @type   string
      *  @access public
      **/
    public $username_on_logout = '';

    /**
      *  致命的エラー発生時のメッセージを保持する
      *
      *  @type string
      *  @access public
      **/
    public $errMsg = '';
  // properties----

    /**
      *  Constructor
      *  コンストラクタ
      *  特に何もしない
      *
      *  @return void
      **/
    public function __construct()
    {
        //
    }

    /**
      *  認証を行う
      *  認証は全てこのメソッドで行う
      *  必要なプロパティを全てsetメソッドで指定してからstart()を呼ぶこと
      *  処理の流れ
      *  sessionName指定->session_start
      *  $_SESSIONから認証用のデータ復元(restoreAuthData())
      *  正当な認証か確認(checkAuth())
      *  正当な認証が確認できない場合以下の認証を試みる(allowLoginがtrueの場合)
      *  ログインを試みる(login())
      *  認証ができていない場合はログインフォーム関数を呼んでフォームを表示する
      *
      *  @return void
      *  @access public
      **/
    public function start()
    {
        // Auth::start() called
        if (!empty($this->sessionName)) {
            session_name($this->sessionName);
        }

        // ----try session start
        if (session_status() !== PHP_SESSION_ACTIVE) {
            try {
                @session_start();
                if (!session_id()) {
                    throw new Exception('Session could not be started by Auth, ');
                }
            } catch (ErrorException $e) {
                $this->errMsg = $e->getMessage();
                return;
            }
        }
        // try session start----

        // ----$_SESSION内に認証データがあるか確認ない場合は初期化
        if (empty($_SESSION[$this->sessionAuthName])) {
            // sessionId change(for security)
            session_regenerate_id(true);
            $_SESSION[$this->sessionAuthName] = [];
        }
        // $_SESSION内に認証データがあるか確認ない場合は初期化----

        // ----set properties
        $this->session =& $_SESSION[$this->sessionAuthName];
        $this->server =& $_SERVER;
        $this->post =& $_POST;
        // set properties----
        // ----restore session data
        $this->restoreAuthData();
        // estore session data----

        // ----セッションからログインデータを復元し正当なログインか確認
        if ($this->checkAuth()) {
            // ログイン済み
            return;
        }
        // セッションからログインデータを復元し正当なログインか確認----

        // ログイン許可フラグ確認
        if ($this->allowLogin) {
            // ----id/pass認証
            if ($this->login()) {
                // ログイン成功
                return;
            }
            // id/pass認証----
            // id/pass認証失敗
            // ----コールバックログインフォーム関数が利用可能か確認
            if (empty($this->loginFormFunction) || !is_callable($this->loginFormFunction)) {
                // ログインフォーム表示できない
                $this->status = AUTH_LOGIN_FORM_FUNCTION_ABORT;
                return;
            }
            // コールバックログインフォーム関数が利用可能か確認----
            // ----ログインフォーム表示
            call_user_func($this->loginFormFunction, $this->status);
            // ログインフォーム表示----
        }
    }

    /**
      *  ログインセッションデータを復元
      *
      *  @return void
      *  @access protected
      **/
    protected function restoreAuthData()
    {
        // Auth::restoreAuthData called
        if (!empty($this->session) && is_array($this->session) && !empty($this->session['registered'])
            && is_bool($this->session['registered']) && $this->session['registered'] === true
            && !empty($this->session['username']) && is_string($this->session['username'])) {
            // ----sessionの正当性をチェックして問題なければusernameをメンバー変数にセットする
            $this->username = $this->session['username'];
            // sessionの正当性をチェックして問題なければusernameをメンバー変数にセットする----
        }
    }

    /**
      *  ログインを実行し成功したらログインセッション作成(setAuth())に投げる
      *  setAuthの引数に$_POST[postUsername]を使用
      *
      *  @return bool (success: true/ failed: false)
      *  @access protected
      **/
    protected function login()
    {
        // Auth::login() called
        // ----メソッドの確認
        if ($this->server['REQUEST_METHOD'] !== $this->allowLoginMethod) {
            $this->status = AUTH_METHOD_NOT_SUPPORTED;
            return false;
        }
        // メソッドの確認----
        // コールバックログイン関数が利用可能か確認
        if (empty($this->loginFunction) || !is_callable($this->loginFunction)) {
            // コールバックログイン関数を実行できない
            $this->status = AUTH_LOGIN_FUNCTION_ABORT;
            return false;
        }
        // ----ログイン実行
        if (call_user_func($this->loginFunction)) {
            // ログイン成功
            $this->setAuth($this->post[$this->postUsername]);
            return true;
        }
        // ログイン実行----
        // ログイン失敗
        $this->status = AUTH_WRONG_LOGIN;
        return false;
    }

    /**
      *  ログインに成功したときに認証データをセッションにセットする
      *
      *  @param  string username
      *  @return void
      *  @access protected
      **/
    protected function setAuth($username)
    {
        // Auth::setAuth() called
        session_regenerate_id(true);

        if (empty($this->session) || !is_array($this->session)) {
            $this->session = [];
        }

        if (empty($this->session['data'])) {
            $this->session['data'] = [];
        }
        $this->session['data']['REMOTE_ADDR'] = '';
        if (!empty($this->server['REMOTE_ADDR'])) {
            $this->session['data']['REMOTE_ADDR'] = $this->server['REMOTE_ADDR'];
        }
        $this->session['data']['USER_AGENT'] = '';
        if (!empty($this->server['HTTP_USER_AGENT'])) {
            $this->session['data']['USER_AGENT'] = $this->server['HTTP_USER_AGENT'];
        }
        $this->session['data']['FORWADED_FOR'] = '';
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $this->session['data']['FORWADED_FOR'] = $this->server['HTTP_X_FORWARDED_FOR'];
        }
        $this->session['registered'] = true;
        $this->session['username'] = $username;
        $now = time();
        $this->session['loggedin_at']  = $now;
        $this->session['last_accessed_at'] = $now;
        $this->status = 0;
    }

    /**
      *  セッションが正当なログインか確認する
      *  正当なログインの場合idleの基準時間をリセットする
      *  正当なログインでない場合はlogout()を実行する
      *
      *  @access public
      *  @return boolean  Whether or not the user is authenticated.
      **/
    public function checkAuth()
    {
        // Auth::checkAuth() called
        // セッションデータの存在確認
        if (!empty($this->session) && is_array($this->session)) {
            // ----セッションデータあり
            // ----セッションの正当性チェック
            if (empty($this->session['registered']) || !is_bool($this->session['registered'])
                || $this->session['registered'] !== true || empty($this->session['username'])
                || !is_string($this->session['username'])) {
                // 正当なログインと確認できない
                $this->status = AUTH_SESSION_NOT_AVALABLE;
                $this->logout();
                return false;
            }
            // セッションの正当性チェック----

            // ----expireチェック
            if ($this->expire > 0
                && !empty($this->session['loggedin_at'])
                && ($this->session['loggedin_at'] + $this->expire) < time()) {
                // Session Expired
                $this->expired = true;
                $this->status = AUTH_EXPIRED;
                $this->logout();
                return false;
            }
            // expireチェック----

            // ----idleチェック
            if ($this->idle > 0
                && !empty($this->session['last_accessed_at'])
                && ($this->session['last_accessed_at'] + $this->idle) < time()) {
                // Session Idle Time Reached
                $this->idled = true;
                $this->status = AUTH_IDLED;
                $this->logout();
                return false;
            }
            // idleチェック----

            // 確認OK
            // idlle基準時間をリセット
            $this->session['last_accessed_at'] = time();
            return true;

            // セッションデータあり----
        }
        // ----セッションデータなし
        return false;
        // セッションデータなし----
    }

    /**
      *  ログアウト
      *
      *  @access public
      *  @return void
      **/
    public function logout()
    {
        // Auth::logout() called

        // ログアウト時のusernameを別プロパティで保持
        $this->username_on_logout = $this->username;

        $this->username = '';
        $this->session = null;
    }

    /**
      *  Set session Name
      *
      *  @param  string
      *  @return void
      *  @access public
      **/
    public function setSessionName($sessionName = '')
    {
        if (!empty($sessionName) && is_string($sessionName)) {
            $this->sessionName = $sessionName;
        }
    }

    /**
      *  Set allow login
      *
      *  @param bool
      *  @return void
      *  @access public
      **/
    public function setAllowLogin($allowLogin = true)
    {
        if (!is_null($allowLogin) && is_bool($allowLogin)) {
            $this->allowLogin = $allowLogin;
        }
    }

    /**
      *  Set the maximum expire time(sec)
      *
      *  @param  integer time in seconds
      *  @return void
      *  @access public
      **/
    public function setExpire($expire = 0)
    {
        if (!empty($expire) && is_numeric($expire) && $expire >= 0) {
            $this->expire = $expire;
        }
    }

    /**
      *  Set the maximum idle time(sec)
      *
      *  @param  integer time in seconds
      *  @return void
      *  @access public
      **/
    public function setIdle($idle = 0)
    {
        if (!empty($idle) && is_numeric($idle) && $idle >= 0) {
            $this->idle = $idle;
        }
    }

    /**
      *  Set callback login function
      *
      *  @string callback function name
      *  @return void
      *  @access public
      **/
    public function setLoginFunction($loginFunctionName = '')
    {
        if (!empty($loginFunctionName) && is_callable($loginFunctionName)) {
            $this->loginFunction = $loginFunctionName;
        }
    }

    /**
      *  Set callback view login form function
      *
      *  @string callback function name
      *  @return void
      *  @access public
      */
    public function setLoginFormFunction($loginFormFunctionName = '')
    {
        if (!empty($loginFormFunctionName) && is_callable($loginFormFunctionName)) {
            $this->loginFormFunction = $loginFormFunctionName;
        }
    }

    /**
      *  セッションに保持されているデータを返却する
      *  引数: name
      *    nameが指定されていた場合: session[$name]またはsession['data'][$name]を返却する
      *    nameが指定されていない場合: $this->session全体を返却する
      *
      *  @param
      *  @return mixed  (string or array)
      *  @access public
      **/
    public function getSessionData($name = null)
    {
        if (empty($this->session)) {
            return;
        }
        if (emptyt($name)) {
            return $this->session;
        }
        if (isset($this->session[$name])) {
            return $this->session[$name];
        }
        if (isset($this->session['data'][$name])) {
            return $this->session['data'][$name];
        }
        return;
    }

    /**
      *  Get the username
      *
      *  @return string
      *  @access public
      **/
    public function getUsername()
    {
        if (!empty($this->username)) {
            return $this->username;
        }
        return '';
    }

    /**
      *  Get the current status
      *
      *  @return string
      *  @access public
      **/
    public function getStatus()
    {
        return $this->status;
    }
}
