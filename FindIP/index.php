<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desafio 2</title>
  </head>
  <body>
    <form method="post">
      <input type="text" name="ip">
      <input type="submit" value="OK">
    </form>
    <?php
      if (isset($_POST['ip'])) {
        $ch=curl_init("http://ip-api.com/json/".$_POST['ip']);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $resp = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($resp,true);
        if (isset($data['region'])) {
          echo "<p>UF: ".$data['region']."</p>";
          echo "<p>Cidade: ".$data['city']."</p>"; 
        }
      }
    ?>
  </body>
</html>
