<?php

include_once 'REST.php';
include_once 'WB_ReviewParser.php';

/*
 * Class: REST
 * Process user requests to service.
 * Format: <api_url>?action=<action_name>&param1=val1&param2=val2 ....
 */
class API extends REST {

    public $data = "";

    public function __construct() {
        parent::__construct();              // Init parent contructor
    }

    /*
     * Public method for access api.
     * This method dynmically call the method based on the query string
     *
     */
    public function processApi() {

        if (count($_REQUEST) == 0) {
            $this->response('Error code 404, Method not found', 404);
            return false;
        }


        $func = $_REQUEST['action'];

        switch ($func) {

            case 'wb_get_review':
                array_shift($_REQUEST);
                $this->wb_get_review($_REQUEST);
                break;

            case 'wb_get_rating':
                array_shift($_REQUEST);
                $this->wb_get_rating($_REQUEST);
                break;

            default:
                $this->response("Error code 404, Method < $func > not found", 404);
                break;
        }

    }

    private function wb_get_review($params) {

        $reviewParser = new WB_ReviewParser();
        $res = $reviewParser->getItemReviews($params['wb_artikul']);
        $this->response($this->json($res), 200);

    }

    private function wb_get_rating($params) {

        $reviewParser = new WB_ReviewParser();
        $res = $reviewParser->getItemRating($params['wb_artikul']);
        $this->response($this->json($res), 200);

    }

    /*
     *  Encode array into JSON
    */
    private function json($data){
        if(is_array($data)){
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        }
    }
}

// Initiiate Library
/*
$api = new API;
$api->processApi();
*/

?>