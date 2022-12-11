<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XML Books</title>
    <link href="/web/css/main.css" rel="stylesheet">
    <script src="/web/js/main.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <input type="button" value="Import Books" onclick="window.location='?action=import'" />

        <form method="get" action="">
            <input type="hidden" name="action" value="search" />
            <input type="text" name="param" placeholder="Search for author" value="<?=(!empty($_GET['param'])?$_GET['param']:'')?>" />
            <input type="submit" value="Search">
        </form>

        <?php
        if(!empty($searchResults)) {
            foreach ($searchResults as $i=>$record) {
                loadTemplate('bookRow', ['record' => $record, 'i' => $i]);
            }
        }
        ?>


    </div>
</div>
</body>
</html>