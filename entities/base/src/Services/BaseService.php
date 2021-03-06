<?php

namespace InetStudio\AdminPanel\Base\Services;

use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;

/**
 * Class BaseService.
 */
class BaseService implements BaseServiceContract
{
    /**
     * @var
     */
    protected $model;

    /**
     * BaseService constructor.
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Возвращаем модель.
     *
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Получаем объект по id.
     *
     * @param  mixed  $id
     * @param  array  $params
     * @param  bool  $returnNew
     *
     * @return mixed
     */
    public function getItemById($id = 0, array $params = [], bool $returnNew = true)
    {
        $query = $this->model->newQuery();

        if (! empty($params)) {
            $query = $query->buildQuery($params);
        }

        return $query->find($id) ?? (($returnNew) ? new $this->model : null);
    }

    /**
     * Получаем все объекты.
     *
     * @param  array  $params
     *
     * @return mixed
     */
    public function getAllItems(array $params = [])
    {
        return $this->model::buildQuery($params);
    }

    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param $id
     *
     * @return mixed
     */
    public function saveModel(array $data, $id = 0)
    {
        $item = $this->model::updateOrCreate(
            ['id' => $id],
            $data
        );

        return $item;
    }

    /**
     * Удаляем модель.
     *
     * @param  mixed  $id
     *
     * @return bool|null
     */
    public function destroy($id): ?bool
    {
        return $this->model::destroy($id);
    }
}
