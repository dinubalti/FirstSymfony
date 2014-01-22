<?php

namespace GroupPhotoAlbum\GeneralBundle\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;
use \GroupPhotoAlbum\GeneralBundle\Service\LogFileService;

/**
 * GPAUtils
 *
 * @author dguzun
 */
class GPAUtils {
    
    /**
     * verify if id is valid
     * 
     * @param type $id
     * @return boolean
     */
    public static function isValidId($id) {
        if ($id != null && is_numeric($id) && $id > 0) {
            return true;
        }
        return false;
    }
    
    /**
     * return json response
     * 
     * @param type $type
     * @param type $message
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function jsonResponse($type, $message='') {
        $result = array(
            "result" => $type,
            "message" => $message
        );
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * 
     * return an object as JsonResponse
     * 
     * @param type $result
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public static function objToJsonResponse($result) {
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
    
    /**
     * log error into file
     * 
     * @param type $msg
     * @param type $exc
     */
    public static function logError($msg, $exc = null) {
        if ($exc != null) {
            LogFileService::logFile('logFile.log', $msg.' : '.$exc->getMessage());
        } else {
            LogFileService::logFile('logFile.log', $msg);
        }
    }
}
