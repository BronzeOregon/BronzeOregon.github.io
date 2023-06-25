<?php
/*
* this class performs operations with the database. Connecting and CRUD functions.
*/

class subscriber {
    private $dbHost = DB_HOST;
    private $dbUsername = DB_USERNAME;
    private $dbPassword = DB_PASSWORD;
    private $dbName = DB_NAME;
    private $userTb1 = 'subscribers';

    function _construct(){
        if(!isset($this->db)){
            //establish connection with database
            $conn = new mysqli($this->dbHost, $this->dbUsername, $this->dbPassword, $this ->dbName);
            if($conn->connect_error){
                die("Failed to connect with MySQL: " . $conn->connect_error);
            } else{
                $this->db = $conn;
            }
        }
    }

    //returns rows based on conditions, @param is string name of table, @param array select, where, order_by, limit and return_type conditions

    public function getRows($conditions = array()){
    $sql = 'SELECT';
    $sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
    $sql .= ' FROM '.$this->userTb1;
    if(array_key_exists("where",$conditions)){
        $sql .= ' WHERE ';
        $i = 0;
        foreach($conditions['where'] as $key => $value){
            $pre = ($i > 0)?' AND ':'';
            $sql .= $pre.$key." = '".$value."'";
            $i++;
        }
    }

    if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
        $sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit'];
    } elseif(!array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
        $sql .= ' LIMIT '.$conditions['limit'];
    }

    $result = $this->db->query($sql);

    if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
        switch($conditions['return_type']){
            case 'count':
                $data = $result->num_rows;
                break;
            case 'single':
                $data = $result->fetch_assoc();
                break;
            default:
                $data = '';
        }
    } else{
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $data[] = $row;
            }
        }
    }
    return !empty($data)?$data:false;
    }

    //function inserts data into the database. @param is string name of table. @param array the data for insertion
    public function insert($data){
        if(!empty($data) && is_array($data)){
            $columns = '';
            $values = '';
            $i = 0;
            if(!array_key_exists('created',$data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s:");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?',':'';
                $columns .= $pre.$key;
                $values .= $pre."'".$this->db->real_escape_string($val)."'";
                $i++;
            }
            $query = "INSERT INTO ".$this->userTb1." (".$columns.") VALUES (".$values.")";
            $insert = $this->db->query($query);
            return $insert?$this->db->insert_id:false;
        }else{
            return false;
        }
    }

    //Updates data in DB. @param string name of table, @param array the data for updating into table, @param array where condition on updating data
    public function update($data, $conditions){
        if(!empty($data) && is_array($data)){
            $colvalSet = '';
            $whereSql = '';
            $i = 0;
            if(!array_key_exists('modified',$data)){
                $data['modified'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $colvalSet .= $pre.$key."='".$this->db->real_escape_string($val)."'";
                $i++;
            }
            if(!empty($conditions) && is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
            $query = "UPDATE ".$this->userTb1." SET ".$colvalSet.$whereSql;
            $update = $this->db->query($query);
            return $update?$this->db->affected_rows:false;
        }else{
            return false;
        }
    }

    //Delete data from DB, @param string name of table, @param array where condition on deleting data
    public function delete($conditions){
        $whereSql = '';
        if(!empty(conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $query = "DELETE FROM ".$this->userTb1.$whereSql;
        $delete = $this->db->query($query);
        return $delete?true:false;
    }
}
?>