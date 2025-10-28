<?php 

namespace App\HostingModule;

use InvalidArgumentException; 
use App\HostingModule\Server\EmptyServer;

class HostingManager{
    
    const DEBUG = false;
    protected static $instance;

    public static function init($server = null){  
        
        if(is_object($server)){
            $server = $server->getType;
        }

        $class = __NAMESPACE__ . '\\Server\\' . ucfirst($server);

        if(self::DEBUG){
            if (!class_exists($class)) {
                throw new InvalidArgumentException("Invalid server: $server");
            }
        }else{
            if (!class_exists($class)) {
                self::$instance = new EmptyServer();
                return new self();
            }
        }

        self::$instance = new $class();
        return new self();
    }

    public function __call($method, $arguments){

        $data = @$arguments[0];

        if(self::DEBUG){
            if (!self::$instance) {
                throw new InvalidArgumentException("No server initialized.");
            }
    
            if (!method_exists(self::$instance, $method)) {
                throw new InvalidArgumentException("Method $method is not available for server: " . get_class(self::$instance));
            }
        }

        return self::$instance->$method($data);
    }
}