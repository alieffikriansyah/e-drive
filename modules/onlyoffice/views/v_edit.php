<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dokumen: <?= htmlspecialchars($doc->name) ?> - <?= Env::get('APP_NAME') ?></title>
    <script type="text/javascript" src="<?= rtrim($oo_url, '/') ?>/web-apps/apps/api/documents/api.js"></script>
    <style>
        html {
            height: 100%;
            width: 100%;
        }
        body {
            background: #f1f5f9;
            color: #1e293b;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        #placeholder {
            flex-grow: 1;
            width: 100%;
        }
        .header {
            background: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            height: 40px;
            flex-shrink: 0;
        }
        .btn-back {
            text-decoration: none;
            background: #1e293b;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-back:hover {
            background: #334155;
        }
        .doc-title {
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="header">
        <button onclick="window.close()" class="btn-back" style="cursor: pointer; border: none;">
            &larr; Tutup Editor
        </button>
        <div class="doc-title"><?= htmlspecialchars($doc->name . '.' . $doc->file_type) ?></div>
        <div style="width:100px;"></div>
    </div>
    
    <div id="placeholder"></div>
    
    <script>
        var config = <?= json_encode($config) ?>;
        var docEditor = new DocsAPI.DocEditor("placeholder", config);
    </script>
</body>
</html>
