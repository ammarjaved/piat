 <?php


    $hostname    = '121.121.232.54'; 
    $port        =  5433;
    $database    = 'piat_checklist';
    $username    = 'postgres';
    $password    = 'Admin123';
 
    $pdo = new PDO("pgsql:host=$hostname;dbname=$database;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     
 
    
 

    // class Connection {
    //     private $server = "pgsql:host=121.121.232.54;dbname=piat_checklist;port=5433";
    //     private $username = "postgres";
    //     private $password = "Admin123";
    //     private $options = array(
    //         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    //     );
    //     protected $conn;
     
    //     public function open() {
    //         try {
    //             $this->conn = new PDO($this->server, $this->username, $this->password, $this->options);
    //             return $this->conn;
    //         } catch (PDOException $e) {
    //             echo "There is some problem in connection: " . $e->getMessage();
    //             return null;
    //         }
    //     }
     
    //     public function close() {
    //         $this->conn = null;
    //     }
    // }
    
    // $con = new Connection();

    
 
?>