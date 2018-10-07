// TwitterIDのテキストボックスに、全角半角数字、全角半角英字、全角半角アンダースコアの入力のみ受付
function checkID($this)
{
    var str=$this.value;
    while(str.match(/[^\d０-９A-ZＡ-Ｚa-zａ-ｚ_＿]/))
    {
        str=str.replace(/[^\d０-９A-ZＡ-Ｚa-zａ-ｚ_＿]/,"");
    }
    $this.value=str;
}

// TwitterIDのテキストボックスに、全角半角数字、全角半角英字、全角半角アンダースコアが入力された場合、半角に変換
$(function(){
    $("#twitter_id").change(function(){
        var str = $(this).val();
        str = str.replace( /[Ａ-Ｚａ-ｚ０-９＿]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0) - 65248);
        })
        $(this).val(str);
    }).change();
});

// 日付範囲のテキストボックスに、全角半角数字、全角半角ハイフン、全角半角スラッシュの入力のみ受付
function checkForm($this)
{
    var str=$this.value;
    while(str.match(/[^\d\-\/０-９ー－―／]/))
    {
        str=str.replace(/[^\d\-\/０-９ー－―／]/,"");
    }
    $this.value=str;
}

// 日付範囲のテキストボックスに、全角数字、全角ハイフン、全角スラッシュが入力された場合、半角に変換
$(function(){
    $("input.user_input").change(function(){
        var str = $(this).val();
        str = str.replace( /[０-９－／]/g, function(s) {
            return String.fromCharCode(s.charCodeAt(0) - 65248);
        })
        .replace(/[‐－―ー]/g, "-");
        $(this).val(str);
    }).change();
});