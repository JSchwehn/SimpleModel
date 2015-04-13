<?php
namespace SimpleModel {
    abstract class SimpleModel
    {
        const NOAUTOLOAD        = true;
        const IGNOREOVERWRITE   = false;
        const USEOVERWRITE      = true;

        protected $_config      = null;
        protected $_data        = null;
        protected $_metaData    = array();
        protected $_tableName   = null;
        protected $_today       = null;
        protected $_primaryKey  = "";
        protected $_joins       = null;
        protected $_mappings    = array();

        private $_db            = null;

        /* history */
        public $lastSQL         = "";
        public $lastSQLData     = array();
        public $lastSQLError    = array();
        public $errorOccurred   = false;
        private $_mandatoryFields = array();


        public function __construct(array $configs = array())
        {
            $this->_today = new \DateTime();
            $this->buildMandatory();
        }

        public function init($configs = array())
        {
            $this->_data = array();
            $this->buildMandatory();
            if (is_array($configs)) {
                foreach ($configs as $key => $configValue) {
                    $method = 'set' . ucfirst($key);
                    $this->$method($configValue);
                }
            }
            $this->mandatoryCheck();
        }

        public function setDb(\PDO $db)
        {
            $this->_db = $db;
        }

        /**
         * @return null|\PDO
         */
        public function Db()
        {
            return $this->_db;
        }

        public function Today()
        {
            return $this->_today;
        }


        protected function buildMandatory()
        {
            if (isset($this->_metaData)) {
                foreach ($this->_metaData as $varName => $data) {
                    if (isset($this->_metaData[$varName]['mandatory']) && ($this->_metaData[$varName]['mandatory'] == true)) {
                        $this->_mandatoryFields[$varName] = $varName;
                    }
                }
            }
        }

        /**
         * Checks if there are any not satisfied fields left.
         * @throws \Exception
         * @return bool
         */
        protected function mandatoryCheck()
        {
            if (count($this->_mandatoryFields) > 0) {
                throw new \Exception(get_class(
                        $this
                    ) . ': Not all mandatory fields are given. Following fields are missing: ' . implode(
                        ',',
                        $this->_mandatoryFields
                    ));
            }
            return true;
        }

