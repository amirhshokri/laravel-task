<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class APIVersionControl
{
    public function handle(Request $request, Closure $next, $controllerName, $method): Response
    {
        $version = $request->route('version');

        $correctVersion = $this->getCorrectVersion($request, $controllerName, $method, $version);

        $request->route()->setParameter('version', $correctVersion);

        return $next($request);
    }

    private function getCorrectVersion($request, $controllerName, $method, $version)
    {
       if(!method_exists("App\Http\Controllers\API\\$version\\$controllerName", $method)) {
            $previousVersion = $this->getPreviousVersion($version);
            return $this->getCorrectVersion($request, $controllerName, $method, $previousVersion);
        }

        return $version;
    }

    private function getPreviousVersion($version)
    {
        preg_match("/([0-9]+)/", $version, $matches);
        $versionNumber = $matches[1];

        if($versionNumber <= 1)
            return $version;

        return "V".$versionNumber - 1;
    }
}
