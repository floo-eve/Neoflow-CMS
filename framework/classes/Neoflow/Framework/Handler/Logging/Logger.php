<?php

namespace Neoflow\Framework\Handler\Logging;

use \DateTime;
use \Exception;
use \Neoflow\Framework\App;
use \RuntimeException;

class Logger
{

    /**
     * App trait.
     */
    use \Neoflow\Framework\AppTrait;

    /**
     * @var string
     */
    private $logFilePath;

    /**
     * @var int
     */
    private $logLevelThreshold = LogLevel::DEBUG;

    /**
     * @var array
     */
    private $logLevels = [
        'EMERGENCY', 'ALERT', 'CRITICAL',
        'ERROR', 'WARNING', 'NOTICE',
        'INFO', 'DEBUG',
    ];

    /**
     * @var int
     */
    private $logLineCount = 0;

    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * Constructor.
     *
     * @param App $app
     *
     * @throws RuntimeException
     */
    public function __construct()
    {
        $config = $this->config();

        $logConfig = $config->get('logger');
        $this->logLevelThreshold = strtoupper($logConfig->get('level'));

        $logFileFolder = $config->getTempPath(DIRECTORY_SEPARATOR . 'logs');
        if (!is_dir($logFileFolder)) {
            mkdir($logFileFolder, 077, true);
        }

        $this->logFilePath = $logFileFolder . DIRECTORY_SEPARATOR . $logConfig->get('prefix') . date('Y-m-d') . '.' . $logConfig->get('extension');
        if (file_exists($this->logFilePath) && !is_writable($this->logFilePath)) {
            throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
        }

        $this->fileHandle = fopen($this->logFilePath, 'a+');
        flock($this->fileHandle, LOCK_UN);
        if (!$this->fileHandle) {
            throw new RuntimeException('The file could not be opened. Check permissions.');
        }
    }

    /**
     * Get log level.
     *
     * @return string
     */
    public function getLogLevel()
    {
        return $this->logLevelThreshold;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }

    /**
     * Log message.
     *
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->logLevelThreshold && array_search($this->logLevelThreshold, $this->logLevels) >= $level) {
            $message = $this->formatMessage($level, $message, $context);
            $this->write($message);
        }

        return $this;
    }

    /**
     * Log error message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function error($message, $context = array())
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Log warning message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function warning($message, $context = array())
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Log info message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function info($message, $context = array())
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Log debug message.
     *
     * @param string $message
     * @param array  $context
     *
     * @return Logger
     */
    public function debug($message, $context = array())
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Log exception as error.
     *
     * @param Exception $ex
     *
     * @return Logger
     */
    public function logException($ex)
    {
        return $this->error(get_class($ex) . ': ' . $ex->getMessage(), array(
                'code' => $ex->getCode(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'stack trace' => $ex->getTraceAsString(),));
    }

    /**
     * Writes a line to the log without prepending a status or timestamp.
     *
     * @param string $message Line to write to the log
     *
     * @return Logger
     */
    public function write($message)
    {
        if (null !== $this->fileHandle) {
            if (flock($this->fileHandle, LOCK_EX)) {
                if (fwrite($this->fileHandle, $message) === false) {
                    throw new RuntimeException('The file could not be written to. Check that appropriate permissions have been set.');
                } else {
                    $this->lastLine = trim($message);
                    ++$this->logLineCount;
                }
            }
            flock($this->fileHandle, LOCK_UN);
        }

        return $this;
    }

    /**
     * Get the log file path that the log is currently writing to.
     *
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * Get the last line logged to the log file.
     *
     * @return string
     */
    public function getLastLogLine()
    {
        return $this->lastLine;
    }

    /**
     * Formats the message for logging.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    private function formatMessage($level, $message, $context)
    {
        $message = "[{$this->getTimestamp()}] [{$this->logLevels[$level]}] {$message}";

        if (!empty($context)) {
            $message .= PHP_EOL . $this->indent($this->contextToString($context));
        }

        return $message . PHP_EOL;
    }

    /**
     * Gets the correctly formatted Date/Time for the log entry.
     *
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work correctly, so here it is.
     *
     * @return string
     */
    private function getTimestamp()
    {
        $originalTime = microtime(true);
        $micro = sprintf('%06d', ($originalTime - floor($originalTime)) * 1000000);
        $date = new DateTime(date('Y-m-d H:i:s.' . $micro, $originalTime));

        return $date->format('Y-m-d G:i:s.u');
    }

    /**
     * Takes the given context and coverts it to a string.
     *
     * @param array $context
     *
     * @return string
     */
    private function contextToString($context)
    {
        $export = '';
        foreach ($context as $key => $value) {
            $export .= "{$key}: ";
            $export .= preg_replace(array(
                '/=>\s+([a-zA-Z])/im',
                '/array\(\s+\)/im',
                '/^  |\G  /m',
                ), array(
                '=> $1',
                'array()',
                '    ',
                ), str_replace('array (', 'array(', var_export($value, true)));
            $export .= PHP_EOL;
        }

        return str_replace(array('\\\\', '\\\''), array('\\', '\''), rtrim($export));
    }

    /**
     * Indents the given string with the given indent.
     *
     * @param string $string
     * @param string $indent
     *
     * @return string
     */
    private function indent($string, $indent = '    ')
    {
        return $indent . str_replace("\n", "\n" . $indent, $string);
    }
}
