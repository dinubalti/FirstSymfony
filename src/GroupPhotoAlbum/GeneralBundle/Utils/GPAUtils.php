<?php

namespace GroupPhotoAlbum\GeneralBundle\Utils;

use Symfony\Component\HttpFoundation\JsonResponse;

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
    
    public static function jsonResponse($type, $message) {
        $result = array(
            "result" => $type,
            "message" => $message
        );
        $response = new JsonResponse();
        $response->setData($result);
        return $response;
    }
}
