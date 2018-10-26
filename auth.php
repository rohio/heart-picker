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
	header( "Location: https://api.twitter.com/oauth/authorize?oauth_token=" . $query["oauth_token"] ) ;

	// ユーザーを認証画面へ飛ばす (二回目以降は認証画面をスキップする場合)
	// header( "Location: https://api.twitter.com/oauth/authenticate?oauth_token=" . $query["oauth_token"] ) ;
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
        <script src="check_input.js"></script>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        <script type="text/javascript">
            //■page topボタン
            $(function(){
            var topBtn=$('#pageTop');
            topBtn.hide();
            //◇ボタンの表示設定
            $(window).scroll(function(){
                if($(this).scrollTop()>80){
                    //---- 画面を80pxスクロールしたら、ボタンを表示する
                    topBtn.fadeIn();
                }else{
                    //---- 画面が80pxより上なら、ボタンを表示しない
                    topBtn.fadeOut();
                }
            });
            // ◇ボタンをクリックしたら、スクロールして上に戻る
            topBtn.click(function(){
                $('body,html').animate({
                    scrollTop: 0},500);
                    return false;
                });
            });

            $('head').append(
                '<style type="text/css">#wrapper { display: none; } #fade, #loader { display: block; }</style>'
            );
            
            jQuery.event.add(window,"load",function() { // 全ての読み込み完了後に呼ばれる関数
                var pageH = $("#wrapper").height();
            
                $("#fade").css("height", pageH).delay(900).fadeOut(800);
                $("#loader").delay(600).fadeOut(300);
                $("#wrapper").css("display", "block");
            });
        </script>
    </head>
    <body>
        <div id="loader">
            <img src="./loading.gif" alt="Now Loading..." width="80px" height="80px" />
        </div>
        <div id="fade"></div>
        <div class="wrapper">
            <div class="container">
                <h1>はーと♡ぴっかー</h1>

                いいねはページ下部に表示されます。<br>

                <?php require('logic.php'); ?>
            </div>

            <ul class="bg-bubbles-heart">
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