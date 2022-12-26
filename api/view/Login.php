<?php 

namespace view;

require_once("api/model/DAO.php");
require_once("api/controller/Ctrl.php");

use model\Sql;
use controller\Ctrl;


class Login
{

    public function __construct(string $opt, string $db)
    {

        switch ($opt) {

            case 'login':

             return $this->Login($db);
               
            break;

            case 'logout':

                return $this->Logout($db);
                  
            break;
            
            default:
               
                http_response_code(403);
                echo json_encode([
                    'Sucesso' => 0,
                'Mensagem' => 'Operação inválida!'
                ]);
                exit;
                
            break;
        }
        

    }

    public function Login(string $db) : void
    {
        $data = json_decode(file_get_contents("php://input"));

        if (empty(trim($data->user))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione o usuário!']);
            exit;
        endif;

        if (empty(trim($data->senha))):
            http_response_code(400);
            echo json_encode(['sucesso' => 0, 'mensagem' => 'Adicione a senha!']);
            exit;
        endif;

        $user = htmlspecialchars(strip_tags($data->user));
        $senha = htmlspecialchars(strip_tags($data->senha));
        
        $array_params = array(':username' => $user);

        $dados = Sql::select("SELECT id, password FROM `usuario` WHERE username = :username", $db, $array_params);

        
        if (count($dados) > 0){

            $resu = $dados[0];


            if($senha == $resu['password']){ // password verify
        
                
                $log = Ctrl::Token_call($user);

                Sql::query("UPDATE `usuario` SET id_log = :id_log
                WHERE id = :id", $db, array(':id_log' => $log,
                ':id' => $resu['id']));

            
               //$this->Backlog(date("Y-m-d h:i:sa"), "Logou". $user, $db); // reativar o backlog

               $exp = date("Y-m-d", strtotime("+1 day"));


               setcookie("session_id", $log, time()+86400,  "/", "", false, true);
                
                http_response_code(200);  //HTTP 200 OK
                echo json_encode([
                'Sucesso' => 1,
                'Mensagem' => 'Usuário autenticado - '. $user]);
                exit; 
                

            }else{

                http_response_code(400);
                echo json_encode([
                    'Sucesso' => 0,
                   'Mensagem' => 'Usuário ou senha inválidos!'
                ]);
                exit;
            
            }
        

        }else{
            http_response_code(400);
            echo json_encode([
                'Sucesso' => 0,
               'Mensagem' => 'Usuário ou senha inválidos!'
            ]);
            exit;
        }

    }

    
    private function Backlog($back_log, $user, $db)
    {

        $array_params = array(':user_log' => $back_log, ':user' => $user);

        Sql::query("INSERT INTO `user_log`(user_log, user) VALUES(:user_log, :user)",
        $db, $array_params);
            
    }

    public function Logout(string $db) : void
    {

        $id_log = trim($_SERVER['HTTP_AUTHORIZATION'], 'Bearer');

        
       $query = Sql::select("SELECT id, username FROM `usuario` WHERE id_log = :id_log", $db, array(':id_log' => 
          trim($id_log)));

     
        if(count($query) > 0){

            $dados = $query[0];

            session_regenerate_id();

            $id = password_hash(session_id(), PASSWORD_DEFAULT);
            $log = base64_encode($id.":".date("Y-m-d h:i:sa"));

            Sql::query("UPDATE `usuario` SET id_log = :id_log
            WHERE id = :id", $db, array(':id_log' => $log,
            ':id' => $dados['id']));
            
            session_unset();
            session_destroy();

            
           // $this->Backlog(date("Y-m-d h:i:sa"), "Logout ".$dados['user'], $db);

            http_response_code(200);
            echo json_encode([
                'sucesso' => 1,
                'mensagem' => 'Sessão encerrada!',
                'Session_status'=> session_status()
            ]);
            exit;


        }else{
            http_response_code(400);
            echo json_encode([
                'sucesso' => 0,
                'mensagem' => 'Não foi possível executar a operação.'
            ]);
            exit;
        }

    }
    
}

?>