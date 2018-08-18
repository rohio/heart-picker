<?php
// Webアプリケーションのapi情報を設定
$api_key = '5L41MwG316NQvDhd3ru1UDiIa'; 
$api_secret = 'Y8daT5rjGsfQL49nHIzJKkL07Gq3BB2IAlR6NIl7owWSn00Lkz';

// アクセスキー、アクセストークン
// ユーザごとの認証を使用する場合
if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"])){
	// ユーザごとのアクセストークンを使用する場合、セッションから認証のトークンを取得するため、セッションを開始
	session_start() ;
	// ユーザのアクセストークンを設定
	$access_token = $_SESSION["oauth_token"];
	$access_token_secret = $_SESSION["oauth_token_secret"];
} else {	// アプリケーションごとの認証を使用する場合
	// アプリケーションのアクセストークンを設定
	$access_token = '305336457-f4SkCiMphhamnllp0ezut9dsMsl6OJOYI273IfuK';
	$access_token_secret = 'bSCdmn8QQhhPwnyCD9pV261FC5OAUUTvsJzCGBa7vPEks';
}

/* ユーザのいいね件数、非公開設定か否かを取得 */
// 指定したユーザ情報を返却するAPIを使用
$request_url = 'https://api.twitter.com/1.1/users/show.json' ;
$request_method = 'GET' ;

// TwitterのユーザID(入力値を格納)
$twitter_id = $_POST['twitter_id'];

// htmlテキストを格納する変数を生成
$html = '' ;

// クエリとしてAPIへ送信するパラメータ
$params_a = array(
	"screen_name" => $twitter_id,
	"include_entities" => "false",
) ;

// キーを作成する (URLエンコードする)
$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;

// パラメータB (署名の材料用)
$params_b = array(
	'oauth_token' => $access_token ,
	'oauth_consumer_key' => $api_key ,
	'oauth_signature_method' => 'HMAC-SHA1' ,
	'oauth_timestamp' => time() ,
	'oauth_nonce' => microtime() ,
	'oauth_version' => '1.0' ,
) ;

// パラメータAとパラメータBを合成してパラメータCを作る
$params_c = array_merge( $params_a , $params_b ) ;
// 連想配列をアルファベット順に並び替える
ksort( $params_c ) ;
// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
$request_params = http_build_query( $params_c , '' , '&' ) ;
// 一部の文字列をフォロー
$request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;
// 変換した文字列をURLエンコードする
$request_params = rawurlencode( $request_params ) ;
// リクエストメソッドをURLエンコードする
// ここでは、URL末尾の[?]以下は付けないこと
$encoded_request_method = rawurlencode( $request_method ) ;
// リクエストURLをURLエンコードする
$encoded_request_url = rawurlencode( $request_url ) ;
// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;
// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
$hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;
// base64エンコードして、署名[$signature]が完成する
$signature = base64_encode( $hash ) ;
// パラメータの連想配列、[$params]に、作成した署名を加える
$params_c['oauth_signature'] = $signature ;
// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
$header_params = http_build_query( $params_c , '' , ',' ) ;
// リクエスト用のコンテキスト
$context = array(
	'http' => array(
		'method' => $request_method , // リクエストメソッド
		'header' => array(			  // ヘッダー
			'Authorization: OAuth ' . $header_params ,
		) ,
	) ,
) ;

// パラメータがある場合、URLの末尾に追加
if( $params_a ) {
	$request_url .= '?' . http_build_query( $params_a ) ;
}

// cURLを使ってリクエスト
$curl = curl_init() ;
curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;	// メソッド
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
curl_setopt( $curl, CURLOPT_HTTPHEADER , $context['http']['header'] ) ;	// ヘッダー
curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
$res1 = curl_exec( $curl ) ;
$res2 = curl_getinfo( $curl ) ;
curl_close( $curl ) ;

// 取得したデータ
$json = substr( $res1, $res2['header_size'] ) ;

// JSONを配列に変換
$array_user = json_decode( $json, true);

// APIからエラーが返されている場合、以降のプログラムを実行せずにエラー処理を行う
if(array_key_exists('errors', $array_user)){
	echo('<div class="error">');
	if($array_user['errors'][0]['code'] === 50){
		echo("指定したID [" . $twitter_id . "] は存在しません。\n");
	} elseif($array_user['errors'][0]['code'] === 88){
		echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。\n");
		echo("上限を緩和したい場合は、ページ下部の｢詳細の使い方｣内にあるTwiiterのアイコンをクリックして、Twitterでログインを行ってください。");
	} else {
		echo("何らかのエラーが発生しました。申し訳ございません。\n");
	}
	echo('</div>');
	return;
};

// アカウントが非公開ユーザでいいねにアクセスできない場合、以降の処理を行わず、終了
if($array_user['protected']){
	echo('<div class="error">');
	echo("指定したID [" . $twitter_id . "] は、非公開設定のユーザのため、いいねを取得できません。\n");
	echo('</div>');
	return;
}

