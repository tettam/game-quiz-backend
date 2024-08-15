<?php
require('../config.php');

try {
  $method = strtolower($_SERVER['REQUEST_METHOD']);
  if($method !== 'get') {
    throw new Exception('Método não permitido');
  } 
  $level = filter_input(INPUT_GET, 'nivel');
  if(!$level) {
    throw new Exception('Nível de desafio não especificado!');
  }

  $difficultyMap = [
    'facil' => 1,
    'medio' => 2,
    'dificil' => 3
  ];
  if(!isset($difficultyMap[$level])) {
    throw new Exception(('Nível de desafio não encontrado!'));
  }


  $id = $difficultyMap[$level];
  $sql = $pdo->prepare("SELECT * FROM questions WHERE level_id = :id ORDER BY RANDOM() LIMIT 1");
  $sql->bindValue(":id", $id, pdo::PARAM_INT);
  $sql->execute();
  if($sql->rowCount() === 0) {
    throw new Exception('Nenhuma desafio encontrado para esse nível');
  }

  $dataQuestion = $sql->fetch(PDO::FETCH_ASSOC);
  $array['result'] = [
    'question' => [
      'id' => $dataQuestion['id'],
      'question' => $dataQuestion['question_text'],
      'answers' => []
    ]
  ];

  //Buscando as respostas
  $sql = $pdo->prepare("SELECT * FROM answers WHERE question_id = :questionId");
  $sql->bindValue(':questionId', $dataQuestion['id']);
  $sql->execute();
  if($sql->rowCount() === 0) {
    throw new Exception('Erro inesperado. Nenhuma resposta encontrada!');
  }

  $dataOptions = $sql->fetchAll(PDO::FETCH_ASSOC);
  foreach ($dataOptions as $item) {
    $array['result']['question']['answers'][] = [
        'answer' => $item['answer'],
        'isCorrect' => $item['is_correct']
    ];
  }

} catch (Exception $e) {
  $array['error'] = $e->getMessage();
}
   

require('../return.php');