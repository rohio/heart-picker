<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>はーとぴっかー</title>
        <link rel="stylesheet/less" type="text/css" href="style.less">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
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
                    <label for="twitter_id" accesskey="n"><font size="5">TwitterID　</font><font class="must">必須</font><br>
                    <font size="2" color="aqua">@ は付けないでください </font></label>
                    <input type="text" name="twitter_id" placeholder="例: TwitterJP" id="twitter_id" required>

                    <label for="display_num" accesskey="n"><font size="4">表示件数　</font><font class="must">必須</font><br>
                    <font size="2" color="lightcyan">1~200の間で指定してください</font></label>
                    <input type="number" name="display_num" placeholder="例: 10" id="display_num" min="1" max="200" required><br>

                    <label for="begin_date" accesskey="n"><font size="4">日付範囲(開始日)　</font><font class="free">任意</font><br>
                    <font size="2" color="lightcyan">Year-Month-Dayの形式で指定してください</font></label>
                    <input type="text" name="begin_date" placeholder="例: 2015-1-1" id="begin_date" maxlength="10" onInput="checkForm(this)" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|[0-2][1-9]|3[01])">

                    <label for="end_date" accesskey="n"><font size="4">日付範囲(終了日)　</font><font class="free">任意</font><br>
                    <font size="2" color="lightcyan">Year-Month-Dayの形式で指定してください</font></label>
                    <input type="text" name="end_date" placeholder="例: 2016-1-31" id="end_date" maxlength="10" onInput="checkForm(this)" pattern="201[0-9][/-]([1-9]|0[1-9]|1[12])[/-]([1-9]|[0-2][1-9]|3[01])">

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