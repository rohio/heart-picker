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

                <h1>はーとぴっかー - Heart♡Pickker</h1>
                
                <form class="form" action="heart.php" method="post">
                    必須だよ
                    <input type="text" name="twitter_id" placeholder="TwitterID" value="<?php echo $_POST["twitter_id"]?>">
                    <input type="text" name="display_num" placeholder="表示件数" value="<?php echo $_POST["display_num"]?>">
                    以降は、必須じゃないよ<br>
                    日付指定範囲(開始日)
                    <input type="text" name="begin_date" placeholder="yyyy/mm/dd" value="<?php echo $_POST["begin_date"]?>">
                    日付指定範囲(終了日)
                    <input type="text" name="end_date" placeholder="yyyy/mm/dd" value="<?php echo $_POST["end_date"]?>">
                    <button type="submit">GET!</button><br>
                    
					
					<?php require('logic.php'); ?>

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