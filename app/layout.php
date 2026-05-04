<?php require_once __DIR__.'/helpers.php'; require_once __DIR__.'/auth.php';
function render_header($title='Tableau de bord') { $u=user(); ?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> - LahlalCar</title><link rel="stylesheet" href="/assets/style.css"></head>
<body><div class="app-shell">
<aside class="sidebar"><div class="brand"><div class="brand-icon">LC</div><div><strong>LahlalCar</strong><span>Gestion location</span></div></div>
<nav>
<a href="/index.php" class="nav-link">📊 Tableau de bord</a>
<?php if(can('clients')||can('clients_view')): ?><a href="/clients.php" class="nav-link">👤 Clients</a><?php endif; ?>
<?php if(can('voitures')||can('voitures_view')): ?><a href="/voitures.php" class="nav-link">🚗 Véhicules</a><?php endif; ?>
<?php if(can('reservations')||can('reservations_view')): ?><a href="/reservations.php" class="nav-link">📅 Réservations</a><?php endif; ?>
<?php if(can('contrats')): ?><a href="/contrats.php" class="nav-link">📄 Contrats</a><?php endif; ?>
<?php if(can('paiements')): ?><a href="/paiements.php" class="nav-link">💳 Paiements</a><?php endif; ?>
<?php if(can('maintenance')): ?><a href="/maintenance.php" class="nav-link">🛠 Maintenance</a><?php endif; ?>
<?php if(can('rapports')): ?><a href="/rapports.php" class="nav-link">📈 Rapports</a><?php endif; ?>
<?php if(can('*')): ?><a href="/users.php" class="nav-link">🔐 Utilisateurs</a><?php endif; ?>
<a href="/logout.php" class="nav-link logout">🚪 Déconnexion</a>
</nav></aside>
<main class="main"><header class="topbar"><div><h1><?=e($title)?></h1><p>Application française de gestion de location de voitures</p></div><div class="user-card"><span><?=e($u['nom']??'')?></span><small><?=e($u['role']??'')?></small></div></header><?php flash(); ?>
<?php }
function render_footer(){ ?></main></div><script src="/assets/app.js"></script></body></html><?php }
function card($title,$value,$sub=''){ echo '<div class="stat-card"><span>'.e($title).'</span><strong>'.e($value).'</strong><small>'.e($sub).'</small></div>'; }
