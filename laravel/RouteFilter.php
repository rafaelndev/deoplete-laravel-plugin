<?php

namespace Laravel;

/**
 * RouteFilter
 */

use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;

class RouteFilter
{
    const HTTP_FOLDER = "/app/Http";
    const CONTROLLER_NAMESPACE = "App\Http\Controllers";

    private $finder;
    private $controllerIter;
    private $root;

    function __construct($root, $base)
    {
        $this->root = $root;
        $this->base = $base;
        $this->finder = new Finder();
        $this->controllerIter = new ClassIterator($this->finder->in($this->root . self::HTTP_FOLDER));

        // Enable reflection by autoloading found classes
        $this->controllerIter->enableAutoloading();
    }

    /**
     * Método para buscar a lista de rotas
     *
     * @return array()
     **/
    public function getRouteList()
    {
        $route_list = [];
        $controllerList = $this->getControllerList();
        // Busca todas classes no namespace "App\Http\Controllers"
        foreach ($controllerList as $class) {
            // Instancia a classe com o ReflectionClass para buscar suas informações.
            $class = new \ReflectionClass($class->getName());
            array_push($route_list, $this->filterControllerRoute($class));
        }

        return $route_list;
    }

    /**
     * Método para filtrar a lista de rotas e formatar para o padrão do Laravel
     *
     * return string
     **/
    private function filterControllerRoute(\ReflectionClass $class)
    {
        $routes = "";
        // Caso a classe for subclasse de Controller..
        if ($class->isSubclassOf(\App\Http\Controllers\Controller::class)) {
            // Buscando os metodos publicos da classe
            $class_methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
            // Listando os metodos da classe
            foreach ($class_methods as $method) {
                if($method->class == $class->getName()){
                    if ($method->name !== "__construct") {
                        $route = $method->class . '@' . $method->name . "\n";
                        $route = str_replace("App\Http\Controllers\\", "", $route);
                        if (strlen($this->base) == 0 || strpos(addslashes($route), addslashes($this->base)) !== false) {
                            $routes = $routes . $route;
                        }
                    }
                }
            }
        }

        return $routes;
    }


    /**
     * Método para buscar a lista de classes correspondentes ao namespace do controller
     *
     * @return array()
     **/
    private function getControllerList()
    {
        return $this->controllerIter->inNamespace(self::CONTROLLER_NAMESPACE);
    }

}
