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
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </head>
    <body>
        <div id="loader">
            <img src="./loading.gif" alt="Now Loading..." width="80px" height="80px" />
        </div>
        <div id="fade"></div>
        <div class="wrapper">
            <div class="container">
                <h1>Heart♡Picker</h1>

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