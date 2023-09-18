<?php
/*Questa pagina serve da cart per il sito. Ogni gioco o DLC verra mostrato e pota essere selezionato per la rimozione,
oppure si potra andare al pagamento. Il cart attivo sarà un unico file xml dove ogni cart sarà differnzato dall'altro in base al id utente di riferimento.
Ogni gioco nel carrello utente avrà un id gioco come attributo, mentre i suoi child saranno il titolo e il prezzo del gioco stesso.
Va da se che ogni utente avra più giochi nel carrello e che cart di utenti diversi possono avere lo stesso game al proprio interno.
Lo stesso utente però non potra avere lo stesso gioco nel suo carrello (questa condizione esige un controllo eseguito nella pagina precedente gameList.php)*/
session_name('HillDownService');
session_start();
require("utility.php");


$error="";
$error2="";


if (isset($_SESSION['ttk']) && $_SESSION['ttk']>0) {
  //Apertura del contenitore dei vari carrelli
  $xmlstream=streamChanger("cache/carrello_attivo.xml");
  $XMLcart=new DOMDocument();
  $XMLcart->loadXML($xmlstream);
  $rootCart=$XMLcart->documentElement;
  //Apertura della lista Giochi disponibili per l'acquisto
  $xmlstream=streamChanger("cache/gamesCache.xml");
  $XMLcache=new DOMDocument();
  $XMLcache->loadXML($xmlstream);
  $rootCache=$XMLcache->documentElement;
  $games=$rootCache->childNodes;

  $userNodeFlag=false; //Serve per segnalare nella condizione 'payAll' se l'utente possiede già giochi o va creato un nuovo nodo utente



  if (isset($_POST['send'])) {

    if ($_POST['send']=='pay All') { //Al pagamento inserisco i giochi nel file UserGameTable.xml
      //Apertura file che contiene tutti gli utenti e i relativi giochi o DLC posseduti
      $xmlstream=streamChanger("cache/UserGameTable.xml");
      $XMLuserTable=new DOMDocument();
      $XMLuserTable->loadXML($xmlstream);
      $rootUserTable=$XMLuserTable->documentElement;
      $usersList=$rootUserTable->childNodes;

      //Sfoglia  tutti i cart e trova quello compilato per l'utente che sta visualizzando la pagina
      $carts=$rootCart->childNodes;
      for ($i=0; $i < $carts->length; $i++) {
        $cart=$carts->item($i);
        if($cart->getAttribute('id_user')==$_SESSION['id']) $userCart=$cart;
      }
      //Sfoglia tutti gli utenti con i relativi giochi e DlC aqcuistati in seguito aggiungi in nuovi giochi in acquisto
      for ($i=0; $i < $usersList->length; $i++) {
        $userList=$usersList->item($i);
        if ($userList->getAttribute('id_user')==$_SESSION['id']) {
          foreach ($userCart->childNodes as $game) {
            $newRecord=$XMLuserTable->createElement("id_game", $game->getAttribute('id_game'));
            $userList->appendChild($newRecord);
          }
          $userNodeFlag=true; //Nel caso non ci fosse nessun nodo utente...
        }
      }
      //... crea un nuovo nodo utente cosi da inserire i giochi in acquisto. Poi appendi il nuovo utente al root del file XMLuserTable
      if(!($userNodeFlag)){
        $newUser=$XMLuserTable->createElement("UserGames");
        $newUser->setAttribute('id_user', $_SESSION['id']);
        foreach ($userCart->childNodes as $game) {
          $newRecord=$XMLuserTable->createElement("id_game", $game->getAttribute('id_game'));
          $newUser->appendChild($newRecord);
        }
        $rootUserTable->appendChild($newUser);
      }
      //Salva tutto, rimuovi il carrello attivo e libera il buffer degli ids selezionati in precedenza
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

//Il seguente codice verifica che ci siano prodotti in un carrello già attivo in precedenza per l'utente.
//Inseguito mette questi prodotti in un array, che conserva tutte le informazioni; in seguito saranno inserite in nuovo nodo da appendere al rootCart
  $flag=0;
  $active=false;//Segnala se il nodo carrello che fa riferimento all'utente esiste o va creato (non esiste un carrello attivo per l'utente X)

//Seleziono il nodo cart che far riferimento all'utente
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

 //Se ci sono nuovi giochi da mettere nel carrello
  if(isset($_SESSION['ids_to_cart'])){
    //e se esiste già un nodo carrello per questo utente
    if($active){
      foreach ($_SESSION['ids_to_cart'] as $id) {
        $newRecord=$XMLcart->createElement("gioco");
        $newRecord->setAttribute("id_game", $id);
        //cerca i relativi giochi
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
        // e inseriscili come nuovi elementi nel nodo cart esistente.
        $newTitolo=$XMLcart->createElement("titolo", $recordBay[0]);
        $newPrezzo=$XMLcart->createElement("prezzo", $recordBay[1]);
        $newRecord->appendChild($newTitolo);
        $newRecord->appendChild($newPrezzo);
        $userCart->appendChild($newRecord);
    }
  }
    else{ //Se non esiste un nodo cart per l'utente invece
      foreach ($_SESSION['ids_to_cart'] as $id) {
        //Crea un nuovo nodo cart con attributo id_user pari all'id dell'utente in questione
        $newCart=$XMLcart->createElement("carrello");
        $newCart->setAttribute("id_user", $_SESSION['id']);
        $newRecord=$XMLcart->createElement("gioco");
        $newRecord->setAttribute("id_game", $id);
        //preleva le informazioni relative al gioco sempre nella lista giochi disponibili
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
        //compila il nuovo nodo cart e poi appendilo al rootCart
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
    $userCartDeleted=false; //Serve a capire sel il cart dell'utente é stato acncellato da una porzione di codice
    if ($_POST['send']=='delete') {
      $carts=$rootCart->childNodes;
      for ($i=0; $i < $carts->length ; $i++) {//Seleziono da capo con questo codice il cart dell'utente di riferimento
        $cart=$carts->item($i);
        if($cart->getAttribute('id_user')==$_SESSION['id']){
          $userCart=$cart;
        }
      }
      //Rimuovo i vari giochi selezionati dal cart attivo
      foreach ($_POST['delete_checked'] as $to_del) {
        foreach ($userCart->childNodes as $game) {
          if ($game->getAttribute('id_game')==$to_del) {
            $userCart->removeChild($game);
          }
        }
      }
      //se il cart é vuoto lo elimino
      if (!($userCart->hasChildNodes())) {
        $rootCart->removeChild($userCart);
        $userCartDeleted=true;
      }
    }
    $XMLcart->save("cache/carrello_attivo.xml");
    if(!($rootCart->hasChildNodes()) || $userCartDeleted) $flag=0; //Setta flag a 0 nel momento in cui non esiste più un cart attivo che fa riferimento all'utente
    }
      //!($rootCart->hasChildNodes()) serve a verificare se non ci siano carrelli in assoluto

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
    <link rel="stylesheet" href="Init_Struct_.css" media="screen">
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
          /*Stampo per ogni nodo gioco nel cart, il nome e il prezzo. Aggiungendo una casella di check, per la selezione e l'eventuale eliminazione del gioco dal carrello*/
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
