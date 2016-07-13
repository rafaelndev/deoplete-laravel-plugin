<?php

namespace Laravel;

/**
 * RouteFilter
 */

use Symfony\Component\Finder\Finder;
use hanneskod\classtools\Iterator\ClassIterator;
use Monolog\Logger;

class RouteFilter
{
    const HTTP_FOLDER = "/app/Http";
    const CONTROLLER_NAMESPACE = "App\Http\Controllers";
    const CACHE_FILE = "/deoplete-laravel.cache";

    private $finder;
    private $controllerIter;
    private $root;
    private $logger;
    private $class_list;

    function __construct($root, Logger $logger)
    {
        $this->root = $root;
        $this->finder = new Finder();
        $this->logger = $logger;
        $this->class_list = $this->finder->in($this->root . self::HTTP_FOLDER);

        $this->controllerIter = new ClassIterator($this->class_list);

        // Enable reflection by autoloading found classes
        $this->controllerIter->enableAutoloading();
    }

    /**
     * Método para buscar a lista de rotas
     *
     * @return array()
     **/
    private function getRouteList()
    {
        $route_list = [];
        $controllerList = $this->getControllerList();
        // Busca todas classes no namespace "App\Http\Controllers"
        foreach ($controllerList as $class) {
            // Instancia a classe com o ReflectionClass para buscar suas informações.
            $class = new \ReflectionClass($class->getName());
            // Caso a classe for subclasse de Controller..
            if ($class->isSubclassOf(\App\Http\Controllers\Controller::class)) {
                // Buscando os metodos publicos da classe
                $class_methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
                // Listando os metodos da classe
                foreach ($class_methods as $method) {
                    $route = "";
                    if ($method->class == $class->getName()) {
                        if ($method->name !== "__construct") {
                            $route = $method->class . '@' . $method->name . "\n";
                            $route = str_replace("App\Http\Controllers\\", "", $route);
                        }
                    }

                    if (strlen($route) > 0) {
                        array_push($route_list, $route);
                    }
                }
            }
        }

        // Tentar atualizar o arquivo de cache.
        $this->updateCache($route_list);

        return $route_list;
    }

    /**
     * Método para atualizar o arquivo de cache
     *
     * @return void
     **/
    private function updateCache($route_list)
    {
        $cached_routes = [];
        $cached_classes = [];

        // Verificar o cache de rotas, caso algum arquivo de controller for modificado, refaz o arquivo.
        if (file_exists($this->root . self::CACHE_FILE)) {
            $s = file_get_contents($this->root . self::CACHE_FILE);
            $file_cache = json_decode($s, true);
            $cached_classes = $file_cache[0]['classes'];
        }

        $classes = [];
        foreach ($this->class_list as $class) {
            array_push($classes, ['file' => $class->getRealPath(), 'date' => filemtime($class->getRealPath())]);
        }

        $class_cache = [];
        array_push($class_cache, ['classes' => $classes, 'routes' => $route_list]);

        if (file_exists($this->root . self::CACHE_FILE)) {
            unlink($this->root . self::CACHE_FILE);
        }

        $class_cache_json = json_encode($class_cache);
        file_put_contents($this->root . self::CACHE_FILE, $class_cache_json);
    }

    /**
     * Método para buscar as rotas dependendo do filtro $base do vim
     *
     * @return string
     **/
    public function getRoutes($base)
    {
        $routes = "";
        $route_list = $this->getRouteList();
        foreach ($route_list as $route) {
            if (strlen($base) == 0 || strpos(addslashes($route), addslashes($base)) !== false) {
                $routes = $routes . $route;
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
