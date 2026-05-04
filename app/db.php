<?php
function config($key){ static $c=null; if(!$c){$c=require __DIR__.'/config.php';} return $c[$key] ?? null; }
function db_server_pdo(){
  $dsn='mysql:host='.config('db_host').';port='.config('db_port').';charset=utf8mb4';
  return new PDO($dsn, config('db_user'), config('db_pass'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
}
function pdo(){
  static $pdo=null; if($pdo) return $pdo;
  try{
    $dsn='mysql:host='.config('db_host').';port='.config('db_port').';dbname='.config('db_name').';charset=utf8mb4';
    $pdo=new PDO($dsn, config('db_user'), config('db_pass'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    return $pdo;
  } catch(PDOException $e){
    if(str_contains($e->getMessage(),'Unknown database')){ header('Location: /install.php'); exit; }
    throw $e;
  }
}
function migrate(){
  $server=db_server_pdo();
  $db=config('db_name');
  $server->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  $pdo=pdo();
  $pdo->exec("CREATE TABLE IF NOT EXISTS users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(120) NOT NULL,
    identifiant VARCHAR(80) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin','gerant','agent','comptable','mecanicien') NOT NULL DEFAULT 'agent',
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS clients(
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_client ENUM('Marocain','Etranger','Entreprise') DEFAULT 'Marocain',
    nom VARCHAR(120) NOT NULL,
    prenom VARCHAR(120) DEFAULT '',
    telephone VARCHAR(40), email VARCHAR(120), nationalite VARCHAR(80),
    cin VARCHAR(80), passport VARCHAR(80), date_expiration_cin DATE NULL,
    numero_permis VARCHAR(80), date_delivrance_permis DATE NULL, delivre_a_permis VARCHAR(120),
    date_naissance DATE NULL, lieu_naissance VARCHAR(120), adresse TEXT, adresse_etrangere TEXT,
    notes TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS voitures(
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(50) UNIQUE NOT NULL,
    marque VARCHAR(80) NOT NULL, modele VARCHAR(80) NOT NULL, type_vehicule VARCHAR(80),
    carburant VARCHAR(50), puissance_fiscale VARCHAR(50), consommation VARCHAR(50), rejet_co2 VARCHAR(50),
    date_immatriculation DATE NULL, date_acquisition DATE NULL,
    kilometrage_actuel INT DEFAULT 0, cout_jour DECIMAL(10,2) DEFAULT 0,
    statut ENUM('Disponible','Louée','Maintenance','Vendue') DEFAULT 'Disponible',
    date_dernier_controle DATE NULL, date_prochain_controle DATE NULL,
    cadence_vidange INT DEFAULT 10000, km_derniere_vidange INT DEFAULT 0,
    cadence_courroie INT DEFAULT 80000, km_derniere_courroie INT DEFAULT 0,
    assurance_expire_le DATE NULL, remarques TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS reservations(
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) UNIQUE,
    client_id INT NOT NULL,
    voiture_id INT NOT NULL,
    date_debut DATE NOT NULL, date_fin DATE NOT NULL,
    heure_depart VARCHAR(20), heure_retour VARCHAR(20), lieu_depart VARCHAR(160), lieu_retour VARCHAR(160),
    prix_jour DECIMAL(10,2) NOT NULL DEFAULT 0, remise DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0, avance DECIMAL(10,2) DEFAULT 0, reste DECIMAL(10,2) DEFAULT 0,
    statut ENUM('Réservée','En cours','Terminée','Annulée') DEFAULT 'Réservée',
    avec_conducteur2 TINYINT(1) DEFAULT 0,
    conducteur2_nom VARCHAR(120), conducteur2_prenom VARCHAR(120), conducteur2_nationalite VARCHAR(80), conducteur2_cin VARCHAR(80), conducteur2_passport VARCHAR(80), conducteur2_permis VARCHAR(80), conducteur2_telephone VARCHAR(40), conducteur2_adresse TEXT,
    conditions TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(client_id) REFERENCES clients(id) ON DELETE RESTRICT,
    FOREIGN KEY(voiture_id) REFERENCES voitures(id) ON DELETE RESTRICT
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS paiements(
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT NOT NULL, montant DECIMAL(10,2) NOT NULL, methode VARCHAR(60), date_paiement DATE NOT NULL, note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(reservation_id) REFERENCES reservations(id) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS documents(
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_objet VARCHAR(50), objet_id INT, titre VARCHAR(160), chemin VARCHAR(255), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS historique(
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, action VARCHAR(120), details TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  seed();
}
function seed(){
  $pdo=pdo();
  if((int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()===0){
    $stmt=$pdo->prepare('INSERT INTO users(nom,identifiant,mot_de_passe,role) VALUES (?,?,?,?)');
    foreach([
      ['Administrateur','admin',password_hash('admin123',PASSWORD_DEFAULT),'admin'],
      ['Gérant','gerant',password_hash('gerant123',PASSWORD_DEFAULT),'gerant'],
      ['Agent accueil','agent',password_hash('agent123',PASSWORD_DEFAULT),'agent'],
      ['Comptable','comptable',password_hash('comptable123',PASSWORD_DEFAULT),'comptable'],
      ['Mécanicien','mecanicien',password_hash('mecanicien123',PASSWORD_DEFAULT),'mecanicien'],
    ] as $u) $stmt->execute($u);
  }
  if((int)$pdo->query('SELECT COUNT(*) FROM voitures')->fetchColumn()===0){
    $stmt=$pdo->prepare('INSERT INTO voitures(immatriculation,marque,modele,type_vehicule,carburant,kilometrage_actuel,cout_jour,statut,date_prochain_controle,assurance_expire_le) VALUES (?,?,?,?,?,?,?,?,?,?)');
    foreach([
      ['45211-A-48','Dacia','Logan','Berline','Diesel',82000,250,'Disponible','2026-08-12','2026-10-01'],
      ['63722-B-48','Renault','Clio 5','Compacte','Essence',41000,320,'Disponible','2026-07-15','2026-09-20'],
      ['90811-D-48','Hyundai','Tucson','SUV','Diesel',62000,600,'Maintenance','2026-06-01','2026-12-20'],
    ] as $v) $stmt->execute($v);
  }
  if((int)$pdo->query('SELECT COUNT(*) FROM clients')->fetchColumn()===0){
    $stmt=$pdo->prepare('INSERT INTO clients(nom,prenom,telephone,nationalite,cin,numero_permis,adresse) VALUES (?,?,?,?,?,?,?)');
    $stmt->execute(['Benali','Youssef','0611223344','Marocaine','AB123456','P987654','Oujda, Maroc']);
    $stmt->execute(['Martin','Lucas','+33611223344','Française',null,'FR123456','Paris, France']);
  }
}
