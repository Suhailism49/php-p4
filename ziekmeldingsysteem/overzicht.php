<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overzicht Ziekmeldingen - Ziekmeldsysteem</title>
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
            max-width: 1400px;
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
        
        .filters {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            align-items: end;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .filter-group select,
        .filter-group input {
            padding: 10px 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        
        .table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .table tr:hover {
            background: #e3f2fd;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-actief {
            background: #ffebee;
            color: #c62828;
        }
        
        .status-hersteld {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .status-verlengd {
            background: #fff3e0;
            color: #ef6c00;
        }
        
        .vervanger-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .vervanger-ja {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .vervanger-nee {
            background: #ffebee;
            color: #c62828;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
        }
        
        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .btn-status {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
        }
        
        .summary {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }
        
        .summary-item {
            padding: 15px;
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 10px;
        }
        
        .summary-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .summary-label {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-top: 5px;
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
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            margin-bottom: 20px;
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
        
        @media (max-width: 1200px) {
            .table {
                font-size: 14px;
            }
            
            .table th,
            .table td {
                padding: 8px 6px;
            }
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
            }
            
            .content {
                padding: 20px;
            }
            
            .filter-row {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <?php
    require_once 'config.php';
    require_once 'classes.php';
    
    $ziekmeldingObj = new Ziekmelding();
    $docentObj = new Docent();
    
    // Filters ophalen uit URL parameters
    $statusFilter = $_GET['status'] ?? '';
    $docentFilter = $_GET['docent'] ?? '';
    $startDatumFilter = $_GET['start_datum'] ?? '';
    $eindDatumFilter = $_GET['eind_datum'] ?? '';
    
    // Ziekmeldingen ophalen
    if ($statusFilter) {
        $ziekmeldingen = $ziekmeldingObj->getAllZiekmeldingen($statusFilter);
    } else {
        $ziekmeldingen = $ziekmeldingObj->getAllZiekmeldingen();
    }
    
    // Extra filters toepassen
    if ($docentFilter || $startDatumFilter || $eindDatumFilter) {
        $ziekmeldingen = array_filter($ziekmeldingen, function($melding) use ($docentFilter, $startDatumFilter, $eindDatumFilter) {
            $match = true;
            
            if ($docentFilter && stripos($melding['docent_naam'], $docentFilter) === false) {
                $match = false;
            }
            
            if ($startDatumFilter && $melding['startdatum'] < $startDatumFilter) {
                $match = false;
            }
            
            if ($eindDatumFilter && $melding['startdatum'] > $eindDatumFilter) {
                $match = false;
            }
            
            return $match;
        });
    }
    
    // Statistieken berekenen
    $totaal = count($ziekmeldingen);
    $actief = count(array_filter($ziekmeldingen, fn($m) => $m['status'] === 'actief'));
    $hersteld = count(array_filter($ziekmeldingen, fn($m) => $m['status'] === 'hersteld'));
    $verlengd = count(array_filter($ziekmeldingen, fn($m) => $m['status'] === 'verlengd'));
    
    // Status wijzigen als er een POST request is
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'update_status' && isset($_POST['melding_id'], $_POST['new_status'])) {
            $ziekmeldingObj->updateStatus($_POST['melding_id'], $_POST['new_status']);
            header('Location: ' . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
            exit;
        }
    }
    ?>
    
    <div class="container">
        <div class="header">
            <h1>📋 Overzicht Ziekmeldingen</h1>
            <p>Alle geregistreerde ziekmeldingen in één overzicht</p>
        </div>
        
        <nav class="nav">
            <a href="index.php">🏠 Dashboard</a>
            <a href="nieuwe_melding.php">➕ Nieuwe Melding</a>
            <a href="overzicht.php" class="active">📋 Overzicht</a>
            <a href="docenten.php">👥 Docenten</a>
        </nav>
        
        <div class="content">
            <!-- Samenvatting -->
            <div class="summary">
                <h2 style="margin-bottom: 20px; color: #2c3e50;">📊 Overzicht</h2>
                <div class="summary-stats">
                    <div class="summary-item">
                        <div class="summary-number"><?php echo $totaal; ?></div>
                        <div class="summary-label">Totaal Meldingen</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number" style="color: #e74c3c;"><?php echo $actief; ?></div>
                        <div class="summary-label">Actief</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number" style="color: #27ae60;"><?php echo $hersteld; ?></div>
                        <div class="summary-label">Hersteld</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-number" style="color: #f39c12;"><?php echo $verlengd; ?></div>
                        <div class="summary-label">Verlengd</div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="filters">
                <form method="GET" action="">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="">Alle statussen</option>
                                <option value="actief" <?php echo $statusFilter === 'actief' ? 'selected' : ''; ?>>Actief</option>
                                <option value="hersteld" <?php echo $statusFilter === 'hersteld' ? 'selected' : ''; ?>>Hersteld</option>
                                <option value="verlengd" <?php echo $statusFilter === 'verlengd' ? 'selected' : ''; ?>>Verlengd</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="docent">Docent</label>
                            <input type="text" name="docent" id="docent" placeholder="Zoek op naam..." 
                                   value="<?php echo h($docentFilter); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="start_datum">Vanaf datum</label>
                            <input type="date" name="start_datum" id="start_datum" 
                                   value="<?php echo h($startDatumFilter); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <label for="eind_datum">Tot datum</label>
                            <input type="date" name="eind_datum" id="eind_datum" 
                                   value="<?php echo h($eindDatumFilter); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <button type="submit" class="btn">🔍 Filteren</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Tabel -->
            <div class="table-container">
                <?php if (!empty($ziekmeldingen)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Docent</th>
                                <th>Afdeling</th>
                                <th>Startdatum</th>
                                <th>Einddatum</th>
                                <th>Dagen</th>
                                <th>Reden</th>
                                <th>Vervanger</th>
                                <th>Status</th>
                                <th>Gemeld op</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ziekmeldingen as $melding): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo h($melding['docent_naam']); ?></strong><br>
                                        <small style="color: #7f8c8d;"><?php echo h($melding['email']); ?></small>
                                    </td>
                                    <td><?php echo h($melding['afdeling']); ?></td>
                                    <td><?php echo formatDatum($melding['startdatum']); ?></td>
                                    <td>
                                        <?php if ($melding['einddatum']): ?>
                                            <?php echo formatDatum($melding['einddatum']); ?>
                                        <?php else: ?>
                                            <em style="color: #7f8c8d;">Onbekend</em>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $dagen = $melding['einddatum'] 
                                            ? (strtotime($melding['einddatum']) - strtotime($melding['startdatum'])) / (60*60*24) + 1
                                            : (time() - strtotime($melding['startdatum'])) / (60*60*24) + 1;
                                        echo floor($dagen);
                                        ?>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo h($melding['reden']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($melding['vervanger_geregeld']): ?>
                                            <span class="vervanger-badge vervanger-ja">✓ Ja</span>
                                            <?php if ($melding['vervanger_naam']): ?>
                                                <br><small><?php echo h($melding['vervanger_naam']); ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="vervanger-badge vervanger-nee">✗ Nee</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo h($melding['status']); ?>">
                                            <?php echo ucfirst(h($melding['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDatumTijd($melding['gemeld_op']); ?></td>
                                    <td>
                                        <div class="actions">
                                            <button onclick="openStatusModal(<?php echo $melding['id']; ?>, '<?php echo h($melding['status']); ?>')" 
                                                    class="btn btn-sm btn-status" title="Status wijzigen">
                                                📝
                                            </button>
                                            <button onclick="viewDetails(<?php echo $melding['id']; ?>)" 
                                                    class="btn btn-sm btn-edit" title="Details bekijken">
                                                👁️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <h3>Geen ziekmeldingen gevonden</h3>
                        <p>Er zijn geen ziekmeldingen die voldoen aan de geselecteerde criteria.</p>
                        <a href="nieuwe_melding.php" class="btn" style="margin-top: 20px;">➕ Nieuwe Melding Aanmaken</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Status Wijzig Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h3>Status Wijzigen</h3>
                <p>Wijzig de status van de ziekmelding</p>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="melding_id" id="modal_melding_id">
                
                <div style="margin-bottom: 20px;">
                    <label for="new_status" style="display: block; margin-bottom: 10px; font-weight: 600;">Nieuwe Status:</label>
                    <select name="new_status" id="new_status" style="width: 100%; padding: 10px; border: 2px solid #e9ecef; border-radius: 8px;">
                        <option value="actief">Actief</option>
                        <option value="hersteld">Hersteld</option>
                        <option value="verlengd">Verlengd</option>
                    </select>
                </div>
                
                <div style="text-align: center;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary" style="margin-right: 15px;">Annuleren</button>
                    <button type="submit" class="btn">Status Wijzigen</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openStatusModal(meldingId, currentStatus) {
            document.getElementById('modal_melding_id').value = meldingId;
            document.getElementById('new_status').value = currentStatus;
            document.getElementById('statusModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        function viewDetails(meldingId) {
            // Placeholder voor details functionaliteit
            alert('Details functionaliteit kan hier worden geïmplementeerd voor melding ID: ' + meldingId);
        }
        
        // Sluit modal als er buiten geklikt wordt
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        
        // Auto-submit form bij status wijziging (optioneel)
        document.getElementById('status').addEventListener('change', function() {
            if (this.value !== '') {
                this.form.submit();
            }
        });
    </script>
</body>
</html>