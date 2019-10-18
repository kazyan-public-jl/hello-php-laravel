<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>Laravel API test</title>
</head>
<body>
    <div class="content">
        <p>Push and Change Message!<p>
            <ul class="link-list">
                <li class="link"><a href="/api/message">get message hello0</a></li>
                <li class="link"><a href="/api/message/hello01">get message hello01</a></li>
                <li class="link">
                    <form id='test' name='test' action="/api/message" method="post">
                        <input type="hidden" name="message" value='post hello02' />
                        <input id='btn' type="submit" value="post hello02">
                    </form>
                </li>
                <li class="link">
                    <form id='testAjax' name='testAjax' action="/api/message" method="post">
                        <input type="hidden" name="message" value='post hello03' />
                        <input id='btnAjax' type="button" value="post hello03">
                    </form>
                    → <input type="text" name="message" id="ResultMessage" style='width:250px;' placeholder="click any link, and change message..." value={{ $message }} />
                </li>
            </ul>
        </div>
    </body>
    <script>
        function findParentForm(elem){ 
            const parent = elem.parentNode; 
            if(parent && parent.tagName != 'FORM'){
                parent = findParentForm(parent);
            }
            return parent;
        }
        
        function getParentForm( elem )
        {
            const parentForm = findParentForm(elem);
            if(parentForm){
                console.log("Form found: ID = " + parentForm.id + " & Name = " +parentForm.name);
                return parentForm;
            }else{
                console.log("unable to locate parent Form");
                return undefined;
            }
            
        }

        function submitAjaxHandler(event){
            // 通信情報を整理
            const targetElement = event.currentTarget;
            const formElement = findParentForm(targetElement);
            const url     = formElement.action;
            const message = formElement.querySelector('input[name="message"]').value;

            // 非同期通信オブジェクトを定義
            const req = new XMLHttpRequest();
            // レスポンスがあった時は、結果を #ResultMessage に表示する
            req.onreadystatechange = function() {
                let messageElement = document.getElementById('ResultMessage');
                if (req.readyState == 4) { // 通信の完了時
                    if (req.status == 200) { // 通信の成功時
                        messageElement.value = req.responseText;
                    }
                }else{
                    messageElement.value = "通信中...";
                }
            }
            // 非同期通信を設定
            req.open('POST', url, true);
            req.setRequestHeader(
                'content-type',
                'application/x-www-form-urlencoded;charset=UTF-8'
            );
            // 非同期通信を送信, リクエストの返事で onreadystatechange が発火する
            req.send('message='+message);
        }
        window.onload = function(){
            // イベント登録
            let submitAjaxElement = document.getElementById('btnAjax');
            submitAjaxElement.addEventListener('click', submitAjaxHandler);
        }
    </script>
    </html>
    