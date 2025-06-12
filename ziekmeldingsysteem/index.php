<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ziekmeldsysteem Docenten</title>
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
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
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
            transform: translateY(-2px);
        }
        
        .nav a.active {
            background: #2c3e50;
            border-bottom-color: #e74c3c;
        }
        
        .content {
            padding: 40px;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .card-icon {
            font-size: 3em;
            margin-bottom: 15px;
            color: #3498db;
        }
        
        .card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .card p {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
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
        
        .btn-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .btn-warning:hover {
            box-shadow: 0 10px 20px rgba(243, 156, 18, 0.3);
        }
        
        .quick-stats {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .quick-stats h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .stat-item {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%);
            border-radius: 10px;
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #e74c3c;
            display: block;
        }
        
        .stat-label {
            color: #2c3e50;
            font-weight: 600;
            margin-top: 10px;
        }
        
        .recent-activity {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .recent-activity h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .activity-item {
            padding: 15px;
            border-left: 4px solid #3498db;
            margin-bottom: 15px;
            background: #f8f9fa;
            border-radius: 0 10px 10px 0;
        }
        
        .activity-item:last-child {
            margin-bottom: 0;
        }
        
        .activity-date {
            color: #7f8c8d;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        
        .activity-text {
            color: #2c3e50;
            font-weight: 500;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
            .nav {
                flex-direction: column;
            }
            
            .content {
                padding: 20px;
            }
            
            .dashboard {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php
    // Inclusie van benodigde bestanden
    require_once 'config.php';
    require_once 'classes.php';
    
    // Instantiëren van klassen
    $ziekmeldingObj = new Ziekmelding();
    $docentObj = new Docent();
    
    // Haal statistieken op
    $actieveZiekmeldingen = $ziekmeldingObj->getActieveZiekmeldingen();
    $alleZiekmeldingen = $ziekmeldingObj->getAllZiekmeldingen();
    $alleDocenten = $docentObj->getAllDocenten();
    ?>
    
    <div class="container">
        <div class="header">
            <h1>🏥 Ziekmeldsysteem</h1>
            <p>Centraal systeem voor docent ziekmeldingen</p>
        </div>
        
        <nav class="nav">
            <a href="index.php" class="active">🏠 Dashboard</a>
            <a href="nieuwe_meldingen.php">➕ Nieuwe Melding</a>
            <a href="overzicht.php">📋 Overzicht</a>
            <a href="docenten.php">👥 Docenten</a>
        </nav>
        
        <div class="content">
            <!-- Dashboard Cards -->
            <div class="dashboard">
                <div class="card">
                    <div class="card-icon">➕</div>
                    <h3>Nieuwe Ziekmelding</h3>
                    <p>Meld een nieuwe ziekte van een docent aan in het systeem. Vul alle benodigde gegevens in voor een complete registratie.</p>
                    <a href="nieuwe_meldingen.php" class="btn btn-success">Melding Aanmaken</a>
                </div>
                
                <div class="card">
                    <div class="card-icon">📋</div>
                    <h3>Overzicht Meldingen</h3>
                    <p>Bekijk alle ziekmeldingen in een overzichtelijke lijst. Filter op status en zoek specifieke meldingen.</p>
                    <a href="overzicht.php" class="btn">Bekijk Overzicht</a>
                </div>
                
                <div class="card">
                    <div class="card-icon">👥</div>
                    <h3>Docenten Beheer</h3>
                    <p>Beheer de lijst van docenten, voeg nieuwe docenten toe en wijzig bestaande gegevens.</p>
                    <a href="docenten.php" class="btn">Beheer Docenten</a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="recent-activity">
                <h2>🕐 Recente Activiteit</h2>
                <?php if (!empty($alleZiekmeldingen)): ?>
                    <?php 
                    // Sorteer op datum (nieuwste eerst) en toon laatste 5
                    usort($alleZiekmeldingen, function($a, $b) {
                        return strtotime($b['gemeld_op']) - strtotime($a['gemeld_op']);
                    });
                    $recenteMeldingen = array_slice($alleZiekmeldingen, 0, 5);
                    ?>
                    <?php foreach ($recenteMeldingen as $melding): ?>
                        <div class="activity-item">
                            <div class="activity-date"><?php echo formatDatumTijd($melding['gemeld_op']); ?></div>
                            <div class="activity-text">
                                <strong><?php echo h($melding['docent_naam']); ?></strong> 
                                heeft zich ziek gemeld vanaf <?php echo formatDatum($melding['startdatum']); ?>
                                <?php if ($melding['einddatum']): ?>
                                    tot <?php echo formatDatum($melding['einddatum']); ?>
                                <?php endif; ?>
                                - Status: <strong><?php echo ucfirst(h($melding['status'])); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="activity-item">
                        <div class="activity-text">Geen recente activiteit gevonden.</div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Actieve Ziekmeldingen -->
            <?php if (!empty($actieveZiekmeldingen)): ?>
            <div class="recent-activity" style="margin-top: 30px;">
                <h2>🚨 Actieve Ziekmeldingen</h2>
                <?php foreach ($actieveZiekmeldingen as $melding): ?>
                    <div class="activity-item" style="border-left-color: #e74c3c;">
                        <div class="activity-date">
                            Ziek sinds: <?php echo formatDatum($melding['startdatum']); ?>
                            <?php if ($melding['dagen_ziek'] > 0): ?>
                                (<?php echo $melding['dagen_ziek']; ?> dagen)
                            <?php endif; ?>
                        </div>
                        <div class="activity-text">
                            <strong><?php echo h($melding['docent_naam']); ?></strong> 
                            - <?php echo h($melding['afdeling']); ?>
                            <?php if ($melding['vervanger_geregeld']): ?>
                                <br>Vervanger: <?php echo h($melding['vervanger_naam']); ?>
                            <?php else: ?>
                                <br><em>Geen vervanger geregeld</em>
                            <?php endif; ?>
                            <?php if ($melding['reden']): ?>
                                <br>Reden: <?php echo h($melding['reden']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 Ziekmeldsysteem | Ontwikkeld voor Opdracht 18A</p>
    </div>
</body>
</html>