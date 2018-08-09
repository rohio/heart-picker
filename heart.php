<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>はーとぴっかー</title>
        <link rel="stylesheet/less" type="text/css" href="style.less">
        <meta name="viewport" content="width=device-width, user-scale=yes, initial-scale=1.0, maximum-scale=5.0" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
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
        </script>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">

                <h1>はーとぴっかー</h1> 
                <h1>Heart♡Pickker</h1>
                
                <form class="form" action="heart.php" method="post">
                    <label for="twitter_id" accesskey="n"><font size="5">TwitterID　</font><font class="must">必須</font><br>
                    <font size="2" color="aqua">@ は付けないでください </font></label>
                    <input type="text" name="twitter_id" placeholder="例: TwitterJP" id="twitter_id" value="<?php echo $_POST["twitter_id"]?>" required>

                    <label for="display_num" accesskey="n"><font size="4">表示件数　</font><font class="must">必須</font><br>
                    <font size="2" color="lightcyan">1~20の間で指定してください</font></label>
                    <input type="number" name="display_num" placeholder="例: 10" id="display_num" min="1" max="20" value="<?php echo $_POST["display_num"]?>" required><br>

                    <label for="begin_date" accesskey="n"><font size="4">日付範囲(開始日)　</font><font class="free">任意</font><br>
                    <font size="2" color="lightcyan">Year-Month-Dayの形式で指定してください</font></label>
                    <input type="text" name="begin_date" placeholder="例: 2015-1-1" id="begin_date" maxlength="10" onInput="checkForm(this)" value="<?php echo $_POST["begin_date"]?>" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])">

                    <label for="end_date" accesskey="n"><font size="4">日付範囲(終了日)　</font><font class="free">任意</font><br>
                    <font size="2" color="lightcyan">Year-Month-Dayの形式で指定してください</font></label>
                    <input type="text" name="end_date" placeholder="例: 2016-1-31" id="end_date" maxlength="10" onInput="checkForm(this)" value="<?php echo $_POST["end_date"]?>" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|0[1-9]|[1-2][0-9]|3[01])">

                    <br>
                    <button type="submit">Heart Pick!</button>
                    <br>

                    <?php require('logic.php'); ?>

                    <p id="pageTop"><a href="#"><i class="fa fa-chevron-up"></i>↑</a></p>

                </form>                
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