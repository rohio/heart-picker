
<html>
    <head>
        <link rel="stylesheet/less" type="text/css" href="style.less">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.5.1/less.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script type="text/javascript" src="sample.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <div class="container">

                <h1>Welcome</h1>
                
                <form class="form" action="heart.php" method="get">
                    <input type="text" name="twitter_id" placeholder="TwitterID">
                    <input type="text" name="display_num" placeholder="表示件数">
                    日付指定範囲(開始日)
                    <input type="text" name="begin_date" placeholder="yyyy/mm/dd">
                    日付指定範囲(終了日)
                    <input type="text" name="end_date" placeholder="yyyy/mm/dd">
                    <!-- <button type="submit" id="login-button-head">GET!</button> -->
                    <button type="submit">GET!</button>
                    <br>
                    <blockquote class="twitter-tweet" data-lang="ja"><p lang="ja" dir="ltr">サンムーンS2【最高レート2102】2100達成愚地流滅殺構築 <br>SM最高レート更新とはいきませんでしたが今の私の全力の全力を尽くしたパーティです！是非ご覧になってください！<a href="https://t.co/POHNKg4tRb">https://t.co/POHNKg4tRb</a></p>&mdash; かっと (@pocketermm) <a href="https://twitter.com/pocketermm/status/844126961475624960?ref_src=twsrc%5Etfw">2017年3月21日</a></blockquote>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
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