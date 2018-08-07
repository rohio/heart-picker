<?php
// TwitterIDを入力していない場合
if($_POST['twitter_id'] === ""){
	echo('<h2>TwitterIDを入力してね！</h2>');
	return;
}

// アクセスキー、アクセストークン
$api_key = '5L41MwG316NQvDhd3ru1UDiIa'; 
$api_secret = 'Y8daT5rjGsfQL49nHIzJKkL07Gq3BB2IAlR6NIl7owWSn00Lkz';
$access_token = '305336457-BqgHQqfKFhIPCXNvFtXbLiQRPulkBpBXTQFO6EXV';
$access_token_secret = 'wPn9LWO6XXYLNHfovD0PRXXJQ4CDxZS5x75vFdqB4CL7g';

$request_url = 'https://api.twitter.com/1.1/users/show.json' ;
$request_method = 'GET' ;

// TwitterのユーザID(入力値を格納)
$twitter_id = $_POST['twitter_id'];

// htmlテキストを格納する変数を予め生成
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
$json = substr( $res1, $res2['header_size'] ) ;		// 取得したデータ(JSONなど)
// $header = substr( $res1, 0, $res2['header_size'] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

// JSONを配列に変換
$array_user = json_decode( $json, true);

// APIからエラーが返されている場合、以降の処理を行わず、終了
if(array_key_exists('errors', $array_user)){
	// DEBUG
	echo "<pre>";
	print_r($array_user['errors']);
	echo "</pre>";
	
	if($array_user['errors'][0]['code'] === 50){
		echo("指定したID [" . $twitter_id . "] は存在しません。\n");
	} elseif($array_user['errors'][0]['code'] === 32){
		echo("認証でエラーが発生しました。\n");
	} elseif($array_user['errors'][0]['code'] === 88){
		echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。\n");
	} else {
		echo("何らかのエラーが発生しました。\n");
	}
	return;
};

// アカウントが非公開ユーザでいいねにアクセスできない場合、以降の処理を行わず、終了
if($array_user['protected']){
	echo("指定したID [" . $twitter_id . "] は、非公開設定のユーザのため、いいねを取得できません。\n");
	// 以降の処理を行わず、終了
	return;
}

// $array_userが取得できない場合、以降の処理を行わず、終了
if(count($array_user) == 0){
	echo("何らかの理由で、指定したID [" . $twitter_id . "] の情報を取得できませんでした。\n");
	// 以降の処理を行わず、終了
	return;
}


// タイトル
// $html .= '<h1 style="text-align:center; border-bottom:1px solid #555; padding-bottom:12px; margin-bottom:48px; color:#D36015;">GET users/show</h1>' ;

// // エラー判定
// if( !$json || !$array_user ) {
// 	$html .= '<h2>エラー内容</h2>' ;
// 	$html .= '<p>データを取得することができませんでした…。設定を見直して下さい。</p>' ;
// }

// // 検証用
// $html .= '<h2>取得したデータ</h2>' ;
// $html .= '<p>下記のデータを取得できました。</p>' ;
// $html .= 	'<h3>ボディ(JSON)</h3>' ;
// $html .= 	'<p><textarea style="width:80%" rows="8">' . $json . '</textarea></p>' ;
// $html .= 	'<h3>レスポンスヘッダー</h3>' ;
// $html .= 	'<p><textarea style="width:80%" rows="8">' . $header . '</textarea></p>' ;

// // 検証用
// $html .= '<h2>リクエストしたデータ</h2>' ;
// $html .= '<p>下記内容でリクエストをしました。</p>' ;
// $html .= 	'<h3>URL</h3>' ;
// $html .= 	'<p><textarea style="width:80%" rows="8">' . $context['http']['method'] . ' ' . $request_url . '</textarea></p>' ;
// $html .= 	'<h3>ヘッダー</h3>' ;
// $html .= 	'<p><textarea style="width:80%" rows="8">' . implode( "\r\n" , $context['http']['header'] ) . '</textarea></p>' ;

// // フッター
// $html .= '<small style="display:block; border-top:1px solid #555; padding-top:12px; margin-top:72px; text-align:center; font-weight:700;">プログラムの説明: <a href="https://syncer.jp/Web/API/Twitter/REST_API/GET/users/show/" target="_blank">SYNCER</a></small>' ;

// // 出力 (本稼働時はHTMLのヘッダー、フッターを付けよう)
// echo $html ;

$request_method = 'GET' ;

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

// $rand_minの初期値を設定
$rand_min = 0;

// $rand_maxの初期値を設定
$current_date = date("Y/m/d H:i:s");
$rand_max = create_id($current_date); 

// 画面に表示するいいねの件数(入力値を格納)
$display_num = $_POST['display_num'];

// 日付指定範囲の開始日(入力値を格納)
// $begin_date = "2015/09/28";

// 日付指定範囲の終了日(入力値を格納)
// $end_date = "2015/09/28";

//DEBUG
$begin_date = $_POST['begin_date'];
$end_date = $_POST['end_date'];

// 一度のAPIへのアクセスで取得するいいねの件数
$GET_NUM = 200;

// APIへアクセスする処理の最大ループ回数
$MAX_LOOP = 5;

// $begin_dateが入力されていた場合、疑似IDを生成
if($begin_date != ""){
	// $begin_dateの全日を含めるために00:00:00の時間を設定
	$begin_date .= " 00:00:00";
	$rand_min = create_id($begin_date);
	// echo $begin_date;
}

// $end_dateが入力されていた場合、疑似IDを生成
if($end_date != ""){
	// $end_dateの全日を含めるために23:59:59の時間を設定
	$end_date .= " 23:59:59";
	$rand_max = create_id($end_date);
}

