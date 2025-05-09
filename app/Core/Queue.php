<?php
namespace Rapulo\Core;

class Queue {
    private static $queueDir;

    public static function init($dir) {
        if (!is_writable($dir)) {
            throw new \Exception("Queue directory '$dir' is not writable");
        }
        self::$queueDir = $dir;
    }

    public static function push($job, $data) {
        $id = uniqid();
        $task = serialize(['job' => $job, 'data' => $data]);
        $file = self::$queueDir . '/' . $id . '.job';
        if (!@file_put_contents($file, $task)) {
            throw new \Exception("Failed to write queue task to '$file'");
        }
    }

    public static function process() {
        $files = glob(self::$queueDir . '/*.job');
        foreach ($files as $file) {
            $task = @unserialize(file_get_contents($file));
            if ($task === false) {
                @unlink($file);
                continue;
            }
            try {
                $job = new $task['job']();
                $job->handle($task['data']);
                @unlink($file);
            } catch (\Exception $e) {
                $logFile = __DIR__ . '/../../storage/logs/app.log';
                $timestamp = date('Y-m-d H:i:s');
                file_put_contents($logFile, "[$timestamp] Queue error: " . $e->getMessage() . "
", FILE_APPEND);
            }
        }
    }
}