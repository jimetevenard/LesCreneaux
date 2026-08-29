<?php
// Seed des modèles de créneaux récurrents — table "Paramètres" du Sheet.
declare(strict_types=1);

$appDir = dirname(__DIR__) . '/app';
require $appDir . '/config.php';
require $appDir . '/src/helpers.php';
require $appDir . '/src/Database.php';
require $appDir . '/src/LabelRepo.php';

$pdo = Database::connect(DB_PATH);
$pdo->query('DELETE FROM modeles');

// Ids des labels seedés par la migration init.
$labelId = [];
foreach ($pdo->query("SELECT id, nom FROM labels") as $r) {
    $labelId[$r['nom']] = (int)$r['id'];
}
$MILLAT = $labelId['Alice Millat']   ?? null;
$PARADIS = $labelId['Marie Paradis']   ?? null;
$LADOUMEGUE = $labelId['Jules Ladoumègue']   ?? null;
$AURIOL = $labelId['Jacqueline Auriol']   ?? null;
$MEURICE = $labelId['Paul Meurice']   ?? null;
  


// [jour_semaine, hd, hf, capa, labels[], commentaire]
$modeles = [
    [1, '17:30', '22:30', 15, [$LADOUMEGUE],  null ], // Ladoumègue
    [1, '20:00', '22:00', 15, [$MILLAT],  null ], // Alice Milliat
    [2, '17:30', '20:00', 15, [$MILLAT],  null ], // Alice Milliat
    [3, '12:00', '13:30', 15, [$MILLAT],  null ], // Alice Milliat
    [3, '19:00', '22:30', 15, [$MEURICE],  null ], // Paul Meurice
    [4, '17:30', '20:00', 15, [$MILLAT],  null ], // Alice Milliat
    [4, '18:00', '20:00', 15, [$AURIOL],  null ], // Jacqueline Auriol
    [4, '18:00', '20:00', 15, [$PARADIS],  null ], // Marie Paradis
    [5, '18:30', '22:30', 15, [$MEURICE],  null ], // Paul Meurice
    [6, '09:00', '12:00', 15, [$MEURICE],  null ], // Paul Meurice
    [6, '14:00', '22:00', 15, [$MEURICE],  null ], // Paul Meurice
    [6, '18:30', '22:30', 15, [$PARADIS],  null ], // Marie Paradis
    [6, '13:00', '20:00', 15, [$MILLAT],  null ], // Alice Milliat
    [7, '09:00', '13:00', 15, [$MILLAT],  null ], // Alice Milliat
    [7, '09:00', '13:00', 15, [$PARADIS],  null ], // Marie Paradis
    [7, '14:00', '18:00', 15, [$MEURICE],  null ], // Paul Meurice
];

$ins = $pdo->prepare(
    'INSERT INTO modeles (jour_semaine, heure_debut, heure_fin, capacite, note_defaut, active)
     VALUES (?, ?, ?, ?, ?, 1)'
);
foreach ($modeles as [$js, $hd, $hf, $cap, $labels, $note]) {
    $ins->execute([$js, $hd, $hf, $cap, $note]);
    $mid = (int)$pdo->lastInsertId();
    LabelRepo::syncModele($pdo, $mid, array_values(array_filter($labels)));
}
printf("Modèles réinitialisés : %d entrées%s", count($modeles), PHP_EOL);
