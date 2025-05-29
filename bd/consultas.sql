/* Aca guarden todas las consultas sql y ponga para que sirve*/


/*EmpleadoModel get_empleado()*/
$sql = "SELECT e.*, c.*, d.* FROM empleado e INNER JOIN contrato c ON
             e.id_e = c.id_c INNER JOIN departamento d ON e.id_e = d.id_d";