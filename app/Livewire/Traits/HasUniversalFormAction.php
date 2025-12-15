<?php

namespace App\Livewire\Traits;

use App\Attributes\FieldInput;
use Illuminate\Http\UploadedFile;
use App\Attributes\ParameterIDRoute;
use App\Livewire\UniversalFormAction;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait HasUniversalFormAction
{       
    protected $form_data = [];
    public $urlAction = [];

    public function setUrlSaveData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = 'saveData("'.$routeName.'", '.$paramsJson.')';
        return $this->urlAction[$nameVariable];
    }

    public function setUrlLoadData($nameVariable, $routeName, array $params = [])
    {
        $paramsJson = json_encode($params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->urlAction[$nameVariable] = 'loadDataForm("'.$routeName.'", '.$paramsJson.')';
        return $this->urlAction[$nameVariable];
    }

    public function saveData(string $routeName, array $params = [])
    {
        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave();
        }

        if (method_exists($this, 'setterFormData')) {
            $this->setterFormData();
        } else {
            foreach ($this->getUniversalFieldInputs() as $key => $value) {
                $this->form_data[$value] = $this->{$value};
            }
        }

        // Proses form_data secara rekursif untuk mencari dan convert file
        $result = $this->processFilesRecursively($this->form_data);
        $this->form_data = $result['data'];
        $listFile = $result['files'];

        $primaryKey = $this->getValidatePrimaryKey();
        if ($primaryKey) $this->form_data[$primaryKey] = $this->{$primaryKey};

        $payload = (new UniversalFormAction($this))->saveData([
            'route' => $routeName,
            'params' => $params,
            'formData' => $this->form_data
        ]);

        if (!isset($payload)) return;

        if (method_exists($this, 'afterSave')) {
            $this->afterSave($payload);
        }

        if ($payload->error === false) {
            foreach ($listFile as $key => $value) {
                if ($value instanceof TemporaryUploadedFile) {
                    $value->delete();
                } elseif (is_array($value) && isset($value['real_path'])) {
                    @unlink($value['real_path']);
                }
            }
        }
    }

    /**
     * Proses data secara rekursif untuk mencari dan convert TemporaryUploadedFile
     * 
     * @param mixed $data Data yang akan diproses (bisa array, object, atau value biasa)
     * @return array Array dengan keys: 'data' (data yang sudah di-convert), 'files' (list file untuk cleanup)
     */
    private function processFilesRecursively($data): array
    {
        $listFile = [];

        // Jika TemporaryUploadedFile, convert ke UploadedFile
        if ($data instanceof TemporaryUploadedFile) {
            $fileInfo = [
                'real_path' => $data->getRealPath(),
                'client_original_name' => $data->getClientOriginalName(),
                'mime_type' => $data->getMimeType(),
            ];
            $listFile[] = $data;
            return [
                'data' => $this->convertToUploadedFile($fileInfo),
                'files' => $listFile
            ];
        }

        // Jika array dengan struktur file (real_path, client_original_name, mime_type)
        // Cek apakah ini array file info (bukan array biasa yang kebetulan punya keys tersebut)
        if (
            is_array($data) &&
            array_key_exists('real_path', $data) &&
            array_key_exists('client_original_name', $data) &&
            array_key_exists('mime_type', $data) &&
            // Pastikan real_path adalah string path yang valid
            is_string($data['real_path']) &&
            // Pastikan ini bukan array nested (jika ada key numerik, berarti array biasa)
            !$this->isNumericArray($data)
        ) {
            $listFile[] = $data;
            return [
                'data' => $this->convertToUploadedFile($data),
                'files' => $listFile
            ];
        }

        // Jika array, proses secara rekursif
        if (is_array($data)) {
            $processedData = [];
            $allFiles = [];

            foreach ($data as $key => $value) {
                $result = $this->processFilesRecursively($value);
                $processedData[$key] = $result['data'];
                $allFiles = array_merge($allFiles, $result['files']);
            }

            return [
                'data' => $processedData,
                'files' => $allFiles
            ];
        }

        // Jika bukan file dan bukan struktur yang perlu diproses, return as-is
        return [
            'data' => $data,
            'files' => []
        ];
    }

    /**
     * Cek apakah array adalah numeric array (array dengan index numerik)
     * 
     * @param array $array Array yang akan dicek
     * @return bool True jika numeric array, false jika associative array
     */
    private function isNumericArray(array $array): bool
    {
        if (empty($array)) {
            return false;
        }
        
        // Cek apakah semua keys adalah numerik dan sequential
        $keys = array_keys($array);
        return array_keys($keys) === $keys;
    }

    /**
     * Convert array file info menjadi UploadedFile
     * 
     * @param array $data Array dengan keys: real_path, client_original_name, mime_type
     * @return UploadedFile
     */
    private function convertToUploadedFile(array $data): UploadedFile
    {
        $file = new UploadedFile(
            $data['real_path'],
            $data['client_original_name'],
            $data['mime_type'],
            null,
            true // penting! tandai sebagai "test file" agar laravel menerimanya
        );
        
        return $file;
    }

    public function loadDataForm(string $routeName, array $params = [])
    {
        if (method_exists($this, 'beforeLoadData')) {
            $this->beforeLoadData($params);
        }

        $payload = (new UniversalFormAction($this))->loadData([
            'route' => $routeName,
            'params' => $params,
        ]);
        
        if (method_exists($this, 'afterLoadData')) {
            $this->afterLoadData($payload);
        }
    }

    private function getUniversalFieldInputs(): array
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(FieldInput::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->all();
    }

    private function getUniversakPrimaryKey()
    {
        $reflection = new \ReflectionClass($this);

        return collect($reflection->getProperties())
            ->filter(fn($p) => $p->getAttributes(ParameterIDRoute::class))
            ->map(fn($p) => $p->getName())
            ->values()
            ->first();
    }
}