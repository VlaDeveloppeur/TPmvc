<?php

class commande {
	
	// Objet PDO servant à la connexion à la base
	private $pdo;

	// Connexion à la base de données
	public function __construct() {
		$config = parse_ini_file("config.ini");
		
		try {
			$this->pdo = new \PDO("mysql:host=".$config["host"].";dbname=".$config["database"].";charset=utf8", $config["user"], $config["password"]);
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
	
	// Récupérer toutes les commandes d'un client passé en paramètre
	public function getCommandesClient($client) {
		$sql = "SELECT * FROM commande WHERE idClient = :id";
		
		$req = $this->pdo->prepare($sql);
		$req->bindParam(':id', $client, PDO::PARAM_INT);
		$req->execute();
		
		return $req->fetchAll();
	}
	
	// Permet de créer la commande du client passé en paramètre avec l'ensemble des articles qu'il a commandé en paramètre
	public function validerCommande($client, $lesArticles, $quantite) {
		$sql = "INSERT INTO commande VALUES ('".date("d/m/Y")."', ".$client['idClient'].";";

		$ins = $this->pdo->prepare($sql);
		$ins->execute();

		$sel = "SELECT MAX(numeroCommande) FROM commande";

		$recup = $this->pdo->prepare($sel);
		$recup->execute();
		$recup->fetchAll();

		$sql2 = "INSERT INTO commander VALUES (".$recup[0].", ".$lesArticles['codeProduit'].", ".$quantite.");";

		$insérer = $this->pdo->prepare($sql2);
		$insérer = execute();
	}
}