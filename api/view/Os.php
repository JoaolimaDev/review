<?php 
namespace view;

require_once("api/model/DAO.php");

use model\Sql;


class Os 
{


    public static function Status_Os(string $db) :void
    {
        
    
        $sql = Sql::select("SELECT id_os_ponto, id_status, datahora, observacao, rua, bairro, latitude, longitude, OD.protocolo FROM `ordem_servico_ponto_status` as O JOIN ordem_servico_ponto as A ON O.id_os_ponto = A.ordem_servico_id 
        JOIN pontos as P on A.ponto_id = P.id JOIN ordem_servicos as OD on A.protocolo = OD.protocolo",$db);

            

            http_response_code(200);
            echo json_encode([
            'Sucesso' => 1,
            'Dados' => $sql
            ]);

            exit;
        
    }
}


?>