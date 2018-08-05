<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>はーとぴっかー</title>
        <link rel="stylesheet/less" type="text/css" href="style.less">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="sample.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">

                <h1>はーとぴっかー - Heart♡Pickker</h1>
                
                <form class="form" action="heart.php" method="post">
                    <label for="twitter_id" accesskey="n"><font size="5">TwitterID</font><font size="3">＊必須</font><br>
                    <font size="2" color="aqua">@ は付けないでください </font></label>
                    <input type="text" name="twitter_id" placeholder="例: TwitterJP" id="twitter_id">

                    <label for="display_num" accesskey="n"><font size="4">表示件数</font><font size="3">＊必須</font><br>
                    <font size="2" color="lightcyan">1~200の間で指定してください</font></label>
                    <input type="text" name="display_num" placeholder="例: 10" id="display_num"><br>

                    <label for="begin_date" accesskey="n"><font size="4">日付指定(開始日)　</font><font size="3">任意</font><br>
                    <font size="2" color="lightcyan">yyyy/mm/ddの形式で指定してください</font></label>
                    <input type="date" name="begin_date" placeholder="例: 2015/01/01" id="begin_date">

                    <label for="end_date" accesskey="n"><font size="4">日付指定(終了日)　</font><font size="3">任意</font><br>
                    <font size="2" color="lightcyan">yyyy/mm/ddの形式で指定してください</font></label>
                    <input type="text" name="end_date" placeholder="例: 2016/01/31" id="end_date">

                    <br>
                    <button type="submit">Heart Pick!</button>
                </form>                
            </div>


            <ul class="bg-bubbles">
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