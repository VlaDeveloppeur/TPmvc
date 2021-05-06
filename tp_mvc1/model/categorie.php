<?php

class categorie {
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
	
	// Récupérer toutes les catégories
	public function getAll() {
		$sql = "SELECT * FROM categorie";
		
		$req = $this->pdo->prepare($sql);
		$req->execute();
		
		return $req->fetchAll();
	}
	
	// Récupérer la liste des produits de la catégorie passée en paramètre
	public function getProduits($categorie) {
		$liste = "SELECT * FROM produit WHERE produit.idCategorie= :categorie;";

		$requ = $this->pdo->prepare($liste);
		$requ->bindParam(':categorie', $categorie, PDO::PARAM_STR);
		$requ->execute();

		return $requ->fetchAll();
	}

	// Récupérer le nom de la catégorie passé en paramètre
	public function getNomCategorie($categorie) {
		$liste = "SELECT nomCategorie FROM categorie WHERE idCategorie = :categorie;";

		$requ  = $this->pdo->prepare($liste);
		$requ->bindParam(':categorie', $categorie, PDO::PARAM_STR);
		$requ->execute();

		return $requ->fetch();
	}
}