        public function __call($name, $arguments)
        {
            /* handle set-methods */
            if (substr($name, 0, 3) == 'set') {
                $varName     = lcfirst(substr($name, 3));
                $configValue = $arguments[0];
                /* validate input */
                if (isset($this->_metaData[$varName]['validValues'])) {
                    if (!in_array($configValue, $this->_metaData[$varName]['validValues'])) {
                        throw new \Exception(get_class(
                                $this
                            ) . ': Invalid parameter for ' . $varName . ' given: ', $configValue . ' expected where one of: ' . implode(
                                $this->_metaData[$varName]['validValues']
                            ));
                    }
                }
                /* do a pattern matching to make sure that only valid data is given*/
                if (isset($this->_metaData[$varName]['validationPattern'])) {
                    $pattern = "/" . $this->_metaData[$varName]['validationPattern'] . "/";
                    if (!preg_match($pattern, $configValue)) {
                        throw new \Exception(get_class(
                                $this
                            ) . ': Invalid parameter for ' . $varName . ' given: ', $configValue . ' did not match: ' . $this->_metaData[$varName]['validationPattern']);
                    }
                }
                /** Handling minimum length */
                if (isset($this->_metaData[$varName]['minLength'])) {
                    if (mb_strlen($configValue) > $this->_metaData[$varName]['minLength']) {
                        throw new \Exception(get_class(
                                $this
                            ) . ': Invalid parameter. Parameter ' . $varName . ' exceeds ' . $this->_metaData[$varName]['minLength'] . ' length.');
                    }
                }
                if (isset($this->_metaData[$varName]['addString'])) {
                    $configValue = $configValue . $this->_metaData[$varName]['addString'];
                }
                /* call a callback method to custom convert given data */
                if (isset($this->_metaData[$varName]['callback'])) {
                    // allows build-in function
                    if (method_exists($this, $this->_metaData[$varName]['callback'])) {
                        $fnc         = $this->_metaData[$varName]['callback'];
                        $configValue = $this->$fnc($configValue);
                    } elseif ($this->is_closure($this->_metaData[$varName]['callback'])) { // needs testing
                        $this->_metaData[$varName]['callback']($configValue);
                    }
                }
                /** Handle max length by just cutting off the string */
                if (isset($this->_metaData[$varName]['maxLength'])) {
                    if (is_string($configValue)) {
                        $configValue = mb_substr($configValue, 0, $this->_metaData[$varName]['maxLength']);
                    }
                }
                /* Mandatory check */
                if (isset($this->_mandatoryFields[$varName])) {
                    unset($this->_mandatoryFields[$varName]);
                }
                $objectName = null;
                /** Set Var */
                if (strpos($this->_metaData[$varName]['type'], ':') !== false) {
                    list($varType, $objectName) = explode(":", $this->_metaData[$varName]['type']);
                    $this->_metaData[$varName]['type'] = $varType;
                }

                switch ($this->_metaData[$varName]['type']) {
                    case 'array':
                        if (isset($this->_metaData[$varName]['handelConverts'])) {
                            if (!is_array($configValue)) {
                                $configValue = array($configValue);
                            }
                        }
                        if (!is_array($configValue)) {
                            throw new \Exception('Expected an array for $varName but ' . gettype(
                                    $configValue
                                ) . ' has been provided');
                        }
                        $this->_data[$varName] = $configValue;
                        break;
                    case 'obj':
                        if (!$configValue instanceof $objectName) {
                            throw new \Exception('Expected an object of ' . $objectName . ' but received ' . get_class(
                                    $this->_metaData[$varName]['type']
                                ) . "'");
                        }
                        $this->_data[$varName] = $configValue;
                        break;
                    case 'int':
                    case 'integer':
                        $this->_data[$varName] = (int)$configValue;
                        break;
                    case 'float':
                    case 'double':
                    case 'decimal':
                    case 'money':
                        $this->_data[$varName] = (floatval(str_replace(',', '.', $configValue)));
                        break;
                    case 'bool':
                    case 'boolean':
                        if (true == $configValue) {
                            $this->_data[$varName] = 'true';
                        } else {
                            $this->_data[$varName] = 'false';
                        }
                        break;
                    default:
                        $this->_data[$varName] = $configValue;
                }
                return true;
            } // END setter handling
            /* handle get-methods */
            if ('get' === substr($name, 0, 3)) {
                $varName = lcfirst(substr($name, 3));

                if (!isset($this->_metaData[$varName])) {
                    throw new \Exception(get_class($this) . ': Method ' . $name . ' does not exists.');
                }
                if (isset($this->_data[$varName])) {
                    // handle output format
                    if (isset($this->_metaData[$varName]['format'])) {
                        switch (strtolower($this->_metaData[$varName]['type'])) {
                            case 'datetime':
                            case 'timedate':
                                if ($this->_data[$varName] instanceof \DateTime) {
                                    return $this->_data[$varName]->format($this->_metaData[$varName]['format']);
                                }
                                break;
                            case 'money': // todo use sprintf() for universal format options
                                // windows does not support money_format
                                if (function_exists('money_format')) {
                                    $money = str_replace(',', '.', $this->_data[$varName]);
                                    return money_format($this->_metaData[$varName]['format'], $money);
                                }
                                break;
                        }
                    }

                    return $this->_data[$varName];
                } else {
                    // return default values
                    if (isset($this->_metaData[$varName]['default'])) {
                        return $this->_metaData[$varName]['default'];
                    }
                    // nothing found, return null
                    return null;
                }
            }
            /* Inform the system that we could not execute the call */
            throw new \Exception('Called Method ' . $name . ' failed.');
        }

        /**
         * Allows access to the raw data structure. Can be used to circumvent the getter and setter methods - but this
         * is not recommended
         *
         * @return null|array
         */
        public function getRawData()
        {
            return $this->_data;
        }

        /**
         * @param $data
         * @return bool
         */
        private function is_closure($data)
        {
            return is_object($data) && ($data instanceof \Closure);
        }

        private function getValue($indexes, $arrayToAccess)
        {
            if (count($indexes) > 1) {
                return $this->getValue(array_slice($indexes, 1), $arrayToAccess[$indexes[0]]);
            } else {
                return $arrayToAccess[$indexes[0]];
            }
        }

