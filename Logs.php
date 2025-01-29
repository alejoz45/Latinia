<?php
class Logger {
    private $logDirectory;
    private $maxFileSize;

    public function __construct($logDirectory = "logs", $maxFileSize = 31457280) { // 30 MB
        $this->logDirectory = $logDirectory;
        $this->maxFileSize = $maxFileSize;
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0777, true);
        }
    }

    private function getLogFile($level) {
        $filePath = "{$this->logDirectory}/{$level}.log";
        if (file_exists($filePath) && filesize($filePath) >= $this->maxFileSize) {
            rename($filePath, "{$this->logDirectory}/{$level}_" . date("Y-m-d_H-i-s") . ".log");
        }
        return $filePath;
    }

    public function log($level, $message, $branch = "General") {
        $filePath = $this->getLogFile($level);
        $timestamp = date("Y-m-d H:i:s");
        $formattedMessage = "[$timestamp] [$branch] $message\n";
        file_put_contents($filePath, $formattedMessage, FILE_APPEND);
    }

    public function info($message, $branch = "General") {
        $this->log("INFO", $message, $branch);
    }

    public function trace($message, $branch = "General") {
        $this->log("TRACE", $message, $branch);
    }
}

// Ejemplo de uso
$logger = new Logger();
$logger->info("Consulta de productos realizada exitosamente.", "Fábrica");
$logger->trace("Error al conectar con la base de datos.", "Triángulo");
?>
