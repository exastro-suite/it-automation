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
    public $errCode = '';

    /**
      *  エラーが発生判定フラグ
      *
      *  @type bool
      *  @access public
      **/
    public $isError = false;

    /**
      *  エラー発生時の詳細を保持
      *
      *  @type string
      *  @access public
      **/
    public $errDetail = '';
  // properties----

    /**
      *  Constructor
      *  コンストラクタ
      *
      *  @return void
      **/
    public function __construct()
    {
        //
    }

    /**
      * initiarize
      * 事前処理
      **/
    public function initialize ()
    {
        // ----try session start
        if (session_status() !== PHP_SESSION_ACTIVE) {
            try {
                if (!empty($this->sessionName)) {
                    session_name($this->sessionName);
                }
                @session_start();
                if (!session_id()) {
                    throw new Exception('Session could not be started by Auth, ');
                }
            } catch (ErrorException $e) {
                $this->errMsg = $e->getMessage();
                return;
            }
            if (empty($_SESSION[$this->sessionAuthName])) {
                session_regenerate_id(true);
                $_SESSION[$this->sessionAuthName] = [];
            }
            $this->session =& $_SESSION[$this->sessionAuthName];
            $this->server =& $_SERVER;
            $this->post =& $_POST;
        }
        // try session start----
    }

    /**
      *  認証を行う
      *  認証は全てこのメソッドで行う
      *  必要なプロパティを全てsetメソッドで指定してからstart()を呼ぶこと
      *  処理の流れ
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
        $this->initialize();
        // ----$_SESSION内に認証データがあるか確認ない場合は初期化
        //if (empty($_SESSION[$this->sessionAuthName])) {
        //    // sessionId change(for security)
        //    session_regenerate_id(true);
        //    $_SESSION[$this->sessionAuthName] = [];
        //}
        // $_SESSION内に認証データがあるか確認ない場合は初期化----
        //
        //$this->session =& $_SESSION[$this->sessionAuthName];
        //$this->server =& $_SERVER;
        //$this->post =& $_POST;
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
        //session_regenerate_id(true);

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
        if (empty($name)) {
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

    // 最終ログイン日時設定
    protected function setLastLoginTime() {
      $objDBCA = new DBConnectAgent();
      $tmpResult = $objDBCA->connectOpen();
      $tmpArrayBind = array('USERNAME'=>$this->session['username'] );
      $sql = "UPDATE A_ACCOUNT_LIST SET LAST_LOGIN_TIME = SYSDATE() WHERE USERNAME = :USERNAME";
      $objQuery = $objDBCA->sqlPrepare($sql);
      $objQuery->sqlBind($tmpArrayBind);
      $r = $objQuery->sqlExecute();
    }
}

class OAuth2 extends Auth
{
  // ----propeties
    /**
      * config
      * @type array
      **/
    protected $config = [
        'providerId'       => '',
        'providerName'     => '',
        'providerLogo'     => '',
        'clientId'         => '',
        'clientSecret'     => '',
        'authorizationUri' => '',
        'accessTokenUri'   => '',
        'resourceOwnerUri' => '',
        'scope'            => '',
        'id'               => '',
        'name'             => '',
        'email'            => '',
        'imageUrl'         => '',
        'proxy'            => '',
        'visibleFlag'      => '',
        'debug'            => '',
        'ignoreSslVerify'  => '',
    ];

    // provider設定用のconfig取得callback関数名保持変数
    protected $setConfigFunction = '';

    // authorizationUriにアクセスする際のproviderId判定用のGETパラメーター名
    public $httpAuthorizationProviderParam = 'providerId';

    // providerからのcallbackリクエスト(code返却されるURI)判別用GETパラメーター名
    public $httpCallbackParam = 'callback';

    /**
      * provider id
      * @type string
      **/
    protected $providerId = '';

    /**
      * regist form function name
      * callback function name
      * function args ($this->session)   //
      * $this->session is [
      *                      'provider_id'         => provider_id,
      *                      'provider_name'       => provider_name,
      *                      'provider_user_id'    => provider_user_id,
      *                      'provider_user_name'  => provider_user_name,
      *                      'provider_user_email' => provider_user_email,
      *                    ]
      *
      * @type string
      **/
    protected $registFormFunction = '';

    /**
      * regist function name
      * callback function name
      * function args ($this->session)   //
      * $this->session is [
      *                      'provider_id'         => provider_id,
      *                      'provider_name'       => provider_name,
      *                      'provider_user_id'    => provider_user_id,
      *                      'provider_user_name'  => provider_user_name,
      *                      'provider_user_email' => provider_user_email,
      *                    ]
      *
      * @type string
      **/
    protected $registFunction = '';

    /**
      * find user function
      * callback function name
      * function args ($this->session)   //
      *   $this->session is [
      *                      'provider_id'         => provider_id,
      *                      'provider_user_id'    => provider_user_id,
      *                    ]
      *    @function return $username
      * @type string
      **/
    protected $findUserFunction = '';

    /**
      * redirect URI
      * @type string
      **/
    protected $redirectUri = '';
    public $isDebug = false;
  // propeties----

    /**
      *  Constructor
      *  コンストラクタ
      *  特に何もしない
      *
      *  @return void
      **/
    /**
      *  setConfig
      *
      *  @return void
      **/
    public function __construct()
    {
        //
    }

    public function setConfigFunction ($functionName)
    {
        if (!empty($functionName)) {
            $this->setConfigFunction = $functionName;
        }
    }

    /**
      * setConfig
      * providerに関する設定を一括または個別に設定する
      * fefautの設定は必要なkeyとブランク値を設定している
      + 不要なkeyを追加設定はできない。
      * defaultで存在しているkeyのみ上書き可能
      **/
    protected function setConfig()
    {
        if (empty($this->getProvider())) {
            $this->isError = true;
            $this->errMsg = 'can not get provider';
            $this->errCode = 'ERR-SETCONFIG-01';
            return false;
        }
        // コールバック関数が利用可能か確認
        if (empty($this->setConfigFunction) || !is_callable($this->setConfigFunction)) {
            // コールバックログイン関数を実行できない
            $this->isError = true;
            $this->errMsg = 'can not use set config function';
            $this->errCode = 'ERR-01-02';
            return false;
        }
        $config = call_user_func($this->setConfigFunction, $this->providerId);
        if (empty($config)) {
            $this->isError = true;
            $this->errMsg = 'config is empty';
            $this->errCode = 'ERR-SETCONFIG-03';
            return false;
        }
        if (!is_array($config)) {
            $this->isError = true;
            $this->errMsg = 'config formart invalid';
            $this->errCode = 'ERR-SETCONFIG-04';
            return false;
        }
        foreach ($config as $k=>$v) {
            if (isset($this->config[$k])) {
                $this->config[$k] = $v;
            }
        }
        if ($this->config['debug'] == true || $this->config['visibleFlag'] === 0) {
            $this->isDebug = true;
        }
        return true;
    }

    /**
      * getConfig
      * @pram key: return $config[key]
      *       null: rewturn $config
      */
    public function getConfig($key = null) {
        if (empty($key)) {
            return $this->config;
        }
        if (is_string($key) && isset($this->config[$key])) {
           return $this->config[$key];
        }
    }

    /**
      * set NextURI
      *
      * @return void
      */
    public function setNextUri ($nextUri)
    {
        if (!empty($nextUri)) {
            $this->nextUri = $nextUri;
        }
    }

    /**
      * set redirect URI
      *
      * @return void
      */
    public function setRedirectUri ($redirectUri)
    {
        if (!empty($redirectUri)) {
            $this->redirectUri = $redirectUri;
        }
    }

    public function getProvider ()
    {
        $this->initialize();
        if (isset($_GET[$this->httpAuthorizationProviderParam]) && !empty($_GET[$this->httpAuthorizationProviderParam])) {
            $this->providerId = htmlspecialchars($_GET[$this->httpAuthorizationProviderParam], ENT_QUOTES, "UTF-8");
            return $this->providerId;
        }
        if (isset($this->session['provider_id']) && !empty($this->session['provider_id'])) {
            $this->providerId = $this->session['provider_id'];
            return $this->providerId;
        }
    }

    public function setRegistFormFunction($functionName) {
        if (!empty($functionName)) {
            $this->registFormFunction = $functionName;
        }
    }

    public function setRegistFunction($functionName) {
        if (!empty($functionName)) {
            $this->registFunction = $functionName;
        }
    }

    public function setFindUserFunction($functionName) {
        if (!empty($functionName)) {
            $this->findUserFunction = $functionName;
        }
    }

    /**
      *  start
      *  OAuth2認証はここで行う
      * @return void
      */
    public function start()
    {
        $this->initialize();
        if (isset($this->session['debug'])) {
            $this->isDebug = 1;
            unset($this->session['debug']);
        }
        //$this->debug('start()');
        if (!$this->setConfig()) {
            return false;
        }
        if (isset($_GET[$this->httpAuthorizationProviderParam])) {
            // ----外部provider認証(ブラウザ経由の認証)
            return $this->authorization();
            // 外部provider認証----
        }
        if (isset($_GET[$this->httpCallbackParam])) {
            // ----外部providerからcode返却 , codeをtokenに交換, tokenを使ってuser情報取得
            if (!$this->callback()) {
                return false;
            }
        }

        // ----ローカルDBへの登録関数の呼び出し
        //$this->debug('call regist user func');
        // コールバック関数が利用可能か確認
        if (empty($this->registFunction) || !is_callable($this->registFunction)) {
            // コールバックログイン関数を実行できない
            $this->isError = true;
            $this->errMsg = 'can not use regist function';
            $this->errCode = 'ERR-START-01';
            return false;
        }
        // ----ローカルDBへの登録関数実行
        // ローカルDBへの登録関数実行時のエラーはisError,errMsg,errCodeにセットして返却する
        $strRegMsg = call_user_func($this->registFunction, $this->session);
        if ($this->isError) {
            return false;
        }
        // ローカルDBへの登録関数実行----
        // ローカルDBへの登録関数の呼び出し----

        // ----ローカルDBに登録されているusernameの取得用関数の呼び出し
        //$this->debug('call find user func');
        // コールバック関数が利用可能か確認
        if (empty($this->findUserFunction) || !is_callable($this->findUserFunction)) {
            // コールバックログイン関数を実行できない
            $this->isError = true;
            $this->errMsg = 'can not use find user function';
            $this->errCode = 'ERR-START-02';
            return false;
        }
        $username = call_user_func($this->findUserFunction, $this->session);
        // ローカルDBに登録されているusernameの取得用関数の呼び出し----

        //$this->debug('[class]'.__CLASS__.',username:'.$username);
        if (!empty($username)) {
            // $username取得できた
            // ログイン処理
            $this->setAuth($username);
            $this->checkAuth();
            $this->nextUri = $this->session['next_uri'];
            unset($this->session['next_uri']);
            if (!empty($this->nextUri)) {
                header("Location: $this->nextUri");
                return true;
            }
            // next uriがなかったらどうしよう。。。(認証には成功しているがリダイレクト先が不明)
        }

        // $usernameを取得できなかった
        // 未登録ユーザーは登録フォーム表示関数の呼び出し
        // コールバック関数が利用可能か確認
        if (empty($this->registFormFunction) || !is_callable($this->registFormFunction)) {
            // コールバックログイン関数を実行できない
            $this->isError = true;
            $this->errMsg = 'can not use regist form function';
            $this->errCode = 'ERR-START-03';
            return false;
        }
        // ---formを表示
        // form表示関数のエラーはfalseをreturnしてisError,errMsg,errCodeをセットして返却する
        //call_user_func($this->registFormFunction, $this->session, $strRegMsg, $this->isError, $this->errMsg, $this->errCode);
        call_user_func($this->registFormFunction, $this->session, $strRegMsg);
        if ($this->isError) {
            return false;
        }
        // formを表示----

        // 元の処理に戻す
        return true;
    }

    protected function authorization()
    {
        if (empty($this->config['authorizationUri'])) {
            $this->isError = true;
            $this->errMsg = 'authorizationUri is empty';
            $this->errCode = 'ERR-AUTHORIZATION-01';
            return false;
        }
        if (empty($this->config['clientId'])) {
            $this->isError = true;
            $this->errMsg = 'clientId is empty';
            $this->errCode = 'ERR-AUTHORIZATION-02';
            return false;
        }
        if (empty($this->redirectUri)) {
            $this->isError = true;
            $this->errMsg = 'redirectUri is empty';
            $this->errCode = 'ERR-AUTHORIZATION-03';
            return false;
        }
        // ----clear session
        $this->session = [];
        // clear session----
        $state = bin2hex(file_get_contents('/dev/urandom', false, null, 0, 32));
        $this->session['state'] = $state;
        if ($this->isDebug) {
            $this->session['debug'] = 1;
        }
        $this->session['provider_id'] = $this->providerId;
        $this->session['next_uri'] = $this->nextUri;
        //echo '<pre>(1)$_SESSION:'.print_r($_SESSION,true).'</pre>';
        $uri = $this->config['authorizationUri'];
        $query = [
            'response_type'   => 'code',
            'redirect_uri'    => $this->redirectUri,
            'client_id'       => $this->config['clientId'],
            'state'           => $state,
        ];
        if (!empty($this->config['scope'])) {
            $query += ['scope' => $this->config['scope']];
        }
        if (preg_match('/\?/',$uri)) {
            $uri .= '&'.http_build_query($query);
        } else {
            $uri .= '?'.http_build_query($query);
        }
        header('Location: '.$uri);
    }

    protected function callback()
    {
        if (isset($this->session['provider_id']) && isset($this->session['provider_user_id'])) {
            // 外部ログインン済み
            return true;
        }
        // ----check config
        $check_keys = ['providerId', 'providerName', 'clientId', 'clientSecret', 'accessTokenUri', 'resourceOwnerUri', 'id', 'name'];
        foreach ($check_keys as $key) {
            if (empty($this->config[$key])) {
                $this->isError = true;
                $this->errMsg = $key.' is empty';
                $this->errCode = 'ERR-CALLBACK-01';
                return false;
            }
        }
        if (empty($this->redirectUri)) {
            $this->isError = true;
            $this->errMsg = 'redirectUri is empty';
            $this->errCode = 'ERR-CALLBACK-02';
            return false;
        }
        // check config----
        // ----check GET parameter
        if (isset($_GET['error']) || isset($_GET['error_message'])) {
            unset($this->session['state']);
            $this->isError = true;
            $this->errMsg = "get error from provider";
            $this->errCode = 'ERR-CALLBACK-03';
            $this->errDetail = '$_GET:'.print_r($_GET, true);
            //$this->debug($this->errDetail);
            return false;
        }
        // check GET parameter----
        // ----check state
        if (empty($_GET['state']) || (htmlspecialchars($_GET['state'], ENT_QUOTES, "UTF-8") !== $this->session['state'])) {
            $this->isError = true;
            $this->errMsg = "invalid state(unmatch)";
            $this->errCode = 'ERR-CALLBACK-04';
            $this->errDetail = '$_GET:'.print_r($_GET,true).',$_SESSION:'.print_r($this->session,true);
            //$this->debug($this->errDetail);
            unset($this->session['state']);
            return false;
        }
        // check state----
        // callback process start
        unset($this->session['state']);

        $errTrace = '';
        // get access token(code => accessToken)
        $uri = $this->config['accessTokenUri'];
        $response = $this->http_request('POST', $uri, [
            'client_id'     => $this->config['clientId'],
            'client_secret' => $this->config['clientSecret'],
            'redirect_uri'  => $this->redirectUri,
            'code'          => htmlspecialchars($_GET['code'], ENT_QUOTES, "UTF-8"),
            'grant_type'    => 'authorization_code',
        ]);
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404 ) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('POST', $uri,[
                'redirect_uri'  => $this->redirectUri,
                'code'          => htmlspecialchars($_GET['code'], ENT_QUOTES, "UTF-8"),
                'grant_type'    => 'authorization_code',
                ], 'Authorization: Basic '.base64_encode($this->config['clientId'].':'.$this->config['clientSecret']));
        }
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404 ) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('GET', $uri,[
                'redirect_uri'  => $this->redirectUri,
                'code'          => htmlspecialchars($_GET['code'], ENT_QUOTES, "UTF-8"),
                'grant_type'    => 'authorization_code',
                ], 'Authorization: Basic '.base64_encode($this->config['clientId'].':'.$this->config['clientSecret']));
        }
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404 ) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('GET', $uri,[
                'client_id'     => $this->config['clientId'],
                'client_secret' => $this->config['clientSecret'],
                'redirect_uri'  => $this->redirectUri,
                'code'          => htmlspecialchars($_GET['code'], ENT_QUOTES, "UTF-8"),
                'grant_type'    => 'authorization_code',
            ]);
        }
        if ($response->code !== 200 || !isset($response->body->access_token)) {
            $errTrace .= print_r($response, true);
            $this->isError = true;
            $this->errMsg = "can not get access_token";
            $this->errCode = 'ERR-CALLBACK-05';
            $this->errDetail = $errTrace;
            //$this->debug($this->errDetail);
            return false;
        }
        if (!is_string($response->body->access_token) || empty($response->body->access_token)) {
            $this->isError = true;
            $this->errMsg = "access token is invalid.";
            $this->errCode = 'ERR-CALLBACK-06';
            $this->errDetail = print_r($response->body->access_token, true);
            return false;
        }
        $accessToken = $response->body->access_token;

        // user情報取得
        $errTrace = '';
        $uri = $this->config['resourceOwnerUri'];
        $query = ['access_token' => $accessToken];
        if (preg_match('|^https://graph\.facebook\.com|', $uri)) {
            $query += ['appsecret_proof' =>  hash_hmac('sha256', $accessToken, $this->config['clientSecret'])];
        }
        $response = $this->http_request('POST', $uri, $query);
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('GET', $uri, null, 'Authorization: bearer '.$accessToken);
        }
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('GET', $uri, null, 'Authorization: token '.$accessToken);
        }
        if ($response->code === 400 || $response->code === 401 || $response->code === 403 || $response->code === 404) {
            $errTrace .= print_r($response, true);
            $response = $this->http_request('GET', $uri, $query);
        }

        if ($response->code !== 200) {
            $errTrace .= print_r($response, true);
            $this->isError = true;
            $this->errMsg = "can not get user informations.(code={$response->code})";
            $this->errCode = 'ERR-CALLBACK-07';
            $this->errDetail = $errTrace;
            //$this->debug($this->errDetail);
            return false;
        }
        if (gettype($response->body) !== 'object') {
            $this->isError = true;
            $this->errMsg = "can not get user informations.(content-type:(".gettype($response->body).")";
            $this->errCode = 'ERR-CALLBACK-08';
            $this->errDetail = print_r($response->body, true);
            return false;
        }

        $providerUserId = '';
        if (isset($response->body->{$this->config['id']}) && !empty($response->body->{$this->config['id']})) {
            $providerUserId = $response->body->{$this->config['id']};
        }

        $providerUserName = '';
        if (isset($response->body->{$this->config['name']}) && !empty($response->body->{$this->config['name']})) {
            $providerUserName = $response->body->{$this->config['name']};
        }

        $providerUserEmail = '';
        if (isset($response->body->{$this->config['email']}) && !empty($response->body->{$this->config['email']})) {
            $providerUserEmail = $response->body->{$this->config['email']};
        }

        $providerUserImageUrl = '';
        if (!empty($this->config['imageUrl'])) {
            $items = preg_split('/\s*>\s*/', $this->config['imageUrl']);
            $data = $response->body;
            while ($item = array_shift($items)) {
                if (isset($data->{$item}) && !empty($data->{$item})) {
                    $data = $data->{$item};
                }
            }
            if (!empty($data) && is_string($data)) {
                $providerUserImageUrl = $data;
            }
        }

        // ----まとめてsessionに登録
        $this->session['provider_id'] = $this->providerId;
        $this->session['provider_name'] = $this->config['providerName'];
        $this->session['provider_logo'] = $this->config['providerLogo'];
        $this->session['provider_user_id'] = $providerUserId;
        $this->session['provider_user_name'] = $providerUserName;
        $this->session['provider_user_email'] = $providerUserEmail;
        $this->session['provider_user_image_url'] = $providerUserImageUrl;
        $this->session['provider_user_data'] = $response->body;
        // まとめてsessionに登録----

        //最終ログイン日時設定
        $this->setLastLoginTime();
        return true;
    }

    protected function http_request ($method, $uri = '', $data = null, $add_header = null)
    {
        if (empty($uri)) {
            return;
        }
        if ($method !== 'GET' && $method !== 'POST') {
            return;
        }
        $options = [
            'http' => [
                'ignore_errors' => true,
                'method'        => $method,
                'user_agent'    => php_uname().'; PHP/'. PHP_VERSION,
            ]
        ];
        if (!empty($this->config['proxy']) && preg_match('!^(tcp|http)://[0-9a-zA-Z\.\-]+:[1-9][0-9]+$!',$this->config['proxy'])) {
            $proxy = $this->config['proxy'];
            if (preg_match('|^http://|', $proxy)) {
                $proxy = preg_replace('|^http://|', 'tcp://', $proxy);
            }
            $options['http']['proxy'] = $proxy;
            $options['http']['request_fulluri'] = true;
        }
        $header[] = 'Accept: application/json';
        if ($add_header) {
            $header[] = $add_header;
        }

        if ($method === 'POST') {
            $post_query = '';
            if (!empty($data) && is_array($data)) {
                $post_query = http_build_query($data);
            }
            $header[] = 'Content-Type: application/x-www-form-urlencoded';
            $header[] = 'Content-Length: '.strlen($post_query);
            $options['http']['content'] = $post_query;
        } elseif ($method === 'GET' && !empty($data)) {
            $add_query = '';
            if (is_string($data)) {
                $add_query = $data;
            } elseif (is_array($data)) {
                $add_query = http_build_query($data);
            }
            if (strstr($uri, '?')) {
                $uri .= '&'.$add_query;
            } else {
                $uri .= '?'.$add_query;
            }
        }
        $options['http']['header'] = implode("\r\n",$header);

        $ignoreSslVerify = $this->config['ignoreSslVerify'];

        if ( $ignoreSslVerify === '1' ){
            $options['ssl']['verify_peer']      = false;
            $options['ssl']['verify_peer_name'] = false;
        }

        $body = file_get_contents($uri, false, stream_context_create($options));
        preg_match('|^HTTP/1\.[01] ([12345][0-9][0-9]) (.+)$|', $http_response_header[0],$match);
        $code = intval($match[1]);
        $status = $match[2];
        $type1 = '';
        $type2 = '';
        foreach ($http_response_header as $line) {
            if (preg_match('|^[Cc]ontent-[Tt]ype:\s*([a-zA-z0-9]+)/([a-zA-Z0-9]+);*\s*|', $line, $match)) {
                $type1 = $match[1];
                $type2 = $match[2];
                break;
            }
        }
        if (preg_match('/(json|javascript)/i', $type2)) {
            $body = json_decode($body);
        }
        if (preg_match('/html/i', $type2)) {
            //$body = preg_replace('/</', '&lt;', $body);
            //$body = preg_replace('/>/', '&gt;', $body);
        }
        $response = json_decode(json_encode([
            'uri'  => "{$method} {$uri}",
            'request' => $options,
            'code' => $code,
            'status' => $status,
            'type' => "{$type1}/{$type2}",
            'body' => $body,
        ]));
        //$this->debug(print_r($response,true));
        return $response;
    }

    protected function debug($str='') {
        if ($this->isDebug) error_log($str);
    }

    // 最終ログイン日時設定
    protected function setLastLoginTime() {
      $objDBCA = new DBConnectAgent();
      $tmpResult = $objDBCA->connectOpen();
      $tmpArrayBind = array('PROVIDER_USER_ID'=>$this->session['provider_user_id'] );
      $sql = "UPDATE A_ACCOUNT_LIST SET LAST_LOGIN_TIME = SYSDATE() WHERE PROVIDER_USER_ID = :PROVIDER_USER_ID";
      $objQuery = $objDBCA->sqlPrepare($sql);
      $objQuery->sqlBind($tmpArrayBind);
      $r = $objQuery->sqlExecute();
    }
}
