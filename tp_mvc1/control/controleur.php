<?php
class controleur {
	
	public function accueil() {
		$lesCategories = (new categorie)->getAll();
		(new vue)->accueil($lesCategories);
	}
	
	public function erreur404() {
		$lesCategories = (new categorie)->getAll();
		(new vue)->erreur404($lesCategories);
	}

	public function connexion() {
		if(isset($_POST["ok"])) {
			$lesClients = (new client)->connexion($_POST['email'], $_POST['motdepasse']);
			$lesCategories = (new categorie)->getAll();
			if($lesClients == true){
				(new vue)->accueil($lesCategories);
			}
			else{
				(new vue)->connexion($lesCategories);
			}
		}
		else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->connexion($lesCategories);
		}
	}

	public function inscription() {
		if(isset($_POST["ok"])) {
			if ($_POST['motdepasse'] == $_POST['motdepasse2'])
			{
				(new client)->inscriptionClient($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['motdepasse'], $_POST['adresse'], $_POST['cp'], $_POST['ville'], $_POST['tel']);
				$lesCategories = (new categorie)->getAll();
				(new vue)->connexion($lesCategories);
			}
		} else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->inscription($lesCategories);
		}
	}

	public function produit() {
		if(isset($_GET["id"])) {
			$lesCategories = (new categorie)->getAll();
			$infosArticle = (new produit)->getInfosProduit($_GET["id"]);

			if(count($infosArticle) > 0) {
				$message = null;

				// Action du bouton ajouter au panier sur la page du produit
				if(isset($_POST["ajoutPanier"]) && isset($_POST["quantite"])) {
					if((new produit)->estDispoEnStock($_POST["quantite"], $_GET["id"])) {
						if(!(isset($_SESSION["panier"]))) {
							$_SESSION["panier"] = array();
						}
						for($i = 0; $i < $_POST["quantite"]; $i++) {
							array_push($_SESSION["panier"], $_GET["id"]);
						}

						$message = "Ajout au panier réussi!";
					}
					else {
						$message = "Des problèmes ont été rencontrer";
					}
				}

				(new vue)->produit($lesCategories, $infosArticle, $message);
			}
			else {
				(new vue)->erreur404($lesCategories);
			}
		}
		else {
			$lesCategories = (new categorie)->getAll();
			(new vue)->erreur404($lesCategories);
		}
	}

	public function panier() {
		$lesCategories = (new categorie)->getAll();
		$lesArticles = array(); // Toutes les infos des produits du panier seront dans cette variable
		if (isset($_SESSION["panier"]))
		{	
			$Quantites = array_count_values($_SESSION["panier"]);
		
			foreach ($Quantites as $key => $value) 
			{
				$unArticle = (new produit)->getInfosProduit($key);
				$unArticle["quantite"] = $value;
				array_push($lesArticles, $unArticle);
			}
		}

		(new vue)->panier($lesCategories, $lesArticles, null);
	}

	public function commander() {
		if(isset($_POST["supprimer"])) {
			$_SESSION['panier'] = array_values($_SESSION['panier']);
			unset($_SESSION['panier'][array_search($_POST['supprimer'], $_SESSION['panier'])]);
			$this->panier();
		}

		if(isset($_POST["valider"])) {
			// Validation du panier

			/*
				On doit vérifier si l'utilisateur est connecté, si ce n'est pas le cas alors il faut l'inviter à se connecter.
				Si l'utilisateur est connecté alors il faut vérifier que la quantité commandée de chaque produit du panier soit disponbile en stock.
				Si tout est ok alors on créé sa commande dans la base et l'utilisateur doit être averti que sa commande est validée et le panier doit être vidé
				Sinon il faut revenir à la page du panier et avertir l'utilisateur quel produit (préciser sa désignation) pose problème.
			*/
			$lesCategories = (new categorie)->getAll();
			if (isset($_SESSION["connexion"]))
			{
				
				$Quantites = array_count_values($_SESSION["panier"]);

				if ((new produit)->estDispoEnStock($unArticle["quantite"], $_SESSION['panier']))
				{
					$lesArticles = array();
					foreach($lesQuantites as $key => $value)
					{
						$article = (new produit)->getInfosProduit($key);
						$article['quantite'] = $value;
						$lesArticles[] = $article;
					}
					unset($_SESSION["panier"]);
					$lesArticles = (new produit)->getInfosProduit($_SESSION['panier'])
					(new commande)->validerCommande($_SESSION['connexion'], $lesArticles, $unArticle["quantite"]);
					(new vue)->commandeValidee($lesCategories);
				}
				else
				{
					$this->panier();
				}
			}
			else
			{
				(new vue)->connexion($lesCategories);
			}
		}
	}

	public function categorie() {
		$lesCategories = (new categorie)->getAll();

		$categorie = $_GET['id'];
		$lesArticles = (new categorie)->getProduits($categorie);
		$nomCategorie = (new categorie)->getNomCategorie($categorie);

		(new vue)->categorie($lesCategories, $lesArticles, $nomCategorie);
	}

	public function deconnexion() {
		if(isset($_SESSION["connexion"])) {
			unset($_SESSION["connexion"]);
		}

		$this->accueil();
	}
}