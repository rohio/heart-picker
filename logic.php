<?php
// ユーザごとのアクセストークンを使用する場合、セッションから認証のトークンを取得するため、セッションを開始
session_start() ;
// Webアプリケーションのapi情報を設定
$api_key = '5L41MwG316NQvDhd3ru1UDiIa'; 
$api_secret = 'Y8daT5rjGsfQL49nHIzJKkL07Gq3BB2IAlR6NIl7owWSn00Lkz';

// 入力フォームのhtml
$form = "<form class=\"form\" action=\"heart.php\" method=\"post\">
<label for=\"twitter_id\" accesskey=\"n\" class=\"item_EN\">TwitterID　<span class=\"must\">必須</span><br></label>
<div class=\"input-group\">
	<span class=\"input-group__addon\">@</span>
	<input type=\"text\" name=\"twitter_id\" id=\"twitter_id\" class=\"input-group__control\" value=\"" . htmlspecialchars( $_POST['twitter_id'], ENT_QUOTES, 'UTF-8' ) . "\" placeholder=\"例: TwitterJP\" required>
</div>
<br>

<label for=\"begin_date\" accesskey=\"n\" class=\"item_JP\">日付範囲(開始日)　<span class=\"free\">任意</span><br>
<div class=\"explain\">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
<input type=\"text\" name=\"begin_date\" placeholder=\"例: 2015-1-1\" id=\"begin_date\" class=\"user_input\" value=\"" . htmlspecialchars( $_POST['begin_date'], ENT_QUOTES, 'UTF-8' ) . "\" maxlength=\"10\" onInput=\"checkForm(this)\" pattern=\"(201[1-9][/-]([1-9]|0[1-9]|1[012])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))\">

<label for=\"end_date\" accesskey=\"n\" class=\"item_JP\">日付範囲(終了日)　<span class=\"free\">任意</span><br>
<div class=\"explain\">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
<input type=\"text\" name=\"end_date\" placeholder=\"例: 2016-1-31\" id=\"end_date\" class=\"user_input\" value=\"" . htmlspecialchars( $_POST['end_date'], ENT_QUOTES, 'UTF-8' ) . "\" maxlength=\"10\" onInput=\"checkForm(this)\" pattern=\"(201[1-9][/-]([1-9]|0[1-9]|1[012])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))\">

<br>
<button type=\"submit\">Heart Pick!</button>
</form>
<form class=\"form\" action=\"auth.php\" method=\"get\">
<details>
	<summary>詳しい使い方,仕様（クリックで展開）</summary>
	<div class=\"use\">
		<ul>
		<li>TwitterIDは自分、友達のどちらでも指定できます。</li>
		<li>日付範囲が未指定の場合、全件からランダムに選ばれます。</li>
		<li>TwitterAPIの仕様により、3200件より多くのいいねをしているアカウントは、最近3200件のいいねの中からランダムに表示されます。</li>
		<li>非公開アカウント（鍵アカウント）のいいねは表示できません。</li>
		<li>TwitterAPIの使用回数制限のため、サービスを実行する回数が多いと制限がかかり、サービスを利用できなくなります。
		より多く利用したい方は、以下からTwitterでログインしてください。
		<button class=\"login_twitter\" type=\"submit\">Twitterでログイン</button><br>
		TwitterAPIの使用回数制限とTwitterのログインによるアプリケーション認証に関して、詳細を知りたい方は本ページの末尾にて説明しているので、そちらを参照ください。</li>
		<li>日付範囲が未指定であったり、日付範囲が広い場合、最近のものが選ばれる確率が少しだけ高くなります。</li><br>

		<details>
			<summary>使用制限,認証に関して（クリックで展開）</summary>
			<div class=\"limit\">
				<ol>
				<li>使用回数制限に関して<br>
				TwitterAPIは、認証を行わない場合はアプリケーション単位、認証を行った場合はユーザ単位に対して使用回数が制限されます。
				アプリケーション単位の場合は、アプリケーションを複数のユーザが使用している場合、複数ユーザの合計の使用回数を基準としてTwitterAPIの使用が制限されます。
				ユーザ単位の場合は、アプリケーションを複数のユーザが使用している場合でも、ユーザ1人の使用回数を基準として、TwitterAPIの使用が制限されます。
				そのため、アプリケーション認証を行えば使用制限が緩和されます。ユーザ認証を行った場合の使用回数の目安ですが、最低でも15分間に15回のいいね表示を行うことができます。</li>
				<li>アプリケーション認証(read only)に関して<br>
				HeartPickerはユーザ認証を行っても、権限を悪用しユーザの意図に反するようなこと（ツイートする、フォローを行う等）は行いません。
				しかし、1.で述べたようにHeartPickerはAPIの使用回数緩和のため、認証が必要となります。
				そこで、最低限の権限の認証で十分なため、read権限のみの認証を行います。
				以下は補足ですが、勝手にツイートがされてしまう、いわゆるスパムと呼ばれるものはread権限だけでなく、write権限を必要とします。
				そのため、HeartPickerはスパムと呼ばれるようなアプリの動作は権限の面で不可能となっています。</li>
			</div>
		</details>
		</ul>
	</div>