// $array_userが取得できない場合、以降の処理を行わず、終了
if(count($array_user) == 0){
	echo('<div class="error">');
	echo("何らかの理由で、指定したID [" . $twitter_id . "] の情報を取得できませんでした。\n");
	echo('</div>');
	return;
}

/* 入力値を元にいいねを取得 */
// 日付から疑似のツイートのIDを生成する関数
function create_id($time) {
	// 日付をUNIX時に変換
	$timestamp = strtotime($time) * 1000;
	// 2010年11月のUNIX時の値を引く(tweetのIDは2010年11月からのUNIX時で表しているため)
	$padding = 1288834974657;
	$timestamp = $timestamp - $padding;
	// 22bit桁上げを行う(tweetのIDのタイムスタンプ部分は22bit桁上げした部分のため)
	$id = ($timestamp << 22);
	return $id;
}

/* いいねをランダムに出力するために、max_idの乱数の範囲を設定
max_id : APIを使用する際のクエリのパラメータで、取得するTweetのIDの最大値を指定する。
TweetのIDの一部はTimestampで生成されているため、実質日付の範囲を指定している。 */
// 乱数の範囲の最小値の初期値を設定
$rand_min = 0;
// 乱数の範囲の最大値の初期値を設定。最大値は現在の日付を元に生成したTweetIDとする。
// TEST リアルタイムにいいねしたTweetを取得できるか
$current_date = date("Y/m/d H:i:s");
$rand_max = create_id($current_date);

// 画面に表示するいいねの件数
$DISPLAY_NUM = 20

// 日付範囲の開始日(入力値を格納)
$begin_date = $_POST['begin_date'];
// 日付範囲の終了日(入力値を格納)
$end_date = $_POST['end_date'];

// 一度のAPIへのアクセスで取得するいいねの件数。TwitterAPIの仕様の最大値が200なので200を設定。
$GET_NUM = 200;

// APIへアクセスする処理の最大ループ回数
// max_idをランダムに設定してAPIへアクセスするため、ループが過剰に起こらないようにするため設定
$MAX_LOOP = 5;

// $begin_dateが入力されていた場合、疑似のTweetIDを生成
if($begin_date != ""){
	// $begin_dateの全日を含めるために00:00:00の時間を設定
	$begin_date .= " 00:00:00";
	$rand_min = create_id($begin_date);
}

// $end_dateが入力されていた場合、疑似TweetIDを生成
if($end_date != ""){
	// $end_dateの全日を含めるために23:59:59の時間を設定
	$end_date .= " 23:59:59";
	$rand_max = create_id($end_date);
}

// クエリとしてAPIへ送信するパラメータ
$params_a = array(
	"screen_name" => $twitter_id,
	"count" => $GET_NUM,
	"include_entities" => "false",
);

// 日付指定範囲の開始日、終了日を入力しているかによって4つに分岐
if($begin_date != "" && $end_date != ""){
	$params_a["max_id"] = mt_rand($rand_min, $rand_max);
	$params_a["since_id"] = $rand_min;
} elseif($begin_date === "" && $end_date != ""){
	$params_a["max_id"] = mt_rand(0, $rand_max);
} elseif($begin_date != "" && $end_date === ""){
	$params_a["max_id"] = mt_rand($rand_min, $rand_max);
	$params_a["since_id"] = $rand_min;
} elseif($begin_date === "" && $end_date === ""){
	$params_a["max_id"] = mt_rand(0, $rand_max);
}

// 日付入力が互い違いの場合、以降のプログラムを実行せずにエラー処理を行う
// TEST max_idが設定されない時は、本当に日付が互い違いになっているときのみ？
if($params_a["max_id"] == NULL){
	echo('<div class="error">');
	echo("日付が互い違いになっています。開始日と終了日の入力を入れ替えてください。\n");
	echo('</div>');
	// 以降の処理を行わず、終了
	return;
}

// ループ回数をカウントする変数
$loop_count = 1;

// キーを作成する (URLエンコードする)
$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;

