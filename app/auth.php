<?php
if(session_status()===PHP_SESSION_NONE) session_start();
function user(){ return $_SESSION['user'] ?? null; }
function require_login(){ if(!user()){ header('Location: /login.php'); exit; } }
function can($perm){
  $u=user(); if(!$u) return false; $r=$u['role'];
  $map=[
    'admin'=>['*'],
    'gerant'=>['dashboard','clients','voitures','reservations','contrats','paiements','rapports','historique'],
    'agent'=>['dashboard','clients','voitures_view','reservations','contrats'],
    'comptable'=>['dashboard','clients_view','reservations_view','paiements','rapports'],
    'mecanicien'=>['dashboard','voitures','maintenance'],
  ];
  return in_array('*',$map[$r]??[]) || in_array($perm,$map[$r]??[]);
}
function require_perm($perm){ if(!can($perm)){ http_response_code(403); include __DIR__.'/../public/403.php'; exit; } }
function log_action($action,$details=''){
  try{ $pdo=pdo(); $uid=user()['id']??null; $stmt=$pdo->prepare('INSERT INTO historique(user_id,action,details) VALUES (?,?,?)'); $stmt->execute([$uid,$action,$details]); }catch(Exception $e){}
}
