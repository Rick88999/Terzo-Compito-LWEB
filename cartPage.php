<?php
/*Questa pagina serve da cart per il sito. Ogni gioco o DLC verra mostrato e pota essere selezionato per la rimozione,
oppure si pota andare al pagamento. (Ovviamente essendo un HOMEWORK il pulsante payAll riporterà alla pagina di HOME e non ad una di transazione).
Altra cosa importante è come è organizzata la tabella active_cart: ogni utente avrà un id utente nel progetto. Quindi ho pensato che ogni carrello attivo per utente fosse un insieme di tuple riferite all'utente stesso
con chiave primaria la coppia [id_utente + id_prodotto]. Quindi più tuple per lo stesso utente, ma con prodotti diversi.*/
session_name('HillDownService');
session_start();
require("utility.php");


$error="";
$error2="";


if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {


  $xmlstream=streamChanger("cache/carrello_attivo.xml");
  $XMLcart=new DOMDocument();
  $XMLcart->loadXML($xmlstream);
  $rootCart=$XMLcart->documentElement;

  $xmlstream=streamChanger("cache/gamesCache.xml");
  $XMLcache=new DOMDocument();
  $XMLcache->loadXML($xmlstream);
  $rootCache=$XMLcache->documentElement;
  $games=$rootCache->childNodes;

  $userNodeFlag=false;



  if (isset($_POST['send'])) {

    if ($_POST['send']=='pay All') { //Al pagamento inserisco i giochi nella users_game_list

      $xmlstream=streamChanger("cache/UserGameTable.xml");
      $XMLuserTable=new DOMDocument();
      $XMLuserTable->loadXML($xmlstream);
      $rootUserTable=$XMLuserTable->documentElement;
      $usersList=$rootUserTable->childNodes;


      $carts=$rootCart->childNodes;
      for ($i=0; $i < $carts->length; $i++) {
        $cart=$carts->item($i);
        if($cart->getAttribute('id_user')==$_SESSION['id']) $userCart=$cart;
      }
      for ($i=0; $i < $usersList->length; $i++) {
        $userList=$usersList->item($i);
        if ($userList->getAttribute('id_user')==$_SESSION['id']) {
          foreach ($userCart->childNodes as $game) {
            $newRecord=$XMLuserTable->createElement("id_game", $game->getAttribute('id_game'));
            $userList->appendChild($newRecord);
          }
          $userNodeFlag=true;
        }
      }

      if(!($userNodeFlag)){
        $newUser=$XMLuserTable->createElement("UserGames");
        $newUser->setAttribute('id_user', $_SESSION['id']);
        foreach ($userCart->childNodes as $game) {
          $newRecord=$XMLuserTable->createElement("id_game", $game->getAttribute('id_game'));
          $newUser->appendChild($newRecord);
        }
        $rootUserTable->appendChild($newUser);
      }
      $rootCart->removeChild($userCart);
      unset($_SESSION['ids_to_cart']);
      $XMLcart->save("cache/carrello_attivo.xml");
      $XMLuserTable->save("cache/UserGameTable.xml");
    }

    elseif ($_POST['send']=='logout') {
      unset($_SESSION);
      session_destroy();
      header('Location: login.php');
    }
  }

/*Il seguente codice verifica che ci siano prodotti in un carrello già attivo in precedenza per l'utente. Inseguito mette questi prodotti in "array di tuple", che conserva tutte le informazioni*/
  $flag=0;
  $active=false;


  if($rootCart->hasChildNodes()){
    $carts=$rootCart->childNodes;
    for ($i=0; $i < $carts->length ; $i++) {
      $cart=$carts->item($i);
      if($cart->getAttribute('id_user')==$_SESSION['id']){
        $userCart=$cart;
        $active=true;
        $flag++;
      }
    }
  }


  if(isset($_SESSION['ids_to_cart'])){

    if($active){
      foreach ($_SESSION['ids_to_cart'] as $id) {
        $newRecord=$XMLcart->createElement("gioco");
        $newRecord->setAttribute("id_game", $id);

        for ($i=0; $i < $games->length ; $i++) {
          $game=$games->item($i);
          if($game->getAttribute('id')==$id){
            $j=0;
            $recordBay=array();
            foreach ($game->childNodes as $field) {
              if ($j<2) {
                $recordBay[]=$field->textContent;
              }
              $j++;
            }
          }
        }

        $newTitolo=$XMLcart->createElement("titolo", $recordBay[0]);
        $newPrezzo=$XMLcart->createElement("prezzo", $recordBay[1]);
        $newRecord->appendChild($newTitolo);
        $newRecord->appendChild($newPrezzo);
        $userCart->appendChild($newRecord);
    }
  }
    else{
      $newCart=$XMLcart->createElement("carrello");
      $newCart->setAttribute("id_user", $_SESSION['id']);

      foreach ($_SESSION['ids_to_cart'] as $id) {
        $newRecord=$XMLcart->createElement("gioco");
        $newRecord->setAttribute("id_game", $id);

        for ($i=0; $i < $games->length; $i++) {
          $game=$games->item($i);
          if($game->getAttribute('id')==$id){
            $j=0;
            $recordBay=array();
            foreach ($game->childNodes as $field) {
              if ($j<2) {
                $recordBay[]=$field->textContent;
              }
              $j++;
            }
          }
        }

        $newTitolo=$XMLcart->createElement("titolo", $recordBay[0]);
        $newPrezzo=$XMLcart->createElement("prezzo", $recordBay[1]);
        $newRecord->appendChild($newTitolo);
        $newRecord->appendChild($newPrezzo);
        $newCart->appendChild($newRecord);

      }
      $rootCart->appendChild($newCart);
      $userCart=$newCart;
    }
    $flag++;
    unset($_SESSION['ids_to_cart']);
    $XMLcart->save("cache/carrello_attivo.xml");
  }



  if (isset($_POST['delete_checked'])) { //Si attiva solo se abbiamo inviato dalla form alcuni giochi da eliminare
    if ($_POST['send']=='delete') {
      $carts=$rootCart->childNodes;
      for ($i=0; $i < $carts->length ; $i++) {
        $cart=$carts->item($i);
        if($cart->getAttribute('id_user')==$_SESSION['id']){
          $userCart=$cart;
        }
      }
      foreach ($_POST['delete_checked'] as $to_del) {
        foreach ($userCart->childNodes as $game) {
          if ($game->getAttribute('id_game')==$to_del) {
            $userCart->removeChild($game);
          }
        }
      }

      if (!($userCart->hasChildNodes())) {
        $rootCart->removeChild($userCart);
      }
    }
    $XMLcart->save("cache/carrello_attivo.xml");
    if(!($rootCart->hasChildNodes())) $flag=0; //Se, in seguito alla cancellazione di uno o più prodotti, row1[] diventasse vuoto, allora nache il carrello lo sarebbe. Dunque settiamo flag=0
    }


//ATTENZIONE: uso la stringa error solo per fare economia di variabili
  if ($flag==0) {
    $error.="Carrello Vuoto"; //se falg è 0 allora il carrello è vuoto
  }
  unset($XMLcart);
  unset($XMLcache);
  $_SESSION['ttk']--;
}
else {
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
    <link rel="stylesheet" href="Init_Struct__.css" media="screen">
    <link rel="stylesheet" href="cartPage_.css" media="screen">
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


        </form>


        <div class="table">
          <?php
          /*Stampo per ogni tupla gioco, il nome e il prezzo. Aggiungendo una casella di check, per la selezione e l'eventuale eliminazione del gioco dal carrello*/
          $total=0;

          if($flag>0){
            echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
            echo "<table>";
            echo "<tr>";
            echo "<td>";
            foreach ($userCart->childNodes as $game) {
              echo "<label>";
              echo "<input type=\"checkbox\" name=\"delete_checked[]\" value=\"{$game->getAttribute('id_game')}\">";
              foreach ($game->childNodes as $field) {
                switch ($field->nodeName) {
                  case 'titolo':
                    echo $field->textContent." | ";
                    break;
                  case 'prezzo':
                    echo $field->textContent."&euro;";
                    $total += (double)$field->textContent;
                    break;

                  default:
                    break;
                }
              }
              echo "</label>";
              echo "<br>";
            }
            echo "</td>";
            echo "</tr>";

            echo "<tr>";
            echo "<td>";
            echo "TOTALE: ";
            echo $total."&euro;";//Sommo e stampo il totale
            echo "</td>";
            echo "</tr>";
            echo "<tr><td><input type=\"submit\" name=\"send\" value=\"pay All\">";
            echo "<input type=\"submit\" name=\"send\" value=\"delete\">";
            echo "</td>";
            echo "</tr>";

            echo "</table>";
            echo "</form>";
          }
          else{
            echo "<p>".$error."</p>";
          }

          if ($error2!="") {
            echo "<p>".$error2."</p>";
          }

           ?>

        </div>

      </div>

    </body>
  </html>
