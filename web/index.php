<?php
$conn = new mysqli('db', 'user_boissons', 'pass_boissons', 'db_boissons');
if ($conn->connect_error) die("Erreur DB");
$conn->set_charset('utf8mb4');

$auchan  = $conn->query("SELECT * FROM articles WHERE source='auchan'")->fetch_all(MYSQLI_ASSOC);
$sakanal = $conn->query("SELECT * FROM articles WHERE source='sakanal'")->fetch_all(MYSQLI_ASSOC);

/* ========================= OUTILS DE NORMALISATION ========================= */

function norm($s) {
    $s = mb_strtolower((string)$s, 'UTF-8');
    $s = str_replace(
        ['é','è','ê','ë','à','â','ä','î','ï','ô','ö','ù','û','ü','ç','’',"'",""],
        ['e','e','e','e','a','a','a','i','i','o','o','u','u','u','c',' ',' ',' '],
        $s
    );
    return preg_replace('/\s+/', ' ', trim($s));
}

function prixInt($p) {
    $n = preg_replace('/[^0-9]/', '', (string)$p);
    return $n === '' ? 0 : (int)$n;
}

function volumeML($t) {
    $t = str_replace(',', '.', norm($t));

    if (preg_match('/(\d+)\s*(x|×|\*)\s*(\d+(?:\.\d+)?)\s*(ml|cl|l)\b/', $t, $m)) {
        $qty = (int)$m[1];
        $val = (float)$m[3];
        $u   = $m[4];
    }
    elseif (preg_match('/(\d+(?:\.\d+)?)\s*(ml|cl|l)\b/', $t, $m)) {
        $qty = 1;
        $val = (float)$m[1];
        $u   = $m[2];
    }
    else {
        return 0;
    }

    $ml = ($u === 'l') ? $val * 1000 : (($u === 'cl') ? $val * 10 : $val);
    return (int)round($qty * $ml);
}

function getMarque($t) {
    $t = norm($t);
    $marques = [
        'coca cola','coca','sprite','fanta','pepsi','vimto','orangina','kirene','seo',
        'casamancaise','don simon','sunquick','gazelle','zena','pressea','valencia',
        'ivorio','oasis','schweppes','red bull'
    ];
    foreach ($marques as $m) {
        if (strpos($t, norm($m)) !== false) return norm($m);
    }
    return 'autre';
}

function getSaveur($t) {
    $t = norm($t);
    $saveurs = [
        'mandarine','orange','citron','ananas','pomme','goyave','mangue','tropical',
        'bissap','gingembre','fraise','cola','menthe'
    ];
    foreach ($saveurs as $s) {
        if (strpos($t, $s) !== false) return $s;
    }
    return 'nature';
}

function getPack($t) {
    $t = norm($t);
    if (strpos($t,'canette') !== false || strpos($t,'cannette') !== false) return 'canette';
    if (strpos($t,'brique')  !== false || strpos($t,'brq')      !== false) return 'brique';
    if (strpos($t,'bouteille') !== false || strpos($t,'btl')    !== false) return 'bouteille';
    return 'autre';
}

/* ========================= INDEXATION SAKANAL ========================= */

$indexS = [];

foreach ($sakanal as $i => $p) {
    $ml     = volumeML($p['title']);
    $marque = getMarque($p['title']);
    $saveur = getSaveur($p['title']);
    $pack   = getPack($p['title']);

    if (!$ml || $marque == 'autre') continue;

    $key = $marque.'|'.$saveur.'|'.$pack.'|'.$ml;
    $indexS[$key][] = $i;
}

$usedS = [];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comparateur Boissons Pro</title>

    <style>
        body { font-family:'Segoe UI',sans-serif; background:#f4f7f6; padding:20px; }
        .container { max-width:1000px; margin:auto; }
        .card {
            background:#fff; display:flex; margin-bottom:20px; border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,0.05); overflow:hidden;
            border-left:6px solid #2c3e50;
        }
        .side {
            flex:1; padding:20px; text-align:center; display:flex;
            flex-direction:column; justify-content:space-between;
            border-right:1px solid #eee;
        }
        .side:last-child { border-right:none; }
        img { height:120px; object-fit:contain; margin-bottom:15px; }
        .title { font-size:14px; font-weight:700; height:40px; overflow:hidden; margin-bottom:10px; }
        .price { font-size:24px; font-weight:900; color:#2d3436; }
        .meta { font-size:11px; color:#636e72; text-transform:uppercase; font-weight:bold; }
        .win {
            background:#dcfce7; color:#166534; padding:5px 12px; border-radius:20px;
            font-size:11px; font-weight:bold; display:inline-block; margin-top:10px;
        }
        .btn-voir {
            display:block; padding:10px; background:#0984e3; color:#fff;
            text-decoration:none; border-radius:6px; font-weight:bold; margin-top:15px;
        }
    </style>
</head>

<body>
<div class="container">

    <h1 style="text-align:center;">Comparateur de Prix Boissons</h1>

    <?php foreach ($auchan as $pA): ?>

        <?php
        $mlA  = volumeML($pA['title']);
        $marA = getMarque($pA['title']);
        $savA = getSaveur($pA['title']);
        $pckA = getPack($pA['title']);

        if (!$mlA || $marA == 'autre') continue;

        $keyA = $marA.'|'.$savA.'|'.$pckA.'|'.$mlA;

        if (!isset($indexS[$keyA])) continue;

        $idxS = null;
        foreach ($indexS[$keyA] as $cand) {
            if (!isset($usedS[$cand])) {
                $idxS = $cand;
                break;
            }
        }

        if ($idxS === null) continue;

        $usedS[$idxS] = true;

        $pS  = $sakanal[$idxS];
        $prA = prixInt($pA['price']);
        $prS = prixInt($pS['price']);

        $plA = round($prA / ($mlA / 1000));
        $plS = round($prS / ($mlA / 1000));
        ?>

        <div class="card">

            <div class="side">
                <div class="meta">AUCHAN (<?= $mlA/1000 ?>L)</div>
                <img src="<?= $pA['image_url'] ?>">
                <div class="title"><?= htmlspecialchars($pA['title']) ?></div>
                <div class="price"><?= number_format($prA, 0, '.', ' ') ?> F</div>
                <div style="font-size:11px;"><?= $plA ?> F / L</div>

                <?= ($plA <= $plS) ? '<div class="win">MEILLEUR PRIX</div>' : '<div style="height:32px;"></div>' ?>

                <a href="<?= $pA['url'] ?>" target="_blank" class="btn-voir">Voir sur Auchan</a>
            </div>

            <div class="side">
                <div class="meta">SAKANAL (<?= $mlA/1000 ?>L)</div>
                <img src="<?= $pS['image_url'] ?>">
                <div class="title"><?= htmlspecialchars($pS['title']) ?></div>
                <div class="price"><?= number_format($prS, 0, '.', ' ') ?> F</div>
                <div style="font-size:11px;"><?= $plS ?> F / L</div>

                <?= ($plS < $plA) ? '<div class="win">MEILLEUR PRIX</div>' : '<div style="height:32px;"></div>' ?>

                <a href="<?= $pS['url'] ?>" target="_blank" class="btn-voir">Voir sur Sakanal</a>
            </div>

        </div>

    <?php endforeach; ?>

</div>
</body>
</html>
