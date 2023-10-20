<?php
$error='';

$db_name='HillDownGameStore_db';
$users_table='user_table';
$info_user_table='info_user_table';
$add_on_table='games_table';                           //Nomi dele varie tabelle, il profilo che ho utilizzato possiede tutti i privilegi e, come chiamato sugli esempi, prende il nome:'archer' password:'archer'
$users_game_list='users_game_list';
$active_cart_table='active_cart';

$sqlConnect=new mysqli('localhost', 'archer', 'archer');
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}

$db_creation="CREATE DATABASE $db_name";

if(!($res=mysqli_query($sqlConnect, $db_creation))){
  $error.='ERROR1: DATABASE CREATION FAILED!';
  echo $error;
}


$sqlConnect->close();

$sqlConnect=new mysqli('localhost', 'archer', 'archer', $db_name);
if (mysqli_connect_errno()) {
    printf("Errore di connessione: %s\n", mysqli_connect_error());
    exit();
}
//Tabella le cui tuple sono i vari account sulla piattaforma
$query="CREATE TABLE IF not exists $users_table(
  id INT NOT NULL auto_increment,
  email VARCHAR(40),
  nickname VARCHAR(40),
  password VARCHAR(60),
  PRIMARY KEY(id, email),
  UNIQUE(id, email)
)";

if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR2: USER TABLE CREATION ERROR!";
}
/*Nel mio progetto ho deciso di separare la tabella degli utenti da quelle delle info relativa ad essi. Le indormazioni type_game_played, game_played e rank_lv
le immagino come informazioni attue ad una continua ricerca di merca su ogni utente che si iscrive. Questo sarà utile all'azienda per integrare o sviluppare nuovi giochi
secondo le preferenze del nuovo pubblico*/
$query="CREATE TABLE IF not exists $info_user_table(
  id INT NOT NULL,
  citta VARCHAR(40),
  via VARCHAR(40),
  cap VARCHAR(10),
  type_game_played VARCHAR(100),
  game_played VARCHAR(100),
  rank_lv VARCHAR(20),
  PRIMARY KEY(id),
  FOREIGN KEY(id) REFERENCES user_table(id)
)";

if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR2: INFO USER TABLE CREATION ERROR!";
}
//La tabella add_on_table tiene traccia nelle tupl di ogni gioco disponibile sulla piattaforma
$query="CREATE TABLE IF not exists $add_on_table(
  id_prodotto INT NOT NULL auto_increment PRIMARY KEY,
  titolo VARCHAR(50),
  img VARCHAR(20),
  prezzo FLOAT,
  versione FLOAT,
  descrizione VARCHAR(400)
)";

if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR3: GAMES TABLE CREATION ERROR!";
}
//Questa tabella (users_game_table) tiene traccia al suo interno di tutti i gichi possediti da i vari account
$query="CREATE TABLE IF not exists $users_game_list(
  id_user INT NOT NULL,
  id_game INT NOT NULL,
  PRIMARY KEY(id_user, id_game),
  FOREIGN KEY(id_user) REFERENCES user_table(id),
  FOREIGN KEY(id_game) REFERENCES games_table(id_prodotto)
)";

if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR4: GAME LIST USER TABLE CREATION ERROR!";
}
//actve_cart è la tabella che tiene traccia di ogni elemento presente nel carrello di un determinato utente. I carrelli sono sempre attivi, e per non usare coockie che ne tengono traccia ho usato un apposita tabella
$query="CREATE TABLE IF not exists $active_cart_table(
  id_user INT NOT NULL,
  id_prodotto INT NOT NULL,
  titolo VARCHAR(40),
  prezzo FLOAT,
  PRIMARY KEY(id_user, id_prodotto)
)";

if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR5: ACTIVE CART TABLE CREATION ERROR!";
}

$query="INSERT INTO $users_table (email, nickname, password) VALUES (\"george@hotmail.com\", \"Giorgio\", \"sasso\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR6: INSERT OLD USERS ERROR!";
}

$query="INSERT INTO $users_table (email, nickname, password) VALUES (\"perro@gmail.com\", \"Pedro\", \"sasso1\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR6: INSERT OLD USERS ERROR!";
}

$query="INSERT INTO $users_table (email, nickname, password) VALUES (\"superBanzinga99@gmail.com\", \"Alex\", \"jojo1\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR6: INSERT OLD USERS ERROR!";
}

$query="INSERT INTO $info_user_table (id, citta, via, cap, type_game_played, game_played, rank_lv) VALUES (\"1\", \"Palermo\", \"via sassari, 5\", \"90131\", \"Arena:FPS:\", \"Overwatch:Terraria:Call of Duty:\", \"gold\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT OLD USERS INFO ERROR!";
}

$query="INSERT INTO $info_user_table (id, citta, via, cap, type_game_played, game_played, rank_lv) VALUES (\"2\", \"Roma\", \"via lepanto, 12\", \"00042\", \"Arena:FPS:\", \"Overwatch:\", \"diamond\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT OLD USERS INFO ERROR!";
}

$query="INSERT INTO $info_user_table (id, citta, via, cap, type_game_played, game_played, rank_lv) VALUES (\"3\", \"Aprilia\", \"via carroceto, 1\", \"04012\", \"Sandbox:FPS:Arena:\", \"Overwatch:Terraria:\", \"silver\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT OLD USERS INFO ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"Gun Bun\", \"bun.png\", \"19.99\", \"1.2554\", \"Bun è un piccolo coniglio nella terra di Fauna, aiutalo a salvare i suoi amici. Questo platform game con elementi GDR, è stato creato dai creatori di Terraria\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"Fire jet Storm 2\", \"jet.png\", \"49.99\", \"2.0\", \"\Vola libero su ne cielo: Fire jet torna con il secondo capilo! Ispirato a 1945 I&II, il secondo capilo della saga offre una nuova modalità multiplayer\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"Sunlight3: the moon\", \"sun.png\",\"34.99\", \"3.6\", \"Sunlight giunge al suo ultimo capitolo. L'avventura di John Starminer giunge alla conclusione in questo ultimo emozionante capitolo ambientato sulla luna. Valutato da IGN come: 'Il miglior open world del 2023'\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}
/*I DLC differiscono da i giochi di riferimento, la mancanza di una bio e di una immagine di riferimento li rendono degli effettivi sottotipi che verrano gestti opportunamente con l'instruzione 'LIKE' nelle query*/
$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"DLC(Sunlight3: the moon):Saturn day nights dreams\", \"none\",\"14.99\", \"0.1\", \"none\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"DLC(Gun Bun):Racoon\", \"none\",\"4.99\", \"0.1\", \"none\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"DLC(Gun Bun):Sparrows\", \"none\",\"4.99\", \"0.1\", \"none\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $add_on_table (titolo, img, prezzo, versione, descrizione) VALUES (\"DLC(Fire jet Storm 2):Delta's protocol\", \"none\",\"19.99\", \"0.1\", \"none\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}

$query="INSERT INTO $users_game_list (id_user, id_game) VALUES (\"1\", \"1\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}
$query="INSERT INTO $users_game_list (id_user, id_game) VALUES (\"1\", \"5\"); ";
if(!($res=mysqli_query($sqlConnect, $query))){
  $error.="ERROR7: INSERT GAME ERROR!";
}


 ?>

 <?xml version="1.0" encoding="UTF-8"?>
 <!DOCTYPE html
 PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>INSTALL...</title>
  </head>
  <body>
    <?php if(($error=='')){echo '<h1>'.'Tutto installato correttamente'.'</h1>';} else {
      echo '<h1>'.$error.'</h1>';
    }
     ?>

  </body>
</html>
