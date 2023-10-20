<?php
/*La pagina di SignIn serve per l'appunto alla registrazione di nuovi utenti*/
$db_name='HillDownGameStore_db';
$users_table='user_table';
$info_user_table='info_user_table';


  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
    }
    $already1=false; //Le due variabili booleane servono a segnalare se si presenta la condizione (1) e/o la condizione (2) o nessuna delle due
    $already2=false;

  if (isset($_POST['send'])) {
    if($_POST['send']=='invio'){

      /*
      Contollo in seguito all'invio della form:
      (1) Se l'email non é gia presente tra gli utenti dato che è chiave primaria insieme all'id;
      (2)Controllo se i campi non sono vuoti
      */

      $query="SELECT email FROM `{$users_table}` WHERE email=\"{$_POST['email']}\";";
      $return=mysqli_query($sqlConnect, $query);
      if(!($row=mysqli_fetch_array($return))){     //(1)
        if ($_POST['email']!="" && $_POST['nickname']!="" && $_POST['password']!="" && !(empty($_POST['type_game_played'])) && !(empty($_POST['game_played']))) { //(2)
          $query="INSERT INTO {$users_table} (email, nickname, password) VALUES (\"{$_POST['email']}\", \"{$_POST['nickname']}\", \"{$_POST['password']}\");"; //Inserisco il nuovo utente nella tabella users_table, che gli assegnera un id
          $return1=mysqli_query($sqlConnect, $query);

          $typeGameString="";
          $gameString="";
          foreach ($_POST['type_game_played'] as $v) {
            $typeGameString.=$v.":";
          }
          foreach ($_POST['game_played'] as $v) {
            $gameString.=$v.":";
          }
          $query="SELECT id FROM `{$users_table}` WHERE email=\"{$_POST['email']}\";"; //Ricavo l'id assegnatoli e lo associo alla tupla di info_user_table di referimento
          $return=mysqli_query($sqlConnect, $query);
          if($row=mysqli_fetch_array($return)){
            $query="INSERT INTO {$info_user_table} (id, citta, via, cap, type_game_played, game_played, rank_lv) VALUES (\"{$row['id']}\", \"{$_POST['citta']}\", \"{$_POST['via']}\", \"{$_POST['cap']}\", \"{$typeGameString}\", \"{$gameString}\",\"{$_POST['rank_lv']}\");";
            $return2=mysqli_query($sqlConnect, $query);
          }

          header('Location: login.php');
        }
        else $already2=true;

      }
      else $already1=true;
    }
    if ($_POST['send']=='back') {
      $sqlConnect->close();
      header('Location: login.php');
    }
  }
  $sqlConnect->close();



 ?>


 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="signIn_.css" media="screen">
  </head>
  <body>
    <div class="flexContainer">
      <div class="table">
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
          <?php
          if ($already1) {
            echo "<p>Email già presente!</p>";
          }
          elseif ($already2) {
            echo "<p>Completa tutti campi!</p>";
          }
           ?>
        <table>
          <tr>
            <td><input type="submit" name="send" value="back"></td>
          </tr>
          <tr>
            <td><label for="email">Email: <input type="text" name="email" value=""></label></td>
            <td><label for="password">Password: <input type="text" name="password" value=""></label></td>
          </tr>
          <tr>
            <td><label for="nickname">Nickname: <input type="text" name="nickname" value=""></label></td>
            <td><label for="citta">Citt&aacute;: <input type="text" name="citta" value=""></label></td>
          </tr>
          <tr>
            <td><label for="via">Via: <input type="text" name="via" value=""></label></td>
          </tr>
          <tr>
            <td><label for="cap">CAP: <input type="text" name="cap" value=""></label></td>
            <td>
              <label for="type_game_played">Che generi giochi di solito? <br>
              <input type="checkbox" name="type_game_played[]" value="Arena">Arena
              <input type="checkbox" name="type_game_played[]" value="MOBA">MOBA
              <input type="checkbox" name="type_game_played[]" value="FPS">FPS<br>
              <input type="checkbox" name="type_game_played[]" value="Sandbox">Sandbox
              <input type="checkbox" name="type_game_played[]" value="Gacha game">Gacha game
              <input type="checkbox" name="type_game_played[]" value="TPS">TPS<br>
              <input type="checkbox" name="type_game_played[]" value="Sport">Sport
              <input type="checkbox" name="type_game_played[]" value="Simulazione">Simulazione
              <input type="checkbox" name="type_game_played[]" value="VR">VR<br>
            </label>
            </td>
          </tr>
          <tr>
            <td>
              <label for="type_game_played">Che cosa giochi di solito?<br>
              <input type="checkbox" name="game_played[]" value="Overwatch">Overwatch
              <input type="checkbox" name="game_played[]" value="Call of Duty">Call of Duty
              <input type="checkbox" name="game_played[]" value="Terraria">Terraria <br>
              <input type="checkbox" name="game_played[]" value="Minecraft">Minecraft
              <input type="checkbox" name="game_played[]" value="Destiny">Destiny <br>
              <input type="checkbox" name="game_played[]" value="Counter Stricke">Counter Stricke
              <input type="checkbox" name="game_played[]" value="Assetto Corsa">Assetto Corsa <br>
              <input type="checkbox" name="game_played[]" value="Super hot">Super hot
              <input type="checkbox" name="game_played[]" value="Fallout">Fallout <br>
            </label>
            </td>
            <td>
              <label for="rank_lv">Quale è il più alto grado competitivo<br>che hai raggiunto giocando uno di questi giochi?
              <select name="rank_lv" size="1">
                <option value="Master">Master</option>
                <option value="Diamond">Diamond</option>
                <option value="Platinium">Platinium</option>
                <option value="Gold">Gold</option>
                <option value="Silver">Silver</option>
                <option value="Bronze">Bronze</option>
                <option value="Other">Other</option>
              </select>

            </label>
            </td>
          </tr>
          <tr>
            <td><input type="submit" name="send" value="invio"></td>
          </tr>
        </table>
        </form>
      </div>
    </div>


  </body>
  </html>
