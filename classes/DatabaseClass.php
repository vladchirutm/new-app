<?php
class DatabaseClass
{
    /**
     * The singleton instance
     *
     */
    static private $DBInstance;

    /**
     * @param String $host
     * @param String $dbName
     * @param String $dbUser
     * @param String $dbPassword
     * @param Integer $dbPort
     */
    public function __construct(string $host, string $dbName, string $dbUser, string $dbPassword, int $dbPort = 5432)
    {
        if(!self::$DBInstance) {
            try {
                self::$DBInstance = pg_connect("host=$host dbname=$dbName user=$dbUser password=$dbPassword port=$dbPort");
            } catch (\Exception $e) {
                die("DB CONNECTION ERROR: " . $e->getMessage());
            }
        }
        return self::$DBInstance;
    }

    /**
     * @param string $table
     * @param array|null $parameters
     * @return bool|array
     */
    public function get(string $table, array $parameters = null): bool|array
    {
        if(!$result = $this->query($this->buildSelectQuery($table, $parameters))){
            return False;
        }
        $combined=array();
        while ($row = pg_fetch_assoc($result)) {
            $combined[]=$row;
        }
        return $combined;
    }

    /**
     * @param String $table
     * @param array|null $parameters
     * @return bool|array
     */
    public function getOne(string $table, array $parameters = null): bool|array
    {
        if(!$result = $this->query($this->buildSelectQuery($table, $parameters))){
            return False;
        }
        return pg_fetch_row($result);
    }

    /**
     * @param string $table
     * @param array $values
     * @param string|null $pk
     */
    public function insert(string $table, array $values, string $pk=null)
    {
        $query = "INSERT INTO $table (" . implode(", ", array_keys($values)) . ") VALUES ";

        $sqlValues = [];
        foreach ($values as $key=>$value){
            if(is_bool($value)) {
                $sqlValues[] = $value ? "true":"false";
            }
            elseif(is_string($value)) {
                $sqlValues[] = "'$value'";
            }
            elseif (!is_null($value) && ($value != "")) {
                $sqlValues[] = "$value";
            }
        }
        $query .= "(" . implode(", ", $sqlValues) . ")";

        if(!empty($pk)){
            $query .= " RETURNING $pk";
            $res = $this->query($query);
        }
        else{
            $this->query($query);
            $res = true;
        }

        return $res;
    }

    /**
     * @param String $table
     * @param array $parameters
     * @return bool|string
     */
    public function buildSelectQuery(string $table, array $parameters): bool|string
    {
        if(empty($table)){
            return false;
        }

        $fields = '*';
        if(!empty($parameters['fields'])){
            $fields = $parameters['fields'];
            if(is_array($parameters['fields'])){
                $fields = implode(',', $parameters['fields']);
            }
        }

        $conditions = '';
        if(!empty($parameters['conditions'])){
            if(is_array($parameters['conditions'])) {
                $conditions = implode(' AND ', $parameters['conditions']);
            }
            else{
                $conditions = $parameters['conditions'];
            }
        }

        $orderBy = '';
        if(!empty($parameters['order'])){
            if(is_array($parameters['order'])) {
                $orderByArr = [];
                foreach ($parameters['order'] as $field=>$direction) {
                    $orderByArr[] = $field . " " . $direction;
                }
                $orderBy = implode(', ', $orderByArr);
            }
            else{
                $orderBy = $parameters['conditions'];
            }
        }

        $queryJoinArr = [];
        if(!empty($parameters['joins'])){
            foreach ($parameters['joins'] as $join){
                $queryJoin = ' LEFT JOIN';
                if(!empty($join['type'])){
                    $queryJoin = " " . $join['type'];
                }

                $queryJoin .= " " . $join['table'] . " ON " . implode(" AND ", $join['conditions']);
                $queryJoinArr[] = $queryJoin;
            }
        }


        $query = "SELECT $fields FROM $table";

        if(!empty($queryJoinArr)){
            $query .= implode(" ", $queryJoinArr);
        }

        if(!empty($conditions)){
            $query .= " WHERE " . $conditions;
        }
        if(!empty($orderBy)){
            $query .= " ORDER BY ".$orderBy;
        }

        return $query;

    }

    public function query($query){
        return pg_query(self::$DBInstance, $query);
    }

}