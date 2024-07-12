<?php

  $dbHost = 'postgres';
  $dbName = 'game_quiz';
  $dbUser = 'root';
  $dbPassword = '12345';

  try {
    $pdo = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $array = [
      'error' => null,
      'result' => []
    ];
  } catch (PDOException $e) {
    $errorMessage = "Erro ao conectar com o banco de dados ".$e->getMessage();
    $array = [
      'error' => $errorMessage,
      'result' => null
    ];
  }