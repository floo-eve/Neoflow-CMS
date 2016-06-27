<!DOCTYPE html>
<html lang="de">
    <head></head>

    <style>
        body {
            font-family: sans-serif;
        }

        #debugBar {
            display: none;
        }
    </style>
    <body>
        <?= $this->getBlock(1) ?>

        <?php if ($this->hasBlock(2)) { ?>
            <hr />
            <?= $this->getBlock(2) ?>
        <?php } ?>
    </body>
</html>
