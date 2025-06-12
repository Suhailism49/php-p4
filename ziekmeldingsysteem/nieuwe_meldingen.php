<?php
// Zet deze bovenaan, vóór <!DOCTYPE html>
/*
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'index.php') === false) {
    header("Location: index.php");
    exit;
}
*/
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Ziekmelding - Ziekmeldsysteem</title>
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
            max-width: 800px;
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
        
        .form-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
            margin-right: 15px;
        }
        
        .btn-secondary:hover {
            box-shadow: 0 10px 20px rgba(149, 165, 166, 0.3);
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
        
        .required {
            color: #e74c3c;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .nav {
                flex-direction: column;
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
    $docenten = $docentObj->getAllDocenten();
    
    $success = false;
    $error = '';
    $formData = [];
    
    // Verwerk formulier als het is verzonden
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $formData = $_POST;
        
        // Validatie
        $errors = [];
        
        if (empty($_POST['docent_id'])) {
            $errors[] = 'Selecteer een docent';
        }
        
        if (empty($_POST['startdatum'])) {
            $errors[] = 'Startdatum is verplicht';
        } elseif (!validateDate($_POST['startdatum'])) {
            $errors[] = 'Ongeldige startdatum';
        }
        
        if (!empty($_POST['einddatum']) && !validateDate($_POST['einddatum'])) {
            $errors[] = 'Ongeldige einddatum';
        }
        
        if (!empty($_POST['startdatum']) && !empty($_POST['einddatum'])) {
            if (strtotime($_POST['einddatum']) < strtotime($_POST['startdatum'])) {
                $errors[] = 'Einddatum kan niet voor startdatum liggen';
            }
        }
        
        if (empty($_POST['reden'])) {
            $errors[] = 'Reden voor ziekte is verplicht';
        }
        
        if (empty($errors)) {
            $data = [
                'docent_id' => $_POST['docent_id'],
                'startdatum' => $_POST['startdatum'],
                'einddatum' => $_POST['einddatum'],
                'reden' => $_POST['reden'],
                'vervanger_geregeld' => isset($_POST['vervanger_geregeld']),
                'vervanger_naam' => $_POST['vervanger_naam'],
                'opmerkingen' => $_POST['opmerkingen']
            ];
            
            if ($ziekmeldingObj->addZiekmelding($data)) {
                $success = true;
                $formData = []; // Reset form
            } else {
                $error = 'Er is een fout opgetreden bij het opslaan van de ziekmelding.';
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
    ?>
    
    <div class="container">
        <div class="header">
            <h1>➕ Nieuwe Ziekmelding</h1>
            <p>Registreer een nieuwe ziekmelding voor een docent</p>
        </div>
        
        <nav class="nav">
            <a href="index.php">🏠 Dashboard</a>
            <a href="nieuwe_melding.php" class="active">➕ Nieuwe Melding</a>
            <a href="overzicht.php">📋 Overzicht</a>
            <a href="docenten.php">👥 Docenten</a>
            <a href="statistieken.php">📊 Statistieken</a>
        </nav>
        
        <div class="content">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    ✅ Ziekmelding is succesvol aangemaakt!
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="docent_id">Docent <span class="required">*</span></label>
                        <select name="docent_id" id="docent_id" required>
                            <option value="">-- Selecteer een docent --</option>
                            <?php foreach ($docenten as $docent): ?>
                                <option value="<?php echo $docent['id']; ?>" 
                                        <?php echo (isset($formData['docent_id']) && $formData['docent_id'] == $docent['id']) ? 'selected' : ''; ?>>
                                    <?php echo h($docent['voornaam'] . ' ' . $docent['achternaam'] . ' - ' . $docent['afdeling']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="startdatum">Startdatum <span class="required">*</span></label>
                            <input type="date" name="startdatum" id="startdatum" 
                                   value="<?php echo isset($formData['startdatum']) ? h($formData['startdatum']) : date('Y-m-d'); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="einddatum">Einddatum (verwacht)</label>
                            <input type="date" name="einddatum" id="einddatum" 
                                   value="<?php echo isset($formData['einddatum']) ? h($formData['einddatum']) : ''; ?>">
                            <small style="color: #7f8c8d;">Laat leeg als onbekend</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reden">Reden voor ziekte <span class="required">*</span></label>
                        <textarea name="reden" id="reden" placeholder="Beschrijf kort de reden voor de ziekmelding..." required><?php echo isset($formData['reden']) ? h($formData['reden']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" name="vervanger_geregeld" id="vervanger_geregeld" 
                                   <?php echo (isset($formData['vervanger_geregeld']) && $formData['vervanger_geregeld']) ? 'checked' : ''; ?>>
                            <label for="vervanger_geregeld">Vervanger is geregeld</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="vervanger_naam">Naam vervanger</label>
                        <input type="text" name="vervanger_naam" id="vervanger_naam" 
                               placeholder="Naam van de vervanger (indien van toepassing)"
                               value="<?php echo isset($formData['vervanger_naam']) ? h($formData['vervanger_naam']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="opmerkingen">Aanvullende opmerkingen</label>
                        <textarea name="opmerkingen" id="opmerkingen" 
                                  placeholder="Eventuele aanvullende informatie..."><?php echo isset($formData['opmerkingen']) ? h($formData['opmerkingen']) : ''; ?></textarea>
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <a href="index.php" class="btn btn-secondary">↩️ Annuleren</a>
                        <button type="submit" class="btn">💾 Ziekmelding Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // JavaScript voor vervanger veld tonen/verbergen
        document.getElementById('vervanger_geregeld').addEventListener('change', function() {
            const vervangerNaam = document.getElementById('vervanger_naam');
            if (this.checked) {
                vervangerNaam.style.display = 'block';
                vervangerNaam.focus();
            } else {
                vervangerNaam.value = '';
            }
        });
        
        // Validatie voor datums
        document.getElementById('startdatum').addEventListener('change', function() {
            const startdatum = new Date(this.value);
            const einddatumField = document.getElementById('einddatum');
            const einddatum = new Date(einddatumField.value);
            
            if (einddatumField.value && einddatum < startdatum) {
                alert('Einddatum kan niet voor startdatum liggen');
                einddatumField.value = '';
            }
        });
        
        document.getElementById('einddatum').addEventListener('change', function() {
            const einddatum = new Date(this.value);
            const startdatum = new Date(document.getElementById('startdatum').value);
            
            if (this.value && einddatum < startdatum) {
                alert('Einddatum kan niet voor startdatum liggen');
                this.value = '';
            }
        });
    </script>
</body>
</html>