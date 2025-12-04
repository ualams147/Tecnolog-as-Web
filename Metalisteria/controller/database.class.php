<?php

/* Clase encargada de gestionar las conexiones a la base de datos */
Class Db{

   private $servidor='cuberty.ddns.net:3306';
   private $usuario='fulsanuser';
   private $password='FulsanTecWeb25!';
   private $base_datos='metalisteria';
   private $link;
   private $res;
   private $array;
   private $stmt;
   static $_instance;

   /*La función construct es privada para evitar que el objeto pueda ser creado mediante new*/
   private function __construct(){
      $this->conectar();
   }

   /*Evitamos el clonaje del objeto. Patrón Singleton*/
   private function __clone(){ }

   /*Función encargada de crear, si es necesario, el objeto. Esta es la función que debemos llamar desde fuera de la clase para instanciar el objeto, y así, poder utilizar sus métodos*/
   public static function getInstance(){
      if (!(self::$_instance instanceof self)){
         self::$_instance=new self();
      }
      return self::$_instance;
   }

   /*Realiza la conexión a la base de datos.*/
   private function conectar(){
      $this->link=mysqli_connect($this->servidor, $this->usuario, $this->password);
      mysqli_select_db($this->link, $this->base_datos);
      mysqli_set_charset($this->link, "utf8mb4");
      //@mysqli_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
   }

   /*Método para ejecutar una sentencia sql*/
   public function query($sql){
      $this->res=mysqli_query($this->link, $sql);
      return $this->res;
   }

   public function prepare($sql, $vars){
        $a_params = array();
        $param_type = $this->getTypes($vars);
        $n = count($vars);
        $a_params[] = & $param_type;
        for($i = 0; $i < $n; $i++) {
            $a_params[] = & $vars[$i];
        }
      $this->stmt=mysqli_prepare($this->link, $sql);
      call_user_func_array(array($this->stmt, 'bind_param'), $a_params);
      $this->stmt->execute();
      $this->res=$this->stmt->get_result();
      return $this->res;
   }

   private function getTypes($var){
       $types = "";
       foreach($var as $v){
           $types .= $this->getTypeAsChar($v);
       }
       return $types;
   }

   private function getTypeAsChar($var){
        switch(gettype($var)){
            case "string": return "s";
            case "integer": return "i";
            case "double": return "d";
            default: return "b";
        }
   }


   /*Método para obtener una fila de resultados de la sentencia sql*/
   public function obtener_fila($res,$fila){
      if ($fila==0){
         $this->array=mysqli_fetch_array($res);
      }else{
         mysqli_data_seek($res,$fila);
         $this->array=mysqli_fetch_array($res);
      }
      return $this->array;
   }

   //Devuelve el último id del insert introducido
   public function lastID(){
      return mysqli_insert_id($this->link);
   }

   public function real_escape_string($escapestr){
       return mysqli_real_escape_string($this->link, $escapestr);
   }

   public function numberOfRows($res){
       $n=mysqli_num_rows($res);
       return $n;
   }

   //Devuelve el número de filas afectadas por la última operación
   public function affectedRows(){
       if($this->stmt) {
           return mysqli_stmt_affected_rows($this->stmt);
       }
       return mysqli_affected_rows($this->link);
   }
   
   //Devuelve el último error
   public function error(){
       if($this->stmt) {
           return $this->stmt->error;
       }
       return mysqli_error($this->link);
   }

}
?>