</details>
</form>";

// 画面に表示するいいねの件数
$DISPLAY_NUM = 20;
// 一度のAPIへのアクセスで取得するいいねの件数。TwitterAPIの仕様の最大値が200なので200を設定。
$GET_NUM = 200;
// APIへアクセスする処理の最大ループ回数
// max_idをランダムに設定してAPIへアクセスするため、ループが過剰に起こらないようにするため設定
$MAX_LOOP = 5;

/* 入力値チェックをプログラムの最初に行う */
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

// 日付が存在するか確認する関数
function exist_date($date){
	// 日付の入力形式によって場合分け
	if(strptime($date, '%Y-%m-%d')){
		// '-'を区切り文字として分割
		list($Y, $m, $d) = explode('-', $date);
	} elseif(strptime($date, '%Y-%m/%d')){
		list($Y, $tmp) = explode('-', $date);
		list($m, $d) = explode('/', $tmp);
	} elseif(strptime($date, '%Y/%m-%d')){
		list($Y, $tmp) = explode('/', $date);
		list($m, $d) = explode('-', $tmp);
	} elseif(strptime($date, '%Y/%m/%d')){
		list($Y, $m, $d) = explode('/', $date);
	}
	// 日付が存在する場合にtrue、日付が存在しない場合にfalseを返却
	return checkdate($m, $d, $Y);
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

// 日付範囲の開始日(入力値を格納)
$begin_date = $_POST['begin_date'];
// 日付範囲の終了日(入力値を格納)
$end_date = $_POST['end_date'];

// $begin_dateが入力されていた場合、疑似のTweetIDを生成
if($begin_date != ""){
	// 日付が存在するか確認
	if(exist_date($begin_date) === false){
		// 日付が存在しない場合、以降のプログラムを実行せずにエラー処理を行う
		echo('<div class="error">');
		echo("指定した開始日 [" . $begin_date . "] は存在しない日付です。日付を確認してください。\n");
		echo("</div>\n");
		echo($form);
		// 以降の処理を行わず、終了
		return;
	}

	// 日付が未来の場合エラー処理 
	if(strtotime($begin_date) > strtotime(date("Y/m/d"))){
		echo('<div class="error">');
		echo("指定した開始日 [" . $begin_date . " ]は未来です。現在日時の " . date("Y-m-d") . " 以前を入力してください。");
		echo("</div>\n");
		echo($form);
		// 以降の処理を行わず、終了
		return;
	}

	// $begin_dateの全日を含めるために00:00:00の時間を設定
	$begin_date .= " 00:00:00";
	$rand_min = create_id($begin_date);
}

// $end_dateが入力されていた場合、疑似TweetIDを生成
if($end_date != ""){
	// 日付が存在するか確認
	if(exist_date($end_date) === false){
		// 日付が存在しない場合、以降のプログラムを実行せずにエラー処理を行う
		echo('<div class="error">');
		echo("指定した終了日 [" . $end_date . "] は存在しない日付です。日付を確認してください。\n");
		echo("</div>\n");
		echo($form);
		// 以降の処理を行わず、終了
		return;
	}

	// $end_dateの全日を含めるために23:59:59の時間を設定
	$end_date .= " 23:59:59";
	$rand_max = create_id($end_date);
}

// 日付指定範囲の開始日、終了日を入力しているかによって4つに分岐
if($begin_date != "" && $end_date != ""){
	$max_id = mt_rand($rand_min, $rand_max);
	$since_id = $rand_min;
	// 日付入力が互い違いの場合、以降のプログラムを実行せずにエラー処理を行う
	if($max_id == NULL){
		echo('<div class="error">');
		echo("指定した日付が互い違いになっています。開始日と終了日を入れ替えてください。\n");
		echo("</div>\n");
		echo($form);
		// 以降の処理を行わず、終了
		return;
	}
} elseif($begin_date === "" && $end_date != ""){
	$max_id = mt_rand(0, $rand_max);
} elseif($begin_date != "" && $end_date === ""){
	$max_id = mt_rand($rand_min, $rand_max);
	$since_id = $rand_min;
} elseif($begin_date === "" && $end_date === ""){
	$max_id = mt_rand(0, $rand_max);
}
/* 入力値チェック完了 */

/* APIを利用する */
// アクセスキー、アクセストークン
// ユーザごとの認証を使用する場合
if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"])){
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
// XSS対策のために、htmlspecialchars関数を使用
$twitter_id = htmlspecialchars($_POST['twitter_id'], ENT_QUOTES, 'UTF-8' );

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
// セッション情報を破棄するため、ログインしているTwitterIDを表示する前にエラー処理を行う
if(array_key_exists('errors', $array_user)){
	if($array_user['errors'][0]['code'] === 89){
		echo('<div class="session">');
		echo("HeartPickにログインしていたTwitterID: @" . $_SESSION["screen_name"] . " とのアプリ連携の許可が取り消されたため、HeartPick からログアウトしました。");
		echo("申し訳ございませんが、再度 HeartPick! ボタンを押してください。");
		echo('</div>');
		echo($form);	
		// SESSION情報を破棄
		unset($_SESSION["oauth_token"]);
		unset($_SESSION["screen_name"]);
		unset($_SESSION["oauth_token_secret"]);
		return;
	}
}

// ログインしているTwitterIDを表示
if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"])){
	echo('<div class="session">');
	echo ('あなたは今、TwitterID: @' . $_SESSION["screen_name"] . ' で HeartPick にログインしています。');
	echo('</div>');
}

