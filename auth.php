<?php
session_start();
$api_key = '5L41MwG316NQvDhd3ru1UDiIa'; 
$api_secret = 'Y8daT5rjGsfQL49nHIzJKkL07Gq3BB2IAlR6NIl7owWSn00Lkz';
$callback_url = "https://heart-picker.herokuapp.com/auth.php" ;	// Callback URL (このプログラムのURLアドレス)

// 認証画面から戻ってきた時 (認証OK)
if ( isset( $_GET['oauth_token'] ) || isset($_GET["oauth_verifier"]) ) {
	/*** アクセストークンを取得する ***/
	//[リクエストトークン・シークレット]をセッションから取得
	$request_token_secret = $_SESSION["oauth_token_secret"] ;

	// アクセストークンを取得するAPI
	$request_url = "https://api.twitter.com/oauth/access_token" ;

	// リクエストメソッド
	$request_method = "POST" ;

	// キーを作成する
	$signature_key = rawurlencode( $api_secret ) . "&" . rawurlencode( $request_token_secret ) ;

	// パラメータ([oauth_signature]を除く)を連想配列で指定
	$params = array(
		"oauth_consumer_key" => $api_key ,
		"oauth_token" => $_GET["oauth_token"] ,
		"oauth_signature_method" => "HMAC-SHA1" ,
		"oauth_timestamp" => time() ,
		"oauth_verifier" => $_GET["oauth_verifier"] ,
		"oauth_nonce" => microtime() ,
		"oauth_version" => "1.0" ,
	) ;

	// 配列の各パラメータの値をURLエンコード
	foreach( $params as $key => $value ) {
		$params[ $key ] = rawurlencode( $value ) ;
	}
	// 連想配列をアルファベット順に並び替え
	ksort($params) ;
	// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換
	$request_params = http_build_query( $params , "" , "&" ) ;
	// 変換した文字列をURLエンコードする
	$request_params = rawurlencode($request_params) ;
	// リクエストメソッドをURLエンコードする
	$encoded_request_method = rawurlencode( $request_method ) ;
	// リクエストURLをURLエンコードする
	$encoded_request_url = rawurlencode( $request_url ) ;
	// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
	$signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params ;
	// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
	$hash = hash_hmac( "sha1" , $signature_data , $signature_key , TRUE ) ;
	// base64エンコードして、署名[$signature]が完成する
	$signature = base64_encode( $hash ) ;
	// パラメータの連想配列、[$params]に、作成した署名を加える
	$params["oauth_signature"] = $signature ;
	// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
	$header_params = http_build_query( $params, "", "," ) ;

	// リクエスト用のコンテキストを作成する
	$context = array(
		"http" => array(
			"method" => $request_method ,	//リクエストメソッド
			"header" => array(	//カスタムヘッダー
				"Authorization: OAuth " . $header_params ,
			) ,
		) ,
	) ;

	// cURLを使ってリクエスト
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// メソッド
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// ヘッダー
	curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// 取得したデータ
	$response = substr( $res1, $res2["header_size"] ) ;

	// $responseの内容(文字列)を$query(配列)に直す
	// aaa=AAA&bbb=BBB → [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// 取得したアクセストークンをセッションの変数に格納
	$_SESSION["oauth_token"] = $query["oauth_token"];

	// 取得したアクセストークン・シークレットをセッションの変数に格納
    $_SESSION["oauth_token_secret"] = $query["oauth_token_secret"];
    
	// ユーザーID
	// $query["user_id"]

	// スクリーンネーム
	$_SESSION["screen_name"] = $query["screen_name"];

// 認証画面から戻ってきた時 (認証NG) 特に何も処理をしない
} elseif ( isset( $_GET["denied"] ) ) {
// 初回のアクセス
} else {
	/*** リクエストトークンの取得 ***/

	// [アクセストークンシークレット] (まだ存在しないので空文字)
	$access_token_secret = "" ;

	// リクエストトークンを取得するAPI
	$request_url = "https://api.twitter.com/oauth/request_token" ;

	// リクエストメソッド
	$request_method = "POST" ;

	// キーを作成する (URLエンコードする)
	$signature_key = rawurlencode( $api_secret ) . "&" . rawurlencode( $access_token_secret ) ;

	// パラメータ([oauth_signature]を除く)を連想配列で指定
	$params = array(
		"oauth_callback" => $callback_url ,
		"oauth_consumer_key" => $api_key ,
		"oauth_signature_method" => "HMAC-SHA1" ,
		"oauth_timestamp" => time() ,
		"oauth_nonce" => microtime() ,
		"oauth_version" => "1.0" ,
	) ;

	// 各パラメータをURLエンコードする
	foreach( $params as $key => $value ) {
		// コールバックURLはエンコードしない
		if( $key == "oauth_callback" ) {
			continue ;
		}

		// URLエンコード処理
		$params[ $key ] = rawurlencode( $value ) ;
	}

	// 連想配列をアルファベット順に並び替える
	ksort( $params ) ;
	// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
	$request_params = http_build_query( $params , "" , "&" ) ;
	// 変換した文字列をURLエンコードする
	$request_params = rawurlencode( $request_params ) ;
	// リクエストメソッドをURLエンコードする
	$encoded_request_method = rawurlencode( $request_method ) ;
	// リクエストURLをURLエンコードする
	$encoded_request_url = rawurlencode( $request_url ) ;
	// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
	$signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params ;
	// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
	$hash = hash_hmac( "sha1" , $signature_data , $signature_key , TRUE ) ;
	// base64エンコードして、署名[$signature]が完成する
	$signature = base64_encode( $hash ) ;
	// パラメータの連想配列、[$params]に、作成した署名を加える
	$params["oauth_signature"] = $signature ;
	// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
	$header_params = http_build_query( $params , "" , "," ) ;

	// リクエスト用のコンテキストを作成する
	$context = array(
		"http" => array(
			"method" => $request_method , // リクエストメソッド (POST)
			"header" => array(			  // カスタムヘッダー
				"Authorization: OAuth " . $header_params ,
			) ,
		) ,
	) ;

	// cURLを使ってリクエスト
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;	// リクエストURL
	curl_setopt( $curl, CURLOPT_HEADER, true ) ;	// ヘッダーを取得する
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context["http"]["method"] ) ;	// メソッド
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context["http"]["header"] ) ;	// リクエストヘッダーの内容
	curl_setopt( $curl, CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// 取得したデータ
	$response = substr( $res1, $res2["header_size"] ) ;	// 取得したデータ(JSONなど)

	// リクエストトークンを取得できなかった場合
	if( !$response ) {
		echo "<p>何らかの理由で、リクエストトークンを取得できませんでした。申し訳ございません。</p>" ;
		return ;
	}

	// $responseの内容(文字列)を$query(配列)に直す
	// aaa=AAA&bbb=BBB → [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// セッション[$_SESSION["oauth_token_secret"]]に[oauth_token_secret]を保存する
	session_regenerate_id(true) ;
	$_SESSION["oauth_token_secret"] = $query["oauth_token_secret"] ;

	/*** ユーザーを認証画面へ飛ばす ***/

	// ユーザーを認証画面へ飛ばす (毎回ボタンを押す場合)
	// header( "Location: https://api.twitter.com/oauth/authorize?oauth_token=" . $query["oauth_token"] ) ;

	// ユーザーを認証画面へ飛ばす (二回目以降は認証画面をスキップする場合)
	header( "Location: https://api.twitter.com/oauth/authenticate?oauth_token=" . $query["oauth_token"] ) ;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>はーとぴっかー</title>
        <link rel="stylesheet/less" type="text/css" href="style.less">
		<link rel="icon" href="favicon.ico">
        <meta name="viewport" content="width=device-width, user-scale=yes, initial-scale=1.0, maximum-scale=5.0" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript">
            function checkForm($this)
            {
                var str=$this.value;
                while(str.match(/[^\d\-\/]/))
                {
                    str=str.replace(/[^\d\-\/]/,"");
                }
                $this.value=str;
            }
        </script>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">

                <h1>はーと♡ぴっかー</h1>
                
                はーとぴっかーは自分や友達のいいねをランダムに表示するサービスです。<br>
				TwitterIDと日時範囲(任意)を指定して、｢はーとぴっく!｣ボタンを押してね！
                
                <form class="form" action="heart.php" method="post">
                    <label for="twitter_id" accesskey="n" class="item_EN">TwitterID　<span class="must">必須</span><br></label>
                    <div class="input-group">
                        <span class="input-group__addon">@</span>
                        <input type="text" name="twitter_id" id="twitter_id" class="input-group__control" placeholder="例: TwitterJP" required>
                    </div>
                    <br>

                    <label for="begin_date" accesskey="n" class="item_JP">日付範囲(開始日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
                    <input type="text" name="begin_date" placeholder="例: 2015-1-1" id="begin_date" class="user_input" maxlength="10" onInput="checkForm(this)" pattern="(201[1-9][/-]([1-9]|0[1-9]|1[012])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))">

                    <label for="end_date" accesskey="n" class="item_JP">日付範囲(終了日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
                    <input type="text" name="end_date" placeholder="例: 2016-1-31" id="end_date" class="user_input" maxlength="10" onInput="checkForm(this)" pattern="(201[1-9][/-]([1-9]|0[1-9]|1[012])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))">

                    <br>
                    <button type="submit">はーとぴっく!</button>
                </form>
                <form class="form" action="auth.php" method="get">
                <details>
                    <summary>詳しい使い方,仕様（クリックで展開）</summary>
                    <div class="use">
                        <ul>
                        <li>TwitterIDは自分、友達のどちらでも指定できます。</li>
                        <li>日付範囲が未指定の場合、全件からランダムに選ばれます。</li>
                        <li>TwitterAPIの仕様により、3200件より多くのいいねをしているアカウントは、最近3200件のいいねの中からランダムに表示されます。</li>
                        <li>非公開アカウント（鍵アカウント）のいいねは表示できません。</li>
                        <li>TwitterAPIに使用回数の制限があるため、はーとぴっかーを利用する回数が多いと制限がかかり、はーとぴっかーを利用できなくなります。
                        より多く利用したい方は、以下からTwitterでログインしてください。
                        <button class="login_twitter" type="submit">Twitterでログイン</button><br>
                        TwitterAPIの使用回数制限とTwitterのログインによるアプリケーション認証に関して、詳細を知りたい方は本ページの末尾にて説明しているので、そちらを参照ください。</li>
                        <li>日付範囲が未指定であったり、日付範囲が広い場合、最近のものが選ばれる確率が少しだけ高くなります。</li><br>

                        <details>
                            <summary>使用制限,認証に関して（クリックで展開）</summary>
                            <div class="limit">
                                <ol>
                                <li>使用回数制限に関して<br>
                                TwitterAPIは、認証を行わない場合はアプリケーション単位、認証を行った場合はユーザ単位に対して使用回数が制限されます。
                                アプリケーション単位の場合は、アプリケーションを複数のユーザが使用している場合、複数ユーザの合計の使用回数を基準としてTwitterAPIの使用が制限されます。
                                ユーザ単位の場合は、アプリケーションを複数のユーザが使用している場合でも、ユーザ1人の使用回数を基準として、TwitterAPIの使用が制限されます。
                                そのため、アプリケーション認証を行えば使用制限が緩和されます。ユーザ認証を行った場合の使用回数の目安ですが、最低でも15分間当たり、15回のいいね表示を行うことができます。</li>
                                <li>アプリケーション認証(read only)に関して<br>
                                はーとぴっかーはアプリケーション認証を行っても、権限を悪用しユーザの意図に反するようなこと（ツイートする、フォローを行う等）は行いません。
                                しかし、1.で述べたようにはーとぴっかーはAPIの使用回数緩和のため、認証が必要となります。
                                そこで、最低限の権限の認証で十分なため、read権限のみの認証を行います。
                                以下は補足ですが、勝手にツイートがされてしまう、いわゆるスパムと呼ばれるものはread権限だけでなく、write権限を必要とします。
                                そのため、はーとぴっかーはスパムと呼ばれるようなアプリの動作は権限の面で不可能となっています。</li>
                            </div>
                        </details>
                        </ul>
                    </div>
                </details>
                </form>
            </div>
            <ul class="bg-bubbles-index">
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
	</body>
</html>

<!--
Copyright (c) 2015 Lewi Hussey @Lewitje's
Released under the MIT license
http://opensource.org/licenses/mit-license.php
-->