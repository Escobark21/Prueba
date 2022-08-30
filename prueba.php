<?php

$link = 'mysql:host=localhost;dbname=prueba_ventas';
$usuario = 'root';
$pass = '';

try {
    $pdo = new PDO($link, $usuario, $pass);
} catch (PDOexception $e) {
    print "¡Eroor!" . $e ->getMessage(). "<br>";
    
}
function datos (){
    global $pdo;
    $sql= <<<SQL
    SELECT item.id,p.nombre proveedor, monthname(item.created) mes, CASE WHEN EXISTS (SELECT * FROM paquete pa WHERE pa.item = item.id) THEN 'producto' ELSE 'experiencia' END tipo_venta, t.total, p.id id_proveedor  FROM item JOIN actividad_evento ae ON ae.id = item.evento_id JOIN actividad a ON a.id = ae.actividad_id JOIN proveedor p ON p.id = a.proveedor_id JOIN transaccion t ON t.id = item.transaccion_id GROUP BY p.id, item.id,monthname(item.created);
    SQL;
    $smtp=$pdo->prepare($sql);
    $smtp->execute();
    $datos=$smtp->fetchAll(PDO::FETCH_ASSOC);
    //echo '<pre>';
    // print_r($datos);
    //echo '</pre>';
    $filas="";
    foreach($datos as $dato){
       list($id,$proveedor,$mes,$tipo_venta,$total,$id_proveedor) = array_values($dato);
       $filas.=<<<HTML
       <tr>
        <td><a href="https://admin.entrekids.cl/proveedor/{$id_proveedor}">{$proveedor}</a></td>
        <td>{$total}</td>
        <td>{$tipo_venta}</td>
       </tr>
       HTML;
    }
    $table=<<<HTML
    <table>
        <thead>
            <tr>
            <th>Provedor</th>
            <th>Total</th>
            <th>Categoria</th>
            </tr>
        </thead>
        <tbody>
            {$filas}
     </tbody>
    </table>
    HTML;
   $archivo=fopen("prueba.html","w");
   fwrite($archivo,$table);
   fclose($archivo);
   echo"Archivos generados con exito";
}
datos();