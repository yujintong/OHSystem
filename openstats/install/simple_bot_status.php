<html>
<head>
<title>Bot Status</title>
<style>
body {
    width: 600px;
    margin-top: 20px;
    margin-left: 40px;
    font-family: 'trebuchet MS', 'Lucida sans', Arial;
    font-size: 14px;
    color: #444;
}

table {
    *border-collapse: collapse; /* IE7 and lower */
    border-spacing: 0;
    width: 100%;
}

.bordered {
    border: solid #ccc 1px;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    -webkit-box-shadow: 0 1px 1px #ccc;
    -moz-box-shadow: 0 1px 1px #ccc;
    box-shadow: 0 1px 1px #ccc;
}

.bordered tr:hover {
    background: #fbf8e9;
    -o-transition: all 0.1s ease-in-out;
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition: all 0.1s ease-in-out;
    -ms-transition: all 0.1s ease-in-out;
    transition: all 0.1s ease-in-out;
}

.bordered td, .bordered th {
    border-left: 1px solid #ccc;
    border-top: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

.bordered th {
    background-color: #dce9f9;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ebf3fc), to(#dce9f9));
    background-image: -webkit-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:    -moz-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:     -ms-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:      -o-linear-gradient(top, #ebf3fc, #dce9f9);
    background-image:         linear-gradient(top, #ebf3fc, #dce9f9);
    -webkit-box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;
    -moz-box-shadow:0 1px 0 rgba(255,255,255,.8) inset;
    box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;
    border-top: none;
    text-shadow: 0 1px 0 rgba(255,255,255,.5);
}

.bordered td:first-child, .bordered th:first-child {
    border-left: none;
}

.bordered th:first-child {
    -moz-border-radius: 6px 0 0 0;
    -webkit-border-radius: 6px 0 0 0;
    border-radius: 6px 0 0 0;
}

.bordered th:last-child {
    -moz-border-radius: 0 6px 0 0;
    -webkit-border-radius: 0 6px 0 0;
    border-radius: 0 6px 0 0;
}

.bordered th:only-child{
    -moz-border-radius: 6px 6px 0 0;
    -webkit-border-radius: 6px 6px 0 0;
    border-radius: 6px 6px 0 0;
}

.bordered tr:last-child td:first-child {
    -moz-border-radius: 0 0 0 6px;
    -webkit-border-radius: 0 0 0 6px;
    border-radius: 0 0 0 6px;
}

.bordered tr:last-child td:last-child {
    -moz-border-radius: 0 0 6px 0;
    -webkit-border-radius: 0 0 6px 0;
    border-radius: 0 0 6px 0;
}
</style>
</head>
<body>
<h1>Bot Status</h1>

<?php
mysql_connect("localhost", "root", "YOUR_PASSWORD");
mysql_select_db("ohsystem");

$result = mysql_query("SELECT * FROM oh_bot_status WHERE last_update >= addtime(now(), '-00:01:00')");
?>
<table class="bordered">
 <thead>
        <th>BotID</th>
        <th>Bot Name</th>
        <th style="width:220px;">GameName</th>
        <th>IP</th>
        <th>HostPort</th>
        <th>Europe</th>
        <th>USEast</th>
        <th>USWest</th>
        <th>Asia</th>
        <th>XAMP</th>
        <th>Garena</th>
        <th>WC3Connect</th>
 </thead>
 <tbody>
<?php
while($row = mysql_fetch_array($result)) { ?>
 <tr><td><?=$row['botid']?></td><td><?=$row['name']?></td><td><?=$row['gamename']?></td><td><?=$row['ip']?></td><td><?=$row['hostport']?></td>
 <td><?=$row['Europe']?></td><td><?=$row['USEast']?></td><td><?=$row['USWest']?></td><td><?=$row['Asia']?></td><td><?=$row['XAMP']?></td><td><?=$row['Garena']?></td><td><?=$row['WC3Connect']?></td>
 </tr>
<? } ?>
</tbody>
</table>

Bot Status:<br>
0 - Not initalized<br>
1 - Connected<br>
2 - IP banned<br>
3 - disconnected (timed out)<br>
4 - wrong ROC key<br>
5 - wrong TFT key<br>
6 - invalid username or password
</body>
</html>

