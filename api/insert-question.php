<?php
require('../config.php');

$method = strtolower($_SERVER['REQUEST_METHOD']);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
  if($method !== 'post') {
    throw new Exception('Método não permitido');
  }

  $json = file_get_contents('php://input');
  $data = json_decode($json, true);

  $question = isset($data['question']) ? $data['question'] : null;
  $questionLevel = isset($data['level']) ? $data['level'] : null;
  $answers = isset($data['answers']) ? $data['answers'] : null;

  if(empty($question) || empty($questionLevel)) {
    $array['error'] = 'Preencha a pergunta e adicione o nível de dificuldade';
    require('../return.php');
    exit;
  }


  //Initial Validation of responses
  if(count($answers) !== 5) {
    $array['error'] = 'É necessário ter 05 respostas';
    require('../return.php');
    exit;
  }

  $answerTrue = 0;
  $answersFalse = 0;
  foreach ($answers as $answer) {
    if($answer['is_correct'] == true) {
      $answerTrue++;
    } else {
      $answersFalse++;
    }
  }
  if($answersFalse !== 4 || $answerTrue !== 1) {
    $array['error'] = 'Deve haver uma resposta VERDADEIRA e quatro FALSAS';
    require('../return.php');
    exit;
  }
  //Finish Validation of responses


  //Insert Question
  $sql = $pdo->prepare("INSERT INTO questions (question_text, level_id) VALUES (:question, :levelId)");
  $sql->bindValue(":question", $question);
  $sql->bindValue(":levelId", $questionLevel);
  $sql->execute();
  $questionLastId = $pdo->lastInsertId();


  //Insert Answers
  foreach($answers as $item) {
    $sql = $pdo->prepare("INSERT INTO answers (answer, question_id, is_correct) VALUES (:answer, :questionId, :isCorrect)");
    $sql->bindValue(":answer", $item['answer']);
    $sql->bindValue(":questionId", $questionLastId, PDO::PARAM_INT);
    $sql->bindValue(":isCorrect", $item['is_correct'] , PDO::PARAM_BOOL);
    $sql->execute();
  }
  
  $array['result'] = [
    'id' => $questionLastId,
    'question' => $question,
    'answers' => []
  ];

  foreach ($answers as $item) {
    $array['result']['answers'][] = [
      'answer' => $item['answer'],
      'isCorrect' => $item['is_correct']
    ];
  }

  require('../return.php');

} catch (Exception $e) {
  $array['error'] = $e->getMessage();
}

require('../return.php');
?>