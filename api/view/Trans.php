<?php 
namespace view;

require_once("api/model/DAO.php");


use model\Sql;

class Trans{


    public function __construct(string $menuop, string $db)
    {
        switch ($menuop) {
            case 'inserir':
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                    return $this->Trans($db);
   
                   }else{
                       $this->error();
                   }
                break;

                case 'update':
                    if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                        return $this->Trans_Update($db);
       
                       }else{
                           $this->error();
                       }
                break;

                case 'get':
                    if ($_SERVER['REQUEST_METHOD'] == "GET") {
                        return $this->Trans_get($db);
           
                    }else{
                        $this->error();
                    }

                break;

                case 'delete':
                    if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
                        return $this->Trans_Delete($db);
           
                    }else{
                        $this->error();
                    }

                break;
                
            
            default:

                $this->error();

                break;
        }
    }


    public function error() :void
    {
        http_response_code(403);
            echo json_encode([
                'Sucesso' => 0,
            'Mensagem' => 'Operação inválida!'
            ]);
        exit;
    }


    public function Trans_get($db)
    {
        $barramento = $_GET['barramento'] ? filter_input(INPUT_GET, 'barramento', FILTER_SANITIZE_SPECIAL_CHARS) : null;

        $sql = is_null($barramento) ? Sql::select("SELECT * FROM `transformadores`", $db) : Sql::select("SELECT * FROM `transformadores` WHERE barramento LIKE '%$barramento%'", $db);
    
        if (count($sql) > 0) {
        
            http_response_code(200);
            echo json_encode([
            'Sucesso' => 1,
            'Dados' => $sql
            ]);
            exit;

        }else{
            http_response_code(200);
            echo json_encode([
            'Sucesso' => 0,
            'Mensagem' => 'Nenhum registro encontrado, Por favor tente novamente!'
            ]);
            exit;
        }



    }

    public function Trans_Delete(string $db) :void
    {
        $data = json_decode(file_get_contents("php://input"));

        // válidação
        if (!isset($data->barramento)) {
          echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o barramento.']);
          exit;
        }

        if(count(Sql::select("SELECT barramento FROM `transformadores` WHERE barramento = :barramento", $db, array(':barramento' => $data->barramento))) == 0 )
        {
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Barramento inválido!']);
            exit;

        }

        Sql::query("DELETE FROM `transformadores` WHERE barramento = :barramento", $db, array(':barramento' => $data->barramento));


        http_response_code(200);
        echo json_encode([
        'Sucesso' => 1,
        'Mensagem' => 'Ponto Deletado com Sucesso!'
        ]);
        exit;
    }

    public function Trans_Update(string $db) :void
    {
        
        if ($_GET['barramento']) {
        
            $barramento = filter_input(INPUT_GET, 'barramento', FILTER_SANITIZE_SPECIAL_CHARS);

        }else{

            http_response_code(200);
            echo json_encode([
             'Sucesso' => 0,
             'Mensagem' => 'Insira o barramento do ponto!'
            ]);
            exit;
    
        }

        $data = json_decode(file_get_contents("php://input"));

        if(count(Sql::select("SELECT barramento FROM `transformadores` WHERE barramento = :barramento", $db, array(':barramento' => $barramento))) == 0 )
        {
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Barramento inválido!']);
            exit;

        }
        

        if (empty(trim($data->barramento))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o barramento!']);
            exit;
        endif;

        if (empty(trim($data->tombamento))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o tombamento!']);
            exit;
        endif;

        if (empty(trim($data->potencia))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a potencia!']);
            exit;
        endif;

        if (empty(trim($data->compartilhado))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Infome se o Transformador é compartilhado!']);
            exit;
        endif;

        if (empty(trim($data->cep))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o cep!']);
            exit;
        endif;

        if (empty(trim($data->logradouro))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o logradouro!']);
            exit;
        endif;

        if (empty(trim($data->distrito_id))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o distrito!']);
            exit;
        endif;

        if (empty(trim($data->ponto_ref))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o ponto de referencia!']);
            exit;
        endif;

        if (empty(trim($data->localizacao))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a localizaçao!']);
            exit;
        endif;

        if (empty(trim($data->lat)) || empty(trim($data->long))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a latitude e longitude!']);
            exit;
        endif;

        Sql::query("UPDATE `transformadores` SET barramento = :barramento, tombamento = :tombamento, potencia = :potencia, compartilhado = :compartilhado,
        cep = :cep, logradouro = :logradouro, latitude = :latitude, longitude = :longitude, ponto_de_referencia = :ponto_ref, distrito_id = :distrito_id, localizacao= :localizacao 
        WHERE barramento = :barramento_get", $db,
        array(':barramento' => $data->barramento, ':tombamento' => $data->tombamento, ':potencia' => $data->potencia, ':compartilhado' => $data->compartilhado, ':cep' => $data->cep,
        ':logradouro' => $data->logradouro, ':latitude' => $data->lat, ':longitude' => $data->long, ':ponto_ref' => $data->ponto_ref, ':distrito_id' => $data->distrito_id, 
        ':localizacao' => $data->localizacao, ':barramento_get' => $barramento));



        http_response_code(200);
        echo json_encode([
        'Sucesso' => 1,
        'Mensagem' => 'Ponto atualizado com Sucesso!'
        ]);
        exit;


    }

    public function Trans(string $db)
    {
        $data = json_decode(file_get_contents("php://input"));
        

        if (empty(trim($data->barramento))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o barramento!']);
            exit;
        endif;

        if (empty(trim($data->tombamento))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o tombamento!']);
            exit;
        endif;

        if (empty(trim($data->potencia))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a potencia!']);
            exit;
        endif;

        if (empty(trim($data->compartilhado))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Infome se o Transformador é compartilhado!']);
            exit;
        endif;

        if (empty(trim($data->cep))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o cep!']);
            exit;
        endif;

        if (empty(trim($data->logradouro))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o logradouro!']);
            exit;
        endif;

        if (empty(trim($data->distrito_id))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o distrito!']);
            exit;
        endif;

        if (empty(trim($data->ponto_ref))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o ponto de referencia!']);
            exit;
        endif;

        if (empty(trim($data->localizacao))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a localizaçao!']);
            exit;
        endif;

        if (empty(trim($data->lat)) || empty(trim($data->long))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a latitude e longitude!']);
            exit;
        endif;


        
        Sql::query("INSERT INTO `transformadores` (barramento, tombamento, potencia, compartilhado, cep, logradouro,
        latitude, longitude, ponto_de_referencia, distrito_id, localizacao) VALUES(:barramento, :tombamento, :potencia, :compartilhado,
        :cep, :logradouro, :latitude, :longitude, :ponto_ref, :distrito_id, :localizacao)", $db, 
        array(':barramento' => $data->barramento, ':tombamento' => $data->tombamento, ':potencia' => $data->potencia, ':compartilhado' => $data->compartilhado,
        ':cep' => $data->cep, ':logradouro' => $data->logradouro, ':latitude' => $data->lat, ':longitude' => $data->long,
        ':ponto_ref' => $data->ponto_ref, ':distrito_id' => $data->distrito_id, ':localizacao' => $data->localizacao));

        http_response_code(200);
        echo json_encode([
         'Sucesso' => 1,
         'Mensagem' => 'Tranformador cadastrado com Sucesso!'
        ]);
        exit;
    }
    
}
    



?>