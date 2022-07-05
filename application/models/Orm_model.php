<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orm_model extends CI_Model
{
    public $dbName;
    public $tableName;
    public $columns;
    public $prefixChars;

    public function __construct()
    {
        parent::__construct();

        $this->prefixChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $this->dbName      = '';
    }

    /**
     * Consigue un chat por medio del ID $id
     *
     * @param       $id
     * @param array $relations
     *
     * @return array|null
     */
    public function findByID($id, $relations = []): ?array
    {
        /** @var CI_DB_mysqli_driver $db */
        $db          = $this->db;
        $queryArray  = $this->parseQuery($relations, $id);
        $resultQuery = $db->query($queryArray['query']);

        if (!is_bool($resultQuery)) {
            $resultArray = $resultQuery->result_array();
        } else {
            $resultArray = [];
        }

        return !empty($resultArray) ? $this->parseArray($resultArray, $queryArray) : null;
    }

    /**
     * @param array $relations
     * @param       $anchorValue
     * @param       $anchorColumn
     *
     * @return array
     */
    public function findByAnchor($anchorValue, $anchorColumn, $relations = []): array
    {
        /** @var CI_DB_mysqli_driver $db */
        $db          = $this->db;
        $queryArray  = $this->parseQuery($relations, $anchorValue, $anchorColumn);
        $resultQuery = $db->query($queryArray['query']);

        if (!is_bool($resultQuery)) {
            $resultArray = $resultQuery->result_array();
        } else {
            $resultArray = [];
        }

        return !empty($resultArray) ? $this->parseArray($resultArray, $queryArray, true, $anchorColumn) : [];
    }

    /**
     * @param string $queryAnchor
     * @param array $relations
     * @param string $mainColumn
     * @return array
     */
    public function findByQueryAnchor(string $queryAnchor, $relations = [], $mainColumn = 'id'): array
    {
        /** @var CI_DB_mysqli_driver $db */
        $db          = $this->db;
        $queryArray  = $this->parseQuery($relations, null, $queryAnchor);
        $resultQuery = $db->query($queryArray['query']);

        if (!is_bool($resultQuery)) {
            $resultArray = $resultQuery->result_array();
        } else {
            $resultArray = [];
        }

        return !empty($resultArray) ? $this->parseArray($resultArray, $queryArray, true, $mainColumn) : [];
    }

    /**
     * @param array $relations
     * @param string $anchorColumn
     *
     * @return array
     */
    public function findAll($relations = [], $anchorColumn = 'id'): array
    {
        /** @var CI_DB_mysqli_driver $db */
        $db          = $this->db;
        $queryArray  = $this->parseQuery($relations);
        $resultQuery = $db->query($queryArray['query']);

        if (!is_bool($resultQuery)) {
            $resultArray = $resultQuery->result_array();
        } else {
            $resultArray = [];
        }

        return !empty($resultArray) ? $this->parseArray($resultArray, $queryArray, true, $anchorColumn) : [];
    }

    /**
     * @param array $relations
     * @param       $anchorValue
     * @param       $anchorColumn
     *
     * @return array
     */
    public function findAllByAnchor($anchorValue, $anchorColumn, $relations = []): array
    {
        /** @var CI_DB_mysqli_driver $db */
        $db          = $this->db;
        $queryArray  = $this->parseQuery($relations, $anchorValue, $anchorColumn);
        $resultQuery = $db->query($queryArray['query']);

        if (!is_bool($resultQuery)) {
            $resultArray = $resultQuery->result_array();
        } else {
            $resultArray = [];
        }

        return !empty($resultArray) ? $this->parseArray($resultArray, $queryArray, true) : [];
    }

    /**
     * @param      $relations
     * @param      $id
     * @param null $mainAnchor
     *
     * @return array
     */
    private function parseQuery($relations, $id = null, $mainAnchor = null): array
    {
        $mainPrefix = $this->generatePrefix();

        if (!empty($relations)) {
            foreach ($relations as $localColumn => $relation) {
                if (!isset($this->{$relation[0]})) {
                    $this->load->model($relation[0]);
                }
                $prefix                            = $this->generatePrefix($this->{$relation[0]}->tableName);
                $relations[$localColumn]['prefix'] = $prefix;
            }
        }

        $query = '';

        //First get Model query string
        foreach ($this->columns as $colName) {
            $query .= $mainPrefix . '.' . $colName . ' as ' . $mainPrefix . $colName . ', ';
        }

        //Relations models query string
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                foreach ($this->{$relation[0]}->columns as $colName) {
                    $query .= $this->{$relation[0]}->dbName !== '' ? $this->{$relation[0]}->dbName . '.' : '';
                    $query .= $relation['prefix'] . '.' . $colName . ' as ' . $relation['prefix'] . $colName . ', ';
                }
            }
        }

        $query = substr_replace($query, '', -2);
        $query = 'select ' . $query . ' from ';
        $query .= $this->dbName !== '' ? $this->dbName . '.' : '';
        $query .= $this->tableName . ' ' . $mainPrefix;

        if (!empty($relations)) {
            foreach ($relations as $relation) {
                $query .= ' left join ';
                $query .= $this->{$relation[0]}->dbName !== '' ? $this->{$relation[0]}->dbName . '.' : '';
                $query .= $this->{$relation[0]}->tableName . ' ' . $relation['prefix'] . ' on ';
                $i     = 0;
                foreach ($relation[1] as $localColumn => $relatedColumn) {
                    if ($localColumn !== 'plus') {
                        if ($i === 0) {
                            $query .= $relation['prefix'] . '.' . $relatedColumn . ' = ' . $mainPrefix . '.' . $localColumn;
                        } else {
                            $query .= ' and ' . $relation['prefix'] . '.' . $localColumn . ' = ' . $this->db->escape($relatedColumn);
                        }

                        $i++;
                    }

                    if ($localColumn === 'plus') {
                        $queryString = str_replace('@prefix', $relation['prefix'], $relatedColumn);
                        $query       .= ' ' . $queryString;
                    }
                }
            }
        }

        if ($id) {
            if ($mainAnchor) {
                $query .= ' where ' . $mainPrefix . '.' . $mainAnchor . ' = "' . $id . '"';
            } else {
                $query .= ' where ' . $mainPrefix . '.id = "' . $id . '"';
            }
        } else if ($mainAnchor && strstr($mainAnchor, '{{prefix}}')) {
            $mainAnchor = str_replace('{{prefix}}', $mainPrefix . '.', $mainAnchor);
            $query      .= ' where ' . $mainAnchor;
        }

        //        echo '<textarea>';
        //        echo $query;
        //        echo '</textarea>';
        //        die;

        return [
            'query'      => $query,
            'mainPrefix' => $mainPrefix,
            'relations'  => $relations
        ];
    }

    /**
     * @param array $array
     * @param array $prefixesInfo
     * @param bool $multy
     * @param string $anchorColumn
     *
     * @return array
     */
    private function parseArray(
        array $array,
        array $prefixesInfo,
        bool $multy = false,
        string $anchorColumn = 'id'
    ): array {
        $prefixes              = [];
        $multidimensionalArray = [];
        $prefixes[]            = [
            'prefix'     => $prefixesInfo['mainPrefix'],
            'table'      => $this->tableName,
            'type'       => null,
            'manyArr'    => null,
            'anchor'     => null,
            'mainPrefix' => true,
            'multy'      => $multy
        ];

        if (!empty($prefixesInfo['relations'])) {
            foreach ($prefixesInfo['relations'] as $relation) {
                $prefixes[] = [
                    'prefix'     => $relation['prefix'],
                    'table'      => explode('__', $relation['prefix'])[1],
                    'type'       => $relation['type'],
                    'manyArr'    => null,
                    'anchor'     => reset($relation[1]),
                    'mainPrefix' => false
                ];
            }
        }

        foreach ($array as $arKey => $result) {
            foreach ($prefixes as $preKey => $prefix) {
                if ($prefix['mainPrefix'] === true) {
                    if ($multy === false) {
                        foreach ($result as $key => $value) {
                            if ($result[$prefix['prefix'] . $anchorColumn] === null) {
                                $multidimensionalArray[$prefix['table']] = null;
                                break;
                            }

                            if (strpos($key, $prefix['prefix']) !== false) {
                                $multidimensionalArray[$prefix['table']][str_replace(
                                    $prefix['prefix'],
                                    '',
                                    $key
                                )] = $value;
                            }
                        }
                    } else {
                        foreach ($result as $key => $value) {
                            if ($result[$prefix['prefix'] . $anchorColumn] === null) {
                                $multidimensionalArray[$result[$prefix['prefix'] . $anchorColumn]][$prefix['table']] = null;
                                break;
                            }

                            if (strpos($key, $prefix['prefix']) !== false) {
                                $multidimensionalArray[$result[$prefix['prefix'] . $anchorColumn]][$prefix['table']][str_replace($prefix['prefix'], '', $key)] = $value;
                            }
                        }
                    }
                } elseif ($prefix['type'] === 'many') {
                    $manyArray = [];

                    foreach ($result as $key => $value) {
                        if ($result[$prefix['prefix'] . 'id'] !== null && strpos(
                            $key,
                            $prefix['prefix']
                        ) !== false) {
                            $manyArray[str_replace($prefix['prefix'], '', $key)] = $value;
                        }
                    }

                    if (!empty($manyArray)) {
                        if ($multy) {
                            $prefix['manyArr'][$result[$prefix['prefix'] . $prefix['anchor']]][$manyArray['id']] = $manyArray;
                        } else {
                            $prefix['manyArr'][$manyArray['id']] = $manyArray;
                        }
                    }

                    $prefixes[$preKey] = $prefix;

                    if ($multy) {
                        $multidimensionalArray[$result[$prefixesInfo['mainPrefix'] . $anchorColumn]][$prefix['table']] = [];
                    } else {
                        $multidimensionalArray[$prefix['table']] = [];
                    }
                } else {
                    foreach ($result as $key => $value) {
                        if ($result[$prefix['prefix'] . $prefix['anchor']] === null) {
                            if ($multy) {
                                $multidimensionalArray[$result[$prefixesInfo['mainPrefix'] . $anchorColumn]][$prefix['table']] = null;
                            } else {
                                $multidimensionalArray[$prefix['table']] = null;
                            }
                            break;
                        }

                        if (strpos($key, $prefix['prefix']) !== false) {
                            if ($multy) {
                                $multidimensionalArray[$result[$prefixesInfo['mainPrefix'] . $anchorColumn]][$prefix['table']][str_replace(
                                    $prefix['prefix'],
                                    '',
                                    $key
                                )] = $value;
                            } else {
                                $multidimensionalArray[$prefix['table']][str_replace(
                                    $prefix['prefix'],
                                    '',
                                    $key
                                )] = $value;
                            }
                        }
                    }
                }
            }
        }

        foreach ($prefixes as $preKey => $prefix) {
            if ($prefix['type'] === 'many') {
                if ($prefix['manyArr'] !== null) {
                    foreach ($prefix['manyArr'] as $anchorKey => $relation) {
                        if ($multy) {
                            $multidimensionalArray[$anchorKey][$prefix['table']] = $relation;
                        } else {
                            $multidimensionalArray[$prefix['table']] = $prefix['manyArr'];
                        }
                    }
                } elseif ($multy) {
                    foreach ($multidimensionalArray as $key => $multiResult) {
                        $multiResult[$prefix['table']] = null;
                        $multidimensionalArray[$key]   = $multiResult;
                    }
                } else {
                    $multidimensionalArray[$prefix['table']] = null;
                }
            }
        }

        return $this->afterParse($multidimensionalArray);
    }

    private function generatePrefix($additional = false)
    {
        if ($additional) {
            return substr(str_shuffle($this->prefixChars), 0, 8) . '__' . $additional;
        }

        return substr(str_shuffle($this->prefixChars), 0, 8);
    }

    /**
     * Functions to run after parse for children models
     *
     * @param $array
     *
     * @return mixed
     */
    public function afterParse($array)
    {
        return $array;
    }
}
