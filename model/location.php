<?php
/**
 * Created by PhpStorm.
 * User: Mingjie
 * Date: 2018/4/15
 * Time: 15:02
 */

class Location extends DataObject
{
    //Setting the column names into arrays for each table
    protected $locationsColumns = array(
        'name' => '',
        'category' => '',
        'address' => '',
        'state' => '',
        'zip' => '',
        'longitude' => '',
        'latitude' => '',
        'phone' => '',
        'hours' => ''
    );


    /**
     * Get a location list from database
     * @return array
     */
    function getLocations()
    {
        $tblName = 'locations';
        $options = array();
        $orderBy = 'id';
        $order = 'DESC';

        $statement = $this->select($tblName, $options, $orderBy, $order);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Get the location info from database where location_id = $location_id
     *
     * @param $location_id int the location id in the database
     * @return mixed the info of one location in an array
     */
    function getLocation($location_id)
    {
        $tblName = 'locations';
        $options = array('location_id' => $location_id);

        $statement = $this->select($tblName, $options);

        return $statement->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Add a new location info into database
     *
     * @param $data array the array just like $_POST
     * @return int the location_id of this inserted record
     */
    function addNewLocation($data)
    {
        $tblName = 'locations';
        $columns = $this->locationsColumns;

        return $this->insert($columns, $tblName, $data);
    }


    /**
     * Update location info into the database
     *
     * @param $data array the array just like $_POST
     * @param $location_id int the location id in the database
     * @return bool the result of PDO execute() method
     */
    function updateLocation($data, $location_id)
    {
        $tblName = 'locations';
        $columns = $this->locationsColumns;
        $options = array('location_id' => $location_id);
        return $this->update($tblName, $columns, $data, $options);
    }


    /**
     * Delete a specified location from database
     *
     * @param $location_id int the location id in the database
     * @return bool the result of PDO execute() method
     */
    function deleteLocation($location_id)
    {
        $tblName = 'locations';
        $options = array('location_id' => $location_id);

        return $this->delete($tblName, $options);
    }
}