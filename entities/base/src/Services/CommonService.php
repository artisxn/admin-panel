<?php

namespace InetStudio\AdminPanel\Base\Services;

/**
 * Class CommonService.
 */
class CommonService
{
    /**
     * @var
     */
    public $model;

    /**
     * CommonService constructor.
     *
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Получаем объект по id.
     *
     * @param mixed $id
     * @param array $params
     *
     * @return mixed
     */
    public function getItemById($id = 0, array $params = [])
    {
        return $this->model::buildQuery($params)->find($id) ?? new $this->model;
    }

    /**
     * Получаем все объекты.
     *
     * @param array $params
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
     * @param array $data
     * @param int $id
     *
     * @return mixed
     */
    public function saveModel(array $data, int $id = 0)
    {
        $item = $this->model::updateOrCreate(
            ['id' => $id],
            $data
        );

        return $item;
    }
}
