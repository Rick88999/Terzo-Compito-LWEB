<?php
$error='';

$db_name='HillDownGameStore_db';
$users_table='user_table';
$info_user_table='info_user_table';

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
