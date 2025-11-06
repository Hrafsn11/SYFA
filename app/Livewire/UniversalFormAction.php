<?php

namespace App\Livewire;

use ReflectionMethod;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UniversalFormAction
{
    public $formData = [];

    protected $instanceLivewire;

    public function __construct($instanceLivewire = null)
    {
        $this->instanceLivewire = $instanceLivewire;
    }

    public function saveData($params)
    {
        try {
            [$routeName, $params, $this->formData] = [$params['route'], $params['params'], $params['formData']];

            [$controllerClass, $method] = $this->resolveRouteAction($routeName);

            $callback = null;
            if (isset($params['callback'])) {
                $callback = $params['callback'];
                unset($params['callback']);
            }

            $result = $this->callController($controllerClass, $method, $params);
            
            $this->instanceLivewire->dispatch('after-action', [
                'callback' => $callback,
                'payload' => $result,
            ]);

        } catch (ValidationException $e) {
            $this->instanceLivewire->setErrorBag($e->validator->errors());
        } catch (\Throwable $e) {
            $this->instanceLivewire->dispatch('show-error', message: $e->getMessage());
            $this->instanceLivewire->addError('general', $e->getMessage());
        }
    }

    public function loadData($params)
    {
        try {
            [$routeName, $params] = [$params['route'], $params['params']];
            [$controllerClass, $method] = $this->resolveRouteAction($routeName);

            $callback = null;
            if (isset($params['callback'])) {
                $callback = $params['callback'];
                unset($params['callback']);
            }

            $result = $this->callController($controllerClass, $method, $params);

            $this->instanceLivewire->dispatch('after-get-data', [
                'callback' => $callback ?? null,
                'payload' => $result->original,
            ]);

        } catch (ValidationException $e) {
            $this->instanceLivewire->setErrorBag($e->validator->errors());
        } catch (\Throwable $e) {
            $this->instanceLivewire->dispatch('show-error', message: $e->getMessage());
            $this->instanceLivewire->addError('general', $e->getMessage());
        }
    }

    protected function resolveRouteAction($routeName): array
    {
        $route = Route::getRoutes()->getByName($routeName);

        if (!$route) {
            throw new \Exception("Route '{$routeName}' tidak ditemukan.");
        }

        $action = $route->getActionName();

        if (str_contains($action, '@')) {
            return explode('@', $action);
        }

        return [$action, '__invoke'];
    }

    protected function callController(string $controllerClass, string $method, array $params = [])
    {
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} tidak ditemukan.");
        }

        $instance = app($controllerClass);
        $reflector = new ReflectionMethod($controllerClass, $method);
        $arguments = [];     

        foreach ($reflector->getParameters() as $key => $param) {
            $type = $param->getType()?->getName();

            // Jika parameter adalah FormRequest
            if ($type && is_subclass_of($type, FormRequest::class)) {
                $baseRequest = Request::create('/', 'POST', $this->formData);
                $formRequest = $type::createFrom($baseRequest);
                $formRequest->setContainer(app());
                $formRequest->setRedirector(app('redirect'));
                $formRequest->validateResolved();

                $validated = $formRequest->validated();
                $formRequest->merge($validated);

                $arguments[] = $formRequest;

                continue;
            }

            // Jika parameter ada di $params (misal 'id' => 5)
            if (array_key_exists($param->getName(), $params)) {
                $arguments[] = $params[$param->getName()];
                continue;
            }

            // Jika punya default value
            if ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
                continue;
            }

            // Kalau tidak ditemukan apapun, kasih null
            $arguments[] = null;
        }

        try {
            return $reflector->invokeArgs($instance, $arguments);
        } catch (ValidationException $e) {
            $this->instanceLivewire->setErrorBag($e->validator->errors());
            return null;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
    // public function render()
    // {
    //     return view('livewire.universal-form-action');
    // }
}
