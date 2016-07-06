<?php

    $loader = require __DIR__ . '/vendor/autoload.php';

    $root   = $argv[1];
    $base   = $argv[2];
    $classLoader = require $root . "/vendor/autoload.php";

    use Symfony\Component\Finder\Finder;
    use hanneskod\classtools\Iterator\ClassIterator;

    $finder = new Finder();
    $iter = new ClassIterator($finder->in($root . '/app/Http/'));

    // Enable reflection by autoloading found classes
    $iter->enableAutoloading();
    // Busca todas classes no namespace "App\Http\Controllers"
    foreach ($iter->inNamespace('App\Http\Controllers') as $class) {
        // Instancia a classe com o ReflectionClass para buscar suas informações.
        $new_class = new ReflectionClass($class->getName());
        // Caso a classe for subclasse de Controller..
        if ($new_class->isSubclassOf(App\Http\Controllers\Controller::class)) {
            // Buscando os metodos publicos da classe
            $class_methods = $new_class->getMethods(ReflectionMethod::IS_PUBLIC);
            // Listando os metodos da classe
            foreach ($class_methods as $method) {
                if($method->class == $class->getName()){
                    if ($method->name !== "__construct") {
                        $route = $method->class . '@' . $method->name . "\n";
                        $route = str_replace("App\Http\Controllers\\", "", $route);
                        if (strlen($base) == 0 || strpos(addslashes($route), addslashes($base)) !== false) {
                            echo $route;
                        }
                    }
                }
            }
        }
    }
