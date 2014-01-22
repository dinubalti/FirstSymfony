<?php
namespace GroupPhotoAlbum\GeneralBundle\Service;

/**
 * LogFileService class
 *
 */
class LogFileService
{

    /**
     * Log file
     *
     * @param string $file    file name
     * @param string $msgText message text
     *
     * @return string.
     */
    public static function logFile($file, $msgText)
    {
        try {
            $message = date('Y-m-d H:i:s') . " " . $msgText . "\n";
            file_put_contents($file, $message, FILE_APPEND | LOCK_EX);

            return 'success';
        } catch (\Exception $exc) {
            return 'error logFile';
        }
    }

}