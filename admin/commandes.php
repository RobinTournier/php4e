<?php

/*
 * Lister les commandes dans un tableau HTML:
 *   _ ID de la commande
 *   _ Nom et prénom de l'utilisateur qui à passé la commande
 *   _ montant formtaté
 *   _ date de la commande
 *   _ statut
 *   _ date du statut
 * 
 * Passer le statut en liste déroulante avec un bouton modifier
 * pour changer le statut de la commande en bdd (nécessite un champ caché pour 
 * l'id de la commande)
 * 
 * 
 * 
 */

require __DIR__ . '/../include/init.php';
adminSecurity();
require __DIR__ . '/../layout/top.php';

if(  isset($_POST['modifierStatut'])) {
    $query = <<<SQL
UPDATE commande SET 
    statut = :statut,
    date_statut = now()
WHERE id = :id
SQL;
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':statut' => $_POST['statut'],
        ':id' => $_POST['commandeId']  
    ]);
}

$stmt = $pdo->query(
        'SELECT c.*, concat_ws(" ", u.nom, u.prenom) AS utilisateur ' 
        . 'FROM commande c '
        . 'JOIN utilisateur u ON c.utilisateur_id = u.id ;');

$commandes = $stmt->fetchAll();
$statuts = [
    'en cours',
    'envoyée',
    'livrée',
    'annulée'
];

//dump($commandes);
?>

<h1>Liste des commandes</h1>

<table class="table table-dark">
    <thead>
        <tr> 
            <th>id commande: </th>
            <th>nom et prénom : </th>
            <th>montant : </form></th>
            <th>date de la commande: </th>
            <th>statut: </th>
            <th>date du statut: </th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach( $commandes as $commande):             
        
        ?>
            <tr>
                <td><?= $commande['id']?></td>
                <td><?= $commande['utilisateur']?></td>
                <td><?= prixFR($commande['montant_total'])?></td>
                <td><?= dateTimeFr($commande['date_commande'])?></td>
                <td><?= $commande['statut']?></td>
                <td><?= dateTimeFr($commande['date_statut'])?></td>
                <td>
                    <form method="post" class="form-inline">
                        <select name="statut" class="form-control">
                            <?php
                            foreach($statuts as $statut) :
                                $selected = ($statut == $commande['statut'])
                                    ? 'selected'
                                    : ''
                                ;
                            ?>
                                <option value="<?= $statut; ?>" <?= $selected; ?>>
                                    <?= $statut; ?>
                                </option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                        <input type="hidden" name="commandeId" value="<?= $commande['id']?>">
                        <button type="submit" name="modifierStatut" class="btn btn-primary">
                            Modifier
                        </button>    
                    </form>
                </td>
                
            </tr>
            <?php endforeach; ?>
     </tbody>
    
</table>