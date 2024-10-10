<?php declare(strict_types=1);

abstract class Page
{
    protected $_database;
    protected function __construct()
    {
        error_reporting(E_ALL);
        try {
            $this->_database = new PDO('sqlite:shop.db');
            $this->_database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Verbindung fehlgeschlagen: " . $e->getMessage();
        }
    }

    public function __destruct()
    {
        // to do: close database
        $this->_database = null;
    }

    protected function generatePageHeader(string $title = "", string $script = "", string $style = "", bool $autoreload = false): void
    {
        $title = htmlspecialchars($title);
        header("Content-type: text/html; charset=UTF-8");

        echo <<<EOT
        <!DOCTYPE html>
        <html lang="de">
            <head>
                <meta charset="UTF-8">
                <title>$title</title>
                <script src=$script></script>
                <link rel="stylesheet" href="$style" />
            </head>
            <body>
        EOT;
    }

    protected function generatePageFooter(): void
    {
        echo <<<EOT
            </body>
        </html>
        EOT;
    }

    protected function processReceivedData(): void
    {

    }
}