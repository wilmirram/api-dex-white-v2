<?php


namespace App\Utils;


class SqlHelper
{

    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $con;

    public function __construct()
    {
        $this->servername = env('DB_HOST');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');
        $this->dbname = env('DB_DATABASE');

        $this->connect();
    }

    public static function setAndExecQuery($dbname, $username, $password, $query)
    {
        $sql = new SqlHelper();
        $con = $sql->setDb($dbname, $username, $password);

        $result = $con->query($query);
        $data = mysqli_fetch_assoc($result);
        $con->close();
        return $data;
    }

    private function setDb ($dbname, $username, $password)
    {
        return mysqli_connect($this->servername, $username, $password, $dbname);
    }

    private function connect()
    {
        $this->con = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
        if (!$this->con->connect_error) {
            return response()->json(['ERROR' => ['MESSAGE' => 'DATABASE CONNECTION ERROR']], 500);
        }
    }

    public static function exec($query)
    {
        $class = new SqlHelper();
        return $class->executeQuery($query);
    }

    private function executeQuery($query)
    {
        $result = $this->con->query($query);
        $data = mysqli_fetch_assoc($result);
        $this->con->close();
        return $data;
    }

    public static function run($query)
    {
        $class = new SqlHelper();
        return $class->runQuery($query);
    }

    private function runQuery($query)
    {
        $result = $this->con->query($query);
        $this->con->close();
        return $result;
    }

    public static function execParamQuery($query, $secondQuery)
    {
        $class = new SqlHelper();
        return $class->paramQuery($query, $secondQuery);
    }

    private function paramQuery($query, $secondQuery)
    {
        $result = $this->con->query($query);
        $data = ['result' => mysqli_fetch_assoc($result)];
        $this->con->next_result();
        $secondResult = $this->con->query($secondQuery);
        $data += ['param' => mysqli_fetch_assoc($secondResult)];
        $this->con->close();
        return $data;
    }
}
