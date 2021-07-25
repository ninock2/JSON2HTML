<html lang="en">
    <head>
        <title>JSON to HTML converter</title>
        <style>
            html {
                height: 100%;
                background-color: #f7f2e7;
            }
            body {
                background-color: lightgrey;
                font-family: "Trebuchet MS", Helvetica, sans-serif;
                text-align: center;
            }
            table {
                border: 1px solid #aaa;
                margin: 20px;
            }
            tr:nth-child(even) {
                background: #ccc;
            }
            tr:nth-child(odd) {
                background: #eee;
            }
            td {
                padding: 3px 15px;
                vertical-align: top;
            }
            .form{
                width: 500px;
                margin: 0 auto;
            }
            .html-file{
                text-align: left;
            }
            .btnsubmit {
                width: 200px;
                height: 30px;
                background-color: #ccc;
                border: 1px solid #aaa;
                font-size: 17px;
                color: #333;
            }
            .btnsubmit:hover {
                cursor: pointer;
                background-color: #aaa;
                border: 1px solid #888;
                color: #eee;
                text-shadow: 0 0 15px black;
            }
        </style>
    </head>
    <body>
        <h1>JSON to HTML converter</h1>
        <hr>
        <div style="align-content:center;">
            <form action="#" method="post" class="form">
                <input type="hidden" name="action" value="importcsv">
                <table>
                    <tr>
                        <!-- File path taken from POST -->
                        <td colspan="2" align=center><h3>Import JSON file</h3> File name: <?=(isset($_POST['filepath'])) ? $_POST['filepath'] : ""?></td>
                    </tr>
                    <tr>
                        <td>File path:</td>
                        <td><input type="file" name="filepath" id="filepath" value="<?= (isset($_POST['filepath'])) ? $_POST['filepath'] : "" ?>" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" align=center>
                            <br>
                            <input type="submit" value="Import" class="btnsubmit">
                            <input type="button" value="New" class="btnsubmit" onclick="window.location=''">
                            <br><br>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="html-file">
            <hr>
            <h4>Structure of generated HTML file: </h4>
            <b>
            <?php
                //Ustvarjen nov objekt in klicanje celotne funkcije
                $newObj = new json2html;
                $newObj->convert(); 
            ?>
        </b>
        </div>
    </body>
</html>    
   