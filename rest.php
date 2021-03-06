<?php
// initialization script
require_once 'init.php';

use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\Token;



class HRestServer
{
    private $secret ;
    private $database ;
    private $issuer;
    private $expired;


    public function __construct()
    {
        $ini = parse_ini_file('app/config/application.ini', true);
        $this->secret = $ini['rest']['secret'];
        $this->database = $ini['rest']['database'];
        $this->issuer = $ini['rest']['issuer'];
        $this->expired = $ini['rest']['expired'];
    }

    public  function auth($login,$password){

        try{



            TTransaction::open($this->database);

            $users = SystemUser::authenticate($login,$password);



            if($users){
                $builder = new TokenBuilder();
                $expired =  strtotime($this->expired);
                $token = $builder->addPayload(['key' => 'user', 'value' => $users->id])
                    ->setSecret($this->secret)
                    ->setExpiration($expired)
                    ->setIssuer($this->issuer)
                    ->build();

                return $token;
            }else{
                throw new Exception("Usuario inexistente");
            }


            TTransaction::close();

        }catch(Exception $e){

            echo $e->getMessage();


        }
    }

    public  function authToken($token){

        try{

            if(empty($token)){
                throw new Exception("requisição não permitida \r\n");
            }

            $result = Token::validate($token, $this->secret);


            return $result;

        }catch(Exception $e){

            echo $e->getMessage();


        }
    }


    public  function run($request)
    {
        $json = file_get_contents('php://input');

        $obj = json_decode($json);



        $user  = isset($obj->username) ? $obj->username : '';
        $password  = isset($obj->password) ? $obj->password : '';

        $headers = apache_request_headers();

        if(empty($user)  || empty($password)  ){

            foreach ($headers as $header => $value) {

                if($header == 'Authorization'){

                    $data = explode(' ',$value);
                    $bearer = strtolower($data[0]);

                    if($bearer === 'bearer'){

                        $autToken = self::authToken($data[1]);

                        if(!$autToken){
                            return "token invalida";
                        }
                    }

                }

            }
        }



        if(!empty($user) || !empty($password)  ){



            $login = $this->auth($user,$password);


            if ($login  == false)
            {
                return json_encode( array('status' => 'error',
                    'data'   => _t('Permission denied')));
            }else{

                return json_encode( array('status' => 'sucess',
                    'data'   => $login));
                exit;
            }
        }



        $class   = isset($obj->class) ? $obj->class   : '';
        $method  = isset($obj->method) ? $obj->method : '';
        $response = NULL;


        try
        {
            if (class_exists($class))
            {

                if (get_parent_class($class) !== 'Adianti\Service\AdiantiRecordService' )
                {
                    return json_encode( array('status' => 'error',
                        'data'   => _t('Permission denied')));
                }


                if (method_exists($class, $method))
                {
                    $rf = new ReflectionMethod($class, $method);
                    if ($rf->isStatic())
                    {
                        $response = call_user_func(array($class, $method), json_decode($json,true));
                    }
                    else
                    {
                        $response = call_user_func(array(new $class($request), $method), json_decode($json,true));
                    }
                    return $response;
                }
                else
                {
                    $error_message = TAdiantiCoreTranslator::translate('Method ^1 not found', "$class::$method");
                    return json_encode( array('status' => 'error', 'data' => $error_message));
                }
            }
            else
            {
                $error_message = TAdiantiCoreTranslator::translate('Class ^1 not found', $class);
                return json_encode( array('status' => 'error', 'data' => $error_message));
            }
        }
        catch (Exception $e)
        {
            return json_encode( array('status' => 'error', 'data' => $e->getMessage()));
        }
    }


}



$rest =  new HRestServer();
print $rest->run($_REQUEST);
?>