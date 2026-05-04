<?php require_once __DIR__.'/../app/db.php'; require_once __DIR__.'/../app/layout.php'; require_login(); require_perm('dashboard'); $pdo=pdo();
$stats=[
 'clients'=>$pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
 'voitures'=>$pdo->query('SELECT COUNT(*) FROM voitures')->fetchColumn(),
 'dispo'=>$pdo->query("SELECT COUNT(*) FROM voitures WHERE statut='Disponible'")->fetchColumn(),
 'res'=>$pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn(),
 'revenu'=>$pdo->query('SELECT COALESCE(SUM(montant),0) FROM paiements')->fetchColumn(),
];
$alerts=$pdo->query("SELECT * FROM voitures WHERE date_prochain_controle <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR assurance_expire_le <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) OR (kilometrage_actuel-km_derniere_vidange)>=cadence_vidange ORDER BY date_prochain_controle ASC LIMIT 8")->fetchAll();
render_header('Tableau de bord'); ?>
<section class="stats-grid"><?php card('Clients',$stats['clients'],'dossiers enregistrés'); card('Véhicules',$stats['voitures'],'parc total'); card('Disponibles',$stats['dispo'],'prêts à louer'); card('Réservations',$stats['res'],'historique'); card('Revenus',money($stats['revenu']),'paiements encaissés'); ?></section>
<section class="grid two"><div class="panel"><div class="panel-head"><h2>Actions rapides</h2></div><div class="quick-actions"><?php if(can('clients')):?><a class="quick" href="/clients.php?action=new">+ Nouveau client</a><?php endif;?><?php if(can('voitures')):?><a class="quick" href="/voitures.php?action=new">+ Nouveau véhicule</a><?php endif;?><?php if(can('reservations')):?><a class="quick" href="/reservations.php?action=new">+ Nouvelle réservation</a><?php endif;?><?php if(can('contrats')):?><a class="quick" href="/contrats.php">Contrats imprimables</a><?php endif;?></div></div>
<div class="panel"><div class="panel-head"><h2>Alertes importantes</h2><a href="/maintenance.php">Voir tout</a></div><?php if(!$alerts): ?><p class="muted">Aucune alerte urgente.</p><?php else: ?><div class="list"><?php foreach($alerts as $v): ?><div class="list-item"><b><?=e($v['marque'].' '.$v['modele'])?></b><span><?=e($v['immatriculation'])?> — contrôle: <?=e($v['date_prochain_controle'])?> — assurance: <?=e($v['assurance_expire_le'])?></span></div><?php endforeach;?></div><?php endif;?></div></section>
<?php render_footer(); ?>
