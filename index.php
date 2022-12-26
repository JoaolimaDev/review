<?php


header("Access-Control-Allow-Origin: http://www.cidadeconectada.app.br");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");



function loader() : void
{
   
    spl_autoload_register(function($class){

        $prefix = str_replace("\\", DIRECTORY_SEPARATOR, $class);


        require_once("api/".$prefix.".php");


    });
}


require_once("vendor/autoload.php");

require_once("api/controller/Ctrl.php");


use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use view\Chave;
use controller\Ctrl;
use view\Os;




$app = AppFactory::create();


$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

/*
$app->setBasePath("/home/jcasolutions/public_html/newgip");
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true ,true);

*/
  

    $app->post('/client/{menuop}/', function (Request $request) {

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        loader();

        new view\Client($menuop);
            
    });
 


    $app->post('/client/auth-key', function () {

       loader();
    
        Chave::auth_key();
        
    });


    $app->post('/ponto/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);

        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Cad_Fotos($menuop, $_SERVER['DB']);
     
    });

    $app->get('/ponto/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
    
        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Cad_Fotos($menuop, $_SERVER['DB']);
    
    });

    $app->put('/ponto/{menuop}/', function (Request $request) {


        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
    
        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Cad_Fotos($menuop, $_SERVER['DB']);
    
    });

    $app->delete('/ponto/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
    
        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Cad_Fotos($menuop, $_SERVER['DB']);
    
    });



    $app->post('/trans/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);

        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Trans($menuop, $_SERVER['DB']);
     
    });


    $app->put('/trans/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);

        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Trans($menuop, $_SERVER['DB']);
     
    });

    $app->get('/trans/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
        
        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Trans($menuop, $_SERVER['DB']);
     
    });

    $app->delete('/trans/{menuop}/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
        
        loader();

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;

        
        new view\Trans($menuop, $_SERVER['DB']);
     
    });


    $app->get('/os/status/', function (Request $request) {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION']);
        
        loader();

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            Os::Status_Os($_SERVER['DB']);
        }else{
            http_response_code(403);
            echo json_encode([
            'Sucesso' => 0,
            'Mensagem' => 'Operação inválida!'
            ]);
        exit;
        }

     
    });


    $app->post('/app/user-action/{menuop}/', function (Request $request) {
    
        loader();

      $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;
    
      new view\Login($menuop, "jcasolutions_gip2021Admin");

    
    });


    $app->post('/user/cadastro', function () {

        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['DB']);
        
        loader();
        new view\User("cadastro", $_SERVER['DB']);

    });

    $app->post('/user/{menuop}/', function (Request $request) {
        
        Ctrl::Auth_call($_SERVER['HTTP_AUTHORIZATION'], $_SERVER['DB']);

        $menuop = is_string($request->getAttribute('menuop')) ? htmlspecialchars($request->getAttribute('menuop')) : null;
     
         loader();
         new view\User($menuop, "jcasolutions_gip2021Admin");
 
     });

$app->run();


