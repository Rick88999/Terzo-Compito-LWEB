<?php
/*Il campo profilo mostra in base alle tabelle user_table e info_user_table le informazioni modificabili dell'utente. Nel progetto
ho differenziato la tabella user da quella info_user per non andare a creare tabelle con troppi campi.
In seguito sfrutto il JOIN per mettere insieme le due tabelle e stampare i vari campi all'interno delle caselle di testo
per rendreli modificabili */
session_name('HillDownService');
session_start();

$db_name='HillDownGameStore_db';
$users_table='user_table';
$info_user_table='info_user_table';

//
$mod_field_user= array('email', 'password', 'nickname');
$mod_field_info= array('citta', 'via', 'cap');

if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {
  $sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
  if (mysqli_connect_errno()) {
      printf("Errore di connessione: %s\n", mysqli_connect_error());
      exit();
    }

    if (isset($_POST['send'])) {
      if ($_POST['send']=='logout') {
        unset($_SESSION);
        $sqlConnect->close();
        session_destroy();
        header('Location: login.php');
      }
      elseif ($_POST['send']=='Invio') {//Imetto le modifiche
        /*Le modifiche saranno tali solo se il campo presente in quello degli array mod_field_user e mod_field_info é presente anche nel POST*/
        foreach ($mod_field_user as $k) {
          if(isset($_POST[$k])){ //Se il relativo campo esiste in POST, aggiorno la tabella users nella relativa colonna del campo da modificare
            $query="UPDATE {$users_table} SET {$k}=\"{$_POST[$k]}\" WHERE id=\"{$_SESSION['id']}\";";
            $return=mysqli_query($sqlConnect, $query);
          }
        }
        foreach ($mod_field_info as $k) {
          if(isset($_POST[$k])){//Se il relativo campo esiste in POST, aggiorno la tabella info_user nella relativa colonna del campo da modificare
            $query="UPDATE {$info_user_table} SET {$k}=\"{$_POST[$k]}\" WHERE id=\"{$_SESSION['id']}\";";
            $return=mysqli_query($sqlConnect, $query);
          }
        }

      }


    }

/*Effettuo la query che mi restituira la lista degli utenti con le loro relative informazioni*/
  $query="SELECT * FROM `{$users_table}` JOIN `{$info_user_table}` ON `{$users_table}`.id = `{$info_user_table}`.id WHERE `{$users_table}`.id= {$_SESSION['id']};";
  $return=mysqli_query($sqlConnect, $query);
  $sqlConnect->close();
  $_SESSION['ttk']--;
}
else {
  $sqlConnect->close();
  unset($_SESSION);
  session_destroy();
  header('Location: login.php');
}



 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>DownHill Game Store</title>
    <link rel="stylesheet" href="profile.css" media="screen">
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
  </head>
  <body>
    <div class="flexContainer">
      <div class="flexNavBar">
        <div>
          <img src="logo2.png" alt="logo">

        </div>


        <div class="navBarStruct">
          <table>
            <tr>
              <td><a href="StoreHomePage.php">Home</a></td>
              <td><a href="profile.php">Profilo</a></td>
              <td><a href="library.php">Libreria</a></td>
              <td><img src="cart.png" alt="cart" usemap="#cart">
                <map name="cart">
                  <area shape="rect" coords="0,82,89,8" href="cartPage.php" alt="cart">
                </map>
              </td>
              <td>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <input type="submit" name="send" value="logout">
                </form>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="gameListContainer">
        <div class="table">
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">


          <?php
          /*Stampo per ogni utente l'email, la password, il nick name, la citta, la via e il CAP. Rendo modificambile ogni campo, in modo che ogni volta che aggiornerò la tupla le informazioni
          saranno cambiate in base al nome del campo text, che sarà la key presente poi nel POST*/
          if($row=mysqli_fetch_array($return)){

            $string="<table>";
            $string.="<tr>";
            $string.="<td>";
            $string.="<label for=\"email\">Email";
            $string.="<input type=\"text\" name=\"email\" value=\"{$row['email']}\" readonly>"; //Ovviamente la mail non si può modificare dato che è chiave primaria
            $string.="</label>";
            $string.="</td>";
            $string.="<td>";
            $string.="<label for=\"password\"> Password";
            $string.="<input type=\"password\" name=\"password\" value=\"{$row['password']}\">";
            $string.="</label>";
            $string.="</td>";
            $string.="<td>";
            $string.="<label for=\"nickname\">Nickname";
            $string.="<input type=\"text\" name=\"nickname\" value=\"{$row['nickname']}\">";
            $string.="</label>";
            $string.="</td>";
            $string.="</tr>";
            $string.="<tr>";
            $string.="<td>";
            $string.="<label for=\"citta\">Citta";
            $string.="<input type=\"text\" name=\"citta\" value=\"{$row['citta']}\">";
            $string.="</label>";
            $string.="</td>";
            $string.="<td>";
            $string.="<label for=\"via\"> Via";
            $string.="<input type=\"text\" name='via' value=\"{$row['via']}\">";
            $string.="</label>";
            $string.="</td>";
            $string.="<td>";
            $string.="<label for=\"cap\">CAP";
            $string.="<input type=\"text\" name=\"cap\" value=\"{$row['cap']}\">";
            $string.="</label>";
            $string.="</td>";
            $string.="</tr>";
            $string.="<tr>";
            $string.="<td>";
            $string.="<input type=\"submit\" name=\"send\" value=\"Invio\">";
            $string.="</td>";
            $string.="<td>";
            $string.="<input type=\"reset\" value=\"Reset\">";
            $string.="</td>";
            $string.="</tr>";
            $string.="</table>";
            echo $string;
          }
           ?>
           </form>
        </div>

      </div>
    </div>

    </body>
  </html>
