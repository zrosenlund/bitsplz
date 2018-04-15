<?php
/**
 * Created by PhpStorm.
 * User: Mingjie
 * Date: 2018/3/6
 * Time: 17:54
 */

class DataObject {
    //Define the database handle
    protected $dbh;

    /**
     * DataObject constructor.
     * Connect to database when initialize an instance.
     */
    public function __construct()
    {
        $this->dbh = $this->connect();
    }

    /**
     * Disconnect database when object closing
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Connect to database
     * @return PDO
     */
    protected function connect()
    {
        try {
            $dbh = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD );
            $dbh->setAttribute( PDO::ATTR_PERSISTENT, true );
            $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch ( PDOException $e ) {
            die("Connection failed: " . $e->getMessage() );
        }
        return $dbh;
    }

    /**
     * Disconnect database
     */
    protected function disconnect()
    {
        $this->dbh = null;
    }

    /**
     * Transfer the values from $inputData into $data array
     * @param $data
     * @param $inputData
     */
    public static function setData( &$data, $inputData )
    {
        foreach ( $inputData as $key => $value ) {
            if ( array_key_exists( $key, $data ) )
                $data[$key] = $value;
        }
    }

    /**
     * Operate the SELECT sql in database
     * @param String $tblName   the table name
     * @param array $options    an array holding WHERE conditions,for instance,
     *                          $options = array('id' => $id, 'name' => $name)
     *                          means "WHERE id=$id and name=$name"
     * @param string $orderBy   the column name used for order by
     * @param string $order     'ASC' OR 'DESC'
     * @return PDOStatement     the PDO statement
     */
    protected function select($tblName, $options = array(), $orderBy = '', $order = 'ASC')
    {
        if(empty($tblName))
            die("Method " . __METHOD__ . ": parameters error.");

        //Concat the options for select query
        $whereConditions = "";
        foreach ($options as $key => $value) {
            $whereConditions .= $key . '=:' . $key . ' AND ';
        }
        $whereConditions = empty($whereConditions) ? '' : ' WHERE ' . rtrim($whereConditions, ' AND ');
        $orderOption = empty($orderBy) ? '' : "ORDER BY {$orderBy} {$order}";

        //Define the query
        $sql = "SELECT * FROM {$tblName} {$whereConditions} {$orderOption}";

        //Prepare the statement
        $statement = $this->dbh->prepare($sql);

        //Bind parameters
        foreach ($options as $key => &$value) {
            $statement->bindParam(':'.$key, $value);
        }

        //Execute the query
        $statement->execute();

        //Return the statement
        return $statement;
    }


    /**
     * @param $tblName
     * @param array $options
     * @param string $orderBy
     * @param string $order
     * @return PDOStatement
     */
    protected function selectLike($tblName, $options = array(), $orderBy = '', $order = 'ASC')
    {
        if(empty($tblName))
            die("Method " . __METHOD__ . ": parameters error.");

        //Concat the options for select query
        $whereConditions = "";
        foreach ($options as $key => $value) {
            $whereConditions .= $key . " LIKE :" . $key . " OR ";
        }
        $whereConditions = empty($whereConditions) ? '' : ' WHERE ' . rtrim($whereConditions, ' OR ');
        $orderOption = empty($orderBy) ? '' : "ORDER BY {$orderBy} {$order}";

        //Define the query
        $sql = "SELECT * FROM {$tblName} {$whereConditions} {$orderOption}";

        //Prepare the statement
        $statement = $this->dbh->prepare($sql);

        //Bind parameters
        foreach ($options as $key => &$value) {
            $val = "%$value%";
            $statement->bindParam(':'.$key, $val);
        }

        //Execute the query
        $statement->execute();

        //Return the statement
        return $statement;
    }


    /**
     * Operate the INSERT sql in database
     * @param $tblName  the table name
     * @param $columns  an array holding the columns of the table
     * @param $data     an array in "column => value" format to store the value need to be inserted
     * @return int      the ID of the last inserted row
     */
    protected function insert($tblName, $columns, $data)
    {
        //Check the parameters
        if(empty($tblName) || empty($columns) || empty($data)
            || !is_array($columns) || !is_array($data))
            die("Method " . __METHOD__ . ": parameters error.");

        //Fetch data from $data array and set that into $columns data
        $this->setData( $columns, $data );

        //Concat the strings for the $sql variable
        $colName = "";
        $placeholder = "";
        foreach ($columns as $key => $value) {
            $colName .= $key . ', ';
            $placeholder .= ':' . $key . ', ';
        }
        $colName = rtrim($colName, ", ");
        $placeholder = rtrim($placeholder, ", ");

        //Define the query
        $sql = "INSERT INTO {$tblName} ({$colName}) VALUES ({$placeholder})";

        //Prepare the statement
        $statement = $this->dbh->prepare($sql);

        //Bind parameters
        foreach ($columns as $key => &$value) {
            $statement->bindParam(':'.$key, $value);
        }

        //Execute the query
        $result = $statement->execute();

        //Returns the ID of the last inserted row
        return $this->dbh->lastInsertId();
    }

    /**
     * Operate the UPDATE sql in database
     * @param String $tblName   the table name
     * @param array $columns    an array holding the columns of the table
     * @param array $data       the update data using a "column => value" format stored in this array
     * @param array $options    an array holding WHERE conditions
     * @return bool             the result of PDO execute() method
     */
    protected function update($tblName, $columns, $data, $options = array())
    {
        //Check the parameters
        if(empty($tblName) || empty($columns) || empty($data)
            || !is_array($columns) || !is_array($data))
            die("Method " . __METHOD__ . ": parameters error.");

        //Concat the strings for the $sql variable
        $updateValue = "";
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $columns))
                $updateValue .= $key . '=:' . $key . ', ';
        }
        $updateValue = rtrim($updateValue, ", ");

        foreach ($options as $key => $value) {
            $whereConditions = $key . '=:' . $key . ' AND ';
        }
        $whereConditions = empty($whereConditions) ? '' : ' WHERE ' . rtrim($whereConditions, ' AND ');


        //Define the query
        $sql = "UPDATE $tblName SET $updateValue $whereConditions";

        //Prepare the statement
        $statement = $this->dbh->prepare($sql);

        //Bind parameters
        foreach ($data as $key => &$value) {
            $statement->bindParam(':'.$key, $value);
        }
        foreach ($options as $key => &$value) {
            $statement->bindParam(':'.$key, $value);
        }

        //Execute the query
        $result = $statement->execute();

        //Return the results
        return $result;
    }

    /**
     * @param $tblName
     * @param array $options
     * @return bool
     */
    protected function delete($tblName, $options = array())
    {
        if(empty($tblName))
            die("Method " . __METHOD__ . ": parameters error.");

        //Concat the options for select query
        foreach ($options as $key => $value) {
            $whereConditions = $key . '=:' . $key . ' AND ';
        }
        $whereConditions = empty($whereConditions) ? '' : ' WHERE ' . rtrim($whereConditions, ' AND ');

        //Define the query
        $sql = "DELETE FROM {$tblName} {$whereConditions}";

        //Prepare the statement
        $statement = $this->dbh->prepare($sql);

        //Bind parameters
        foreach ($options as $key => &$value) {
            $statement->bindParam(':'.$key, $value);
        }

        //Execute the query
        $result = $statement->execute();

        //Return the results
        return $result;
    }
}