// DEBUG
// echo($rand_min . "\n");
// echo($rand_max . "\n");

// クエリとしてAPIへ送信するパラメータ
$params_a = array(
	"screen_name" => $twitter_id,
	"count" => $GET_NUM,
	"include_entities" => "false",
);

// いいね数が$GET_NUMより大きい場合、クエリにmax_id,since_idを指定
// if($array_user["favourites_count"] > $GET_NUM){
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
	// DEBUG
	// echo($params_a["max_id"] . "\n");
	// echo($GET_NUM . ' up ');
// } else {
// 	// DEBUG
// 	echo($GET_NUM . ' down ');
// }

// TODO この分岐必要?
// いいね数が$display_numより小さい場合、メッセージを表示し、終了
if($array_user["favourites_count"] < $display_num){
	echo('指定したユーザのいいね数が、指定した表示件数より少ないため、表示できませんでした。');
} else {    //いいね数が$display_numより大きい場合、いいねを取得し、表示
	// ループ回数をカウント
	$loop_count = 1;
	
	// キーを作成する (URLエンコードする)
	$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;

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
		$header = substr( $res1, 0, $res2['header_size'] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)

		// JSONをオブジェクトに変換
		$array = json_decode($json, true);

		// $display_numつ以上のいいねを取得できたらループを抜ける
		if(count($array) >= $display_num){break;};

		// $MAX_LOOP回ループして、$display_num個以上のいいねを取得できない場合、break
		// if($loop_count > $MAX_LOOP){
		// DEBUG
		// if($array == NULL){
		// 	echo(' array NULL ');
		// 	break;
		// };
		
		// APIからエラーが返されている場合、ループを抜ける
		if(array_key_exists('errors', $array)){
			echo "<pre>";
			print_r($array['errors']);
			echo "</pre>";
			if($array['errors'][0]['code'] === 32){
				echo("ユーザ認証ができませんでした。\n");
			} elseif($array['errors'][0]['code'] === 88){
				echo("APIの使用回数の上限に達したため、Twitterにアクセスできません。\n");
			} else {
				echo("何らかのエラーが発生しました。申し訳ございません。\n");
			}
			break;
		};

		// ループ回数をインクリメント
		$loop_count++;
		// DEBUG
		echo $loop_count;
		
		// $MAX_LOOP回ループして、$display_num個以上のいいねを取得できない場合、break
		if($loop_count > $MAX_LOOP){
			echo("\n");
			if(count($array)){
				echo("指定した日付の範囲内で " . $display_num . " 件のいいねがありませんでした。\n");
				echo("そのため、指定した日付の範囲内の " . count($array) . " 件のいいねを表示します。");
			} else {
				echo("指定した日付の範囲内でいいねがありませんでした。\n");
			}
			
			break;
		};
		
		// 乱数幅の最小値に、"$display_num"つ以上のデータを取得できなかった値を代入
		$rand_min = $params_a["max_id"];
		// $params_a["max_id"]を再設定
		$params_a["max_id"] = mt_rand($rand_min, $rand_max);
		
		// "$MAX_LOOP-1"回ループして、$display_num個以上のいいねを取得できない場合、
		// max_idを日付指定範囲の開始日、もしくは現在日のUNIX時にする
		if($loop_count >= $MAX_LOOP){
			$params_a["max_id"] = $rand_max;
		};
	}

	// DEBUG
	// echo "<pre>";
	// print_r($array);
	// echo "</pre>";
	
	// $html .= '<h1 style="text-align:center; border-bottom:1px solid #555; padding-bottom:12px; margin-bottom:48px; color:#D36015;">GET favorites/list</h1>' ;  // 検証用

	// $html .= '<h2>取得したデータ</h2>' ;
	// $html .= '<p>下記のデータを取得できました。</p>' ;
	// $html .=     '<h3>ボディ(JSON)</h3>' ;
	// $html .=     '<p><textarea style="width:80%" rows="8">' . $json . '</textarea></p>' ;
	// $html .=     '<h3>レスポンスヘッダー</h3>' ;
	// $html .=     '<p><textarea style="width:80%" rows="8">' . $header . '</textarea></p>' ;        // 検証用
	// $html .= '<h2>リクエストしたデータ</h2>' ;
	// $html .= '<p>下記内容でリクエストをしました。</p>' ;
	// $html .=     '<h3>URL</h3>' ;
	// $html .=     '<p><textarea style="width:80%" rows="8">' . $context['http']['method'] . ' ' . $request_url . '</textarea></p>' ;
	// $html .=     '<h3>ヘッダー</h3>' ; 
	// $html .=     '<p><textarea style="width:80%" rows="8">' . implode( "\r\n" , $context['http']['header'] ) . '</textarea></p>' ;

	$html .= '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';

	// いいねを格納している配列の要素をシャッフル
	shuffle($array);
	
	// いいねを表示している配列数をカウント
	$display_count = 0;
	
	foreach($array as $key => $value){
		// DEBUG
		// var_dump($key);
		// var_dump($value);
		// 埋め込みtweetの形で表示
		$html .= '<blockquote class="twitter-tweet tw-align-center" data-lang="ja"><p lang="ja" dir="ltr" text-align="center">' . $value["text"] . '</p>&mdash; ' . $value["user"]["name"] . '(@' . $value["user"]["screen_name"] . ') <a href="https://twitter.com/' . $value["user"]["screen_name"] . '/status/' . $value["id_str"] . '?ref_src=twsrc%5Etfw">' . $value["created_at"] . '</a></blockquote> <br>';
		$display_count++;
		// 表示したいいね数が表示件数以上になったら、break
		if($display_count >= $display_num){break;}
	}
}

echo $html ;