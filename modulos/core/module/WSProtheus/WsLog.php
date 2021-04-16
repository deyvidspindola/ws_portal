<?php

namespace module\WSProtheus;

class WsLog {

    public function __construct() {
        parent::__construct();
    }

    public static function insertWsLog($dataLog) {

        global $conn;
        $usuario_id = $dataLog['usuario_id'];
        $url_request = $dataLog['url_request'];
        $json = $dataLog['json'];

        $sql = "INSERT INTO wsprotheus_intranet_log(usuario_id, url_request, json) VALUES($usuario_id, '$url_request' , '$json') RETURNING id";
        
		$res = pg_query($conn, $sql);
        if (!$res) {
            return false;
        }else{
            $wsprotheus_intranet_log_id = pg_fetch_array($res);
            return $wsprotheus_intranet_log_id[id];
        }
    }

}
