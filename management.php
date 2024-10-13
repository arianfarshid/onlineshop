<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class Management extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getLastInsertedId()
    {
        $stmt = $this->_database->query("SELECT id FROM Taschen ORDER BY id DESC LIMIT 1");
        $lastInsertedId = $stmt->fetchColumn();
        return $lastInsertedId;
    }

    protected function getLastArtikelNummer(){
        $lastInsertedId = $this->getLastInsertedId();
        $stmt = $this->_database->query("SELECT artikelnummer FROM Taschen WHERE id = $lastInsertedId");
        $artikelnummer = $stmt->fetchColumn();
        return $artikelnummer;
    }

    protected function generateView():void
    {
        $lastArtikelNummer = $this->getLastArtikelNummer();
        $this->generatePageHeader('Management', 'management.js', 'management.css');
        echo "<section id='header-container'></section>";
        echo "<section class='content'>\n";
        echo <<< FORM
            <section class="row">
                <form class="column form-area" action="management.php" method="POST" accept-charset="UTF-8">
                    <section class="input-text">
                        <p>Bezeichnung des Produkts</p>
                        <input type="text" name="bezeichnung" placeholder="Bezeichnung..." required>
                    </section> 
                    <section class="input-text">
                        <p>Artikelnummer des Produkts</p>
                        <input type="text" name="artikelnummer" placeholder="Artikelnummer..." required>
                    </section>
                    <section class="input-text">
                        <p>Preis in Euro</p>
                        <input type="number" name="preis" placeholder="Preis..." min="0" step="0.01" required>
                    </section>
                    <input type="submit" onclick="addPicture()" name="send" id="send" value="Artikel hinzufügen">
                </form>
                <section class="column drag-and-drop-area">
                    <p id="addPics">Hier können Sie nun die Bilder hinzufügen</p>
                    <section id="drop-area">
                        Ziehe deine Bilder hier hinein <br> AN: {$lastArtikelNummer}
                    </section>
                    <section id="gallery"></section>
                </section>
            </section>
        FORM;
        echo "</section>\n";
        echo "<section id='footer-container'></section>";
        $this->generatePageFooter();
    }

    protected function processReceivedData():void
    {
        parent::processReceivedData();
        if (isset($_FILES['file'])) {
            $uploadDir = 'assets/taschen/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $file = $_FILES['file'];
            $fileName = basename($file['name']);
            $targetFile = $uploadDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $targetFile)) {

                }
            }

            $lastInsertedId = $this->getLastInsertedId();
            $sql = "INSERT INTO Bilder (tasche_id, bild_url) 
                    VALUES (:lastInsertedId, :targetFile)";
            $stmt = $this->_database->prepare($sql);
            $stmt->bindParam(':lastInsertedId', $lastInsertedId);
            $stmt->bindParam(':targetFile', $targetFile);
            $stmt->execute();
        }

        if(isset($_POST['bezeichnung']) && isset($_POST['artikelnummer']) && isset($_POST['preis'])) {
            $bezeichnung = $_POST['bezeichnung'];
            $artikelnummer = $_POST['artikelnummer'];
            $preis = $_POST['preis'];

            $sql = "INSERT INTO Taschen (bezeichnung, artikelnummer, preis) 
                    VALUES (:bezeichnung, :artikelnummer, :preis)";

            $stmt = $this->_database->prepare($sql);

            $stmt->bindParam(':bezeichnung', $bezeichnung);
            $stmt->bindParam(':artikelnummer', $artikelnummer);
            $stmt->bindParam(':preis', $preis);

            $stmt->execute();
        }
    }

    public static function main():void
    {
        try {
            $page = new Management();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Management::main();
