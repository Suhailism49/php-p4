<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docenten Beheer - Ziekmeldsysteem</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .nav {
            background: #34495e;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            padding: 15px 25px;
            display: block;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
        }
        
        .nav a:hover {
            background: #2c3e50;
            border-bottom-color: #3498db;
        }
        
        .nav a.active {
            background: #2c3e50;
            border-bottom-color: #e74c3c;
        }
        
        .content {
            padding: 40px;
        }
        
        .toolbar {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        
        .btn-success:hover {
            box-shadow: 0 10px 20px rgba(39, 174, 96, 0.3);
        }
        
        .docenten-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .docent-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .docent-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            border-color: #3498db;
        }
        
        .docent-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .docent-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .docent-info h3 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 1.3em;
        }
        
        .docent-info .afdeling {
            color: #7f8c8d;
            font-size: 0.9em;
            font-weight: 600;
        }
        
        .docent-details {
            margin-bottom: 20px;
        }
        
        .detail-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-icon {
            width: 20px;
            color: #3498db;
            margin-right: 10px;
        }
        
        .detail-text {
            color: #2c3e50;
            font-size: 0.9em;
        }
        
        .docent-stats {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            text-align: center;
        }
        
        .stat-item {
            color: #2c3e50;
        }
        
        .stat-number {
            font-size: 1.5em;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .stat-label {
            font-size: 0.8em;
            margin-top: 5px;
        }
        
        .docent-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 12px;
            border-radius: 20px;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .btn-edit:hover {
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .btn-delete:hover {
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background: white;
            margin: 2% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .modal-header h3 {
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .required {
            color: #e74c3c;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .no-results h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
            }

            .content {
                padding: 20px;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .search-box {
                min-width: 0;
                width: 100%;
            }

            .docenten-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .docent-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'config.php';
    require_once 'classes.php';
    
    $docentObj = new Docent();
    $ziekmeldingObj = new Ziekmelding();
    
    $success = false;
    $error = '';
    $searchTerm = $_GET['search'] ?? '';
    
    // Haal alle docenten op
    $docenten = $docentObj->getAllDocenten();
    
    // Filter docenten op basis van zoekterm
    if ($searchTerm) {
        $docenten = array_filter($docenten, function($docent) use ($searchTerm) {
            $fullName = $docent['voornaam'] . ' ' . $docent['achternaam'];
            return stripos($fullName, $searchTerm) !== false || 
                   stripos($docent['email'], $searchTerm) !== false ||
                   stripos($docent['afdeling'], $searchTerm) !== false;
        });
    }
    
    // Verwerk formulier voor nieuwe/bewerkte docent
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add_docent' || $action === 'edit_docent') {
            // Validatie
            $errors = [];
            
            if (empty($_POST['voornaam'])) {
                $errors[] = 'Voornaam is verplicht';
            }
            
            if (empty($_POST['achternaam'])) {
                $errors[] = 'Achternaam is verplicht';
            }
            
            if (empty($_POST['email'])) {
                $errors[] = 'Email is verplicht';
            } elseif (!validateEmail($_POST['email'])) {
                $errors[] = 'Ongeldig email adres';
            }
            
            if (empty($_POST['afdeling'])) {
                $errors[] = 'Afdeling is verplicht';
            }
            
            if (empty($errors)) {
                $data = [
                    'voornaam' => $_POST['voornaam'],
                    'achternaam' => $_POST['achternaam'],
                    'email' => $_POST['email'],
                    'telefoon' => $_POST['telefoon'],
                    'afdeling' => $_POST['afdeling']
                ];
                
                if ($action === 'add_docent') {
                    // Controleer of email al bestaat
                    $bestaandeDocent = $docentObj->getDocentByEmail($_POST['email']);
                    if ($bestaandeDocent) {
                        $error = 'Een docent met dit email adres bestaat al.';
                    } else {
                        if ($docentObj->addDocent($data)) {
                            $success = true;
                            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=added');
                            exit;
                        } else {
                            $error = 'Er is een fout opgetreden bij het toevoegen van de docent.';
                        }
                    }
                } elseif ($action === 'edit_docent') {
                    $docentId = $_POST['docent_id'];
                    if ($docentObj->updateDocent($docentId, $data)) {
                        $success = true;
                        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=updated');
                        exit;
                    } else {
                        $error = 'Er is een fout opgetreden bij het bijwerken van de docent.';
                    }
                }
            } else {
                $error = implode('<br>', $errors);
            }
        }
    }
    
    // Success message van redirect
    if (isset($_GET['success'])) {
        if ($_GET['success'] === 'added') {
            $success = true;
            $successMessage = 'Docent is succesvol toegevoegd!';
        } elseif ($_GET['success'] === 'updated') {
            $success = true;
            $successMessage = 'Docent is succesvol bijgewerkt!';
        }
    }
    
    // Haal ziekmelding statistieken op voor elke docent
    $docentStatistieken = [];
    foreach ($docenten as $docent) {
        $alleZiekmeldingen = $ziekmeldingObj->getAllZiekmeldingen();
        $docentMeldingen = array_filter($alleZiekmeldingen, function($melding) use ($docent) {
            return $melding['docent_id'] == $docent['id'];
        });
        
        $totaalMeldingen = count($docentMeldingen);
        $actieveMeldingen = count(array_filter($docentMeldingen, function($m) {
            return $m['status'] === 'actief';
        }));
        
        $docentStatistieken[$docent['id']] = [
            'totaal' => $totaalMeldingen,
            'actief' => $actieveMeldingen
        ];
    }
    ?>
    
    <div class="container">
        <div class="header">
            <h1>👥 Docenten Beheer</h1>
            <p>Beheer alle geregistreerde docenten</p>
        </div>
        
        <nav class="nav">
            <a href="index.php">🏠 Dashboard</a>
            <a href="nieuwe_melding.php">➕ Nieuwe Melding</a>
            <a href="overzicht.php">📋 Overzicht</a>
            <a href="docenten.php" class="active">👥 Docenten</a>
        </nav>
        
        <div class="content">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ <?php echo isset($successMessage) ? $successMessage : 'Actie succesvol uitgevoerd!'; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Toolbar -->
            <div class="toolbar">
                <div class="search-box">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Zoek docenten op naam, email of afdeling..." 
                               value="<?php echo h($searchTerm); ?>">
                        <span class="search-icon">🔍</span>
                    </form>
                </div>
                <button onclick="openAddModal()" class="btn btn-success">➕ Nieuwe Docent</button>
            </div>
            
            <!-- Docenten Grid -->
            <?php if (!empty($docenten)): ?>
                <div class="docenten-grid">
                    <?php foreach ($docenten as $docent): ?>
                        <div class="docent-card">
                            <div class="docent-header">
                                <div class="docent-avatar">
                                    <?php echo strtoupper(substr($docent['voornaam'], 0, 1) . substr($docent['achternaam'], 0, 1)); ?>
                                </div>
                                <div class="docent-info">
                                    <h3><?php echo h($docent['voornaam'] . ' ' . $docent['achternaam']); ?></h3>
                                    <div class="afdeling"><?php echo h($docent['afdeling']); ?></div>
                                </div>
                            </div>
                            
                            <div class="docent-details">
                                <div class="detail-row">
                                    <span class="detail-icon">📧</span>
                                    <span class="detail-text"><?php echo h($docent['email']); ?></span>
                                </div>
                                <?php if ($docent['telefoon']): ?>
                                <div class="detail-row">
                                    <span class="detail-icon">📞</span>
                                    <span class="detail-text"><?php echo h($docent['telefoon']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="detail-icon">📅</span>
                                    <span class="detail-text">Sinds <?php echo formatDatum($docent['created_at']); ?></span>
                                </div>
                            </div>
                            
                            <div class="docent-stats">
                                <div class="stats-grid">
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $docentStatistieken[$docent['id']]['totaal']; ?></div>
                                        <div class="stat-label">Totaal Meldingen</div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-number"><?php echo $docentStatistieken[$docent['id']]['actief']; ?></div>
                                        <div class="stat-label">Actief</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="docent-actions">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($docent), ENT_QUOTES, 'UTF-8'); ?>)" 
                                        class="btn btn-sm btn-edit">✏️ Bewerken</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <h3>Geen docenten gevonden</h3>
                    <?php if ($searchTerm): ?>
                        <p>Er zijn geen docenten gevonden die voldoen aan de zoekterm "<?php echo h($searchTerm); ?>".</p>
                        <a href="docenten.php" class="btn" style="margin-top: 20px;">🔄 Alle Docenten Tonen</a>
                    <?php else: ?>
                        <p>Er zijn nog geen docenten geregistreerd in het systeem.</p>
                        <button onclick="openAddModal()" class="btn btn-success" style="margin-top: 20px;">➕ Eerste Docent Toevoegen</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add/Edit Docent Modal -->
    <div id="docentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3 id="modalTitle">Nieuwe Docent Toevoegen</h3>
                <p id="modalSubtitle">Vul alle gegevens in voor de nieuwe docent</p>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" id="formAction" value="add_docent">
                <input type="hidden" name="docent_id" id="docentId">
                
                <div class="form-group">
                    <label for="voornaam">Voornaam <span class="required">*</span></label>
                    <input type="text" name="voornaam" id="voornaam" required>
                </div>
                
                <div class="form-group">
                    <label for="achternaam">Achternaam <span class="required">*</span></label>
                    <input type="text" name="achternaam" id="achternaam" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="telefoon">Telefoon</label>
                    <input type="text" name="telefoon" id="telefoon">
                </div>
                
                <div class="form-group">
                    <label for="afdeling">Afdeling <span class="required">*</span></label>
                    <input type="text" name="afdeling" id="afdeling" required>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary" style="margin-right: 15px; background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);">Annuleren</button>
                    <button type="submit" class="btn" id="submitBtn">💾 Docent Opslaan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Nieuwe Docent Toevoegen';
            document.getElementById('modalSubtitle').textContent = 'Vul alle gegevens in voor de nieuwe docent';
            document.getElementById('formAction').value = 'add_docent';
            document.getElementById('submitBtn').textContent = '💾 Docent Opslaan';
            
            // Reset form
            document.getElementById('docentId').value = '';
            document.getElementById('voornaam').value = '';
            document.getElementById('achternaam').value = '';
            document.getElementById('email').value = '';
            document.getElementById('telefoon').value = '';
            document.getElementById('afdeling').value = '';
            
            document.getElementById('docentModal').style.display = 'block';
        }
        
        function openEditModal(docent) {
            document.getElementById('modalTitle').textContent = 'Docent Bewerken';
            document.getElementById('modalSubtitle').textContent = 'Wijzig de gegevens van de docent';
            document.getElementById('formAction').value = 'edit_docent';
            document.getElementById('submitBtn').textContent = '💾 Wijzigingen Opslaan';
            
            // Fill form with existing data
            document.getElementById('docentId').value = docent.id;
            document.getElementById('voornaam').value = docent.voornaam;
            document.getElementById('achternaam').value = docent.achternaam;
            document.getElementById('email').value = docent.email;
            document.getElementById('telefoon').value = docent.telefoon || '';
            document.getElementById('afdeling').value = docent.afdeling;
            
            document.getElementById('docentModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('docentModal').style.display = 'none';
        }
        
        // Klik buiten de modal om te sluiten
        window.onclick = function(event) {
            var modal = document.getElementById('docentModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>