        /**
         * @param array $order
         * @param array $parameters
         *
         * @return $this
         */
        public function importFromShopware(array $order, array $parameters = array())
        {
            foreach ($this->_mappings as $field => $map) {
                $setter = 'set' . ucfirst($field);
                if (isset($map['default'])) {
                    if ($map['default'] == 'now()') {
                        $this->$setter($this->Today()->format('Y-m-d H:i:s'));
                    } else {
                        $this->$setter($map['default']);
                    }
                } elseif (isset($map['target'])) {
                    if (is_array($map['target'])) {
                        $conData = array();
                        foreach ($map['target'] as $element) {
                            $elements  = explode('.', $element);
                            $conData[] = $this->getValue($elements, $order);
                        }
                        $delimiter = " ";
                        if (isset($map['delimiter'])) {
                            $delimiter = $map['delimiter'];
                        }
                        $this->$setter(implode($delimiter, $conData));
                    } else {
                        $elements = explode('.', $map['target']);
                        $value    = $this->getValue($elements, $order);
                        if (isset($map['converter'])) {
                            $value = $this->$map['converter']($value);
                        }
                        if (isset($map['prefix'])) {
                            $value = $map['prefix'] . $value;
                        }
                        if (isset($map['postfix'])) {
                            $value = $value . $map['postfix'];
                        }
                        $this->$setter($value);
                    }
                } elseif (isset($map['parameters'])) {
                    if (isset($parameters[$map['parameters']])) {
                        $this->$setter($parameters[$map['parameters']]);
                    }
                }
            }
            return $this;
        }

        public function load($id, $debug=false)
        {
            /** @var \PDO $db */
            if (is_null($this->Db())) {
                throw new \Exception('Database Object empty: ' . get_class());
            }
            if (empty($this->_primaryKey)) {
                throw new \Exception('Need a primary key for ' . get_class());
            }
            // join handling
            $joins = "";
            if(!is_null($this->_joins)) {
                foreach($this->_joins as $joinType=>$join) {
                    foreach($join as $joinElement) {
                        $joins .= " ".$joinType ." JOIN `".$joinElement['name']."` AS ".$joinElement['short'] ;
                        if(isset($joinElement['on'])) {
                            $joins .= " ON ".$joinElement['on']." ";
                        }
                    }
                }
            }

            $sql = "SELECT " . implode(
                    ',',
                    $this->makeDbFields('c', SimpleModel::USEOVERWRITE)
                ) . " FROM `" . $this->_tableName . '` c ';
            $sql .= " ".$joins;
            $sql .= ' WHERE c.`' . $this->_primaryKey . '` = ? LIMIT 1';
            $this->errorOccurred = false;
            $sth = $this->Db()->prepare($sql);
            $this->lastSQL = $sql;
            $sth->execute(array($id));
            $this->lastSQLError = $sth->errorInfo();
            if ($sth->errorCode() !=  '000') {
                $this->errorOccurred = true;
            }
            $data = $sth->fetchAll(\PDO::FETCH_ASSOC);

            if (!isset($data[0])) {
                return false;
            }
            $this->init($data[0]);
        }

        public function loadOneBy(array $criteria)
        {
            /** @var \PDO $db */
            if (is_null($this->Db())) {
                throw new \Exception('Database Object empty: ' . get_class());
            }

            $where       = [];
            $searchArray = [];
            foreach ($criteria as $field => $value) {
                $where[]       = " " . $this->escapeIdentifier($field) . " = ? ";
                $searchArray[] = $value;
            }

            $sql = "SELECT " . implode(
                    ',',
                    $this->makeDbFields('c')
                ) . " FROM `" . $this->_tableName . '` c WHERE ' . implode(" AND ", $where) . " LIMIT 1";
            $sth = $this->Db()->prepare($sql);
            $sth->execute($searchArray);
            $data = $sth->fetchAll(\PDO::FETCH_ASSOC);
            $this->lastSQLError = $sth->errorInfo();
            if ($sth->errorCode() !=  '000') {
                $this->errorOccurred = true;
            }
            if (!isset($data[0])) {
                throw new \Exception('Could not load data with provides search data (Fields: ' . implode(
                        ',',
                        $where
                    ) . ') values(' . implode(' and ', $searchArray) . ')');
            }
            $this->init($data[0]);
        }