// APIからエラーが返されている場合、以降のプログラムを実行せずにエラー処理を行う
if(array_key_exists('errors', $array_user)){
	echo('<div class="error">');
	if($array_user['errors'][0]['code'] === 50){
		echo("指定したTwitterID [" . $twitter_id . "] は存在しません。");
	} elseif($array_user['errors'][0]['code'] === 88){
		echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。");
		if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"]) === false){
			echo("上限を緩和したい場合は、ページ下部の｢詳細の使い方｣内にあるTwiiterのアイコンをクリックして、Twitterでログインを行ってください。");
		}
	} else {
		echo("何らかのエラーが発生しました。申し訳ございません。");
		// DEBUG
		echo "<pre>";
		print_r($array_user['errors']);
		echo "</pre>";
	}
	echo("</div>\n");
	echo($form);
	return;
};

// アカウントが非公開ユーザでいいねにアクセスできない場合、以降の処理を行わず、終了
if($array_user['protected']){
	echo('<div class="error">');
	echo("指定したTwitterID [@" . $twitter_id . "] は、非公開設定のユーザのため、いいねを取得できません。\n");
	echo("</div>\n");
	echo($form);
	return;
}

// $array_userが取得できない場合、以降の処理を行わず、終了
if(count($array_user) == 0){
	// DEBUG
	echo "<pre>";
	print_r($array['errors']);
	echo "</pre>";

	echo('<div class="error">');
	echo("何らかの理由で、指定したTwitterID [@" . $twitter_id . "] の情報を取得できませんでした。申し訳ございません。\n");
	echo("</div>\n");
	echo($form);
	return;
}

