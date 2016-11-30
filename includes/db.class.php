<?php

class DB
{
    private $pdo;
    private $successFlag = false;


    function __construct($dbname, $dbuser, $dbpsw,$host="localhost")
    {

        try {
            $this->pdo = new PDO('mysql:host='.$host.';dbname=' . $dbname, $dbuser, $dbpsw);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('SET NAMES "utf8"');
            $this->successFlag = true;
        } catch (PDOException $e) {
            $error = 'Unable to connect to the database server.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }

    }
    function exeNew($s){
        try{
            $this->pdo->exec($s);
            return $this->pdo->lastInsertId();
        }catch(PDOException $e){
            $error = 'exeError' . $e->getMessage();
            include 'error.html.php';
            exit();
        }
    }
    function pdoQuery($tableName, $fields, $where, $append)
    {
        $sql = 'SELECT ';
        $fieldsCount = count($fields);
        if ($fieldsCount > 0) {
            for ($i = 0; $i < $fieldsCount; $i++) {
                $sql = $sql . $fields[$i];
                if ($i < $fieldsCount - 1) $sql = $sql . ',';
            }
        }else{
            $sql=$sql.'* ';
        }
        $sql = $sql . ' FROM ' . $tableName;
        $whereCount = count($where);
        if ($whereCount > 0) {
            $sql = $sql . ' WHERE ';
            $j = 0;
            foreach ($where as $k => $v) {
                if($v==null){
                    $sql.=$k.' in("-1000000")';
                    $j++;
                    continue;
                }
                if(is_array($v)){
                    $sql.=$k.' in(';
                    foreach ($v as $d) {
                        $sql.='"'.$d.'",';
                    }
                    $sql=trim($sql,',');
                    $sql.=')';
                }else{
                    $sql = $sql . $k . '=' . '"' . $v . '"';
                }
                if ($j < $whereCount - 1) $sql = $sql . ' AND ';
                $j++;
            }
        }
        if($append!=null){
            $sql=$sql.' '.$append;
        }
        try {
//            mylog('query:'.$sql);
            $query = $GLOBALS['pdo']->query($sql);
            return $query;
        }catch (PDOException $e) {
            $error = 'Unable to PDOquery to the database server.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }
    }

    /**
     * 连接查询
     * @param $joinType 数组，连接方式如array('left join','left join')
     * @param $fields 字段数组 如：array('a.id','b.price')
     * @param $tables 表数组 如：array('tableA a','tableB b')
     * @param $joinField 连接字段数组 如：array(‘a.name=b.name’)
     * @param $where 数组 如 array('a.name'=>'abc','b.score'=>'80')
     * @param $group 排序 字符串 如 ' order by score desc'或约束条件 比如 ‘ and  b.score>80’
     */
    function joinQuery($joinType,$fields,$tables,$joinField,$where,$group){
        $sql=outerJoinStr($joinType,$fields,$tables,$joinField,$where,$group);
//    echo $sql;
        try {
            $query = $this->pdo->query($sql);

            return $query;
        }catch (PDOException $e) {
            $error = 'Unable to joinquery to the database server.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }

    }
    function pdoInsert($tableName,$value,$str=''){
        $sql='INSERT INTO '.$tableName.' SET ';
        $j = 0;
        $valueCount=count($value);
        $data='';
        foreach ($value as $k => $v) {
            $data .= $k . '=' . '"' . $v . '"';
            if ($j < $valueCount - 1) $data = $data . ',';
            $j++;
        }
        if($str=='ignore'){
            $sql=preg_replace('/INTO/',$str,$sql);
            $sql.=$data;
        }elseif($str=='update'){
            $sql.=$data.' on DUPLICATE KEY update '.$data;
        }else{
            $sql=$sql.$data.$str;
        }
        mylog($sql);
        try {
            $GLOBALS['pdo']->exec($sql);
            return $GLOBALS['pdo']->lastInsertId();

        }catch (PDOException $e) {
            $error = 'Unable to insert to the database server.' . $e->getMessage();
            return $error;
            exit();
        }
    }
    function pdoUpdate($tableName,array $value,array $where,$str=''){
        $sql='UPDATE '.$tableName.' SET ';
        $j = 0;
        $valueCount=count($value);
        foreach ($value as $k => $v) {
            $sql = $sql . $k . '=' . '"' . $v . '"';
            if ($j < $valueCount - 1) $sql = $sql . ',';
            $j++;
        }
        $whereCount = count($where);
        if ($whereCount > 0) {
            $sql = $sql . ' WHERE ';
            $j = 0;
            foreach ($where as $k => $v) {
                if($v==null){
                    $j++;
                    continue;
                }
                $sql = $sql . $k . '=' . '"' . $v . '"';
                if ($j < $whereCount - 1) $sql = $sql . ' AND ';
                $j++;
            }
        }
        $sql=$sql.$str;
//    mylog($sql);
//    echo $sql;
//    exit;
        try {
            $rows=$this->pdo->exec($sql);
            return $rows;

        }catch (PDOException $e) {
            $error = 'Unable to insert to the database server.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }

    }
    function pdoDelete($tableName,array $where,$str=''){
        $sql='DELETE from '.$tableName.' WHERE ';
        $j = 0;
        $whereCount = count($where);
        if ($whereCount > 0) {
            $j = 0;
            foreach ($where as $k => $v) {
                if($v==null){
                    $j++;
                    continue;
                }
                $sql = $sql . $k . '=' . '"' . $v . '"';
                if ($j < $whereCount - 1) $sql = $sql . ' AND ';
                $j++;
            }
        }else{
            if($str==''){
                return 'error:can\'t delete whole table';
            }

        }
        $sql=$sql.$str;
//    mylog($sql);
        try {
            $this->pdo->exec($sql);
            return $this->pdo->lastInsertId();

        }catch (PDOException $e) {
            $error = 'Unable to insert to the database server.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }

    }
    function outerJoinStr($joinType,$fields,$tables,$joinField,$where,$group)
    {
        $sql = 'SELECT ';
        $fieldsCount = count($fields);
        if ($fieldsCount > 0) {
            for ($i = 0; $i < $fieldsCount; $i++) {
                $sql = $sql . $fields[$i];
                if ($i < $fieldsCount - 1) $sql = $sql . ',';
            }
        } else {
            $sql = $sql . '* ';
        }
        $sql = $sql . ' FROM ' . $tables[0];
        for ($i = 1; $i < count($tables); $i++) {
            $sql = $sql . ' ' . $joinType[$i - 1] . ' ' . $tables[$i] . ' ON ' . $joinField[$i - 1];
        }
        $whereCount = count($where);
        if ($whereCount > 0) {
            $sql = $sql . ' WHERE ';
            $j = 0;
            foreach ($where as $k => $v) {
                if($v==null){
                    $j++;
                    continue;
                }
                $sql = $sql . $k . '=' . '"' . $v . '"';
                if ($j < $whereCount - 1) $sql = $sql . ' AND ';
                $j++;
            }
        }
        if ($group != null) {
            $sql = $sql . ' ' . $group;
        }

        return $sql;

    }
    function outerJoinQuery($joinType,$fields,$tables,$joinField,$where,$group){
        $str=outerJoinStr($joinType,$fields,$tables,$joinField,$where,null);
        for($i=0; $i<count($joinType);$i++){
            $joinType[$i]=preg_replace('/left/','right',$joinType[$i]);
        }
        $str=$str.' union all '.outerJoinStr($joinType,$fields,$tables,$joinField,$where,$group);
        echo $str;
        exit;

        try {
            $query = $this->pdo->query($str);

            return $query;
        }catch (PDOException $e) {
            $error = 'Unable to outerJoinQuery.' . $e->getMessage();
            include 'error.html.php';
            exit();
        }

    }
    function pdoBatchInsert($tableName,array $value,$str=''){
        $sql='INSERT INTO '.$tableName.' SET ';
        $j = 0;
        $v1=reset($value);
        $valueCount=count($v1);
        foreach ($v1 as $k => $v) {
            $sql = $sql . $k . '=' . ':' . $k ;
            if ($j < $valueCount - 1) $sql = $sql . ',';
            $j++;
        }
        if($str=='ignore'){
            $sql=preg_replace('/INTO/',$str,$sql);
        }else{
            $sql=$sql.$str;
        }
        $p=$this->pdo->prepare($sql);
        foreach ($value as $data) {
            foreach ($data as $k=>$v) {
                $p->bindValue($k,$v);
            }
            $p->execute();
        }


    }
    


}