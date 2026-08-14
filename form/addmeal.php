<?php

session_start();

require __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header('Location: ../login.php');
    exit;
}

/*
 * Utilisateur réellement connecté
 */
$userId = (int) $_SESSION['user_id'];
$profileName = $_SESSION['user_name'];

/*
 * On garde $profile pour les liens éventuels.
 */
$profile = $_GET['profile'] ?? $_POST['profile'] ?? '';

/*
 * Configuration visuelle
 * On garde ton système de couleurs.
 */
$neon = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => '#FCEE0A',
    'LAPINOU', 'Lapinou', 'lapinou' => '#00FF9C',
    default => '#00FF9C',
};

$neonSoft = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => 'rgba(252,238,10,.35)',
    'LAPINOU', 'Lapinou', 'lapinou' => 'rgba(0,255,156,.35)',
    default => 'rgba(0,255,156,.35)',
};

$avatar = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => '../assets/img/cat.jpeg',
    'LAPINOU', 'Lapinou', 'lapinou' => '../assets/img/rabbit.jpeg',
    default => '',
};

// Récupération des ingrédients directement depuis MySQL
$ingredients = $pdo->query('SELECT id, name, measure FROM ingredient ORDER BY id ASC')->fetchAll();

// Traitement du formulaire
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $nbrPpl = (int) ($_POST['nbr_ppl'] ?? 2);
    $selectedIngredients = $_POST['ingredients'] ?? []; // [id_ingredient => quantity]

    if ($name === '') {
        $error = 'Le nom du repas est obligatoire.';
    } elseif (empty($selectedIngredients)) {
        $error = 'Sélectionne au moins un ingrédient.';
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Créer le repas
            $stmt = $pdo->prepare(
                'INSERT INTO meal (name, description, nbr_ppl) VALUES (:name, :description, :nbr_ppl)'
            );
            $stmt->execute([
                ':name' => $name,
                ':description' => $description !== '' ? $description : null,
                ':nbr_ppl' => $nbrPpl,
            ]);
            $mealId = (int) $pdo->lastInsertId();

            // 2. Créer les liaisons ingrédient <-> repas dans `link`
            $stmtLink = $pdo->prepare(
                'INSERT INTO link (id_meal, id_ingredient, quantity) VALUES (:id_meal, :id_ingredient, :quantity)'
            );
            foreach ($selectedIngredients as $ingId => $quantity) {
                $stmtLink->execute([
                    ':id_meal' => $mealId,
                    ':id_ingredient' => (int) $ingId,
                    ':quantity' => (int) $quantity,
                ]);
            }

            // 3. Log de l'action
            $stmtLog = $pdo->prepare(
                'INSERT INTO log (id_user, ac, table_name, table_id) VALUES (:id_user, :ac, :table_name, :table_id)'
            );
            $stmtLog->execute([
                ':id_user' => $userId,
                ':ac' => 'create',
                ':table_name' => 'meal',
                ':table_id' => $mealId,
            ]);

            $pdo->commit();
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/svg+xml" href="/vite.svg" />
    <link rel="stylesheet" href="../style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MEALPRO // Nouveau repas</title>
    <style>
      :root { --neon: <?= $neon ?>; --neon-soft: <?= $neonSoft ?>; }
      .dash-page { --neon: <?= $neon ?>; --neon-soft: <?= $neonSoft ?>; }
    </style>
</head>
<body>
<div class="scanlines"></div>
<div class="grid-bg"></div>

<section class="screen active dash-page" style="opacity:1;pointer-events:auto;transform:scale(1);padding:2rem;gap:1.2rem;overflow-y:auto;align-items:stretch;justify-content:flex-start;">
    <header class="brand" style="margin-bottom:.5rem;">
        <h1 class="glitch" data-text="MEALPRO">MEALPRO</h1>
        <p class="brand-sub">// NOUVEAU REPAS</p>
    </header>

    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;justify-content:space-between;width:100%;max-width:680px;margin:0 auto;">
        <div style="display:flex;align-items:center;gap:.8rem;">
            <div style="width:56px;height:56px;border-radius:50%;border:2px solid var(--neon);box-shadow:0 0 20px var(--neon-soft);overflow:hidden;">
                <?php if ($avatar): ?>
                <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= $profileName ?>" style="width:100%;height:100%;object-fit:cover;filter:grayscale(.2) contrast(1.1);" />
                <?php endif; ?>
            </div>
            <div>
                <span style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;color:var(--neon);text-shadow:0 0 10px var(--neon-soft);font-size:1.1rem;"><?= $profileName ?></span>
                <p style="font-size:.7rem;letter-spacing:.2em;color:var(--muted);margin:0;">SESSION ACTIVE</p>
            </div>
        </div>
        <a href="../index.php?profile=<?= htmlspecialchars($profile) ?>" style="font-family:'Share Tech Mono',monospace;font-size:.75rem;letter-spacing:.15em;color:var(--muted);border:1px solid var(--border);padding:.5rem 1rem;border-radius:6px;transition:all .25s;text-decoration:none;" onmouseover="this.style.borderColor='var(--neon)';this.style.color='var(--neon)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">← RETOUR</a>
    </div>

    <?php if ($success): ?>
    <div style="width:100%;max-width:680px;margin:0 auto;padding:1.2rem 1.5rem;background:rgba(0,255,156,.08);border:1px solid var(--neon);border-radius:8px;text-align:center;box-shadow:0 0 20px var(--neon-soft);">
        <p style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;color:var(--neon);text-shadow:0 0 10px var(--neon-soft);">REPAS CRÉÉ AVEC SUCCÈS</p>
        <a href="index.php?profile=<?= htmlspecialchars($profile) ?>" style="display:inline-block;margin-top:.8rem;font-family:'Share Tech Mono',monospace;letter-spacing:.15em;color:var(--neon);text-decoration:none;border-bottom:1px solid var(--neon);">→ RETOUR AU TABLEAU DE BORD</a>
    </div>
    <?php else: ?>

    <?php if ($error): ?>
    <div style="width:100%;max-width:680px;margin:0 auto;padding:1rem 1.5rem;background:rgba(255,0,60,.08);border:1px solid rgba(255,0,60,.4);border-radius:8px;">
        <p style="font-family:'Share Tech Mono',monospace;color:#ff003c;letter-spacing:.1em;">ERREUR: <?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" style="width:100%;max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:1.2rem;">
        <div style="display:flex;flex-direction:column;gap:.4rem;">
            <label style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;font-size:.85rem;color:var(--neon);text-shadow:0 0 8px var(--neon-soft);">NOM DU REPAS</label>
            <input type="text" name="name" required maxlength="255" placeholder="Ex: Petit-déjeuner..."
                style="background:rgba(0,0,0,.4);border:1px solid var(--border);color:var(--text);padding:.7rem;border-radius:6px;font-family:'Share Tech Mono',monospace;font-size:.9rem;transition:all .25s;"
                onfocus="this.style.borderColor='var(--neon)';this.style.boxShadow='0 0 12px var(--neon-soft)'"
                onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'" />
        </div>

        <div style="display:flex;flex-direction:column;gap:.5rem;">
            <label style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;font-size:.85rem;color:var(--neon);text-shadow:0 0 8px var(--neon-soft);">INGRÉDIENTS</label>
            <input type="search" id="ingredient-search" placeholder="Rechercher un ingrédient..." autocomplete="off"
                style="background:rgba(0,0,0,.4);border:1px solid var(--border);color:var(--text);padding:.7rem;border-radius:6px;font-family:'Share Tech Mono',monospace;font-size:.9rem;transition:all .25s;"
                onfocus="this.style.borderColor='var(--neon)';this.style.boxShadow='0 0 12px var(--neon-soft)'"
                onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'" />

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:8px;padding:.8rem;min-height:180px;">
                    <p style="font-family:'Share Tech Mono',monospace;font-size:.7rem;letter-spacing:.2em;color:var(--muted);margin-bottom:.5rem;">RÉSULTATS</p>
                    <div id="ingredient-results" style="display:flex;flex-direction:column;gap:.4rem;"></div>
                </div>
                <div style="background:rgba(255,255,255,.02);border:1px solid var(--border);border-radius:8px;padding:.8rem;min-height:180px;">
                    <p style="font-family:'Share Tech Mono',monospace;font-size:.7rem;letter-spacing:.2em;color:var(--muted);margin-bottom:.5rem;">SÉLECTIONNÉS</p>
                    <div id="selected-ingredients" style="display:flex;flex-direction:column;gap:.4rem;"></div>
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:.4rem;">
            <label style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;font-size:.85rem;color:var(--neon);text-shadow:0 0 8px var(--neon-soft);">DESCRIPTION</label>
            <textarea name="description" maxlength="255" placeholder="Optionnel..."
                style="background:rgba(0,0,0,.4);border:1px solid var(--border);color:var(--text);padding:.7rem;border-radius:6px;font-family:'Share Tech Mono',monospace;font-size:.9rem;resize:vertical;min-height:60px;transition:all .25s;"
                onfocus="this.style.borderColor='var(--neon)';this.style.boxShadow='0 0 12px var(--neon-soft)'"
                onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'"></textarea>
        </div>

        <div style="display:flex;flex-direction:column;gap:.4rem;">
            <label style="font-family:'Orbitron',sans-serif;font-weight:700;letter-spacing:.2em;font-size:.85rem;color:var(--neon);text-shadow:0 0 8px var(--neon-soft);">NOMBRE DE PERSONNES</label>
            <input type="number" name="nbr_ppl" min="1" value="2" required
                style="background:rgba(0,0,0,.4);border:1px solid var(--border);color:var(--text);padding:.7rem;border-radius:6px;font-family:'Share Tech Mono',monospace;font-size:.9rem;width:100px;transition:all .25s;"
                onfocus="this.style.borderColor='var(--neon)';this.style.boxShadow='0 0 12px var(--neon-soft)'"
                onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'" />
        </div>

        <div style="display:flex;gap:.8rem;flex-wrap:wrap;">
            <button type="submit" style="background:rgba(252,238,10,.12);border:1px solid var(--neon);color:var(--neon);padding:.7rem 1.8rem;border-radius:6px;font-family:'Share Tech Mono',monospace;letter-spacing:.15em;cursor:pointer;transition:all .25s;"
                onmouseover="this.style.boxShadow='0 0 18px var(--neon-soft)';this.style.background='rgba(252,238,10,.2)'"
                onmouseout="this.style.boxShadow='none';this.style.background='rgba(252,238,10,.12)'">CRÉER LE REPAS</button>
        <a href="../index.php?profile=<?= htmlspecialchars($profile) ?>" style="text-decoration:none;">
            <button type="button"
            style="background:rgba(255,255,255,.04);border:1px solid var(--border);color:var(--muted);padding:.7rem 1.8rem;border-radius:6px;font-family:'Share Tech Mono',monospace;letter-spacing:.15em;cursor:pointer;transition:all .25s;"
            onmouseover="this.style.borderColor='var(--muted)';this.style.color='var(--text)'"
            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">ANNULER</button>
        </a>
        </div>
    </form>
    <?php endif; ?>

    <footer class="status-bar" style="position:static;margin-top:auto;padding-top:1rem;">
        <span class="dot"></span> SESSION ACTIVE — PROTOCOLE ARASAKA
    </footer>
</section>

<script>
    // Ingrédients injectés directement par PHP depuis MySQL (aucun appel réseau externe)
    const ingredients = <?= json_encode(
        $ingredients,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) ?>;

    const selectedIngredients = {};

    const searchInput = document.getElementById('ingredient-search');
    const results = document.getElementById('ingredient-results');
    const selected = document.getElementById('selected-ingredients');

    function displayResults() {
        const search = searchInput.value.toLowerCase().trim();
        results.innerHTML = '';

        if (!search) {
            results.innerHTML = '<p style="color:var(--muted);font-size:.75rem;letter-spacing:.1em;">Tape pour rechercher...</p>';
            return;
        }

        const filtered = ingredients.filter(i => i.name.toLowerCase().includes(search));

        if (filtered.length === 0) {
            results.innerHTML = '<p style="color:var(--muted);font-size:.75rem;letter-spacing:.1em;">Aucun résultat</p>';
            return;
        }

        filtered.forEach(ingredient => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = 'background:rgba(255,255,255,.03);border:1px solid var(--border);color:var(--text);padding:.5rem .8rem;border-radius:6px;font-family:Share Tech Mono,monospace;font-size:.8rem;cursor:pointer;text-align:left;transition:all .25s;display:flex;align-items:center;justify-content:space-between;';

            if (selectedIngredients[ingredient.id]) {
                btn.disabled = true;
                btn.style.opacity = '.4';
                btn.style.cursor = 'default';
                btn.innerHTML = `<span>${escapeHtml(ingredient.name)}</span><span style="color:var(--neon);">✓</span>`;
            } else {
                btn.innerHTML = `<span>${escapeHtml(ingredient.name)}</span><span style="color:var(--muted);font-size:.7rem;">${ingredient.measure}</span>`;
                btn.addEventListener('mouseenter', () => {
                    btn.style.borderColor = 'var(--neon)';
                    btn.style.boxShadow = '0 0 10px var(--neon-soft)';
                });
                btn.addEventListener('mouseleave', () => {
                    btn.style.borderColor = 'var(--border)';
                    btn.style.boxShadow = 'none';
                });
                btn.addEventListener('click', () => {
                    selectedIngredients[ingredient.id] = {
                        id: ingredient.id,
                        name: ingredient.name,
                        measure: ingredient.measure,
                        quantity: 100
                    };
                    displaySelected();
                    displayResults();
                });
            }

            results.appendChild(btn);
        });
    }

    function displaySelected() {
        selected.innerHTML = '';
        const list = Object.values(selectedIngredients);

        if (list.length === 0) {
            selected.innerHTML = '<p style="color:var(--muted);font-size:.75rem;letter-spacing:.1em;">Aucun ingrédient sélectionné</p>';
            return;
        }

        list.forEach(ingredient => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:.5rem;padding:.5rem .8rem;background:rgba(252,238,10,.06);border:1px solid var(--border);border-radius:6px;transition:all .25s;';

            row.innerHTML = `
                <span style="font-family:Share Tech Mono,monospace;font-size:.8rem;color:var(--neon);flex:1;">${escapeHtml(ingredient.name)}</span>
                <input type="number" name="ingredients[${ingredient.id}]" value="${ingredient.quantity}" min="1"
                    style="width:60px;background:rgba(0,0,0,.4);border:1px solid var(--border);color:var(--text);padding:.3rem .5rem;border-radius:4px;font-family:Share Tech Mono,monospace;font-size:.8rem;text-align:center;" />
                <span style="font-size:.7rem;color:var(--muted);">${ingredient.measure}</span>
                <button type="button" style="background:rgba(255,0,60,.1);border:1px solid rgba(255,0,60,.3);color:#ff003c;width:24px;height:24px;border-radius:4px;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;padding:0;">×</button>
            `;

            row.querySelector('input').addEventListener('input', e => {
                ingredient.quantity = e.target.value;
            });

            row.querySelector('button').addEventListener('click', () => {
                delete selectedIngredients[ingredient.id];
                displaySelected();
                displayResults();
            });

            selected.appendChild(row);
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    searchInput.addEventListener('input', displayResults);
    displaySelected();
    displayResults();
</script>
</body>
</html>