/* いいねを取得する処理 */
// ループ回数をカウントする変数
$loop_count = 1;

// クエリとしてAPIへ送信するパラメータ
$params_a = array(
	"screen_name" => $twitter_id,
	"max_id" => $max_id,
	"since_id" => $since_id,
	"count" => $GET_NUM,
	"include_entities" => "false",
);

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
	// $header = substr( $res1, 0, $res2['header_size'] ) ;

	$html .= 	'<p><textarea style="width:80%" rows="8">' . implode( "\r\n" , $context['http']['header'] ) . '</textarea></p>' ;

	// JSONをオブジェクトに変換
	$array = json_decode($json, true);

	// DEBUG
	echo ("pre: " . count($array) . "\n");

	// 非公開アカウントのTweetを削除
	foreach($array as $key => $value){
		if($value["user"]["protected"]){
			unset($array[$key]);
		}
	}

	// DEBUG
	echo ("done: " . count($array) . "\n");

	// 入力値の表示件数以上のいいねを取得できたらループを抜ける
	if(count($array) >= $DISPLAY_NUM){break;};

	// APIからエラーが返されている場合、ループを抜ける
	if(array_key_exists('errors', $array)){
		// DEBUG
		echo "<pre>";
		print_r($array['errors']);
		echo "</pre>";

		echo('<div class="error">');
		if($array['errors'][0]['code'] === 88){
			echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。");
			if(isset($_SESSION["oauth_token"]) && isset($_SESSION["oauth_token_secret"]) === false){
				echo("上限を緩和したい場合は、ページ下部の｢詳細の使い方｣内にあるTwiiterのアイコンをクリックして、Twitterでログインを行ってください。");
			}
		} else {
			echo("何らかのエラーが発生しました。申し訳ございません。\n");
		}
		echo("</div>\n");
		echo($form);
		return;
	};

	// ループ回数をインクリメント
	$loop_count++;
	
	// $MAX_LOOP回ループして、入力値の表示件数以上のいいねを取得できない場合、ループを抜ける
	if($loop_count > $MAX_LOOP){
		echo('<div class="error">');
		if(count($array)){
			echo("HeartPickのいいね表示件数は通常 " . $DISPLAY_NUM . " 件ですが、指定した日付の範囲内で " . $DISPLAY_NUM . " 件のいいねがありませんでした。\n");
			echo("そのため、指定した日付の範囲内の " . count($array) . " 件のいいねを表示します。");
			echo("</div>\n");
			break;
		} else {
			echo("指定した日付の範囲内でいいねがありませんでした。\n");
			echo("</div>\n");
			echo($form);
			return;
		}
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

// いいねを格納している配列の要素をシャッフル
// 最大200件取得したTweetの中から、更にランダムに表示するためにシャッフルする。
shuffle($array);

// いいねを表示している配列数をカウントする変数
$display_count = 0;

echo($form);
$html .= "<form>";
foreach($array as $key => $value){
	// 埋め込みTweetの形で表示
	$html .= '<blockquote class="twitter-tweet tw-align-center" data-lang="ja"><p lang="ja" dir="ltr" text-align="center">' . $value["text"] . '</p>&mdash; ' . $value["user"]["name"] . '(@' . $value["user"]["screen_name"] . ') <a href="https://twitter.com/' . $value["user"]["screen_name"] . '/status/' . $value["id_str"] . '?ref_src=twsrc%5Etfw">' . $value["created_at"] . '</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script> <br>';
	$display_count++;
	// 表示したいいね数が表示件数以上になったら、break
	if($display_count >= $DISPLAY_NUM){break;}
}
$html .= "<p id=\"pageTop\"><a href=\"#\"><i class=\"fa fa-chevron-up\"></i>↑</a></p>";
$html .= "</form>";

echo $html;