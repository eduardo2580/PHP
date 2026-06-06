<?php
// Processamento de cookies e POST movido para o início (necessário para funcionar)
$fundo = "branco";
$letra = "preto";
if(isset($_POST['fundo']) && isset($_POST['letra'])){
  $fundo=$_POST['fundo'];
  $letra=$_POST['letra'];
  setcookie("fundo",$fundo);
  setcookie("letra",$letra);
}else {
  if (isset($_COOKIE['fundo']) && isset($_COOKIE['letra'])) {
    $fundo=$_COOKIE['fundo'];
    $letra=$_COOKIE['letra'];
  } else {
    setcookie("fundo",$fundo);
    setcookie("letra",$letra);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
  </head>
  <body>
<?php
// Os valores de $fundo e $letra já foram definidos acima
?>
<form method="post">
  <p>Fundo:</p>
<input type="radio" id="fpreto" name="fundo" value="preto"
<?php echo ($fundo=="preto") ? 'checked="true"' : ""; ?>> Preto <br>
<input type="radio" id="fcinza" name="fundo" value="cinza"
<?php echo ($fundo=="cinza") ? 'checked="true"' : ""; ?>> Cinza <br>
<input type="radio" id="fbranco" name="fundo" value="branco"
<?php echo ($fundo=="branco") ? 'checked="true"' : ""; ?>> Branco <br>
<p>Letra:</p>
<input type="radio" id="lpreto" name="letra" value="preto"
<?php echo ($letra=="preto") ? 'checked="true"' : ""; ?>> Preto <br>
<input type="radio" id="lcinza" name="letra" value="cinza"
<?php echo ($letra=="cinza") ? 'checked="true"' : ""; ?>> Cinza <br>
<input type="radio" id="lbranco" name="letra" value="branco"
<?php echo ($letra=="branco") ? 'checked="true"' : ""; ?>> Branco <br>
<p><input type="submit" value="Salvar"></p>
</form>
<script>
$(document).ready(function(){
  function verifica(){
    // Corrigido: todos os seletores e propriedades com sintaxe correta
    if ($("#fpreto").prop("checked")) {
      if($("#lpreto").prop("checked")){
        $("#lbranco").prop("checked",true);
        $("body").css("color","white");
      }
      $("body").css("background-color","black");
    }
    if ($("#fcinza").prop("checked")) {
      if ($("#lcinza").prop("checked")) {  // corrigido "chekced"
        $("#lbranco").prop("checked",true);
        $("body").css("color","white");
      }
      $("body").css("background-color","gray");
    }
    if ($("#fbranco").prop("checked")) {   // corrigido: faltava $
      if ($("#lbranco").prop("checked")) {
        $("#lpreto").prop("checked",true);
        $("body").css("color","black");
      }
      $("body").css("background-color","white");
    }
    if ($("#lbranco").prop("checked")) {
      if ($("#fbranco").prop("checked")) {
        $("#fpreto").prop("checked",true);
        $("body").css("background-color","black");
      }
      $("body").css("color","white");
    }
    if ($("#lcinza").prop("checked")) {
      if ($("#fcinza").prop("checked")) {
        $("#fbranco").prop("checked",true);  // corrigido: era $("fwhite")
        $("body").css("background-color","white");
      }
      $("body").css("color","gray");
    }
    if ($("#lpreto").prop("checked")) {
      if ($("#fpreto").prop("checked")) {
        $("#fbranco").prop("checked",true);
        $("body").css("background-color","white");
      }
      $("body").css("color","black");
    }
  }
  $("input").click(verifica);
  verifica();
});
</script>
  </body>
</html>