        /**
         * @param $parentField
         * @param $parentId
         * @param $childType
         *
         * @return array
         */
        public function loadChildren($parentField, $parentId, $childType)
        {
            $retVal = [];
            /** @var SimpleModel $tmp */
            $tmp = new $childType(array('db' => $this->Db()));
            $sql = "SELECT " . $this->escapeIdentifier($tmp->getPrimaryKey()) . " FROM " . $this->escapeIdentifier(
                    $tmp->getTableName()
                ) . " WHERE " . $this->escapeIdentifier($parentField) . " = ?";
            $sth = $this->Db()->prepare($sql);
            $sth->execute(array($parentId));
            $this->lastSQLError = $sth->errorInfo();
            while ($data = $sth->fetch(\PDO::FETCH_ASSOC)) {
                /** @var SimpleModel $obj */
                $obj = new $childType(array('db' => $this->Db()));
                $obj->load($data[$tmp->getPrimaryKey()]);
                $retVal[] = $obj;
            }
            return $retVal;
        }


        public function save(\PDO $pdo = null, $noAutoLoad = false, $debug = false)
        {
            $this->lastSQL = "";
            $data   = $this->getRawData();
            $set    = array();
            $insert = array();
            $d1     = $d2 = array();
            foreach ($data as $key => $value) {
                // handle nested objects inside arrays
                if (is_array($value)) {
                    foreach ($value as $singleItem) {
                        if ($singleItem instanceof SimpleModel) {
                            $singleItem->save($pdo);
                            continue;
                        }
                    }
                    unset($data[$key]);
                    continue;
                }
                //  handle single nested objects
                if ($value instanceof SimpleModel) {
                    $value->save($pdo);
                    unset($data[$key]);
                    continue;
                }
                $set[]    = $this->escapeIdentifier($key) . " = ?";
                $insert[] = $this->escapeIdentifier($key);
                $d1[]     = $value;
                $d2[]     = $value;
            }
            $update  = implode(',', $set);
            $insert  = implode(',', $insert);
            $sql     = 'INSERT INTO ' . $this->_tableName . '
                    (' . $insert . ') VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')
                    ON DUPLICATE KEY UPDATE ' . $update;
            $sth     = $pdo->prepare($sql);
            $sqlData = array_merge($d1, $d2);
            $this->lastSQL = $sql;
            $this->lastSQLData = $sqlData;
            $sth->execute($sqlData);
            $this->lastSQLError = $sth->errorInfo();
            if ($sth->errorCode() !=  '000') {
                $this->errorOccurred = true;
            }
            $newId = $pdo->lastInsertId();
            if (!is_null($newId)) {
                if ($noAutoLoad) {
                    return true;
                }
                // we received a ID, so we must have just insert a new data set
                $this->load($newId);
                return true;
            } else {
                return false;
            }
        }


        public function handelSingleItem($testSubject)
        {
            if (!is_array($testSubject)) {
                return false;
            }
            if (isset($testSubject[0])) {
                return $testSubject;
            }
            return array($testSubject);
        }

        public function getTableName()
        {
            return $this->_tableName;
        }

        public function getPrimaryKey()
        {
            return $this->_primaryKey;
        }
        /**
         * @param $fieldName
         * @return string
         */ // Universal
        public function escapeIdentifier($fieldName)
        {
            $fieldName = str_replace("`", "``", $fieldName);
            return '`' . $fieldName . '`';
        }

        public function makeDbFields($table = '',$useOverwrite=false)
        {
            $fields = array();
            if (!empty($table)) {
                $table = $this->escapeIdentifier($table) . ".";
            }
            foreach ($this->_metaData as $key => $field) {
                if (!isset($this->_metaData[$key]['dbIgnore'])) {
                    if(isset($this->_metaData[$key]['fieldOverwrite']) && $useOverwrite)   {
                        $fields[] = $this->_metaData[$key]['fieldOverwrite'];
                    } else {
                        $fields[] = $table . $this->escapeIdentifier(trim($key));
                    }

                }
            }
            return $fields;
        }
    }
}


