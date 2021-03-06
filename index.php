<!DOCTYPE html>
<html>
    <head>
        
        <meta charset="utf-8">
        <link rel="stylesheet" href="Assets/reset.css">
        <link rel="stylesheet" href="Assets/style.css">
        

        
        
        
        <!-- import external stuff -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script src="http://peterolson.github.com/BigInteger.js/BigInteger.min.js"></script>
        
        <link href='https://fonts.googleapis.com/css?family=Poiret+One' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Raleway:300' rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Comfortaa:300,400" rel="stylesheet">
        
        <!-- Local scripts -->
        <script src="tools.js"></script>
        <script src="parseExpr.js"></script>
        <script src="math.js"></script>
        <script src="exprMath.js"></script>
        <script src="simpExpr.js"></script>
        <script src="vars.js"></script>
        
        <script src="http://fxtend.herokuapp.com/Lyrics/Scripts/panelTransform.js"></script>
        
        
    </head>
    
    <body>
        <div id="mainPage">
            <input type="text" id="input" value="√2"><br><div class="button btnEnter" onclick="parseAndCalc()">Enter<div class="cb cbAuto cbInactive" onclick="toggleEnter()"></div> </div><a id="ans">1.4142135623730951</a><div id="rerender"></div>
        </div>
        
        <!--<iframe id="userScript"></iframe>-->
        
        <script id="userScript"></script>
        
        <script>
            
            var inputBox = document.getElementById("input");
            var ansBox = document.getElementById("ans");
            var btnEnter = document.getElementById("btnEnter")
            var cbAuto = document.getElementById("cbAuto");
            var rerendBox = document.getElementById("rerender");
            var uvarInd = 0;
            var autoCalc = true;
            //inputBox.value.onchange = parseAndCalc()
            
            $('#input').on('input', function() { 
                rerenderStr(inputBox.value);
                if(autoCalc){
                    parseAndCalc();
                }
            });
            
            function toggleEnter(){
                autoCalc = !autoCalc;
                $(".btnEnter").toggleClass("btnActive");
                $(".cbAuto").toggleClass("cbInactive");
            }
            
            var failed = false;
            function err(msg){
                ansBox.innerHTML = msg;
                failed = true;
            }
            
            
            function exprToString(expr, pref, suf){
                // In case you don't want any separators
                if(suf == undefined){
                    suf = "";
                    if(pref == undefined){
                        pref = "";
                    }
                }
                switch(expr.type){
                    case "num":
                        return pref + expr.num + suf;
                    case "unOp":
                        return pref + expr.op + exprToString(expr.expr, pref, suf) + suf;
                    case "binOp":
                        return pref + "(" + exprToString(expr.expr0) + expr.op + exprToString(expr.expr1) + ")" + suf;
                }
            }
            
            
            function parseAndCalc(){
                failed = false;
                ansBox.innerHTML = "";
                if(inputBox.value.length > 0){
                    var expr = purifyStr(inputBox.value);
                    expr = parseExpr(expr)
                    expr = simplify(expr); //TODO: fraction division simplification is wrong for bigInt but not JS numbers
                    var ans = calc(expr);
                    if(!failed)
                        ansBox.innerHTML = ans;
                    rerendBox.innerHTML = exprToString(expr, '<a class="expr">', '</a>');
                }
            }
            
            
            
            function rerenderStr(str){
                var oldCursorPos = inputBox.selectionStart;
                var oldLen = str.length;
                
                for(var i = 0; i < str.length; i++){
                    var newStr = "";
                    var oldLength = 0;
                    var found = false;
                    for(var replInd = 0; replInd < strRepl.length && !found; replInd++){
                        var from = strRepl[replInd].from;
                        for(var fromInd = 0; fromInd < from.length && !found; fromInd++){
                            if(begins(from[fromInd], str.substr(i))){
                                found = true;
                                oldLength = from[fromInd].length;
                                newStr = strRepl[replInd].to;
                                str = str.substr(0, i) + newStr + str.substr(i + oldLength)
                            }
                        }
                    }
                }
                inputBox.value = str;
                var newCursorPos = oldCursorPos + str.length - oldLen;
                inputBox.selectionStart = newCursorPos;
                inputBox.selectionEnd = newCursorPos;
            }
            
            function purifyStr(str){
                var jsInd = str.indexOf("js(");
                while(jsInd > -1){
                    jsEnd = skipPars(str, jsInd + 2, true);
                    var jsStr = str.substring(jsInd + 3, jsEnd - 1);
                    jsStr = calcJs(jsStr);
                    str = str.substring(0, jsInd) + jsStr + str.substr(jsEnd);
                    jsInd = str.indexOf("js(")
                }
                return str;
            }
            
            function calcJs(str){
                var scriptEl = document.getElementById("userScript");
                scriptEl.innerHTML = "function userFunc(){ return " + str + ";}";
                return userFunc();
            }
            
            saveToServer("Hej", "test.txt");

            function saveToServer(content, filePath){
    var data = new FormData();
    data.append("data" , content);
    var xhr = (window.XMLHttpRequest) ? new XMLHttpRequest() : new activeXObject("Microsoft.XMLHTTP");
    xhr.open( 'post', 'saveToServer.php', true );
    xhr.send(data);
}
            
        </script>
        
    </body>
</html>