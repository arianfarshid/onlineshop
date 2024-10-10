<?php declare(strict_types=1);
// UTF-8 marker äöüÄÖÜß€
require_once './Page.php';

class Homepage extends Page
{
    protected function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    protected function getViewData():array
    {
        $data = array();
        $sql = "
        SELECT t.id, t.bezeichnung, t.artikelnummer, t.preis, b.bild_url
        FROM Taschen t
        LEFT JOIN Bilder b ON t.id = b.tasche_id
        ORDER BY t.id
    ";

        $stmt = $this->_database->prepare($sql);
        $stmt->execute();

        // Ergebnisse in ein assoziatives Array umwandeln
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if (!isset($data[$row['id']])) {
                $data[$row['id']] = [
                    'bezeichnung' => $row['bezeichnung'],
                    'artikelnummer' => $row['artikelnummer'],
                    'preis' => $row['preis'],
                    'bilder' => []
                ];
            }
            if ($row['bild_url'] !== null) {
                $data[$row['id']]['bilder'][] = $row['bild_url'];
            }
        }

        return $data;
    }

    protected function generateView(): void
    {
        $this->generatePageHeader('Homepage', 'homepage.js', 'homepage.css');

        $bags = $this->getViewData();

        echo <<< HEADER
        <section id="header-container"></section>
        HEADER;

        echo "<section class='content'>\n";

        foreach ($bags as $bag) {
            echo <<< ARTICLE
            <article class="bag">
                <section class="image-slider">
                    <section class="slider" id="slider-{$bag['artikelnummer']}">
            ARTICLE;

            foreach ($bag['bilder'] as $image) {
                echo "<img src='{$image}' alt='' class='slider-image' />";
            }

            echo <<< SLIDER
                    </section>
                    <button class='prev' onclick='moveSlide(-1, document.getElementById("slider-{$bag['artikelnummer']}"))'>❮</button>
                    <button class='next' onclick='moveSlide(1, document.getElementById("slider-{$bag['artikelnummer']}"))'>❯</button>
                </section>
                <h2>{$bag['bezeichnung']}</h2>
                <section id="article-info">
                <p>AN: {$bag['artikelnummer']}</p>
                <p id="price">{$bag['preis']}€</p>
                </section>
            </article>
        SLIDER;
        }

        echo "</section>\n";

        echo "<section id='footer-container'></section>\n";

        $this->generatePageFooter();
    }


    protected function processReceivedData():void
    {
        parent::processReceivedData();

    }

    public static function main():void
    {
        try {
            $page = new Homepage();
            $page->processReceivedData();
            $page->generateView();
        } catch (Exception $e) {
            header("Content-type: text/plain; charset=UTF-8");
            echo $e->getMessage();
        }
    }
}

Homepage::main();
