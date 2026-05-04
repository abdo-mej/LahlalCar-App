<?php
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function post($k,$d=null){ return $_POST[$k] ?? $d; }
function getv($k,$d=null){ return $_GET[$k] ?? $d; }
function days_between($a,$b){ $d1=new DateTime($a); $d2=new DateTime($b); return max(1,(int)$d1->diff($d2)->days + 1); }
function money($v){ return number_format((float)$v,2,',',' ').' DH'; }
function flash($msg=null,$type='success'){ if(session_status()===PHP_SESSION_NONE) session_start(); if($msg){$_SESSION['flash']=['msg'=>$msg,'type'=>$type];return;} if(isset($_SESSION['flash'])){$f=$_SESSION['flash'];unset($_SESSION['flash']); echo '<div class="toast '.$f['type'].'">'.e($f['msg']).'</div>';}}
function redirect($url){ header('Location: '.$url); exit; }
function ref_reservation(){ return 'LC-'.date('Ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)),0,6)); }
