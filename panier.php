<?php
require __DIR__ . '/include/init.php';
/*
 * - si le panier est vide: afficher un message
 * - sinon afficher un tableau HTML avec pour chaque produit un panier:
 *   _ nom du produit
 *   _ prix unitaire
 *   _ quantite 
 *   _ prix total pour le produit
 * 
 * - Faire une fonction qui calcule le montant total du panier et l'utiliser
 * sous le tableau pour afficher le total
 * 
 * - Remplacer l'affichage de la quantité par un formulaire avec:
 *   _ un <input type ="number"> pour avoir la quantité
 *   _ un <input type ="hidden"> pour avoir l'id du produit
 * dont on va modifier la quantité
 *   _ un bouton submit
 *   _ Faire une fonction modifierquantitePanier($produitId, $quantite )
 * Qui met à jour la quantité pour le produit si la quantité n'est pas 0, et qui 
 * le supprime du panier sinon
 * Applet cette fonction quand un des formulaire est envoyé
 */

//dump($_SESSION['panier']);
//if (!empty($_SESSION['panier']))

require __DIR__ . '/layout/top.php';
if (isset($_POST['commander'])) {
    $query = <<<SQL
INSERT INTO commande(
       utilisateur_id,
       montant_total
    ) VALUES (
       :utilisateur_id,
       :montant_total
    )
SQL;

 $stmt = $pdo->prepare($query);
 $stmt->execute([
            ':utilisateur_id' => $_SESSION['utilisateur']['id'],
            ':montant_total' => totalPanier()
        ]);
// recupèration de l'id de la commande que l'on vient d'inserer
 $commandeID = $pdo->lastInsertID();
 
 $query = <<<SQL
INSERT INTO detail_commande(
         commande_id,
         produit_id,
         prix,
         quantite
) VALUES (
         :commande_id,
         :produit_id,
         :prix,
         :quantite
)
SQL;
 
    $stmt = $pdo->prepare($query);
    
    foreach ($_SESSION['panier'] as $produitID => $produit){
        $stmt->execute([
         ':commande_id' => $commandeID,
         ':produit_id' => $produitID,
         ':prix' => $produit['prix'],
         ':quantite' => $produit['quantite']
        ]);
    }
    
    // on vide le panier
    $_SESSION['panier'] = [];
    setFlashMessage('La commande est enregistrée');
    
}
if (isset($_POST['modifierQuantitePanier']) ) {
    dump($_POST);
    modifierQuantitePanier($_POST['produitId'], $_POST['quantite']);
}

if(empty($_SESSION['panier'])) :
    setFlashMessage('Votre panier est vide');

?>

<h1>Panier</h1>
<?php
    else : 
        ?>
<table class="table table-dark">
    <thead>
        <tr> 
            <th>Nom : </th>
            <th>Prix unitaire : </th>
            <th>Quantité : </form></th>
            <th>Prix total: </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($_SESSION['panier'] as $produitId => $panier):             
        
        ?>
            <tr>
                <td><?= $panier['nom']?></td>
                <td><?= prixFR($panier['prix'])?></td>
                <td>
                    <form method="post" class="form-inline">
                        <input type ="number" name="quantite" style="margin-left: 20px; margin-right: 20px;" min="0" value="<?= $panier['quantite'];?>">  
                        <input type ="hidden" name="produitId" value="<?= $produitId; ?>">
                        <button type="submit" class="btn btn-primary text-center" name="modifierQuantitePanier">
                            Modifier la quantité
                        </button>
                    </form>
                    </td>
                <td><?= prixFR($prixTotal = $panier['prix'] * $panier['quantite']);?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <th colspan="3">TOTAL </th>
                <td><?= prixFR(totalPanier()); ?></td>
            </tr>
    </tbody>
    
</table>

<?php

    if(!isUserConnected()) :
        ?>
    <div class="alert alert-info">
        Vous devez vous conneter ou vous inscrire pour valider la commande
    </div>
    <?php 
        else:
    ?>
        <form method="POST">
            <p class="text-right">
                <button type="submit" name="commander" class="btn btn-primary">
                    Valider la commande
                </button>
        </form>
    <?php
    endif;

endif; 
?>