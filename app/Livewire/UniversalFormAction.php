<?php

namespace App\Livewire;

use ReflectionMethod;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UniversalFormAction extends Component
{
    public $formData = [];

    #[On('universal-save-data')]
    public function saveData($params)
    {
        try {
            [$routeName, $this->formData] = [$params['route'], $params['formData']];

            [$controllerClass, $method] = $this->resolveRouteAction($routeName);
            $result = $this->callController($controllerClass, $method);
            
            $this->dispatch('after-action', [
                'callback' => $params['callback'] ?? null,
                'payload' => $result,
            ]);

        } catch (ValidationException $e) {
            dd('masuk sini');
            $this->setErrorBag($e->validator->errors());
        } catch (\Throwable $e) {
            dd('masuk sini 2');
            $this->addError('general', $e->getMessage());
        }
    }

    protected function resolveRouteAction($routeName): array
    {
        $route = Route::getRoutes()->getByName($routeName);

        // dd($route);

        if (!$route) {
            throw new \Exception("Route '{$routeName}' tidak ditemukan.");
        }

        $action = $route->getActionName();

        // Biasanya formatnya: App\Http\Controllers\UserController@store
        if (str_contains($action, '@')) {
            return explode('@', $action);
        }

        // Jika pakai single-action controller (__invoke)
        return [$action, '__invoke'];
    }

    protected function callController(string $controllerClass, string $method)
    {
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} tidak ditemukan.");
        }

        $instance = app($controllerClass);
        $reflector = new ReflectionMethod($controllerClass, $method);

        foreach ($reflector->getParameters() as $param) {
            $type = $param->getType()?->getName();

            if (is_subclass_of($type, FormRequest::class)) {
                $formRequest = app($type);
                $validated = Validator::make($this->formData, $formRequest->rules())->validate();
                $formRequest->merge($validated);

                return $instance->$method($formRequest);
            }
        }

        $request = request()->merge($this->formData);
        try {
            $result = $instance->$method($request); // ini akan jalankan validasi
            // jika validasi sukses, $result berisi $data dari validate()
            return $result;
        } catch (ValidationException $e) {
            // jika validasi gagal, bisa tangkap error di Livewire
            $this->setErrorBag($e->validator->errors());
            return null;
        }
    }

    public function render()
    {
        return view('livewire.universal-form-action');
    }
}
