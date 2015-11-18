<?php

namespace Pathfinder;

/**
 * Поиск минимального расстояния между двумя точками по алгоритму Дейкстры
 *
 * @param string $start Название начальной точки
 * @param string $end Название конечной точки
 * @param array $edgeList Стоимость проезда между точками в формате [точка1, точка2, стоимость]
 * @return array
 */
function find($start, $end, $edgeList)
{
    // Список вершин (городов)
    $apexList = [];

    // Количество вершин
    $apexCount = 0;

    $getApexIndex = function ($apex) use (&$apexList, &$apexCount) {
        $index = array_search($apex, $apexList, true);
        if ($index === false) {
            $apexList [] = $apex;
            $index = $apexCount;
            $apexCount++;
        }
        return $index;
    };

    // Ребра
    $edges = [];

    foreach ($edgeList as $edge) {
        // Добавим еще неизвестные вершины в список и
        $edge0 = $getApexIndex($edge[0], $apexList);
        $edge1 = $getApexIndex($edge[1], $apexList);

        // Заполним матрицу смежности для прямого и обратного пути
        $edges[$edge0][$edge1] = $edge[2];
        $edges[$edge1][$edge0] = $edge[2];
    }

    // Заполним матрицу посещенных вершин
    $visited = array_fill(0, $apexCount, false);

    // Заполним матрицу расстояний
    $distance = array_fill(0, $apexCount, PHP_INT_MAX);

    // Заполним матрицу маршрутов
    $path = array_fill(0, $apexCount, []);

    // Выберем стартовую вершину
    $startIndex = array_search($start, $apexList, true);
    $distance[$startIndex] = 0;
    $path[$startIndex] = [$apexList[$startIndex]];

    /**
     * Алгоритм Дейкстры
     */
    for ($count = 0; $count < $apexCount - 1; $count++) {
        $min = PHP_INT_MAX;
        // Выберем вершину $u (имеющую минимальное расстояние и еще не посещенную)
        for ($i = 0; $i < $apexCount; $i++) {
            if (!$visited[$i] && $distance[$i] <= $min) {
                $min = $distance[$i];
                $u = $i;
            }
        }
        // Отметим вершину как посещенную
        $visited[$u] = true;
        for ($i = 0; $i < $apexCount; $i++) {
            // Если найденное расстояние меньше известного
            if (
                !$visited[$i] &&
                isset($edges[$u][$i]) &&
                $distance[$u] != PHP_INT_MAX &&
                $distance[$u] + $edges[$u][$i] < $distance[$i]
            ) {
                // Обновим расстояние
                $distance[$i] = $distance[$u] + $edges[$u][$i];

                // Запишем маршрут
                $path[$i] = $path[$u];
                $path[$i] [] = $apexList[$i];
            }
        }
    }

    // Выберем результат
    $endIndex = array_search($end, $apexList, true);

    return [
        'path' => $path[$endIndex],
        'cost' => $distance[$endIndex],
    ];
}
