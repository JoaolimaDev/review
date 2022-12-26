<?php 
namespace view;

require_once("api/model/DAO.php");

use model\Sql;

class Cad_Fotos
{


    public function __construct(string $menuop, string $db)
    {
        switch ($menuop) {
            case 'cad':
                if ($_SERVER['REQUEST_METHOD'] == "POST") {
                 return $this->Cad($db);

                }else{
                    $this->error();
                }

            break;

            case 'get':

                if ($_SERVER['REQUEST_METHOD'] == "GET") {
                   return $this->Cad_Read($db);
                }else{
                    $this->error();
                }
                
            break;

            case 'update':

                if ($_SERVER['REQUEST_METHOD'] == "PUT") {
                   return $this->Cad_update($db);
                }else{
                    $this->error();
                }
                
            break;

            case 'delete':

                if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
                   return $this->Cad_Delete($db);
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
    public function page($db)
    {
        $sql = Sql::select("SELECT count(*) FROM `pontos`", $db);

        foreach ($sql as $value) {
            
            $total = $value['count(*)'];
        }

        $limit = 20;

        $pages = ceil($total / $limit);

        $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default'   => 1,
                'min_range' => 1,
            ),
        )));

        $offset = ($page - 1)  * $limit;

        $query = Sql::select("SELECT * FROM `pontos` ORDER BY `id` DESC LIMIT $limit OFFSET $offset", $db);


        $result = ["Páginas" => $pages,
        "offset" => $query];


        return $result;
    }

    public function Cad_Read(string $db) : void
    {


        $barramento = $_GET['barramento'] ? filter_input(INPUT_GET, 'barramento', FILTER_SANITIZE_SPECIAL_CHARS) : null;

        $sql = is_null($barramento) ? $this->page($db) : Sql::select("SELECT * FROM `pontos` WHERE barramento LIKE '%$barramento%'", $db);

     
    
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

    public function Cad_Delete(string $db) :void
    {
        $data = json_decode(file_get_contents("php://input"));

        // válidação
        if (!isset($data->barramento)) {
          echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o barramento.']);
          exit;
        }

        if(count(Sql::select("SELECT barramento FROM `pontos` WHERE barramento = :barramento", $db, array(':barramento' => $data->barramento))) == 0 )
        {
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Barramento inválido!']);
            exit;

        }

        Sql::query("DELETE FROM `pontos` WHERE barramento = :barramento", $db, array(':barramento' => $data->barramento));


        http_response_code(200);
        echo json_encode([
        'Sucesso' => 1,
        'Mensagem' => 'Ponto Deletado com Sucesso!'
        ]);
        exit;

    }

    public function Cad_update($db) : void
    {
        $data = json_decode(file_get_contents("php://input")); 

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


        
        if(count(Sql::select("SELECT barramento FROM `pontos` WHERE barramento = :barramento", $db, array(':barramento' => $barramento))) == 0 )
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

        if (empty(trim($data->cep))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o cep!']);
            exit;
        endif;

        if (empty(trim($data->rua))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o rua!']);
            exit;
        endif;

        if (empty(trim($data->bairro))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o bairro!']);
            exit;
        endif;

        if (empty(trim($data->geocodigo))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o geocodigo!']);
            exit;
        endif;

        if (empty(trim($data->tipo_poste))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o tipo do poste!']);
            exit;
        endif;

        if (empty(trim($data->altura))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a altura do poste!']);
            exit;
        endif;

        if (empty(trim($data->lat)) || empty(trim($data->long))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a latitude e longitude!']);
            exit;
        endif;

      
       
        $imageData = $_FILES['image']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image']['tmp_name'])) : null;

        $imageData_2 = $_FILES['image_2']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image_2']['tmp_name'])) : null;

        $imageData_3 = $_FILES['image_3']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image_3']['tmp_name'])) : null;

        
        
        Sql::query("UPDATE `pontos` SET barramento = :barramento, cep = :cep, rua = :rua, bairro = :bairro, geocodigo = :geocodigo, 
        tipo_poste = :tipo_poste, altura = :altura, latitude = :latitude, longitude = :longitude, foto_1 = :foto_1, foto_2 = :foto_2, foto_3 = :foto_3 WHERE barramento = :barramento_get", $db,
        array(':barramento' => $data->barramento, ':cep' => $data->cep, ':rua' =>$data->rua, ':bairro'=>$data->bairro, ':geocodigo' => $data->geocodigo
        ,':tipo_poste' => $data->tipo_poste, ':altura' => $data->altura, ':latitude' => $data->latitude, ':longitude' => $data->longitude, ':foto_1' => 
        $imageData, ':foto_2' => $imageData_2, ':foto_3' => $imageData_3, ':barramento_get' => $barramento));


        
    

            http_response_code(200);
            echo json_encode([
            'Sucesso' => 1,
            'Mensagem' => 'Ponto atualizado com Sucesso!'
            ]);
            exit;

    


        

    }

    public function Cad(string $db) :void
    {
        
        $data = json_decode(file_get_contents("php://input")); 

        
        if (empty(trim($data->barramento))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o barramento!']);
            exit;
        endif;

        if (empty(trim($data->cep))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o cep!']);
            exit;
        endif;

        if (empty(trim($data->rua))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o rua!']);
            exit;
        endif;

        if (empty(trim($data->bairro))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o bairro!']);
            exit;
        endif;

        if (empty(trim($data->geocodigo))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o geocodigo!']);
            exit;
        endif;

        if (empty(trim($data->tipo_poste))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o tipo do poste!']);
            exit;
        endif;

        if (empty(trim($data->altura))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a altura do poste!']);
            exit;
        endif;

        if (empty(trim($data->lat)) || empty(trim($data->long))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a latitude e longitude!']);
            exit;
        endif;
        
        
        $imageData = $_FILES['image']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image']['tmp_name'])) : null;

        $imageData_2 = $_FILES['image_2']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image_2']['tmp_name'])) : null;

        $imageData_3 = $_FILES['image_3']['tmp_name'] ? base64_encode(file_get_contents($_FILES['image_3']['tmp_name'])) : null;
        



            
        Sql::query("INSERT INTO `pontos` (barramento, cep, rua, bairro, geocodigo, tipo_poste, altura, latitude, longitude, foto_1, foto_2, foto_3) 
        VALUES(:barramento, :cep, :rua, :bairro, :geocodigo, :tipo_poste, :altura, :latitude, :longitude, :foto_1, :foto_2, :foto_3)", $db, 
        array(':barramento' => $data->barramento, ':cep' => $data->cep, ':rua' => $data->rua, ':bairro' => $data->bairro, ':geocodigo' => $data->geocodigo,
        ':tipo_poste' => $data->tipo_poste, ':altura' => $data->altura, ':latitude' => $data->lat, ':longitude' => $data->long, ':foto_1' => $imageData,
        ':foto_2' => $imageData_2, ':foto_3' => $imageData_3));



        http_response_code(200);
        echo json_encode([
         'Sucesso' => 1,
         'Mensagem' => 'Ponto cadastrado com Sucesso!'
        ]);
        exit;

     

        
    }
}

?>