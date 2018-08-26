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

                <h1>Heart♡Picker</h1>
                
                HeartPickerは自分や友達のいいねをランダムに表示するサービスです。<br>
				TwitterIDと日時範囲(任意)を指定して、｢Heart Pick!｣ボタンを押してね！
                
                <form class="form" action="heart.php" method="post">
                    <label for="twitter_id" accesskey="n" class="item_EN">TwitterID　<span class="must">必須</span><br></label>
                    <div class="input-group">
                        <span class="input-group__addon">@</span>
                        <input type="text" name="twitter_id" id="twitter_id" class="input-group__control" placeholder="例: TwitterJP" required>
                    </div>
                    <br>

                    <label for="begin_date" accesskey="n" class="item_JP">日付範囲(開始日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
                    <input type="text" name="begin_date" placeholder="例: 2015-1-1" id="begin_date" class="user_input" maxlength="10" onInput="checkForm(this)" pattern="(201[1-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))">

                    <label for="end_date" accesskey="n" class="item_JP">日付範囲(終了日)　<span class="free">任意</span><br>
                    <div class="explain">Year-Month-Day の形式で指定してください<br>※｢2010-11-4｣より前は指定できません</div></label>
                    <input type="text" name="end_date" placeholder="例: 2016-1-31" id="end_date" class="user_input" maxlength="10" onInput="checkForm(this)" pattern="(201[1-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])|2010[/-]1(1[/-]([5-9]|0[5-9]|[1-2][0-9]|3[01])|2[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])))">

                    <br>
                    <button type="submit">Heart Pick!</button>
                </form>
                <form class="form" action="auth.php" method="get">
                <details>
                    <summary>詳しい使い方,仕様（クリックで展開）</summary>
                    <div class="use">
                        <ul>
                        <li>TwitterIDは自分、他人のどちらも指定できます。</li>
                        <li>日付範囲が未指定の場合、全件からランダムに選ばれます。</li>
                        <li>TwitterAPIの仕様により、3200件より多くのいいねをしているユーザは、最近3200件のいいねの中からランダムに表示されます。</li>
                        <li>非公開設定にしているアカウント（鍵アカウント）のいいねは表示できません。</li>
                        <li>TwitterAPIの使用回数制限のため、サービスを実行する回数が多いと制限がかかり、サービスを利用できなくなります。
                        より多く使用したい人は、文末のTwitterのアイコンを押して、Twitterでログインしアプリケーション認証を行ってください。
                        
                        TwitterAPIの使用制限とTwitterのログインによるアプリケーション認証に関して、詳細を知りたい方は、本ページの末尾にて説明していますので、そちらを参照ください。</li>
                        <li>日付範囲が未指定であったり、日付範囲が広いと、最近のものが選ばれる確率が少しだけ高くなります。</li><br>
                        <button class="login_twitter" type="submit"><img src="images/buttons.png" alt="Twitterでログイン" width="200" height="40"/></button>  
                        <details>
                            <summary>使用制限,認証に関して（クリックで展開）</summary>
                            <div class="limit">
                                <ol>
                                <li>使用回数制限に関して<br>
                                TwitterAPIは、認証を行わない場合はアプリケーション単位、認証を行った場合はユーザ単位に対して使用回数が制限されます。
                                アプリケーション単位の場合は、アプリケーションを複数のユーザが使用している場合、複数ユーザの合計の使用回数を基準としてTwitterAPIの使用が制限されます。
                                ユーザ単位の場合は、アプリケーションを複数のユーザが使用している場合でも、1ユーザの使用回数を基準として、TwitterAPIの使用が制限されます。
                                そのため、ユーザ認証を行えば、使用制限が緩和されます。ユーザ認証を行った場合の使用回数の目安ですが、最低でも15分間に15回のいいね表示を行うことができます。</li>
                                <li>ユーザ認証(read only)に関して<br>
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