// APIへのアクセスを行うループ
while(true){
	// パラメータB (署名の材料用)
	$params_b = array(
		'oauth_token' => $access_token ,
		'oauth_consumer_key' => $api_key ,
		'oauth_signature_method' => 'HMAC-SHA1' ,
		'oauth_timestamp' => time() ,
		'oauth_nonce' => microtime() ,
		'oauth_version' => '1.0' ,
	) ;

	// $request_urlをクエリ無しのURLに初期化
	$request_url = 'https://api.twitter.com/1.1/favorites/list.json';
	// パラメータAとパラメータBを合成してパラメータCを作る
	$params_c = array_merge( $params_a , $params_b ) ;
	// 連想配列をアルファベット順に並び替える
	ksort( $params_c ) ;
	// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
	$request_params = http_build_query( $params_c , '' , '&' ) ;
	// 一部の文字列をフォロー
	$request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;
	// 変換した文字列をURLエンコードする
	$request_params = rawurlencode( $request_params ) ;
	// リクエストメソッドをURLエンコードする
	// ここでは、URL末尾の[?]以下は付けないこと
	$encoded_request_method = rawurlencode( $request_method ) ;
	// リクエストURLをURLエンコードする
	$encoded_request_url = rawurlencode( $request_url ) ;
	// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
	$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;
	// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
	$hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;
	// base64エンコードして、署名[$signature]が完成する
	$signature = base64_encode( $hash ) ;
	// パラメータの連想配列、[$params]に、作成した署名を加える
	$params_c['oauth_signature'] = $signature ;
	// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
	$header_params = http_build_query( $params_c , '' , ',' ) ;
	
	// リクエスト用のコンテキスト
	$context = array(
		'http' => array(
			'method' => $request_method , // リクエストメソッド
			'header' => array(			  // ヘッダー
				'Authorization: OAuth ' . $header_params ,
			) ,
		) ,
	) ;

	// パラメータがある場合、URLの末尾に追加
	if( $params_a ) {
		$request_url = 'https://api.twitter.com/1.1/favorites/list.json?' . http_build_query( $params_a ) ;
	}

	// cURLを使ってリクエスト
	$curl = curl_init() ;
	curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
	curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
	curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;	// メソッド
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
	curl_setopt( $curl, CURLOPT_HTTPHEADER , $context['http']['header'] ) ;	// ヘッダー
	curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
	$res1 = curl_exec( $curl ) ;
	$res2 = curl_getinfo( $curl ) ;
	curl_close( $curl ) ;

	// 取得したデータ
	$json = substr( $res1, $res2['header_size'] ) ;		// 取得したデータ(JSONなど)
	// TODO ヘッダーからAPIの回復時間を取得できる
	//$header = substr( $res1, 0, $res2['header_size'] ) ;

	// JSONをオブジェクトに変換
	$array = json_decode($json, true);

	// 入力値の表示件数以上のいいねを取得できたらループを抜ける
	if(count($array) >= $DISPLAY_NUM){break;};

	// APIからエラーが返されている場合、ループを抜ける
	if(array_key_exists('errors', $array)){
		echo('<div class="error">');
		if($array['errors'][0]['code'] === 88){
			echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。\n");
			echo("上限を緩和したい場合は、ページ下部の｢詳細の使い方｣内にあるTwiiterのアイコンをクリックして、Twitterでログインを行ってください。");
		} else {
			echo("何らかのエラーが発生しました。申し訳ございません。\n");
		}
		echo('</div>');
		break;
	};

	// ループ回数をインクリメント
	$loop_count++;
	
	// $MAX_LOOP回ループして、入力値の表示件数以上のいいねを取得できない場合、ループを抜ける
	if($loop_count > $MAX_LOOP){
		if(count($array)){
			echo('<div class="error">');
			echo("HeartPickのいいね表示件数は通常 " . $DISPLAY_NUM . " 件ですが、指定した日付の範囲内で " . $DISPLAY_NUM . " 件のいいねがありませんでした。\n");
			echo("そのため、指定した日付の範囲内の " . count($array) . " 件のいいねを表示します。");
			echo('</div>');
		} else {
			echo('<div class="error">');
			echo("指定した日付の範囲内でいいねがありませんでした。\n");
			echo('</div>');
		}
		
		break;
	};
	
	// 乱数幅の最小値に、本ループで入力値の表示件数以上のデータを取得できなかった値を代入
	$rand_min = $params_a["max_id"];
	// 次回のループでのmax_idを変更し、いいねの取得範囲を変更するため、$params_a["max_id"]を再設定
	$params_a["max_id"] = mt_rand($rand_min, $rand_max);
	
	// "$MAX_LOOP-1"回ループして、入力値の表示件数以上のいいねを取得できない場合、
	// max_idを日付指定範囲の開始日、もしくは現在日のUNIX時にする
	if($loop_count >= $MAX_LOOP){
		$params_a["max_id"] = $rand_max;
	};
}

// TODO heart.phpのヘッダーにあるため不要？
// $html .= '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';

// いいねを格納している配列の要素をシャッフル
// 最大200件取得したTweetの中から、更にランダムに表示するためにシャッフルする。
shuffle($array);

// いいねを表示している配列数をカウントする変数
$display_count = 0;

foreach($array as $key => $value){
	// 埋め込みTweetの形で表示
	$html .= '<blockquote class="twitter-tweet tw-align-center" data-lang="ja"><p lang="ja" dir="ltr" text-align="center">' . $value["text"] . '</p>&mdash; ' . $value["user"]["name"] . '(@' . $value["user"]["screen_name"] . ') <a href="https://twitter.com/' . $value["user"]["screen_name"] . '/status/' . $value["id_str"] . '?ref_src=twsrc%5Etfw">' . $value["created_at"] . '</a></blockquote> <br>';
	$display_count++;
	// 表示したいいね数が表示件数以上になったら、break
	if($display_count >= $DISPLAY_NUM){break;}
}

echo $html;