<?php 

ob_start();
session_start();

require __DIR__ . "/vendor/autoload.php";

// unset($_SESSION["userLogin"]);
if (empty($_SESSION["userLogin"])) {
    echo "<h1>Bem vindo ao login do Google</h1>";
    
    $google = new \League\OAuth2\Client\Provider\Google (GOOGLE);
    $authUrl = $google->getAuthorizationUrl();
    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRING);   
    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRING);

    if($error){
        echo "<h3>Você precisa autorizar para continuar!</h3>";
    }
    if($code){
        $token = $google->getAccessToken("authorization_code", [
            "code" => $code
        ]);
    
        $_SESSION["userLogin"] = serialize($google->getResourceOwner($token));
        header("Location: " . GOOGLE["redirectUri"]);
        exit();
    }

    echo "<a title='Logar com o Google' href='{$authUrl}'>Logar no Google</a>";

} else {
  
    $user = unserialize($_SESSION["userLogin"]);

    echo "<h1>Bem Vindo {$user->getName()}</h1>";

    echo "<a title='Sair' href='?off=true'>Sair</a>";
    $off = filter_input( INPUT_GET, "off", FILTER_VALIDATE_BOOLEAN);
    if ($off) {
        unset($_SESSION["userLogin"]);
        header("Location: " . GOOGLE["redirectUri"]);
    }

}

ob_end_flush();