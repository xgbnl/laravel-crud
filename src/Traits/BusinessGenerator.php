<?php

namespace Xgbnl\Business\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Xgbnl\Business\Cache\Cacheable;
use Xgbnl\Business\Enum\GeneratorEnum;
use Xgbnl\Business\Repositories\Repositories;
use Xgbnl\Business\Services\BaseService;
use Xgbnl\Business\Utils\Fail;
use Xgbnl\Business\Utils\Helper;

/**
 * @property Repositories $repository
 * @property BaseService $service
 * @property Cacheable $cache
 */
trait BusinessGenerator
{
    private ?string $businessModel = null;

    public function __get(string $name)
    {
        return match ($name) {
            GeneratorEnum::REPOSITORY => $this->makeBusinessModel(GeneratorEnum::REPOSITORY),
            GeneratorEnum::SERVICE    => $this->makeBusinessModel(GeneratorEnum::SERVICE),
            GeneratorEnum::CACHE      => $this->makeBusinessModel(GeneratorEnum::CACHE, ['repositories' => $this->repository]),
        };
    }

    private function makeBusinessModel(string $business, array $params = []): BaseService|Repositories|Cacheable
    {
        $class = $this->checkBusiness($business);

        ['class' => $parentClass, 'type' => $type] = match ($business) {
            GeneratorEnum::REPOSITORY => ['class' => Repositories::class, 'type' => '仓库'],
            GeneratorEnum::SERVICE    => ['class' => BaseService::class, 'type' => '服务'],
            GeneratorEnum::CACHE      => ['class' => Cacheable::class, 'type' => '缓存'],
        };

        if (!is_subclass_of($class, $parentClass)) {
            Fail::throwFailException(message: '获取' . $type . '模型[ ' . $class . ' ]错误,必须继承: [' . $parentClass . ' ]');
        }

        try {
            return !empty($params) ? app($class, $params) : app($class);
        } catch (\Exception $e) {
            Fail::throwFailException(message: '实例化' . $type . '模型出错:[ ' . $e->getMessage() . ' ]');
        }
    }

    private function checkBusiness(?string $business): string
    {
        if (!is_null($this->businessModel)) {

            if (str_ends_with($this->businessModel, ucwords($business))) {
                return $this->businessModel;
            }

            $this->refreshBusinessModel();
        }

        $class = $this->getBusinessModel($business);

        if (!class_exists($class)) {

            $msg = match (true) {
                str_ends_with($class, 'Request')    => '验证',
                str_ends_with($class, 'Service')    => '服务',
                str_ends_with($class, 'Repository') => '仓库',
                str_ends_with($class, 'Cache')      => '缓存'
            };

            Fail::throwFailException($msg . '模型[ ' . $class . '] 不存在');
        }

        return $this->businessModel = $class;
    }

    private function getBusinessModel(string $business = GeneratorEnum::REQUEST): string
    {
        $class = str_replace('\\Http\\Controllers\\', '\\', get_called_class());

        $parts = explode('\\', $class);

        $ns = match ($business) {
            GeneratorEnum::SERVICE    => array_shift($parts) . '\\Services\\',
            GeneratorEnum::REPOSITORY => array_shift($parts) . '\\Repositories\\',
            GeneratorEnum::REQUEST    => array_shift($parts) . '\\Http\\Requests\\',
            GeneratorEnum::CACHE      => array_shift($parts) . '\\Caches\\',
        };

        $class = array_pop($parts);

        $class = Helper::strEndWith($class, 'Controller');

        $clazz = $ns . $class;

        return $clazz . ucwords($business);
    }

    final protected function validatedForm(array $extras = []): array
    {
        $this->checkBusiness(GeneratorEnum::REQUEST);

        if (!is_subclass_of($this->businessModel, FormRequest::class)) {
            Fail::throwFailException('无法验证表单');
        }

        if (!empty($extras)) {
            $this->request->merge($extras);
            return app($this->businessModel)->all();
        }

        return app($this->businessModel)->validated();
    }

    final protected function refreshBusinessModel(string $class = null): void
    {
        $this->businessModel = $class;
    }
}
