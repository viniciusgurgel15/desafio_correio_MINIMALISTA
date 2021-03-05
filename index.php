<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastreamento correios</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&family=Roboto:wght@300;400;500&display=swap');
    </style>
    <style>
        * { 
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }
        h1{ 
            font-weight: 200;
            text-align: center;
        }
        .main {
            width: 50%;
            min-height: 200px;
            height: auto;
            margin: 0 auto;
            padding: 20px;
        }
        .inputs{
            width: 80%;
            margin: 20px auto;
            min-height: 30px;
            background: #e6e6e6;
            padding: 16px;
        }
        .inputs > input { 
            border: none;
            width: 80%;
            height: 20px;
            padding: 8px;
        }
        .inputs > button { 
            width: 15%;
            padding: 10px 0px;
            background: #49b749;
            color: #FFF;
            border: none;
            margin-left: -3px;
        }
        div#results {
            width: 50%;
            margin: 0 auto;
            background: #fafafa;
        }
        table {
            width: 80%;
            margin: 0px auto;
            padding-top: 20px;
            font-size: 13px;
            line-height: 27px;
            text-align: center;
        }
        p {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="main">
        <h1>Rastreamento de objetos correios:</h1>
        <div class="inputs">
            <input type="text" id="obj" placeholder="OBJETO. Ex.: AA123456789BR">
            <button id="rastrear">Rastrear</button>
        </div>
    </div>

    <div id="results"></div>
</body>

<script src="js/jquery-3.3.1.min.js"></script>
<script>

    $("body").on("click", "#rastrear", function() { 
        var obj = $("#obj").val();

        if(obj != "") { 
            $.ajax({
                type: 'GET',
                async: false,                 
                data: { obj: obj },
                url: 'api/Rastreamento.php',
                success: function(data) {
                    var r = JSON.parse(data);
                    if(r.code == 1) { 
                        var result = r.data;
                        var tbl = `
                            <table>
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Local</th>
                                        <th>Mensagem</th>
                                        <th>Atualização</th>
                                    </tr>
                                </thead>
                                <tbody>
                        `;
                        
                            var tr =  ``;
                            $(result).each(function(i, v) { 
                                console.log(v);
                                tr += `
                                    <tr>
                                        <td>${v.date} às ${v.hour}</td>
                                        <td>${v.location}</td>
                                        <td>${v.action}</td>
                                        <td>${v.change}</td>
                                    </tr>
                                `;
                            });

                        tbl += ` ${tr}
                                </tbody>
                            </table>`;

                        $("#results").html(tbl);
                    }
                    else {
                        var p = `<p>${r.msg}</p>`;
                        $("#results").html(p);
                    }
                }
            });
        }

        return false;
    });
</script>
</html>