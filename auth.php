<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>はーとぴっかー</title>
        <link rel="stylesheet/less" type="text/css" href="style.less">
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

                <h1>はーとぴっかー</h1> 
                <h1>Heart♡Pickker</h1>
                
                <form class="form" action="heart.php" method="post">
                    <label for="twitter_id" accesskey="n" class="item_EN">TwitterID　<span class="must">必須</span><br>
                    <div class="explain">@ は付けないでください </div></label>
                    <input type="text" name="twitter_id" placeholder="例: TwitterJP" id="twitter_id" required>

                    <label for="display_num" accesskey="n" class="item_JP">表示件数　<span class="must">必須</span><br>
                    <div class="explain">1~20 の間で指定してください</div></label>
                    <input type="number" name="display_num" placeholder="例: 10" id="display_num" min="1" max="20" required><br>

                    <label for="begin_date" accesskey="n" class="item_JP">日付範囲(開始日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください</div></label>
                    <input type="text" name="begin_date" placeholder="例: 2015-1-1" id="begin_date" maxlength="10" onInput="checkForm(this)" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])">

                    <label for="end_date" accesskey="n" class="item_JP">日付範囲(終了日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください</div></label>
                    <input type="text" name="end_date" placeholder="例: 2016-1-31" id="end_date" maxlength="10" onInput="checkForm(this)" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])">

                    <br>
                    <button type="submit">Heart Pick!</button>
                </form>

                <form class="form" action="auth.php" method="get">
                    <button type="submit">認証</button>
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

<?php
$api_key = '5L41MwG316NQvDhd3ru1UDiIa'; 
$api_secret = 'Y8daT5rjGsfQL49nHIzJKkL07Gq3BB2IAlR6NIl7owWSn00Lkz';
$callback_url = "https://inputform.herokuapp.com/auth.php" ;	// Callback URL (このプログラムのURLアドレス)
// $callback_url = "http://192.168.33.10:8000/" ;	// Callback URL (このプログラムのURLアドレス)

/*** [手順4] ユーザーが戻ってくる ***/

// 認証画面から戻ってきた時 (認証OK)
if ( isset( $_GET['oauth_token'] ) || isset($_GET["oauth_verifier"]) ) {
	/*** [手順5] [手順5] アクセストークンを取得する ***/

	//[リクエストトークン・シークレット]をセッションから呼び出す
	session_start() ;
	$request_token_secret = $_SESSION["oauth_token_secret"] ;

	// リクエストURL
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
	$response = substr( $res1, $res2["header_size"] ) ;	// 取得したデータ(JSONなど)
	$header = substr( $res1, 0, $res2["header_size"] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

	// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
	// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

	// $responseの内容(文字列)を$query(配列)に直す
	// aaa=AAA&bbb=BBB → [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// アクセストークン
	$_SESSION["oauth_token"] = $query["oauth_token"];

	// アクセストークン・シークレット
    $_SESSION["oauth_token_secret"] = $query["oauth_token_secret"];
    
    // DEBUG
    echo $_SESSION["oauth_token"];
    echo $_SESSION["oauth_token_secret"];

	// ユーザーID
	// $query["user_id"]

	// スクリーンネーム
	// $query["screen_name"]

	// 配列の内容を出力する (本番では不要)
	echo '<p>下記の認証情報を取得しました。(<a href="' . explode( "?", $_SERVER["REQUEST_URI"] )[0] . '">もう1回やってみる</a>)</p>' ;

	foreach ( $query as $key => $value ) {
		echo "<b>" . $key . "</b>: " . $value . "<BR>" ;
	}

// 認証画面から戻ってきた時 (認証NG)
} elseif ( isset( $_GET["denied"] ) ) {
	// エラーメッセージを出力して終了
	echo "連携を拒否しました。" ;
	exit ;

// 初回のアクセス
} else {
	/*** [手順1] リクエストトークンの取得 ***/

	// [アクセストークンシークレット] (まだ存在しないので「なし」)
	$access_token_secret = "" ;

	// エンドポイントURL
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
	$header = substr( $res1, 0, $res2["header_size"] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

	// [cURL]ではなく、[file_get_contents()]を使うには下記の通りです…
	// $response = file_get_contents( $request_url , false , stream_context_create( $context ) ) ;

	// リクエストトークンを取得できなかった場合
	if( !$response ) {
		echo "<p>リクエストトークンを取得できませんでした…。$api_keyと$callback_url、そしてTwitterのアプリケーションに設定しているCallback URLを確認して下さい。</p>" ;
		exit ;
	}

	// $responseの内容(文字列)を$query(配列)に直す
	// aaa=AAA&bbb=BBB → [ "aaa"=>"AAA", "bbb"=>"BBB" ]
	$query = [] ;
	parse_str( $response, $query ) ;

	// セッション[$_SESSION["oauth_token_secret"]]に[oauth_token_secret]を保存する
	session_start() ;
	session_regenerate_id( true ) ;
	$_SESSION["oauth_token_secret"] = $query["oauth_token_secret"] ;

	/*** [手順2] ユーザーを認証画面へ飛ばす ***/

	// ユーザーを認証画面へ飛ばす (毎回ボタンを押す場合)
	// header( "Location: https://api.twitter.com/oauth/authorize?oauth_token=" . $query["oauth_token"] ) ;

	// ユーザーを認証画面へ飛ばす (二回目以降は認証画面をスキップする場合)
	header( "Location: https://api.twitter.com/oauth/authenticate?oauth_token=" . $query["oauth_token"] ) ;
}

?>